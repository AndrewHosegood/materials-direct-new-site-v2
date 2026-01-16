<?php
add_action('woocommerce_order_item_meta_end', 'add_order_number_to_admin_email_table', 10, 4);
function add_order_number_to_admin_email_table($item_id, $item, $order, $plain_text) {

    // Get the voucher discount rate
    $voucher_discount = (float) $order->get_meta('_voucher_discount'); // Retrieve the meta value
    echo "Voucher Discount: " . $voucher_discount . "<br><br>";
    // Get the voucher discount rate

    $order_items = $order->get_items();
    foreach ($order_items as $order_item) {
        $despatch_string = null;
        $despatch_notes  = null;
        $total_del_weight = null;

        // First, collect both meta values from the same order item
        foreach ($order_item->get_meta_data() as $meta_data) {

            // echo "<pre>";
            // print_r($meta_data);
            // echo "</pre>";

            if ($meta_data->key === 'despatch_string') {
                $despatch_string = wp_kses_post($meta_data->value);
            }
            if ($meta_data->key === 'despatch_notes') {
                $despatch_notes = wp_kses_post($meta_data->value);
            }
            if ($meta_data->key === 'total_del_weight') {
                $total_del_weight = floatval(wp_kses_post($meta_data->value));
            }
            if ($meta_data->key === 'cost_per_part') {
                $cost_per_part = floatval(wp_kses_post($meta_data->value));
            }
            if ($meta_data->key === 'stock_quantity') {
                $stock_quantity = floatval(wp_kses_post($meta_data->value));
            }

            if ($meta_data->key === '_advanced_woo_discount_item_total_discount') {
		
                $cart_discount_details = $meta_data->value;

                if (isset($cart_discount_details['cart_discount_details']) && is_array($cart_discount_details['cart_discount_details']) && !empty($cart_discount_details['cart_discount_details'])) {
                    foreach ($cart_discount_details['cart_discount_details'] as $discount) {
                        if (isset($discount['cart_discount'])) {
                            $cart_discount_percent = $discount['cart_discount'];
                        } else {
                            $cart_discount_percent = 0;
                        }
                        if (isset($discount['cart_discount_price'])) {
                            $cart_discount_price = $discount['cart_discount_price'];
                            break;
                        } else {
                            $cart_discount_price = 0;
                        }
                    }
                } else {
                    echo "Cart discount details not found or invalid.";
                }
            }

        }

        // Only proceed if we have despatch_string data
        echo $despatch_string;
        if (!empty($despatch_string)) {
            $despatch_string = rtrim(trim($despatch_string), ',');

            $pattern = '/(\d{1,3}(?:,\d{3})*),\s*(\d{2}\/\d{2}\/\d{4}),\s*(\d+(?:\.\d+)?),\s*(.*?)(?=,\s*\d{1,3}(?:,\d{3})*|$)/';
            preg_match_all($pattern, $despatch_string, $matches, PREG_SET_ORDER);

            $num_dates = count($matches);

            // Calculate per-delivery weight (only if we have a valid total_del_weight and at least one date)
            $per_delivery_weight = ($total_del_weight !== null && $num_dates > 0) 
                ? round($total_del_weight / $num_dates, 12) 
                : null;

            // Extract despatch_notes lines into an array (one per scheduled delivery)
            $notes_lines = [];
            if (!empty($despatch_notes)) {
                //echo $despatch_notes . "<br>";
                // Split by line breaks (handles \r\n, \n, \r)
                $lines = preg_split('/\r\n|\r|\n/', trim($despatch_notes));

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $notes_lines[] = $line;
                    }
                }
            }    

            

            // Get the total shipping cost for the entire order (excluding taxes if you prefer incl. tax use get_shipping_total())
            $shipping_total = $order->get_shipping_total(); // This is the shipping cost (excl. tax)
            $shipping_calc = $shipping_total / $num_dates;

            //echo "<p>Shipping: " . $shipping_total . "</p>";

            echo '<ul class="delivery-options-list">';

            if ($num_dates >= 2) {
                echo '<strong style="width:100%; padding-right:10px; float:left; margin-right:.25em; margin-bottom:0.45em; clear:both;">Scheduled Deliveries:</strong>';
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

                echo number_format($cofc_total, 2) . "<br>";


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
                $total_1 = $cost_per_part * $qty; // temporary delete

                $discount_amount = ($discount_raw / 100) * $subtotal;

                if($stock_quantity <= 0){
                    $subtotal_after_discount = $subtotal;
                } else {
                    $subtotal_after_discount = $subtotal - $discount_amount;
                }
                

                $vat_total = $total_after_discount + $cart_discount_price + $shipping_total; // delete?
                $vat_amount = $vat_total * 0.2; // delete?
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

                // Calculate Final Total
                $final_total = $subtotal_after_discount - $tf_3 + $shipping_calc + $total_vat_display + $cofc_total - $voucher_percent; //** 

                echo "<br>";
                echo '<li class="delivery-options-list__li">Qty: ' . $qty . '</li>';
                echo '<li class="delivery-options-list__li">Dispatch Date: ' . $formatted_date . '</li>';
                //echo "<pre>";
                //print_r($notes_lines[$index]);
                //echo "</pre>";
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
                echo '<li style="font-weight:bold;" class="delivery-options-list__li">Total Price: £' . number_format($final_total, 2) . '</li>';






                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">subtotal: ' . $subtotal . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">total_1: ' . $total_1 . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">tf_3: ' . $tf_3 . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">shipping_calc: ' . $shipping_calc . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">VAT: ' . $total_vat_display . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">FAIRS: ' . number_format($cofc_total, 2) . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">Vouchers: ' . $voucher_percent . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">Vouchers: ' . $voucher_discount . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">Cost Per Part: ' . $cost_per_part . '</li>';
                echo '<li style="font-weight:bold; color:orange;" class="delivery-options-list__li">Qty: ' . $qty . '</li>';















            }

            echo "</ul>";
        }
    }
}