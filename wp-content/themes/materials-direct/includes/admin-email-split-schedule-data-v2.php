<?php
add_action('woocommerce_order_item_meta_end', 'add_order_number_to_admin_email_table', 10, 4);
function add_order_number_to_admin_email_table($item_id, $item, $order, $plain_text) {


    $order_items = $order->get_items();
    foreach ($order_items as $order_item) {
        $despatch_string = null;


        // First, collect both meta values from the same order item
        foreach ($order_item->get_meta_data() as $meta_data) {


            if ($meta_data->key === 'despatch_string') {
                $despatch_string = wp_kses_post($meta_data->value);
            }


        }

        // Only proceed if we have despatch_string data

        if (!empty($despatch_string)) {
            $despatch_string = rtrim(trim($despatch_string), ',');

            //echo $despatch_string;

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


            echo '<ul class="delivery-options-list">';
            // echo '<li>~Test</li>';
            
            
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

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $formatted_date = $date ? $date->format('jS F Y') : $date_str;

                $discount_percent = (floatval($discount) * 100) . '%';
                $discount_raw = (floatval($discount) * 100);

                if (!empty($desc)) {
                    $desc = preg_replace('/\s£/', ' - £', $desc);
                    $desc = '(' . $desc . ')';
                }

                echo "<br>";
                echo '<li class="delivery-options-list__li">Qty: ' . $qty . '</li>';
                echo '<li class="delivery-options-list__li">Dispatch Date: ' . $formatted_date . '</li>';
            }
            
            echo "</ul>";
        }
    }
}