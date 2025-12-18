<?php
add_action('woocommerce_thankyou', 'custom_debug_entire_order', 10, 1);

function custom_debug_entire_order($order_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'split_schedule_orders';

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    // Billing details (with defaults)
    $billing_firstname = $order->get_billing_first_name() ?: '';
    $billing_lastname  = $order->get_billing_last_name() ?: '';
    $billing_company   = $order->get_billing_company() ?: '';
    $po_ref_no         = $order->get_meta('_po_order_ref_no') ?: '';
    $shipping_total    = $order->get_shipping_total();

    // User repayment terms
    $user_id          = get_current_user_id();
    $repayment_terms  = $user_id ? get_field('repayment_terms', 'user_' . $user_id) : '';

    $order_items = $order->get_items();
    $scheduled_item_index = 0; // To generate unique invoice suffix

    foreach ($order_items as $item_id => $item) {
        if ($item->get_meta('is_scheduled') != 1) {
            continue; // Skip non-scheduled items
            echo "<p>Regular Order Completed Successfully</p>";
        }

        $scheduled_item_index++;

        $product      = $item->get_product();
        $sku          = $product ? $product->get_sku() : '';
        $product_name = str_replace("&#x2122;", "", $item->get_name());

        $invoice_no   = 50101 + intval($order_id) . "-" . $scheduled_item_index;

        // Dimensions
        $width        = $item->get_meta('width') ?: 0;
        $length       = $item->get_meta('length') ?: 0;
        $width_in     = $width / 25.4;
        $length_in    = $length / 25.4;
        $radius       = $width / 2;
        $radius_in    = $radius / 25.4;

        // Files
        $pdf_url      = $item->get_meta('pdf_path') ?: '';
        $pdf_file_name= $pdf_url ? basename($pdf_url) : '';
        $dxf_url      = $item->get_meta('dxf_path') ?: '';
        $dxf_file_name= $dxf_url ? basename($dxf_url) : '';

        // Shape & rolls
        $shape_type   = $item->get_meta('shape_type');
        $rolls_value  = ($shape_type === 'rolls') ? 'Rolls' : '';
        $roll_length  = $item->get_meta('roll_length') ?: '';

        // Discounts - reset per item
        $cart_discount_percent = 0;
        $cart_discount_price   = 0;

        $discount_meta = $item->get_meta('_advanced_woo_discount_item_total_discount', true);
        if (!empty($discount_meta['cart_discount_details'])) {
            foreach ($discount_meta['cart_discount_details'] as $rule_data) {
                if (!empty($rule_data['cart_discount'])) {
                    $cart_discount_percent = $rule_data['cart_discount'];
                }
                if (!empty($rule_data['cart_discount_price'])) {
                    $cart_discount_price += (float)$rule_data['cart_discount_price'];
                }
            }
        }

        $notes = trim($item->get_meta('despatch_notes') ?: '');
        if (empty($notes)) {
            continue; // No delivery lines
        }

        $lines = preg_split("/\r\n|\n|\r/", $notes);
        $line_count = count($lines);

        // Count total delivery dates (for meta)
        preg_match_all('/(\d{2}\/\d{2}\/\d{4})/', $notes, $date_matches);
        $delivery_count = count($date_matches[1]);

        foreach ($lines as $line_index => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Extract date (required)
            if (!preg_match('/(\d{2}\/\d{2}\/\d{4})/', $line, $date_match)) {
                continue; // Skip lines without date
            }
            $my_date = $date_match[1];

            // Extract quantity (at start of line)
            $schedule_qty = null;
            if (preg_match('/^\s*([\d,]+)/', $line, $qty_match)) {
                $schedule_qty = (int)str_replace(',', '', $qty_match[1]);
            }
            if ($schedule_qty === null) {
                continue; // No qty found
            }

            // Optional COFC/FAIR fee
            $mcofc_fair = 0;
            $mcofc_fair_value = 0;
            if (preg_match('/-\s*(.*?)\s*£([\d\.]+)/', $line, $fee_match)) {
                $mcofc_fair = trim($fee_match[1]);
                $mcofc_fair_value = number_format((float)$fee_match[2], 2, '.', '');
            }

            // Determine if this is the last delivery line
            $last = ($line_index + 1 === $line_count) ? 1 : 0;

            // Schedule description
            $schedule = "part " . ($line_index + 1) . " of " . $delivery_count;

            // lets format the date
            $date_new = DateTime::createFromFormat('d/m/Y', $my_date);
            $formattedDateNew = $date_new->format('Y-m-d');

            $data = [
                'invoice_no'            => $invoice_no,
                'title'                 => $product_name,
                'firstname'             => $billing_firstname,
                'lastname'              => $billing_lastname,
                'company'               => $billing_company,
                'order_no'              => $order_id,
                'customer_po'           => $po_ref_no,
                'sku'                   => $sku,
                'order_count'           => $scheduled_item_index, // or total scheduled items?
                'delivery_count'        => $delivery_count,
                //'discount_rate'         => $discounts[$loop_iteration],
                'discount_rate'         => 0,
                'on_backorder'          => $item->get_meta('is_backorder'),
                'cost_per_part'         => $item->get_meta('price'),
                'cost_per_part_raw'     => $item->get_meta('price'),
                'split_schedule'        => 1,
                'status'                => 'pending',
                'schedule'              => $schedule,
                'schedule_qty'          => $schedule_qty,
                'part_shape'            => $shape_type,
                'pdf_part_shape_link'   => $pdf_url,
                'pdf'                   => $pdf_file_name,
                'dxf_part_shape_link'   => $dxf_url,
                'dxf'                   => $dxf_file_name,
                'notes'                 => $notes,
                'shipping'              => $shipping_total,
                'shipping_numeric'      => $shipping_total,
                'shipping_unique'       => $shipping_total,
                'shipping_weights'      => $item->get_meta('total_del_weight'), //
                'meta_shipping_qty'     => $delivery_count,
                'meta_shipping_total'   => $shipping_total,
                'dimension_type'        => 'mm',
                'width'                 => $width,
                'length'                => $length,
                'width_inch'            => round($width_in, 4),
                'length_inch'           => round($length_in, 4),
                'radius'                => $radius,
                'radius_inch'           => round($radius_in, 4),
                'qty'                   => $item->get_meta('qty'),
                'mm'                    => 0,
                'currency'              => '',
                'currency_rate'         => 1,
                'country'               => $order->get_shipping_country(),
                'last'                  => $last, //
                'cart_discount_price'   => $cart_discount_price,
                'cart_discount_percent' => $cart_discount_percent,
                'mcofc_fair'            => $mcofc_fair,
                'mcofc_fair_value'      => $mcofc_fair_value,
                'md_title'              => 0,
                'md_value'              => 0,
                'rolls_value'           => $rolls_value,
                'rolls_length'          => $roll_length,
                'repayment_terms'       => $repayment_terms,
                'date'                  => $formattedDateNew,
            ];

            echo '<pre style="color:red;">';
                print_r( $data  );
            echo '</pre>';

            // echo '<pre>';
            //     print_r( $order->get_data() );
            // echo '</pre>';

            

            // Optional: debug
            error_log('Inserting split schedule row: ' . print_r($data, true));

            $result = $wpdb->insert($table_name, $data);

            if ($result === false) {
                error_log('INSERT FAILED. Error: ' . $wpdb->last_error);
                error_log('Last query: ' . $wpdb->last_query);
            } else {
                error_log('INSERT SUCCESSFUL. Insert ID: ' . $wpdb->insert_id);
                echo "<p>Delivery Options Order Completed Successfully</p>";
            }
        }
    }
}