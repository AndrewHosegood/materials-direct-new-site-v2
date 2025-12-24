<?php
add_action('woocommerce_thankyou', 'custom_debug_entire_order', 10, 1);

function custom_debug_entire_order($order_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'split_schedule_orders';

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    // Prevent duplicate execution on page refresh
    if ($order->get_meta('_split_schedules_processed') === 'yes') {
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
    $has_inserted = false;

    foreach ($order_items as $item_id => $item) {

        if ($item->get_meta('is_scheduled') != 1) {
            continue; // Skip non-scheduled items
        }

        $despatch_string = trim($item->get_meta('despatch_string'));

        if (empty($despatch_string)) {
            continue;
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
        $last = 0;
        $delivery_count = 0;
        $schedule = 0;
        $loop_iteration = 0;
        

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

        // Regex pattern:
        // Group 1: quantity (1,000)
        // Group 2: date (20/12/2025)
        // Group 3: discount (0 or 0.04)
        // Group 4: one or more fee entries like "Manufacturers COFC £10.00"
        // Then repeats or ends
        $pattern = '/([\d,]+),\s*(\d{2}\/\d{2}\/\d{4}),\s*([\d.]+),\s*((?:[^,]+?£[\d\.]+(?:\s*,\s*[^,]+?£[\d\.]+)*)?)(?:,|$)/';

        preg_match_all($pattern, $despatch_string, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            continue;
        }

        $delivery_count = count($matches);

        foreach ($matches as $match) {
            $schedule_qty  = (int) str_replace(',', '', $match[1]);
            $my_date       = trim($match[2]);
            $discount_rate = (float) $match[3];
            $discount_rate_v = $discount_rate * 100;
            $fees_section  = trim($match[4]); // May contain multiple fees separated by commas

            $loop_iteration++;

            // Parse all fee entries in this section
            $fee_names  = [];
            $total_fee_value = 0.0;

            if (!empty($fees_section)) {
                // Split by comma, but only outside of the £ values
                // Safer: match each individual "Text £XX.XX"
                preg_match_all('/([^£]+?)£([\d\.]+)/', $fees_section, $fee_matches, PREG_SET_ORDER);

                foreach ($fee_matches as $fee_match) {
                    $name = trim($fee_match[1]);
                    $value = (float) $fee_match[2];

                    $fee_names[] = $name;
                    $total_fee_value += $value;
                }
            }

            // Build comma-separated string of fee names
            $mcofc_fair_string = !empty($fee_names) ? implode(', ', $fee_names) : '';

            // Hardcoded label
            $mcofc_fair = 'COFC\'s';

            // Format total value to 2 decimal places
            $mcofc_fair_value = number_format($total_fee_value, 2, '.', '');

            // Format date
            $date_obj = DateTime::createFromFormat('d/m/Y', $my_date);
            if ($date_obj === false) {
                continue;
            }
            $formattedDateNew = $date_obj->format('Y-m-d');

            $last = ($loop_iteration === $delivery_count) ? 1 : 0;

            // Final simplified data array
            $data = [
                    'invoice_no'            => $invoice_no,
                    'title'                 => $product_name,
                    'firstname'             => $billing_firstname,
                    'lastname'              => $billing_lastname,
                    'company'               => $billing_company,
                    'order_no'              => $order_id,
                    'customer_po'           => $po_ref_no,
                    'sku'                   => $sku,
                    'order_count'           => $scheduled_item_index,
                    'delivery_count'        => $delivery_count,
                    'discount_rate'         => $discount_rate_v,
                    'on_backorder'          => $item->get_meta('is_backorder'),
                    'cost_per_part'         => $item->get_meta('price'),
                    'cost_per_part_raw'     => $item->get_meta('cost_per_part'),
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
                    'shipping_weights'      => $item->get_meta('total_del_weight'), 
                    'shipping_duplicates'   => 0, 
                    'meta_shipping_qty'     => $delivery_count,
                    'meta_shipping_total'   => $shipping_total,
                    'dimension_type'        => 'mm',
                    'width'                 => $width,
                    'length'                => $length,
                    'width_inch'            => 0,
                    'length_inch'           => 0,
                    'radius'                => 0,
                    'radius_inch'           => 0,
                    'qty'                   => $item->get_meta('qty'),
                    'mm'                    => 0,
                    'currency'              => '',
                    'currency_rate'         => 1,
                    'country'               => $order->get_shipping_country(),
                    'last'                  => $last, //
                    'cart_discount_price'   => $cart_discount_price,
                    'cart_discount_percent' => $cart_discount_percent,

                    'mcofc_fair'          => $mcofc_fair,
                    'mcofc_fair_string'   => $mcofc_fair_string,
                    'mcofc_fair_value'    => $mcofc_fair_value,
                    'md_title'              => 0,
                    'md_value'              => 0,
                    'rolls_value'           => $rolls_value,
                    'rolls_length'          => $roll_length,
                    'repayment_terms'       => $repayment_terms,
                    'date'                => $formattedDateNew,
            ];


            // echo '<pre style="color:red;">';
            // print_r( $data  );
            // echo '</pre>';

            // Insert into database
            $result = $wpdb->insert($table_name, $data);

            if ($result !== false) {
                $has_inserted = true;
                error_log('Split schedule INSERT SUCCESS: ID ' . $wpdb->insert_id . ' | Data: ' . print_r($data, true));
            } else {
                error_log('Split schedule INSERT FAILED: ' . $wpdb->last_error);
                error_log('Last query: ' . $wpdb->last_query);
            }
        } // end foreach
    } // end foreach

    // Mark as processed to prevent duplicates
    $order->update_meta_data('_split_schedules_processed', 'yes');
    $order->save();

    // Show success message only once, if any rows were inserted
    if ($has_inserted) {
        wc_add_notice('Delivery Options Order Completed Successfully', 'success', ['notice_class' => 'my-split-success']);
        
        // Force the notices to display immediately on this page
        wc_print_notices();
    }

    // echo '<pre>';
    // print_r( $order->get_data() );
    // echo '</pre>';

}


/* USE THIS CODE ON THE PAGE PDF GENERATION INVOICE */
/*
$mcofc_fair_string = 'Manufacturers COFC,, First Article Inspection Report,,  Materials Direct COFC,, ';

$mcofc_fair_formatted = '';

if (!empty($mcofc_fair_string)) {

    $mcofc_fair_array = array_filter(
        array_map('trim', explode(',', $mcofc_fair_string)),
        function ($value) {
            return $value !== '';
        }
    );

    $mcofc_fair_array = array_map(function ($value) {
        if ($value === "Manufacturers COFC") {
            return '(' . $value . ' - £10)';
        } elseif ($value === "First Article Inspection Report") {
            return '(' . $value . ' - £95)';
        } else {
            return '(' . $value . ' - £12.50)';
        }
    }, $mcofc_fair_array);

    $mcofc_fair_formatted = implode('<br>', $mcofc_fair_array);
}

echo $mcofc_fair_formatted;
*/
