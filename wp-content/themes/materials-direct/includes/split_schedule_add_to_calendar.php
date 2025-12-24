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
                $email = 'pauls@materials-direct.com, andrewh@materials-direct.com';
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
    // collect billing address data for main email delivery
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d", $order_id);
    $results = $wpdb->get_results($sql);
    $total_delivery_count = count($results);

    $order = wc_get_order($order_id);

    if(!empty($order->get_billing_first_name())){
        $billing_firstname = $order->get_billing_first_name();
    }
    if(!empty($order->get_billing_last_name())){
        $billing_lastname = $order->get_billing_last_name();
    }
    if(!empty($order->get_billing_company())){
        $billing_company =  $order->get_billing_company();
    }
    if(!empty($order->get_billing_address_1())){
        $billing_address_1 =  $order->get_billing_address_1();
    }
    if(!empty($order->get_billing_address_2())){
        $billing_address_2 =  $order->get_billing_address_2();
    }
    if(!empty($order->get_billing_city())){
        $billing_city = $order->get_billing_city();
    }
    if(!empty($order->get_billing_postcode())){
        $billing_postcode = $order->get_billing_postcode();
    }
    if(!empty($order->get_billing_country())){
        $billing_country = $order->get_billing_country();
    }
    if(!empty($order->get_billing_email())){
        $billing_email = $order->get_billing_email();
    }
    if(!empty($order->get_billing_phone())){
        $billing_phone = $order->get_billing_phone();
    }
    $billing_phone_link = str_replace(' ', '', $billing_phone);
    if(!empty($order->get_meta('_billing_po'))){
        echo $billing_po = $order->get_meta('_billing_po');
    }
    // end collect billing address data for main email delivery

    // collect database data for main email delivery
    try {
        $results = $wpdb->get_results($sql, ARRAY_A);
        $count = 0;
        foreach ($results as $row) {
            $count++;
            $id = $row['id'];
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
            $radius_inch = $row['radius_inch'];
            $qty = $row['qty'];
            $date = $row['date'];
            $notes = $row['notes'];
            $shipping_unique = $row['shipping_unique'];
            $shipping = $row['shipping'];
            $shipping_numeric = $row['shipping_numeric'];
            $delivery_count = $row['delivery_count'];
            $order_count = $row['order_count'];
            $cost_per_part_string = preg_replace('/\(Average Price\)/', '', $row['cost_per_part']);
            $cost_per_part_raw = $row['cost_per_part_raw'];
            $country = $row['country'];
            $discount_rate = $row['discount_rate'];
            $voucher_code = $row['voucher_code'];
            $ah_voucher_percent = $row['voucher_percent'];
            $rolls_value = $row['rolls_value'];
            $rolls_length = $row['rolls_length'];
            $date = new DateTime($date);
            $formatted_date_pdf = $date->format('jS F Y');

            $customer_email = $order->get_billing_email();

        } // end foreach

    } catch (Exception $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }  
    // collect database data for main email delivery

    foreach ($order_items as $item_id => $item) {
        $is_scheduled_check = (int) $item->get_meta('is_scheduled');
        if ($is_scheduled_check === 1) {
  
                echo "is_scheduled value: " . $is_scheduled_check;
                
                $table_name = $wpdb->prefix . 'split_schedule_orders';
                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d", $order_id);

                $order_date = $order->get_date_created();

                if ($order_date) {
                    $date_string = $order_date->date('Y-m-d H:i:s'); // Adjust if $order_date is a DateTime object directly
                    $date_time = new DateTime($date_string);
                    $order_date_formatted = $date_time->format('F j, Y');
                }


                //$customer_email_3 = "andrewh@materials-direct.com";
                $message_3 = '<h2 style="display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Order Acknowledgement</h2>';

                if($payment_title == "Pay on account (Payment is due 15 days after despatch of goods on all credit orders)"){
                    $message_3 .= '<p>Hi '.$order->get_billing_first_name().',<br>Thank you for your credit account order. Please ensure payments are made on time</p>';
                } else {
                    $message_3 .= '<p>Hi '.$order->get_billing_first_name().',<br>Thank you for your order.</p>';
                }


                $message_3 .= '<h2 style="color: #ef9003; display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">
                Customer PO: '.$billing_po.'<br>MD Order: #'.$order_id.' <br>Order Date: '.$order_date_formatted.'</h2>';

                $message_3 .= '<div style="margin-bottom: 40px;">';

                $message_3 .= '<table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;">';
                $message_3 .= '<thead>';
                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Product</th>';
                $message_3 .= '<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 120px; text-align: center;">Price</th>';
                $message_3 .= '</tr>';
                $message_3 .= '</thead>';
                $message_3 .= '<tbody>';
                $message_3 .= '<tr class="order_item">';

                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; text-align: left; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;">';

                
                try {
                    $results = $wpdb->get_results($sql, ARRAY_A);
                    foreach ($results as $row) {
                        $order_count = $row['order_count'];
                    }
                } catch (Exception $e) {
                    // Handle any errors
                    echo "Error: " . $e->getMessage();
                }


                if($order_count == 1){
                    $message_3 .= '<strong style="color:#ef9003;">' . $product_name . '</strong>';
                }

                $message_3 .= '<ul class="wc-item-meta" style="list-style-type: none; padding-left: 0;">';
                if($order_count == 1){
                    $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Part shape</strong> <p>'.$part_shape.'</p>';

                    if(!empty($row['pdf_part_shape_link'])){
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Upload .PDF Drawing</strong> <p><a href="'.$pdf_part_shape_link.'">'.$pdf.'</a></p>';
                    }
                    if(!empty($row['dxf_part_shape_link'])){
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Upload .DXF Drawing</strong> <p><a href="'.$dxf_part_shape_link.'">'.$dxf.'</a></p>';
                    }

                    if($item->get_meta('_Currently Showing') == "mm"){
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Width ('.$item->get_meta('_Currently Showing').')</strong> <p>'.$item->get_meta('Width (MM)').'</p>';
                        if($rolls_value != "Rolls"){
                            $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Length ('.$item->get_meta('_Currently Showing').')</strong> <p>'.$item->get_meta('Length (MM)').'</p>';
                        }
                        if($rolls_value == "Rolls"){
                            $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Roll Length (Metres)</strong> <p>'.$item->get_meta('Roll Length (Metres)').'</p>';
                        }
                    } else {
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Width ('.$item->get_meta('_Currently Showing').')</strong> <p>'.$item->get_meta('Width (INCH)').'</p>';
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Length ('.$item->get_meta('_Currently Showing').')</strong> <p>'.$item->get_meta('Length (INCH)').'</p>';
                    }
                    if($radius != 0){
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Radius (MM)</strong> <p>'.$item->get_meta('Radius (MM)').'</p>';
                    }
                    if($radius_inch != 0){
                        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Radius (INCH)</strong> <p>'.$item->get_meta('Radius (INCH)').'</p>';
                    }
                }
                

                try {
                    $results = $wpdb->get_results($sql, ARRAY_A);
                    $qty_sum = 0;
                    $weights = [];
                
                    foreach ($results as $row) {
                        // get the total number of parts
                        $schedule_qty = $row['schedule_qty'];
                        $schedule_qty = str_replace(',', '', $schedule_qty);
                        $schedule_qty_array = explode(',', $schedule_qty);
                        $schedule_qty_array = array_map('floatval', $schedule_qty_array);
                        $row_sum = array_sum($schedule_qty_array);
                        $qty_sum += $row_sum;
                        // get the total number of parts

                        // Get the shipping weights
                        $shipping_weights = $row['shipping_weights'];
                        $weights[] = $shipping_weights . 'kg';
                    }
                } catch (Exception $e) {
                    // Handle any errors
                    echo "Error: " . $e->getMessage();
                }

                $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Total number of parts</strong> <p>'.$qty_sum.'</p>';

                $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Customer Shipping Weight(s)</strong> <p>'.implode(', ', $weights).'</p>';

                $message_3 .= '<strong class="wc-item-meta-label" style="width: 100%; padding-right: 10px; float: left; margin-right: .25em; margin-bottom: 0.45em; clear: both;">Scheduled Deliveries</strong>';
                
                
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
                        if($row['on_backorder'] == 1){
                            $discount_rate = 0;
                        } else {
                            $discount_rate = $row['discount_rate'];
                        }
                        $mcofc_fair = $row['mcofc_fair'];
                        $mcofc_fair_string = $row['mcofc_fair_string'];
                        $md_title = $row['md_title'];
                        $md_value = $row['md_value'];
                        $ah_cart_discount_price = $row['cart_discount_price'];
                        $cart_discount_prices[] = $ah_cart_discount_price;
                        $rolls_value = $row['rolls_value'];
                        $rolls_length = $row['rolls_length'];

                        $date = new DateTime($date);
                        $formatted_date_pdf = $date->format('jS F Y');

                        $schedule_qty = str_replace(',', '', $schedule_qty);
                        
                        $total_cart_discount_price = array_sum($cart_discount_prices);

                        $total_cart_discount_price_calc = $total_cart_discount_price / $delivery_count;

                        $vat_cart_discount = $total_cart_discount_price / $order_count;
        
                        $vat_display_customer = $order->get_subtotal() + $custom_fee_total - $vat_cart_discount;
                        $vat_percent = 20;
                        $vat_display_new = ($vat_display_customer * $vat_percent) / 100;



                        $shipping_display_new = $response;
                        $shipping_display_new_calc += $response;

                        if($rolls_value == "Rolls"){
                            $schedule_qty = $schedule_qty * $rolls_length;
                        }

                        if (!isset($voucher_code) || $voucher_code <= 0) {
                            $discount_code_value_new = 0;
                        } else {
                            $discount_code_value_new = $voucher_code / ($delivery_count * $order_count);
                        }


                        $total_1 = $cost_per_part_raw * $schedule_qty;

                        $discount_amount = ($total_1 * $discount_rate) / 100;



                        $mcofc_fair_value_calc = $ah_mcofc_fair_value / $delivery_count;


                        $cpp = $total_1 - $discount_amount;

                        $cppnew = $cpp;
                        $cart_discount_amount = ($cppnew * $cart_discount_percent) / 100;
                        $subtotal_display = number_format($cppnew, 2);
                        $subtotal_display_2 = $cppnew + $shipping_display_new;
                        $cart_discount_price_new = $cart_discount_amount;
                        $tf_3 = round($cart_discount_price_new, 2);
                        $tf_3_calc += round($cart_discount_price_new, 2);

                        $md_value_final = $md_value / $delivery_count / $order_count;
                        
                        $voucher_percent = $cppnew * $voucher_discount;


                        if($meta_qty_2 > 1){
                            $my_shipping_response = $meta_shipping_total_2 / $meta_qty_2;
                            $my_shipping_response_calc += $meta_shipping_total_2 / $meta_qty_2;
                        } else {
                            $my_shipping_response = $meta_shipping_total_2;
                            $my_shipping_response_calc += $meta_shipping_total_2;
                        }


                        preg_match_all('/(?:[a-zA-Z ]+?COFC|FAIR)/', $mcofc_fair_string, $mcofc_matches);

                        $mcofc_fair_numeric = 0;

                        foreach ($mcofc_matches[0] as $mcofc_match) {

                            

                            if($mcofc_match == "Manufacturers COFC"){
                                $match_number = 10;
                                $match_value = $mcofc_match . "- £" . $match_number;
                            } elseif($mcofc_match == "FAIR"){
                                $match_number = 95;
                                $match_value = $mcofc_match . "- £" . $match_number;
                            } else {
                                $match_number = 12.50;
                                $match_value = $mcofc_match . "- £" . $match_number;
                            }

                            $mcofc_fair_numeric += $match_number;

                        }


                        //$vat_amount = $cppnew + $shipping_display_new - $tf_3 + $md_value_final - $discount_code_value_new + $mcofc_fair_numeric;
                        $vat_amount = $cppnew + $my_shipping_response - $tf_3 + $md_value_final + $mcofc_fair_numeric - $voucher_percent; 

                        $vat_percent = 20;

                        if($country == "United Kingdom"){
                            $vat_display = ($vat_amount * $vat_percent) / 100;
                        } else {
                            $vat_display = 0;
                        }

                        $mcofc_fair_numeric_display += $mcofc_fair_numeric;

                        $subtotal = $cppnew;
                        //$total_final = $subtotal + $shipping_display_new + $vat_display - $tf_3 + $md_value_final - $discount_code_value_new + $mcofc_fair_numeric;
                        $total_final = $subtotal + $my_shipping_response + $vat_display - $tf_3 + $md_value_final + $mcofc_fair_numeric - $voucher_percent; 
                        $newtotal = floor($total_final * 100) / 100;



                        // Generate the PDF link for my-account
                        $custom_link = add_query_arg(array(
                            'id'       => $row['id'],
                            'order_no' => $row['order_no'],
                            'date'     => $row['date'],
                            'title'    => $row['title'],
                            'pdf_title'    => "Order Acknowledgement"
                        ), site_url('/pdf-generation-invoice/'));

                        $pdf_links[] = esc_url_raw($custom_link);
                        // Generate the PDF link for my-account

                        if($order_count > 1){
                            $message_3 .= '<p style="margin: 0; padding: 0;">'.$row['title'].'</p>';
                            $message_3 .= '<p style="margin: 0; padding: 0;">Part shape: '.$row['part_shape'].'</p>';
                            $message_3 .= '<p style="margin: 0; padding: 0;">Width (mm): '.$row['width'].'</p>';
                            $message_3 .= '<p style="margin: 0; padding: 0;">Length (mm): '.$row['length'].'</p>';
                        }
                        
                        $message_3 .= '<p style="margin: 0; padding: 0;">Qty: '.$row['schedule_qty'].'</p>';
                        $message_3 .= '<p style="margin: 0; padding: 0;">Dispatch Date: '.$formatted_date_pdf.'</p>';
                        $message_3 .= '<p style="margin: 0; padding: 0;">Details: '.$row['schedule_qty'].' parts to be dispatched in/on '.$formatted_date_pdf.'</p>';
                        $message_3 .= '<p style="margin: 0; padding: 0;">Shipping Weight: '.$row['shipping_weights'].'kg</p>';
                        $message_3 .= '<p style="margin: 0; padding: 0;">Discount: '.$discount_rate.'%</p>';


                        foreach ($mcofc_matches[0] as $mcofc_match) {
                            if($mcofc_match == "Manufacturers COFC"){
                                $match_number = 10;
                                $match_value = $mcofc_match . "- £" . $match_number;
                            } elseif($mcofc_match == "FAIR"){
                                $match_number = 95;
                                $match_value = $mcofc_match . "- £" . $match_number;
                            } else {
                                $mcofc_match = ltrim($mcofc_match);
                                $match_number = 12.50;
                                $match_value = $mcofc_match . "- £" . $match_number;
                            }
                            $message_3 .= '<p style="margin: 0; padding: 0;">('.$match_value.') </p>';
                        }


                        $message_3 .= '<p style="margin: 0; padding: 0;"><strong style="color:#ef9003;">Products Purchased Subtotal: £'.$subtotal_display.'</strong></p>';
                        $message_3 .= '<p style="margin: 0; padding: 0;"><strong>Total Price: £'.number_format($total_final, 2).'</strong></p><br>';


                        //$message_3 .= '<p style="margin: 0; padding: 0;">ROLLS VALUE: '.$rolls_value.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">MCOFC FAIR VALUE: '.$ah_mcofc_fair_value.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">$mcofc_fair_value_calc: '.$mcofc_fair_value_calc .'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">DELIVERY COUNT: '.$delivery_count.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">ORDER COUNT: '.$order_count.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">CCPR: '.$cost_per_part_raw.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">CPP: '.$cpp.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">SUBTOTAL: '.$subtotal.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">SHIPPING DISPLAY NEW: '.$shipping_display_new.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">$tf_3: '.$tf_3.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">MD VALUE FINAL: '.$md_value_final.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">DISCOUNT CODE VALUE NEW: '.$discount_code_value_new.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">MCOFC FAIR NUMERIC: '.$mcofc_fair_numeric.'</p><br>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">1. VAT DISPLAY: '.$vat_display.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">2. VAT AMOUNT: '.$vat_amount.'</p><br>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Total 1: '.$total_1.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Discount Amount: '.$discount_amount.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Discount: '.$discount_rate.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">QTY: '.$schedule_qty.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">MD Value: '.$md_value_final.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Total Sum mcofc fair: '.$total_sum_mcofc_fair.'</p><br><br><br>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Voucher Code: '.$discount_code_value_new.'</p>';

                        // $message_3 .= '<p style="margin: 0; padding: 0;">SUBTOTAL: '.$subtotal.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">SHIPPING DISPLAY NEW: '.$shipping_display_new.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">1. VAT DISPLAY: '.$vat_display.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">$tf_3: '.$tf_3.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">MD VALUE FINAL: '.$md_value_final.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">DISCOUNT CODE VALUE NEW: '.$discount_code_value_new.'</p>';
                        // $message_3 .= '<p style="margin: 0; padding: 0;">MCOFC FAIR NUMERIC: '.$mcofc_fair_numeric.'</p>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Voucher Percent: '.$voucher_percent.'</p><br>';
                        //$message_3 .= '<p style="margin: 0; padding: 0;">Voucher Percent: '.$response.'</p><br>';



                        
                        
                    } // end foreach

                } catch (Exception $e) {
                    // Handle any errors
                    echo "Error: " . $e->getMessage();
                }  

                // save the PDF links as meta data
                if (!empty($pdf_links)) {
                    $order->update_meta_data('_custom_pdf_links', $pdf_links); // save as array
                    $order->save();
                }
                // save the PDF links as meta data

                /* Calculate the final VAT for display */
                $final_voucher_discount_calc = $order->get_subtotal() * $ah_voucher_percent;
                $ah_final_vat = $order->get_subtotal() - $tf_3_calc - $final_voucher_discount_calc + $mcofc_fair_numeric_display + $my_shipping_response_calc;
                $ah_final_vat_result = ($ah_final_vat * $tax_rate) / 100;
                /* Calculate the final VAT for display */

                



                $message_3 .= '</ul>';

                $message_3 .= '</td>';

                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; text-align: center; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($order->get_subtotal(), 2).'</span></td>';
                $message_3 .= '</tr></tbody>';

                $message_3 .= '<tfoot>';

                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="row" style="color: #ef9003; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Products Purchased:</th>';
                $message_3 .= '<td class="td" style="color: #ef9003; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><strong>£'.number_format($order->get_subtotal(), 2).'</strong></td>';
                $message_3 .= '</tr>';


                if(isset($cart_discount_percent)){

                    $message_3 .= '<tr>';
                    $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Discount:</th>';
                    $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£-</span>'.$tf_3_calc.'</span></td>';
                    $message_3 .= '</tr>';

                }

                if($mcofc_fair_numeric_display != 0){
                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">All COFCs & FAIRs:</th>';
                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($mcofc_fair_numeric_display, 2).'</span></td>';
                $message_3 .= '</tr>';
                }

                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Shipping Total:</th>';
                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($custom_fee_total, 2).'</span></td>';
                $message_3 .= '</tr>';

                if($discount_code_value_new != 0){
                    $message_3 .= '<tr>';
                    $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Discount Code:</th>';
                    $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£-</span>'.number_format($voucher_code, 2).'</span></td>';
                    $message_3 .= '</tr>';
                }

                // if($md_value != 0){
                //     $message_3 .= '<tr>';
                //     $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Materials Direct COFC:</th>';
                //     $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.$md_value.'</span></td>';
                //     $message_3 .= '</tr>';
                // }

                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">VAT:</th>';
                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($ah_final_vat_result, 2).'</span></td>';
                $message_3 .= '</tr>';

                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Payment method:</th>';
                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;">'.$payment_title.'</td>';
                $message_3 .= '</tr>';

                $message_3 .= '<tr>';
                $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Total:</th>';
                $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($order->get_total(), 2).'</span></strong></td>';
                $message_3 .= '</tr>';

                $message_3 .= '</tfoot>';
                $message_3 .= '</table>';
                $message_3 .= '<small style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 10px; margin-top:6px; display: block;">Legal Disclaimer: The responsibility for selecting the correct material and uploading the correct data is with the customer. This order is non-cancellable. No refund can be issued. VAT 223799965</small>';
                $message_3 .= '</div>';


                $message_3 .= '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; margin-top: 20px; padding: 0;">';
                $message_3 .= '<tbody><tr>';
                $message_3 .= '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">';
                $message_3 .= '<h2 style="font-family:Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; color: #ef9003; display: block;">Our Bank Details</h2>';
                $message_3 .= '<h2 style="font-family:Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; color: #ef9003; display: block;">UNIVERSAL SCIENCE (UK) LIMITED T/A Materials Direct</h2>';
                $message_3 .= '<ul>';
                $message_3 .= '<li>Bank: <strong>HSBC</strong></li>';
                $message_3 .= '<li>Account number: <strong>42625172</strong></li>';
                $message_3 .= '<li>Sort code: <strong>40-33-33</strong></li>';
                $message_3 .= '<li>IBAN: <strong>GB04HBUK40333342625172</strong></li>';
                $message_3 .= '<li>BIC/Swift: <strong>HBUKGB4XXX</strong></li>';
                $message_3 .= '</ul>';
                $message_3 .= '</td>';
                $message_3 .= '</tbody></tr>';
                $message_3 .= '</table>';

                
                $message_3 .= '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; margin-top: 20px; padding: 0;">';
                $message_3 .= '<tbody><tr>';
                $message_3 .= '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">';
                $message_3 .= '<h2 style="font-family:Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; color: #ef9003; display: block;">Shipping address</h2>';
                //$message_3 .= '<address class="x_address" style="padding: 12px; color: #636363; border: 1px solid #e5e5e5;">'.$order->get_billing_first_name().' '.$order->get_billing_last_name().'<br>'.$order->get_billing_company().'<br>'.$_SESSION['street_address'].'<br>'.$_SESSION['address_line_2'].'<br>'.$_SESSION['city'].'<br>'.$_SESSION['county'].'<br>'.$_SESSION['postcode'].'<br>'.$_SESSION['country'] .'</address>';
                $message_3 .= '<address class="x_address" style="padding:12px; color:#636363; border:1px solid #e5e5e5">';
                $message_3 .= '' . $order->get_formatted_shipping_address(). '';
                $message_3 .= '</address>';


                $message_3 .= '</td>';
                $message_3 .= '</tbody></tr>';
                $message_3 .= '</table>';

                $message_3 .= '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; margin-top: 20px; padding: 0;">';
                $message_3 .= '<tbody><tr>';
                $message_3 .= '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">';
                $message_3 .= '<h2 style="color: #ef9003; display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">Billing address</h2>';
                $message_3 .= '<address class="address" style="padding: 12px; color: #636363; border: 1px solid #e5e5e5;">'.$order->get_billing_first_name().' '.$order->get_billing_last_name().'<br>'.$order->get_billing_company().'<br>'.$order->get_billing_address_1().'<br>'.$order->get_billing_address_2().'<br>'.$order->get_billing_city().'<br>'.$order->get_billing_postcode().'<br><a href="tel:'.$billing_phone_link.'" style="color: #202020; font-weight: normal; text-decoration: underline;">'.$order->get_billing_phone().'</a><br>'.$order->get_billing_email().'</address>';
                $message_3 .= '</td>';
                $message_3 .= '</tbody></tr>';
                $message_3 .= '</table>';

                //echo $message_3;

                $subject_3 = 'Order Acknowledgement[#' .$order_id. ']'; 
                $headers_3 = array('Content-Type: text/html; charset=UTF-8');

                $mail_sent_3 = wp_mail( $customer_email, $subject_3, $message_3, $headers_3);

                if ($mail_sent_3) {
                    echo "Email with invoice sent successfully.";
                    // generate pdf link for admin
                    $custom_link = add_query_arg(array(
                        'id'       => 82,
                        'order_no' => $order->get_order_number(),
                        'date'     => date('Y-m-d'),
                        'title'    => urlencode('Universal Science T-Pad 1500 - A0 - 0.23mm')
                    ), site_url('/pdf-generation-invoice/'));

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


