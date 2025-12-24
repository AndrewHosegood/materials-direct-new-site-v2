<?php
add_action('woocommerce_thankyou', 'custom_debug_entire_order', 10, 1);

function custom_debug_entire_order($order_id) {
    global $wpdb;
    $domain = $_SERVER['HTTP_HOST'];
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $order = wc_get_order($order_id);
    $my_shipping_response_calc = 0;
    $custom_fee_total = 0;
    $cart_discount_percent = 0;
    $cart_discount_price = 0;
    $pdf_url = 0;
    $dxf_url = 0;
    $ah_mcofc_fair_value = 0;

    $payment_title = $order->get_payment_method_title();

    if (!$order) {
        return;
    }

    // Prevent duplicate execution on page refresh
    // if ($order->get_meta('_split_schedules_processed') === 'yes') {
    //     return;
    // }

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
    if($order){
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
                        'voucher_code'          => 0,
                        'voucher_percent'       => 0,
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
                $email = 'andrewh@materials-direct.com';
                $Subject_message_error = 'Split & Schedule Order( Database Error )';

                if ($result !== false) {
                    $has_inserted = true;
                    error_log('Split schedule INSERT SUCCESS: ID ' . $wpdb->insert_id . ' | Data: ' . print_r($data, true));
                    $requiredDate = date("F d Y", strtotime($formattedDateNew));

                } else {
                    error_log('Split schedule INSERT FAILED: ' . $wpdb->last_error);
                    error_log('Last query: ' . $wpdb->last_query);
                    echo "Error inserting data into the database.";
                    $db_error = $wpdb->last_error;
                    echo "Error inserting data into the database: " . $db_error;
                    $to = $email;
                    $message_error = 'There was a database error for Order No' . $order_id . '<br>Please contact the website administrator <a href="mailto:andrewh@materials-direct.com">here</a>';
                    $headers = array('Content-Type: text/html; charset=UTF-8'); // Email headers
                    $mail_sent_error = wp_mail($to, $Subject_message_error, $message_error, $headers);
                    if ($mail_sent_error) {
                        echo "Database error has accured. Please check your emails";
                        //$add_custom_meta_condition = false;
                    }
                }
            } // end foreach
            // echo '<pre>';
            // print_r( $order->get_data() );
            // echo '</pre>';
        } // end foreach
    } else {
        echo "Invalid order ID.";
    }





    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d", $order_id);
    $results = $wpdb->get_results($sql);
    $total_delivery_count = count($results);

    $order = wc_get_order($order_id);





    foreach ($order_items as $item_id => $item) {
        $is_scheduled_check = (int) $item->get_meta('is_scheduled');
        if ($is_scheduled_check === 1) {
  
                echo "is_scheduled value: " . $is_scheduled_check;
                
                $table_name = $wpdb->prefix . 'split_schedule_orders';
                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d", $order_id);

                $order_date = $order->get_date_created();



                $customer_email = "andrewh@materials-direct.com";

                $message_3 = '<h2 style="display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Order Acknowledgement</h2>';

                $message_3 .= '<ul class="wc-item-meta" style="list-style-type: none; padding-left: 0;">';
                
                        try {
                            $results = $wpdb->get_results($sql, ARRAY_A);
                            $shipping_display_new_calc = 0;
                            $mcofc_fair_numeric_display = 0;
                            $response = 0;
                            $tf_3_calc = 0;
                            foreach ($results as $row) {
                                $title = $row['title'];
                                $invoice_no = $row['invoice_no'];
                                $customer_po = $row['customer_po'];
                                $firstname = $row['firstname'];
                                $lastname = $row['lastname'];
                                $company = $row['company'];
                                $order_no = $row['order_no'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $schedule_qty = $row['schedule_qty'];
                                $part_shape = $row['part_shape'];
                                $pdf_part_shape_link = $row['pdf_part_shape_link'];
                                $pdf = $row['pdf'];
                                $dxf_part_shape_link = $row['dxf_part_shape_link'];
                                $dxf = $row['dxf'];
                                $dimension_type = $row['dimension_type'];
                                $width = $row['width'];
                                $width_inch = $row['width_inch'];
                                $length = $row['length'];
                                $length_inch = $row['length_inch'];
                                $radius = $row['radius'];
                                $qty = $row['qty'];
                                $date = $row['date'];
                                $notes = $row['notes'];
                                $shipping_unique = $row['shipping_unique'];
                                $shipping = $row['shipping'];
                                $shipping_numeric = $row['shipping_numeric'];
                                $shipping_weights = $row['shipping_weights'];
                                $shipping_duplicates = $row['shipping_duplicates'];
                                $meta_qty_2 = $row['meta_shipping_qty'];
                                $meta_shipping_total_2 = $row['meta_shipping_total'];
                                $delivery_count = $row['delivery_count'];
                                $order_count = $row['order_count'];
                                $voucher_code = $row['voucher_code'];
                                $ah_voucher_percent = $row['voucher_percent'];
                                $cost_per_part_string = preg_replace('/\(Average Price\)/', '', $row['cost_per_part']);
                                $cost_per_part_raw = $row['cost_per_part_raw'];
                                $country = $row['country'];
                                $on_backorder = $row['on_backorder'];
                                $date = new DateTime($date);
                                $formatted_date_pdf = $date->format('jS F Y');



                                $message_3 .= '<p style="margin: 0; padding: 0;">Qty: '.$row['schedule_qty'].'</p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;">Dispatch Date: '.$formatted_date_pdf.'</p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;">Details: '.$row['schedule_qty'].' parts to be dispatched in/on '.$formatted_date_pdf.'</p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;">Shipping Weight: '.$row['shipping_weights'].'kg</p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;">Width: '.$width.'%</p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;">Width: '.$length.'%</p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;"><strong style="color:#ef9003;">Products Purchased Subtotal: £'.$subtotal_display.'</strong></p>';
                                $message_3 .= '<p style="margin: 0; padding: 0;"><strong>Total Price: £'.number_format($total_final, 2).'</strong></p><br>';

                                
                                
                            } // end foreach

                        } catch (Exception $e) {
                            // Handle any errors
                            echo "Error: " . $e->getMessage();
                        }  

                $message_3 .= '</ul>';



                $subject_3 = 'Order Acknowledgement[#' .$order_id. ']'; 
                $headers_3 = array('Content-Type: text/html; charset=UTF-8');

                $mail_sent_3 = wp_mail( $customer_email, $subject_3, $message_3, $headers_3);

                if ($mail_sent_3) {
                    echo "Email with invoice sent successfully.";
                    // generate pdf link for admail

                } else {
                    echo "Error sending invoice email.";
                }


        } else {
            echo "3";
        } //end if is_scheduled_check
    }




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


