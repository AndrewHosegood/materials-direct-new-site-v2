<?php
add_action('woocommerce_order_item_meta_end', 'add_order_number_to_admin_email_table', 10, 4);
function add_order_number_to_admin_email_table($item_id, $item, $order, $plain_text) {

    if ($order->get_status() !== 'pending') {
        return;
    }

    // Count the number of products in the order
    $total_product_lines = count($order->get_items());

    // Get the voucher discount rate
    $voucher_discount = (float) $order->get_meta('_voucher_discount');

    // Get the shipping address
    //$address = WC()->session->get('custom_shipping_address');
    $address = $order->get_meta('_custom_shipping_address');

    if (!is_array($address)) {
        $address = [];
    }

    //print_r($address);

    // Get despatch data from THIS item only
    $despatch_string = $item->get_meta('despatch_string', true);
    $despatch_notes  = $item->get_meta('despatch_notes', true);
    $total_del_weight = $item->get_meta('total_del_weight', true);
    $cost_per_part    = $item->get_meta('cost_per_part', true);
    $stock_quantity   = $item->get_meta('stock_quantity', true);
    $get_discount     = $item->get_meta('_advanced_woo_discount_item_total_discount', true);

    /* Get despatch dates for this product */
    $shipments_new = $item->get_meta('despatch_date', true);
    $shipment_qty_map = [];
    if (is_array($shipments_new)) {
        foreach ($shipments_new as $date_key => $data) {
            $shipment_qty_map[$date_key] = isset($data['qty']) ? (int) $data['qty'] : 1;
        }
    }

    if (empty($despatch_string)) {
        return;
    }

    $despatch_string = rtrim(trim($despatch_string), ',');
    $pattern = '/(\d{1,3}(?:,\d{3})*),\s*(\d{2}\/\d{2}\/\d{4}),\s*(\d+(?:\.\d+)?),\s*(.*?)(?=,\s*\d{1,3}(?:,\d{3})*|$)/';
    preg_match_all($pattern, $despatch_string, $matches, PREG_SET_ORDER);

    $num_dates = count($matches);
    if (empty($matches)) {
        return;
    }

    // Calculate per-delivery weight
    $per_delivery_weight = ($total_del_weight !== null && $num_dates > 0)
        ? round($total_del_weight / $num_dates, 12)
        : null;

    // Extract despatch_notes lines
    $notes_lines = [];
    if (!empty($despatch_notes)) {
        $lines = preg_split('/\r\n|\r|\n/', trim($despatch_notes));
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $notes_lines[] = $line;
            }
        }
    }

    $shipping_total = $order->get_shipping_total();



    // ====================== NEW AVERAGED SHIPPING LOGIC ======================
    // Count TOTAL scheduled deliveries across the ENTIRE order
    $total_delivery_count = 0;
    $order_items = $order->get_items();
    foreach ($order_items as $order_item) {
        if ($order_item->get_meta('is_scheduled') != 1) {
            continue;
        }
        $item_despatch_string = trim($order_item->get_meta('despatch_string'));
        if (empty($item_despatch_string)) {
            continue;
        }

        $pattern_count = '/([\d,]+),\s*(\d{2}\/\d{2}\/\d{4})/';
        preg_match_all($pattern_count, $item_despatch_string, $count_matches);
        $total_delivery_count += count($count_matches[0]);
    }

    // Calculate average shipping per delivery (same for every line)
    $average_shipping = ($total_delivery_count > 0)
        ? round($shipping_total / $total_delivery_count, 3) 
        : 0;

    // Debug (remove or comment out after testing)
    //echo '<pre>Total deliveries in order: ' . $total_delivery_count . '<br>';
    //echo 'Total shipping: £' . $shipping_total . '<br>';
    //echo 'Average shipping per delivery: £' . number_format($average_shipping, 3) . '</pre>';
    // ====================== END NEW LOGIC ======================



    // Get cart discount percent
    $cart_discount_percent = 0;
    if (isset($get_discount['cart_discount_details']) && is_array($get_discount['cart_discount_details'])) {
        foreach ($get_discount['cart_discount_details'] as $detail) {
            $cart_discount_percent = $detail['cart_discount'] ?? 0;
        }
    }

    if ($num_dates = 1) {
    echo '<span style="font-weight: bold; margin-top: 0.8rem; display: block;">Scheduled Deliveries:</span>';
    }
    
    echo '<ul class="delivery-options-list">';

    if ($num_dates >= 2) {
        echo '<br><strong style="width:100%; padding-right:10px; float:left; margin-right:.25em; margin-bottom:0.45em; clear:both;">Scheduled Deliveries:</strong>';
    }

    foreach ($matches as $index => $match) {
        $delivery_shipping = $average_shipping;

        $qty      = str_replace(',', '', $match[1]);
        $date_str = $match[2];
        $discount = $match[3];
        $desc     = trim($match[4]);

        $meta_qty = $shipment_qty_map[$date_str] ?? 1;

        // Extract COFC costs from description
        preg_match_all('/£\s*([\d]+(?:\.\d{1,2})?)/', $desc, $price_matches);
        $cofc_total = 0.0;
        foreach ($price_matches[1] as $price) {
            $cofc_total += (float) $price;
        }

        $date = DateTime::createFromFormat('d/m/Y', $date_str);
        $formatted_date = $date ? $date->format('jS F Y') : $date_str;

        $discount_percent = (floatval($discount) * 100) . '%';
        $discount_raw     = (floatval($discount) * 100);

        if (!empty($desc)) {
            $desc = preg_replace('/\s£/', ' - £', $desc);
            $desc = '(' . $desc . ')';
        }

        // Calculate subtotal
        $subtotal = $cost_per_part * $qty;
        $discount_amount = ($discount_raw / 100) * $subtotal;

        $subtotal_after_discount = ($stock_quantity <= 0)
            ? $subtotal
            : $subtotal - $discount_amount;

        // Cart discount
        $cart_discount_amount = ($subtotal_after_discount * $cart_discount_percent) / 100;
        $tf_3 = round($cart_discount_amount, 2);

        // Voucher discount
        $voucher_percent = $subtotal_after_discount * $voucher_discount;

        // Shipping per delivery (adjusted by meta qty)
        // if ($meta_qty > 1) {
        //     $my_shipping_response = $delivery_shipping / $meta_qty;
        // } else {
        //     $my_shipping_response = $delivery_shipping;
        // }

        $my_shipping_response = $delivery_shipping;

        // VAT (UK only)
        $total_vat = $subtotal_after_discount - $tf_3 + $my_shipping_response + $cofc_total - $voucher_percent;
        $country = $address['country'] ?? '';
        $total_vat_display = ($country === "United Kingdom") ? $total_vat * 0.2 : 0;

        // Final total
        $final_total = $subtotal_after_discount - $tf_3 + $my_shipping_response + $total_vat_display + $cofc_total - $voucher_percent;

        // Output
        echo '<li class="delivery-options-list__li">Qty: ' . esc_html($qty) . '</li>';
        echo '<li class="delivery-options-list__li">Dispatch Date: ' . esc_html($formatted_date) . '</li>';

        if (isset($notes_lines[$index])) {
            echo '<li class="delivery-options-list__li">Details: ' . esc_html($notes_lines[$index]) . '</li>';
        }

        if ($per_delivery_weight !== null) {
            echo '<li class="delivery-options-list__li">(Shipping Weight: ' . number_format($per_delivery_weight, 4) . 'kg)</li>';
        }

        echo '<li class="delivery-options-list__li">(Discount: ' . $discount_percent . ')</li>';

        if (!empty($desc)) {
            echo '<li class="delivery-options-list__li">' . $desc . '</li>';
        }

        echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">Products Purchased Subtotal: £' . number_format($subtotal_after_discount, 2) . '</li>';
        echo '<li style="font-weight:bold;" class="delivery-options-list__li">Total Price: £' . number_format($final_total, 2) . '</li><br>';

        // echo '<li class="delivery-options-list__li">1. subtotal_after_discount: ' . esc_html($subtotal_after_discount) . '</li>';
        // echo '<li class="delivery-options-list__li">2. tf_3: ' . esc_html($tf_3) . '</li>';
        // echo '<li class="delivery-options-list__li">3. my_shipping_response: ' . esc_html($my_shipping_response) . '</li>';
        // echo '<li class="delivery-options-list__li">4. total_vat_display: ' . esc_html($total_vat_display) . '</li>';
        // echo '<li class="delivery-options-list__li">5. cofc_total: ' . esc_html($cofc_total) . '</li>';
        // echo '<li class="delivery-options-list__li">6. voucher_percent: ' . esc_html($voucher_percent) . '</li><br>';
    }

    echo '</ul>';
}