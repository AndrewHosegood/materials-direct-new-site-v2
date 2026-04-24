<?php
add_action('woocommerce_thankyou', 'split_schedule_insert_data', 10, 1);

function split_schedule_insert_data($order_id) {
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
    $tax_rate = 0;
    $item_totals = $order->get_order_item_totals();
    $customer_email = $order->get_billing_email();

    // Get the voucher discount rate
    $voucher_discount = (float) $order->get_meta('_voucher_discount'); // Retrieve the meta value
    // Get the voucher discount rate

    // Get the tax rates
    $tax_rates = [];
    foreach ($order->get_tax_totals() as $tax) {
        $tax_rates[] = [
            'rate' => $tax->rate_id,
            'label' => $tax->label,
            'amount' => $tax->amount,
            'percentage' => WC_Tax::get_rate_percent($tax->rate_id)
        ];
        if(WC_Tax::get_rate_percent($tax->rate_id)){
            $tax_rate = WC_Tax::get_rate_percent($tax->rate_id);
        }
        
    }
    $tax_rate = str_replace('%', '', $tax_rate);
    // Get the tax rates

    $payment_title = $order->get_payment_method_title();

    // Extract the voucher discount value
    $discount_value = isset($item_totals['discount']) ? $item_totals['discount']['value'] : '-£0.00';
    $numericAmount = str_replace(['-', '£'], '', $discount_value);
    $numericAmount = preg_replace('/[^0-9.,]/', '', $discount_value);
    // Extract the voucher discount value

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



    // ====================== NEW AVERAGED SHIPPING LOGIC ======================
    // Count total number of scheduled deliveries across ALL products
    $total_delivery_count = 0;
    foreach ($order_items as $item) {
        if ($item->get_meta('is_scheduled') != 1) {
            continue;
        }
        $despatch_string = trim($item->get_meta('despatch_string'));
        if (empty($despatch_string)) {
            continue;
        }

        $pattern_count = '/([\d,]+),\s*(\d{2}\/\d{2}\/\d{4})/';
        preg_match_all($pattern_count, $despatch_string, $count_matches);
        $total_delivery_count += count($count_matches[0]);
    }

    // Calculate average shipping per delivery
    $average_shipping = ($total_delivery_count > 0)
        ? round($shipping_total / $total_delivery_count, 3)   // or use 2 for standard money rounding
        : 0;

    // ====================== END NEW LOGIC ======================


      

    if($order){
        foreach ($order_items as $item_id => $item) {

            if ($item->get_meta('is_scheduled') != 1) {
                continue; 
            }

            $despatch_string = trim($item->get_meta('despatch_string'));

            if (empty($despatch_string)) {
                continue;
            }

            $scheduled_item_index++;

            $product      = $item->get_product();
            $sku          = $product ? $product->get_sku() : '';
            $product_name = str_replace('™', '', $item->get_name());
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

            // Lets calculate the meta shipping quantity
            $shipments_new   = $item->get_meta('despatch_date', true);

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
            $stock_quantity = $item->get_meta('stock_quantity');

            if($stock_quantity <= 0){
                $my_backorder = 1;
            } else {
                $my_backorder = 0;
            }


            foreach ($matches as $index => $match) {
                $schedule_qty  = (int) str_replace(',', '', $match[1]);
                $schedule = "part " . ($index + 1) . " of " . $delivery_count;
                $my_date       = trim($match[2]);
                $discount_rate = (float) $match[3];
                $discount_rate_v = $discount_rate * 100;
                $fees_section  = trim($match[4]); // May contain multiple fees separated by commas
                

                // lets extract the meta shipping values
                $meta_shipping_qty = 1;
                if (isset($shipments_new[$my_date])) {
                    $shipment_data = $shipments_new[$my_date];
                    $meta_shipping_qty = $shipment_data['qty'];
                }
                // lets extract the meta shipping values

                $loop_iteration++;

                // Parse all fee entries in this section
                $fee_names = [];
                $total_fee_value = 0.0;

                if (!empty($fees_section)) {
                    // Improved regex: handles possible extra commas/spaces better
                    preg_match_all('/\s*([^£,]+?)\s*£([\d\.]+)/', $fees_section, $fee_matches, PREG_SET_ORDER);
                    
                    foreach ($fee_matches as $fee_match) {
                        $name = trim($fee_match[1]);       
                        $value = (float) $fee_match[2];
                        
                        if ($name !== '') {   
                            $fee_names[] = $name;
                            $total_fee_value += $value;
                        }
                    }
                }

                // Build clean key for lookup - NO extra commas
                $mcofc_fair_string = !empty($fee_names) 
                    ? implode(', ', $fee_names) 
                    : '';


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

                // Use the averaged shipping value for every delivery
                $delivery_shipping = $average_shipping;


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
                        'voucher_code'          => $numericAmount,
                        'voucher_percent'       => $voucher_discount,
                        'on_backorder'          => $my_backorder,
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
                        'meta_shipping_qty'     => $meta_shipping_qty, 
                        'meta_shipping_total'   => $delivery_shipping, 
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
                        'rolls_value'           => 0,
                        'rolls_length'          => 0,
                        'repayment_terms'       => $repayment_terms,
                        'date'                => $formattedDateNew,
                ];


                // echo '<pre style="color:red;">';
                // print_r( $data  );
                // echo '</pre>';

                // Insert into database
                $result = $wpdb->insert($table_name, $data);

                $pauls_email = get_field('email_to_paul', 'option') ?: 'andrewh@materials-direct.com'; // Retrieve the ACF field email addresses from backend for paul
                $email = 'andrewh@materials-direct.com'; // email address for inser failure email

                $Subject_message_error = 'Split & Schedule Order( Database Error )';

                if ($result === false) {
                    error_log('Split schedule INSERT FAILED: ' . $wpdb->last_error);
                    error_log('Last query: ' . $wpdb->last_query);
                    echo "Error inserting data into the database.";
                    $db_error = $wpdb->last_error;
                    echo "Error inserting data into the database: " . $db_error;
                    $to = $email;
                    $message_error = 'There was a database error for Order No' . $order_id . '<br>Please contact the website administrator <a href="mailto:andrewh@materials-direct.com">here</a>';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $mail_sent_error = wp_mail($to, $Subject_message_error, $message_error, $headers);
                    if ($mail_sent_error) {
                        echo "Database error has accured. Please check your emails";
                        //$add_custom_meta_condition = false;
                    }
                } else {
                    echo "Data inserted successfully into the database.";
                    $has_inserted = true;
                    $requiredDate = date("F d Y", strtotime($formattedDateNew));

                    // Send email to Paul
                    $to = $pauls_email;
                    $subject = 'Delivery Options ('.$schedule.')';
                    $message = '<h2 style="display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Delivery Options Order ('.$schedule.')</h2>';
                    $message .= '<h3 style="display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 22px; color: #000000;">Scheduled Date: '.$requiredDate.'</h3>';
                    $message .= $billing_firstname . ' ' . $billing_lastname . ' has placed a delivery options order. Details are below:';
                    $message .= '<ul>';
                    $message .= '<li>Order ID: '. $order_id .'</li>';
                    $message .= '<li>Product Name: '. $product_name .'</li>';
                    $message .= '<li>Client Name: '. $billing_firstname .' ' . $billing_lastname . ' ' . $billing_company .'</li>';
                    $message .= '<li>Part Shape: '. $shape_type .'</li>';
                    $message .= '<li>PDF Upload: '. $pdf_url .'</li>';
                    $message .= '<li>DXF Upload: '. $dxf_url .'</li>';
                    $message .= '<li>Width (MM): '. $width .'</li>';
                    $message .= '<li>Length (MM): '. $length .'</li>';
                    // if($item->get_meta('rolls_value') != "Rolls"){
                    //     $message .= '<li>Length (MM): '. $length .'</li>';
                    // }
                    // if($item->get_meta('rolls_value') == "Rolls"){
                    //     $message .= '<li>Roll Length (Metres): '. $roll_length .'</li>';
                    // }
                    // if(!empty($item->get_meta('Radius (MM)'))){
                    //     $message .= '<li>Radius (MM): '. $item->get_meta('Radius (MM)') .'</li>';
                    // }
                    $message .= '<li>Quantity: '. $schedule_qty .'</li>';
                    $message .= '<li>Date: '. $requiredDate .'</li>';
                    $message .= '</ul>';
                    $message .= '<br>';
                    $message .= '<span style="margin-right:10px;">You can view the order <a href="https://'.$domain.'/wp-admin/post.php?post='.$order_id.'&action=edit">HERE</a></span><span>and you can view the calendar entries <a href="https://'.$domain.'/wp-admin/admin.php?page=view_admin">HERE</a></span>';
                    $headers = array('Content-Type: text/html; charset=UTF-8'); // Email headers
                    $mail_sent = wp_mail($to, $subject, $message, $headers);
                    if ($mail_sent) {
                        echo "Email to Paul sent successfully.";

                    } else {
                        echo "Error sending email to Paul.";
                    }
                    // Send email to Paul
                }

            } // end foreach

            // echo '<pre>';
            // print_r( $order->get_data() );
            // echo '</pre>';

        } // end foreach
    } else {
        echo "Invalid order ID.";
    }




    




    if ($has_inserted) {

        $order = wc_get_order($order_id);

        echo "is_scheduled value: " . $is_scheduled_check;
        
        $table_name = $wpdb->prefix . 'split_schedule_orders';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d", $order_id);

        $order_date = $order->get_date_created();

        if ($order_date) {
            $date_string = $order_date->date('Y-m-d H:i:s'); 
            $date_time = new DateTime($date_string);
            $order_date_formatted = $date_time->format('F j, Y');
        }


        // EMAIL GENERATION CODE GOES IN HERE
        $message_3 = '<h2 style="display: block; font-family: Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Order Acknowledgement</h2>';

        if($payment_title == "Pay on account (Payment is due 15 days after despatch of goods on all credit orders)"){
            $message_3 .= '<p>Hi '.$order->get_billing_first_name().',<br>Thank you for your credit account order. Please ensure payments are made on time</p>';
        } else {
            $message_3 .= '<p>Hi '.$order->get_billing_first_name().',<br>Thank you for your order.</p>';
        }

        $message_3 .= '<h2 style="font-family: Helvetica, Roboto, Arial, sans-serif; color: #ef9003; display: block; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">';
        $message_3 .= 'Customer PO: '.$po_ref_no.'<br>';
        $message_3 .= 'MD Order: #'.$order_id.' <br>';
        $message_3 .= 'Order Date: ' . $order_date_formatted;
        $message_3 .= '</h2>';
        $message_3 .= '<div style="margin-bottom: 40px;">';
        $message_3 .= '<table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: helvetica, roboto, arial, sans-serif;">';

        // Table Head
        $message_3 .= '<thead>';
        $message_3 .= '<tr>';
        $message_3 .= '<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">';
        $message_3 .= 'Product';
        $message_3 .= '</th>';
        $message_3 .= '<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 120px; text-align: center;">';
        $message_3 .= 'Price';
        $message_3 .= '</th>';
        $message_3 .= '</tr>';
        $message_3 .= '</thead>';

        // Table Body
        $message_3 .= '<tbody>';
        $message_3 .= '<tr class="order_item">';

        $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; text-align: left; vertical-align: middle; font-family: Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;">';

        try {
            $results = $wpdb->get_results($sql, ARRAY_A);
            foreach ($results as $row) {
                $order_count = $row['order_count'];
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        if($order_count == 1){
            $message_3 .= '<strong style="color:#ef9003;">'.$product_name.'</strong>';
        }
            
        $message_3 .= '<ul class="wc-item-meta" style="list-style-type: none; padding-left: 0;">';

        if($order_count == 1){
            $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Part shape</strong>';
            $message_3 .= '<p>'.$shape_type.'</p>';

            if(!empty($row['pdf_part_shape_link'])){
                $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Upload .PDF Drawing</strong> <p><a href="'.$pdf_url.'">'.$pdf_file_name.'</a></p>';
            }
            if(!empty($row['dxf_part_shape_link'])){
                $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Upload .DXF Drawing</strong> <p><a href="'.$dxf_url.'">'.$dxf_file_name.'</a></p>';
            }

            $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Width (mm)</strong>';
            $message_3 .= '<p>'.$width.'</p>';

            $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Length (mm)</strong>';
            $message_3 .= '<p>'.$length.'</p>';
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
                $shipping_weights = ceil($row['shipping_weights'] * 10000) / 10000;
                $weights[] = $shipping_weights . 'kg';
            }
        } catch (Exception $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
        }




        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Total number of parts</strong>';
        $message_3 .= '<p>'.$qty_sum.'</p>';
        $message_3 .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Customer Shipping Weight(s)</strong>';
        $message_3 .= '<p>'.implode(', ', $weights).'</p>';
        $message_3 .= '<strong class="wc-item-meta-label" style="width: 100%; padding-right: 10px; float: left; margin-right: .25em; margin-bottom: 0.45em; clear: both;">Scheduled Deliveries</strong>';


        try {
            $results = $wpdb->get_results($sql, ARRAY_A);
            $shipping_display_new_calc = 0;
            $mcofc_fair_numeric_display = 0;
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


                // if($rolls_value == "Rolls"){
                //     $schedule_qty = $schedule_qty * $rolls_length;
                // }

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


                // if($meta_qty_2 > 1){
                //     $my_shipping_response = $meta_shipping_total_2 / $meta_qty_2;
                //     $my_shipping_response_calc += $meta_shipping_total_2 / $meta_qty_2;
                // } else {
                //     $my_shipping_response = $meta_shipping_total_2;
                //     $my_shipping_response_calc += $meta_shipping_total_2;
                // }

                $my_shipping_response = $meta_shipping_total_2;
                $my_shipping_response_calc += $meta_shipping_total_2;
                
                // Get the fair values
                $prices = [
                    'Manufacturers COFC'                          => 10,
                    'Materials Direct COFC'                       => 12.50,
                    'First Article Inspection Report'             => 95,
                    'Manufacturers COFC, First Article Inspection Report' => 105,
                    'Manufacturers COFC, First Article Inspection Report, Materials Direct COFC' => 117.5,
                    'Manufacturers COFC, Materials Direct COFC' => 22.5,
                    'First Article Inspection Report, Materials Direct COFC' => 107.5,
                ];

                // Normalize the string before lookup (remove any double commas/spaces)
                $lookup_key = trim(preg_replace('/\s*,\s*,+\s*/', ', ', $mcofc_fair_string));
                $lookup_key = preg_replace('/\s+/', ' ', $lookup_key);   // collapse multiple spaces

                $mcofc_fair_numeric = $prices[$lookup_key] ?? 0;

                
                /* NEW CODE */

                // Get the fair values

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


                /* NEW CODE */
                if($mcofc_fair_numeric == "10.00"){
                    $mcofc_fair_numeric_title = "Manufacturers COFC";
                } elseif($mcofc_fair_numeric == "95.00"){
                    $mcofc_fair_numeric_title = "First Article Inspection Report";
                } else {
                    $mcofc_fair_numeric_title = "Materials Direct COFC";
                }  
                /* NEW CODE */

                $message_3 .= '<p style="margin: 0; padding: 0;">('.$mcofc_fair_numeric_title.' - £'.$mcofc_fair_numeric.') </p>';
                $message_3 .= '<p style="margin: 0; padding: 0;"><strong style="color:#ef9003;">Products Purchased Subtotal: £'.$subtotal_display.'</strong></p>';
                $message_3 .= '<p style="margin: 0; padding: 0;"><strong>Total Price: £'.number_format($total_final, 2).'</strong></p><br><br>';




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

        $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; text-align: center; vertical-align: middle; font-family: Helvetica, Roboto, Arial, sans-serif;">';
        $message_3 .= '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($order->get_subtotal(), 2).'</span>';
        $message_3 .= '</td>';
        $message_3 .= '</tr>';
        $message_3 .= '</tbody>';

        // Table Footer
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
        $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($shipping_total, 2).'</span></td>';
        $message_3 .= '</tr>';

        if($discount_code_value_new != 0){
            $message_3 .= '<tr>';
            $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Discount Code:</th>';
            $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£-</span>'.number_format((float)$voucher_code, 2).'</span></td>';
            $message_3 .= '</tr>';
        }

        $message_3 .= '<tr>';
        $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">VAT:</th>';
        $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($ah_final_vat_result, 2).'</span></td>';
        $message_3 .= '</tr>';

        $message_3 .= '<tr>';
        $message_3 .= '<th class="td" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Payment method:</th>';
        $message_3 .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;">'.$payment_title.'</td>';
        $message_3 .= '</tr>';

        $message_3 .= '<tr>';
        $message_3 .= '<th class="td total" scope="row" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Total:</th>';
        $message_3 .= '<td class="td total" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: center;"><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>'.number_format($order->get_total(), 2).'</span></strong></td>';
        $message_3 .= '</tr>';

        $message_3 .= '</tfoot>';

        $message_3 .= '</table>';

        $message_3 .= '<small style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 10px; margin-top:6px; display: block;">Legal Disclaimer: The responsibility for selecting the correct material and uploading the correct data is with the customer. This order is non-cancellable. No refund can be issued. VAT 223799965</small>';

        $message_3 .= '</div>';

        // Bank Details Table
        $message_3 .= '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; margin-top: 20px; padding: 0;">';
        $message_3 .= '<tbody>';
        $message_3 .= '<tr>';
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
        $message_3 .= '</tr>';
        $message_3 .= '</tbody>';
        $message_3 .= '</table>';

        // Shipping Address Table
        $message_3 .= '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; margin-top: 20px; padding: 0;">';
        $message_3 .= '<tbody>';
        $message_3 .= '<tr>';
        $message_3 .= '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">';
        $message_3 .= '<h2 style="font-family:Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; color: #ef9003; display: block;">Shipping address</h2>';
        $message_3 .= '<address class="x_address" style="padding:12px; color:#636363; border:1px solid #e5e5e5">';
        $message_3 .= '' . $order->get_formatted_shipping_address(). '';
        $message_3 .= '</address>';
        $message_3 .= '</td>';
        $message_3 .= '</tr>';
        $message_3 .= '</tbody>';
        $message_3 .= '</table>';

        // Billing Address Table
        $message_3 .= '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; margin-top: 20px; padding: 0;">';
        $message_3 .= '<tbody>';
        $message_3 .= '<tr>';
        $message_3 .= '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">';
        $message_3 .= '<h2 style="color: #ef9003; display: block; font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">Billing address</h2>';
        $message_3 .= '<address class="address" style="padding: 12px; color: #636363; border: 1px solid #e5e5e5;">'.$order->get_billing_first_name().'<br>'.$order->get_billing_last_name().'<br>'.$order->get_billing_company().'<br>'.$order->get_billing_address_1().'<br>'.$order->get_billing_address_2().'<br>'.$order->get_billing_city().'<br>'.$order->get_billing_postcode().'<br><a href="tel:'.$billing_phone_link.'" style="color: #202020; font-weight: normal; text-decoration: underline;">'.$order->get_billing_phone().'</a><br>'.$order->get_billing_email().'</address>';
        $message_3 .= '</td>';
        $message_3 .= '</tr>';
        $message_3 .= '</tbody>';
        $message_3 .= '</table>';

        $subject_3 = 'Order Acknowledgement[#' .$order_id. ']'; 

        // Retrieve the ACF field email addresses from backend
        $admin_email = get_field('delivery_options_order_acknowledgement_admin_email', 'option') ?: 'andrewh@materials-direct.com';
        $bcc_email   = get_field('delivery_options_order_acknowledgement_bcc_email', 'option') ?: 'andrewh@materials-direct.com';


        $to_new = $customer_email . ", " . $admin_email;


        $headers_3 = array(
            'Content-Type: text/html; charset=UTF-8',
            'Bcc: ' . $bcc_email
        );

        $mail_sent_3 = wp_mail( $to_new, $subject_3, $message_3, $headers_3);

        if ($mail_sent_3) {
            echo "Email with invoice sent successfully.";
            // generate pdf link for admail

        } else {
            echo "Error sending invoice email.";
        }
    } //end if has inserted            
    // EMAIL GENERATION CODE GOES IN HERE


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


