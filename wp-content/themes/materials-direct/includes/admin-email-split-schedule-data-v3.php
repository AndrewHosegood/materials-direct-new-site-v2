<?php
add_action('woocommerce_order_item_meta_end', 'add_order_number_to_admin_email_table', 10, 4);
function add_order_number_to_admin_email_table($item_id, $item, $order, $plain_text) {

    // count the number of products in the order
    $total_product_lines = count( $order->get_items() );
    // count the number of products in the order

    // Get the voucher discount rate
    $voucher_discount = (float) $order->get_meta('_voucher_discount'); // Retrieve the meta value
    // Get the voucher discount rate

    // Get despatch_string from THIS item only
    $despatch_string = $item->get_meta('despatch_string', true);
    $despatch_notes = $item->get_meta('despatch_notes', true);
    $total_del_weight = $item->get_meta('total_del_weight', true);
    $cost_per_part = $item->get_meta('cost_per_part', true);
    $stock_quantity = $item->get_meta('stock_quantity', true);

    $get_discount = $item->get_meta('_advanced_woo_discount_item_total_discount', true);

    // echo "<pre>";
    // print_r($get_discount);
    // echo "</pre>";



    if (isset($get_discount['cart_discount_details']) && is_array($get_discount['cart_discount_details']) && !empty($get_discount['cart_discount_details'])) {
        foreach ($get_discount['cart_discount_details'] as $detail) {
            $cart_discount_percent   = $detail['cart_discount'] ?? '';
        }
    }


    // if (!empty($cart_discount_lines)) {
    //     $total_cart_discount = 0;
    //     foreach ($cart_discount_lines as $line) {
    //         $total_cart_discount += $line['amount'];
    //     }
    //     if (count($cart_discount_lines) > 1) {
    //     }
    // }

    

    if (empty($despatch_string)) {
        return;
    }

    $despatch_string = rtrim(trim($despatch_string), ',');

    //echo $despatch_string;

    $pattern = '/(\d{1,3}(?:,\d{3})*),\s*(\d{2}\/\d{2}\/\d{4}),\s*(\d+(?:\.\d+)?),\s*(.*?)(?=,\s*\d{1,3}(?:,\d{3})*|$)/';
    preg_match_all($pattern, $despatch_string, $matches, PREG_SET_ORDER);

    $num_dates = count($matches);

    if (empty($matches)) {
        return;
    }





        // Calculate per-delivery weight (only if we have a valid total_del_weight and at least one date)
        $per_delivery_weight = ($total_del_weight !== null && $num_dates > 0) 
            ? round($total_del_weight / $num_dates, 12) 
            : null;

        // Extract despatch_notes lines into an array (one per scheduled delivery)
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
        //$shipping_calc = $shipping_total / $num_dates / $total_product_lines; // lets temporarily add in $total_product_lines (but need to get dynamic shipping values coming in as a proper fix)
         $shipping_calc = $shipping_total / 5;

    echo '<ul class="delivery-options-list">';

    if ($num_dates >= 2) {
        echo '<br><strong style="width:100%; padding-right:10px; float:left; margin-right:.25em; margin-bottom:0.45em; clear:both;">Scheduled Deliveries:</strong>';
    }

    foreach ($matches as $index => $match) {

        $qty = str_replace(',', '', $match[1]);
        $date_str = $match[2];
        $discount = $match[3];
        $desc = trim($match[4]);

        preg_match_all('/£\s*([\d]+(?:\.\d{1,2})?)/', $desc, $price_matches);

        $cofc_total = 0.0;

        foreach ($price_matches[1] as $price) {
            $cofc_total += (float) $price;
        }

        //echo number_format($cofc_total, 2) . "<br>";

        $date = DateTime::createFromFormat('d/m/Y', $date_str);
        $formatted_date = $date ? $date->format('jS F Y') : $date_str;

        $discount_percent = (floatval($discount) * 100) . '%';
        $discount_raw = (floatval($discount) * 100);

        if (!empty($desc)) {
            $desc = preg_replace('/\s£/', ' - £', $desc);
            $desc = '(' . $desc . ')';
        }

        // Calculate subtotal
        $subtotal = $cost_per_part * $qty;
        $discount_amount = ($discount_raw / 100) * $subtotal;
        if($stock_quantity <= 0){
            $subtotal_after_discount = $subtotal;
        } else {
            $subtotal_after_discount = $subtotal - $discount_amount;
        }
        // Calculate subtotal

        // Calculate the cart discount based on percent
        $cart_discount_amount = ($subtotal_after_discount * $cart_discount_percent) / 100; //** 
        $tf_3 = round($cart_discount_amount, 2);
        // Calculate the cart discount based on percent

        // Calculate the voucher discount
        $voucher_percent = $subtotal_after_discount * $voucher_discount;
        // Calculate the voucher discount

        // Calculate VAT
        $total_vat = $subtotal_after_discount - $tf_3 + $shipping_calc + $cofc_total - $voucher_percent; //** 
        $total_vat_display = $total_vat * 0.2;
        // Calculate VAT

        // Calculate VAT based on country (i need to get the tax rate to make this work)
        // if($country == "United Kingdom"){
        //     $vat_display = ($vat_amount * $tax_rate) / 100;
        // } else {
        //     $vat_display = 0;
        // }
        // Calculate VAT based on country (i need to get the tax rate to make this work)

        // Calculate Final Total
        $final_total = $subtotal_after_discount - $tf_3 + $shipping_calc + $total_vat_display + $cofc_total - $voucher_percent; //** 

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
        echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">Products Purchased Subtotal: £' . number_format($subtotal_after_discount, 2) . '</li>'; //** 
        echo '<li style="font-weight:bold;" class="delivery-options-list__li">Total Price: £' . number_format($final_total, 2) . '</li><br>';


 
        // echo '<li class="delivery-options-list__li">1. subtotal_after_discount: ' . esc_html($subtotal_after_discount) . '</li>';
        // echo '<li class="delivery-options-list__li">2. tf_3: ' . esc_html($tf_3) . '</li>';
        echo '<li class="delivery-options-list__li">3. shipping_calc: ' . esc_html($shipping_calc) . '</li>';
        // echo '<li class="delivery-options-list__li">4. cofc_total: ' . esc_html($cofc_total) . '</li>';
        // echo '<li class="delivery-options-list__li">5. Voucher Percent: ' . esc_html($voucher_percent) . '</li>';
        // echo '<li class="delivery-options-list__li">Cart Discount Amount: ' . esc_html($cart_discount_amount) . '</li>';
        // echo '<li class="delivery-options-list__li">subtotal_after_discount: ' . esc_html($subtotal_after_discount) . '</li>';
        // echo '<li class="delivery-options-list__li">Cart Discount Percent: ' . esc_html($cart_discount_percent) . '</li>';
        echo '<li class="delivery-options-list__li">num_dates: ' . esc_html($num_dates) . '</li><br>';
        // echo '<li class="delivery-options-list__li">shipping total: ' . esc_html($shipping_total) . '</li>';
        // echo '<li class="delivery-options-list__li">Product Count: ' . esc_html($total_product_lines) . '</li>';
        // echo '<li class="delivery-options-list__li">total_vat_display: ' . esc_html($total_vat_display) . '</li>';
        // echo '<li class="delivery-options-list__li">Total VAT: ' . esc_html($total_vat) . '</li><br>';
        
        
 

        // echo '<li class="delivery-options-list__li">Cost Per Part: ' . esc_html($cost_per_part) . '</li>';
        // echo '<li class="delivery-options-list__li">Stock Quantity: ' . esc_html($stock_quantity) . '</li>';
        // echo '<li class="delivery-options-list__li">Get Discount: ' . esc_html($get_discount) . '</li>';
        // echo '<li class="delivery-options-list__li">Discount Amount: ' . wc_format_decimal($line['amount'], 2) . '</li>';
        // echo '<li class="delivery-options-list__li">Discount Value: ' . wc_format_decimal($line['value'], 2) . '</li>';
        // esc_html($line['value'])
        

    }

    echo '</ul>';
}
