<?php
add_action('wp_ajax_update_order_status_single', 'update_order_status_single');

function update_order_status_single() {

    global $wpdb;

    $domain = $_SERVER['HTTP_HOST'];
    $http = "http"; //change to https for staging and live

    if($domain == "localhost:8888"){
        require_once('/Applications/MAMP/htdocs/materials-direct-new/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');
    } else {
        require_once('/kunden/homepages/2/d4298640024/htdocs/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');
    }

    // Retrieve order ID and status from AJAX request
    $id = $_POST['id'];
    $status = $_POST['status'];
    $order_no = $_POST['order_no'];
    $new_date = $_POST['date'];
    $order = wc_get_order($order_no);
    $totals_html = '';

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    // Update the status in the database
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $id),
        array('%s'), // Data format
        array('%d') // Where format
    );

    // Check if update was successful
    if ($result !== false) {

        echo "Status updated successfully";

                // if part is complete and marked as DESPATCH send the customer an email with despatch note PDF
                if($status == "dispatch"){ 

                    echo "Part schedule is complete! Run the stuff for "  . $id . "<br>";
                    echo "Order No = " . $order_no . "<br>";
                    echo "Date = " . $new_date;
        
                    global $wpdb;
        
        
                    // Get the voucher discount rate
                    $voucher_discount = $order->get_meta('_voucher_discount'); // Retrieve the meta value
                    // Get the voucher discount rate
        
                    // Get the tax rates
                    $tax_rates = [];
        
                    foreach ($order->get_tax_totals() as $tax) {
                        $tax_rates[] = [
                            'rate' => $tax->rate_id, // This is the tax rate ID
                            'label' => $tax->label,   // Tax name (e.g., "VAT")
                            'amount' => $tax->amount, // Tax amount applied
                            'percentage' => WC_Tax::get_rate_percent($tax->rate_id) // Tax percentage
                        ];
                        $tax_rate = WC_Tax::get_rate_percent($tax->rate_id);
                    }
        
                    $tax_rate = str_replace('%', '', $tax_rate);
        
                    // Get the tax rates
        
        
        
                    /* Old query */ 
                    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
                    /* Old query */ 
        
                    /* New query with date */ 
                    // $sql = $wpdb->prepare(
                    //     "SELECT * FROM $table_name WHERE order_no = %s AND date = %s",
                    //     $order_no,
                    //     $new_date
                    // );
                    /* New query with date */
        
                    try {
                        $results = $wpdb->get_results($sql, ARRAY_A);
                        $duplicate_date_count = count($results);
        
        
                        if ($results) {
        
                            // Get the customer billing and shipping details
                            $billing_firstname = $order->get_billing_first_name() ?? '';
                            $billing_lastname = $order->get_billing_last_name() ?? '';
                            $billing_address_1 = $order->get_billing_address_1() ?? '';
                            $billing_address_2 = $order->get_billing_address_2() ?? '';
                            $billing_city = $order->get_billing_city() ?? '';
                            $billing_postcode = $order->get_billing_postcode() ?? '';
                            $billing_state = $order->get_billing_state() ?? '';
                            $billing_country = ($order->get_billing_country() == "GB") ? "United Kingdom" : $order->get_billing_country();
        
                            $shipping_firstname = $order->get_shipping_first_name() ?? '';
                            $shipping_lastname = $order->get_shipping_last_name() ?? '';
                            $shipping_address_1 = $order->get_shipping_address_1() ?? '';
                            $shipping_address_2 = $order->get_shipping_address_2() ?? '';
                            $shipping_city = $order->get_shipping_city() ?? '';
                            $shipping_postcode = $order->get_shipping_postcode() ?? '';
                            $shipping_state = $order->get_shipping_state() ?? '';
                            $shipping_country = ($order->get_shipping_country() == "GB") ? "United Kingdom" : $order->get_shipping_country();
        
                            // Format address for PDF
                            $value_1 = $billing_firstname . " " . $billing_lastname . "\n" .
                            $billing_address_1 . ", " . $billing_address_2 . "\n" .
                            $billing_city . "\n" . $billing_postcode . "\n" . $billing_state . "\n" . $billing_country;
        
                            $value_2 = $shipping_firstname . " " . $shipping_lastname . "\n" .
                            $shipping_address_1 . ", " . $shipping_address_2 . "\n" .
                            $shipping_city . "\n" . $shipping_postcode . "\n" . $shipping_state . "\n" . $shipping_country;
        
                            $logo_path = get_stylesheet_directory_uri() . '/pdf-generation/examples/images/logo_example.jpg';
        
                            
        
                            // Format date for PDF filename
                            $date_obj = new DateTime($new_date);
                            $formatted_date_pdf = $date_obj->format('jS-F-Y'); // e.g. 16th-May-2025
        
                            // Create unique PDF filename (replace with real order number if needed)
                            $pdf_filename = $order_no;
        
        
                            /* DELIVERY NOTE PDF */
        
        
                            // Path to save the PDF
                            if ($domain == "localhost:8888") {
                                $tempFilePath1 = '/Applications/MAMP/htdocs/materials-direct-new/wp-content/themes/creative-mon/pdf-generation/pdf/Materials-Direct-DELIVERY-NOTE-' . $pdf_filename . '-' . $formatted_date_pdf . '-1.pdf';
                            } else {
                                $tempFilePath1 = '/home/customer/www/staging.materials-direct.com/public_html/wp-content/themes/creative-mon/pdf-generation/pdf/Materials-Direct-DELIVERY-NOTE-' . $pdf_filename . '-' . $formatted_date_pdf . '-1.pdf';
                            }
        
                            class MDPDF extends TCPDF {
                                public $iso_logo_path = '';
                            
                                public function setIsoLogoPath($path) {
                                    $this->iso_logo_path = $path;
                                }
                            
                                // Footer override
                                public function Footer() {
                                    // Set position 15mm from bottom
                                    $this->SetY(-25);
                                    // Add image to bottom-left
                                    if ($this->iso_logo_path) {
                                        $yPosition = $this->getPageHeight() - 22; 
                                        $this->Image($this->iso_logo_path, 10, $yPosition, 17); 
                                    }
        
                                }
                            }
        
                            // Create PDF
                            //$pdf1 = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $pdf1 = new MDPDF('P', 'mm', 'A4', true, 'UTF-8', false);
                            $pdf1->setIsoLogoPath(get_stylesheet_directory() . '/pdf-generation/examples/images/iso.png');
        
                            $pdf1->SetLeftMargin(10);
        
                            // Set document information
                            $pdf1->SetCreator(PDF_CREATOR);
                            $pdf1->SetAuthor('Materials Direct');
                            $pdf1->SetTitle('Materials-Direct-DELIVERY NOTE');
                            $pdf1->SetSubject('Order Date Report');
                            $pdf1->SetKeywords('Order, PDF, Dates');
                            //$pdf1->SetMargins(15, 27, 15);
                            
                            // Disable header and footer
                            $pdf1->setPrintHeader(false);
                            $pdf1->setPrintFooter(true);
        
                            // Set default font
                            $pdf1->SetFont('helvetica', '', 8.8);
        
                            //$pdf1->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                            $pdf1->AddPage();
        
        
                            // Start building the HTML content
                            $html = '<table style="border-collapse: collapse; width: 100%; margin: 0 auto; padding: 20px; margin-top: 0; margin-bottom: 0px; padding: 3px;">';
        
                            // Header Section (Logo + Company Info)
                            $html .= '<tr>';
                            $html .= '<td style="width: 45%; padding: 20px;">';
                            $html .= '<img src="' . $logo_path . '" alt="Company Logo" style="width:150px; height:auto;">';
                            $html .= '</td>';
                            $html .= '<td style="width: 55%; padding: 20px; text-align:right; color: #999999;">';
                            $html .= '<p style="font-size:8px;">Materials Direct<br>76 Burners Lane, Kiln Farm<br>Milton Keynes, MK11 3HD<br>United Kingdom<br><br>Tel: +44 (0) 1908 222 211<br>Email: info@materials-direct.com<br>www.materials-direct.com</p>';
                            $html .= '</td>';
                            $html .= '</tr>';
        
                            // Invoice and Delivery Address Section
                            $html .= '<tr>';
                            $html .= '<td style="width: 40%; padding: 20px;">';
                            $html .= '<br><br>';
                            $html .= '<p style="line-height: 0.1;"><strong>Invoice Address</strong></p>';
                            $html .= '<p>' . nl2br($value_1) . '</p><br>';
                            $html .= '</td>';
        
                            $html .= '<td style="width: 60%; padding: 20px;">';
                            $html .= '<br><br>';
                            $html .= '<p style="line-height: 0.1;"><strong>Delivery Address</strong></p>';
                            $html .= '<p>' . nl2br($value_2) . '</p><br>';
                            $html .= '</td>';
                            $html .= '</tr>';
        
                            // --------------------------
                            // 🎯 Buffer Order and Invoice Content
                            // --------------------------
                            $order_details_html = '';  // Store order details
                            $invoice_details_html = ''; // Store invoice details
                            $order_details_added = false;
        
                            foreach ($results as $row) {
        
                                $schedule_qty = str_replace(',', '', $row['schedule_qty']);
                                $cost_per_part_raw = $row['cost_per_part_raw'];
                                $discount_rate = $row['discount_rate'];
                                $shipping_unique = $row['shipping_unique'];
                                $delivery_count = $row['delivery_count'];
                                $order_count = $row['order_count'];
                                $country = $row['country'];
                                $cart_discount_percent = (float) $row['cart_discount_percent'];
                    
                                $md_value = $row['md_value'];
                                $part_shape = $row['part_shape'];
                                $width = $row['width'];
                                $length = $row['length'];
                                $voucher_code = $row['voucher_code'];
                                $voucher_percent = $row['voucher_percent'];
                                $shipping_weights = $row['shipping_weights'];
                                $total_shipping_duplicates = $row['shipping_duplicates'];
                                $meta_qty = $row['meta_shipping_qty'];
                                $meta_shipping_total = $row['meta_shipping_total'];
                                $last = $row['last']; 
                                $date = $row['date'];
        
                                if($last == "1"){
                                    $complete = "<br><br>Order is complete";
                                } else {
                                    $complete = "";
                                }
        
                                
        
                                //collect the values for the invoice numbers on merged dates
                                $invoice_no_calc = $row['invoice_no'];
                                $invoice_no_calc = substr($invoice_no_calc, 0, -2);
                                $invoice_date = $row['date'];
                                $invoice_date = str_replace("-", "", $invoice_date);
                                $merged_invoice = $invoice_no_calc ."-". $invoice_date;
                                //collect the values for the invoice numbers on merged dates
        
        
        
                                // Order Details (First Table)
                                if (!$order_details_added) {
                                    $formatted_date_pdf = date('jS F Y', strtotime($row['date']));
                                    $order_details_html .= '<tr>';
                                    if($duplicate_date_count > 1){
                                        $order_details_html .= '<td>#'. $merged_invoice .'</td>';
                                    } else {
                                        $order_details_html .= '<td>#' . $row['invoice_no'] . '</td>';
                                    }
                                    $order_details_html .= '<td>' . $formatted_date_pdf . '</td>';
                                    $order_details_html .= '<td>#' . $order_no . '</td>';
                                    $order_details_html .= '<td>' . $row['customer_po'] . '</td>';
                                    $order_details_html .= '</tr>';
                                    $order_details_added = true;
                                }
        
                                // Invoice Details (Second Table) $duplicate_date_count
                                $invoice_details_html .= '<tr style="height: 500px;">';
                                $invoice_details_html .= '<td style="vertical-align:top;">' . $row['sku'] . '</td>';
                                $invoice_details_html .= '<td style="vertical-align:top;">' . $row['title'] . '<br>Part shape: ' . $part_shape  . '<br>Width (MM): ' . $width . '<br>Length (MM): ' . $length . '<br><br>Schedule: ' .$row['schedule'] . $complete .  '</td>';
                                $invoice_details_html .= '<td style="vertical-align:top;">' . $row['schedule_qty'] . '</td>';
                                $invoice_details_html .= '</tr>';
        
                            } // end foreach
        
        
        
        
                            // -------------------------------
                            // 🎯 Display Order Details Table
                            // -------------------------------
                            $html .= '<tr>';
                            $html .= '<td colspan="2" style="padding: 20px;">';
                            $html .= '<h3 style="margin-bottom: 10px;">Order Details</h3>';
                            $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
                            $html .= '<thead>';
                            $html .= '<tr style="background-color: rgb(244, 147, 2);">';
                            $html .= '<th><strong>Invoice No.</strong></th>';
                            $html .= '<th><strong>Despatch Date</strong></th>';
                            $html .= '<th><strong>MD Order No.</strong></th>';
                            $html .= '<th><strong>Customer PO Number</strong></th>';
                            $html .= '</tr>';
                            $html .= '</thead>';
                            $html .= '<tbody>' . $order_details_html . '</tbody>';
                            $html .= '</table>';
                            $html .= '</td>';
                            $html .= '</tr>';
        
                            // --------------------------
                            // 🎯 Display Invoice Table
                            // --------------------------
                            $html .= '<tr>';
                            $html .= '<td colspan="2" style="padding: 20px;">';
                            $html .= '<br>';
                            $html .= '<h2 style="margin-bottom: 10px; margin-top: 10px; text-align:center;">Delivery Note</h2>';
                            $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
                            $html .= '<thead>';
                            $html .= '<tr style="background-color:rgb(244, 147, 2);">';
                            $html .= '<th style="width: 10%;"><strong>Part No.</strong></th>';
                            $html .= '<th style="width: 80%;"><strong>Description</strong></th>';
                            $html .= '<th style="width: 10%;"><strong>Qty</strong></th>';
                            $html .= '</tr>';
                            $html .= '</thead>';
                            $html .= '<tbody>' . $invoice_details_html . '</tbody>';
                            $html .= '</table>';
                            $html .= '</td>';
                            $html .= '</tr>';
        
        
        
                            // End HTML content
                            $html .= '</table>';
        
        
                            $pdf1->writeHTML($html, true, false, true, false, '');
        
                            // Output PDF to file
                            $pdf1->Output($tempFilePath1, 'F');
        
        
                            /* DELIVERY NOTE PDF */
        
        
        
                            /* INVOICE PDF */
        
                            // === Create second PDF: Invoice ===
                            if ($domain == "localhost:8888") {
                                $tempFilePath2 = '/Applications/MAMP/htdocs/materials-direct-new/wp-content/themes/creative-mon/pdf-generation/pdf/Materials-Direct-INVOICE-' . $pdf_filename . '-' . $formatted_date_pdf . '-2.pdf';
                            } else {
                                $tempFilePath2 = '/home/customer/www/staging.materials-direct.com/public_html/wp-content/themes/creative-mon/pdf-generation/pdf/Materials-Direct-INVOICE-' . $pdf_filename . '-' . $formatted_date_pdf . '-2.pdf';
                            }
        
                            class MDPDF2 extends TCPDF {
                                public $iso_logo_path = '';
                            
                                public function setIsoLogoPath($path) {
                                    $this->iso_logo_path = $path;
                                }
                            
                                // Footer override
                                public function Footer() {
                                    // Set position 15mm from bottom
                                    $this->SetY(-25);
                                    // Add image to bottom-left
                                    if ($this->iso_logo_path) {
                                        $yPosition = $this->getPageHeight() - 22; 
                                        $this->Image($this->iso_logo_path, 10, $yPosition, 17); 
                                    }
        
                                }
                            }
        
                            // Create PDF
                            $pdf2 = new MDPDF2('P', 'mm', 'A4', true, 'UTF-8', false);
                            $pdf2->setIsoLogoPath(get_stylesheet_directory() . '/pdf-generation/examples/images/iso.png');
        
                            $pdf2->SetLeftMargin(10);
        
                            $pdf2->SetCreator(PDF_CREATOR);
                            $pdf2->SetAuthor('Materials Direct');
                            $pdf2->SetTitle('Materials-Direct-INVOICE');
                            $pdf2->SetSubject('Order Date Report');
                            $pdf2->SetKeywords('Order, PDF, Dates');
        
                            //$pdf2->SetMargins(15, 27, 15);
        
                            // Disable header and footer
                            $pdf2->setPrintHeader(false);
                            $pdf2->setPrintFooter(true);
        
                            // Set default font
                            $pdf2->SetFont('helvetica', '', 9.2);
        
                            //$pdf2->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                            $pdf2->AddPage();
        
        
                            
        
                            // Write content
                            //$html2 = "<h1>Invoice</h1>";
        
                            // Start building the HTML content
                            $html2 = '<table style="border-collapse: collapse; width: 100%; margin: 0 auto; padding: 20px; margin-top: 0; margin-bottom: 0px; padding: 3px;">';
        
                            // Header Section (Logo + Company Info)
                            $html2 .= '<tr>';
                            $html2 .= '<td style="width: 45%; padding: 20px;">';
                            $html2 .= '<img src="' . $logo_path . '" alt="Company Logo" style="width:150px; height:auto;">';
                            $html2 .= '</td>';
                            $html2 .= '<td style="width: 55%; padding: 20px; text-align:right; color: #999999;">';
                            $html2 .= '<p style="font-size:8px;">Materials Direct<br>76 Burners Lane, Kiln Farm<br>Milton Keynes, MK11 3HD<br>United Kingdom<br><br>Tel: +44 (0) 1908 222 211<br>Email: info@materials-direct.com<br>www.materials-direct.com</p>';
                            $html2 .= '</td>';
                            $html2 .= '</tr>';
        
                            // Invoice and Delivery Address Section
                            $html2 .= '<tr>';
                            $html2 .= '<td style="width: 40%; padding: 20px;">';
                            $html2 .= '<br><br>';
                            $html2 .= '<p style="line-height: 0.1;"><strong>Invoice Address</strong></p>';
                            $html2 .= '<p>' . nl2br($value_1) . '</p><br>';
                            $html2 .= '</td>';
        
                            $html2 .= '<td style="width: 60%; padding: 20px;">';
                            $html2 .= '<br><br>';
                            $html2 .= '<p style="line-height: 0.1;"><strong>Delivery Address</strong></p>';
                            $html2 .= '<p>' . nl2br($value_2) . '</p><br>';
                            $html2 .= '</td>';
                            $html2 .= '</tr>';
        
                            // --------------------------
                            // 🎯 Buffer Order and Invoice Content
                            // --------------------------
                            $order_details_html = '';  // Store order details
                            $invoice_details_html = ''; // Store invoice details
                            $order_details_added = false;
        
                            foreach ($results as $row) {
                                $title = $row['title'];
                                $schedule_qty = str_replace(',', '', $row['schedule_qty']);
                                $cost_per_part_raw = $row['cost_per_part_raw'];
                                $discount_rate = $row['discount_rate'];
                                $shipping_unique = $row['shipping_unique'];
                                $delivery_count = $row['delivery_count'];
                                $order_count = $row['order_count'];
                                $country = $row['country'];
                                $cart_discount_percent = (float) $row['cart_discount_percent'];
                    
                                $md_value = $row['md_value'];
                                $part_shape = $row['part_shape'];
                                $width = $row['width'];
                                $length = $row['length'];
                                $width_inch = $row['width_inch'];
                                $length_inch = $row['length_inch'];
                                $voucher_code = $row['voucher_code'];
                                $voucher_percent = $row['voucher_percent'];
                                $shipping_weights = $row['shipping_weights'];
                                $total_shipping_duplicates = $row['shipping_duplicates'];
                                $meta_qty = $row['meta_shipping_qty'];
                                $meta_shipping_total = $row['meta_shipping_total'];
                    
                                $mcofc_fair = $row['mcofc_fair'];
                                $rolls_value = $row['rolls_value'];
                                $rolls_length = $row['rolls_length'];
                                $currency_rate = $row['currency_rate'];
                                $currency = $row['currency'];
                                $sku = $row['sku'];
                                $shipping_numeric = $row['shipping_numeric'];
                                $shipping = $row['shipping'];
                                $dimension_type = $row['dimension_type'];
                                $dimension_type = strtoupper($dimension_type);
                                $part_shape = $row['part_shape'];
                                $pdf_part_shape_link = $row['pdf_part_shape_link'];
                                $pdf_display = $row['pdf'];
                                $dxf_part_shape_link = $row['dxf_part_shape_link'];
                                $dxf_display = $row['dxf'];
                                $last = $row['last'];
                                $cart_discount_price = $row['cart_discount_price'];
                                $cart_discount_percent = $row['cart_discount_percent'];
                                $address_1 = $row['address_1'];
                                $address_2 = $row['address_2'];
                                $address_3 = $row['address_3'];
                                $address_4 = $row['address_4'];
                                $address_5 = $row['address_5'];
                    
                                $date = $row['date'];
                    
                    
                                $title = str_replace("&#x2122;", "", $title);
                                if (strlen($title) > 40) {
                                    $title = substr($title, 0, 46);
                                }
        
                                if($currency == "EUR"){
                                    $cost_per_part = str_replace("€", "", $cost_per_part_string);
                                } elseif($currency == "USD"){
                                    $cost_per_part = str_replace("$", "", $cost_per_part_string);
                                } else {
                                    //$cost_per_part = str_replace("£", "", $cost_per_part_string);
                                    $cost_per_part = 99;
                                }
                    
                                if($row['on_backorder'] == 0){
                                    $discount_rate = $row['discount_rate'];
                                } else {
                                    $discount_rate = 0;
                                }
                    
                    
                                if($meta_qty > 1){
                                    $flag = 1;
                                } else {
                                    $flag = 0;
                                }
        
        
                                if($row['on_backorder'] == 0){
                                    $discount_rate = $row['discount_rate'];
                                } else {
                                    $discount_rate = 0;
                                }
                    
                    
                                if($rolls_value == "Rolls"){
                                    $schedule_qty = $schedule_qty * $rolls_length;
                                }
        
                                //calculate subtotal
                                $total_1 = $cost_per_part_raw * $schedule_qty;
                                $discount_amount = ($total_1 * $discount_rate) / 100;
                                $cpp = $total_1 - $discount_amount;
                                $cppnew = $cpp;
                                $subtotal_sum += $cppnew;
                                $subtotal_display = number_format($cppnew, 2);
                                //calculate subtotal
        
        
                                // ah Calculate the cart discount based on percent
                                $cart_discount_amount = ((float) $cppnew * $cart_discount_percent) / 100;
                                // ah Calculate the cart discount based on percent
        
                                // calculate voucher codes
                                if (!isset($voucher_code) || $voucher_code <= 0) {
                                    $discount_code_value_new = 0;
                                    $discount_code_value_sum += $discount_code_value_new;
                                } else {
                                    $discount_code_value_new = $cppnew * $voucher_percent;
                                    $discount_code_value_sum += $discount_code_value_new;
                                }
                                // calculate voucher codes
        
                                // calculate MCOFC FAIR values
                                if($row['mcofc_fair'] === "Manufacturers COFC"){
                                    $mcofc_fair_value_display = 10;
                                }
                                elseif($row['mcofc_fair'] === "FAIR"){
                                    $mcofc_fair_value_display = 95;
                                }
                                elseif($row['mcofc_fair'] === "Materials Direct COFC"){
                                    $mcofc_fair_value_display = 12.50;
                                }
                                else {
                                    $mcofc_fair_value_display = 0;
                                }
                                // calculate MCOFC FAIR values
        
                                // calculate VAT
                                $cart_discount_price_new = $cart_discount_amount;
                                $tf_3 = round($cart_discount_price_new, 2);
                                $tf_3_sum += $tf_3;
                                //$shipping_display_new = $shipping_unique / $delivery_count /$order_count;
                                $shipping_display_new = $meta_shipping_total;
        
                                $shipping_display_sum += $shipping_display_new;
        
                                $voucher_percent = $cppnew * $voucher_discount;
        
                                $total_delivery_count = $order_count * $delivery_count;
        
                                $shipping_subtract = $total_shipping_duplicates / $total_delivery_count; // we need to subtract to total for duplicate dates from the $shipping_responses total
        
                                if($flag == 1){
                                    $my_shipping_response = $shipping_display_new / $meta_qty;
                                } else {
                                    $my_shipping_response = $shipping_display_new;
                                }
        
                                $vat_amount = $cppnew + $mcofc_fair_value_display + $my_shipping_response - $tf_3 - $voucher_percent;
        
        
                                if($country == "United Kingdom"){
                                    $vat_display = ($vat_amount * $tax_rate) / 100;
                                    $vat_sum += $vat_display;
                                } else {
                                    $vat_display = 0;
                                    $vat_sum += $vat_display;
                                }
                                // calculate VAT
        
        
                                // calculate totals
                                $total_final = $cppnew + $my_shipping_response + $vat_display - $tf_3 + $md_value + $mcofc_fair_value_display - $voucher_percent;
                                $newtotal = floor($total_final * 100) / 100; // Total inc VAT
                                $total_sum += $newtotal;
                                // calculate totals
        
        
                                // calculate MCOFC FAIR
                                if(empty($row['mcofc_fair'])){
                                    $mcf = "";
                                    $mcf_v = 0;
                                    $mcf_v = (float) $mcf_v;
                                } else {
                                    if($row['mcofc_fair'] == "FAIR"){
                                        $mcf = "\n".$mcofc_fair.": £95";
                                        $mcf_v = 95;
                                        $mcf_v = (float) $mcf_v;
                                        
                                    } 
                                    elseif($row['mcofc_fair'] == "MCOFC"){
                                        $mcf = "\n".$mcofc_fair.": £10";
                                        $mcf_v = 10;
                                        $mcf_v = (float) $mcf_v;
                                    }
                                    elseif($row['mcofc_fair'] == "Materials Direct COFC"){
                                        $mcf = "\n".$mcofc_fair.": £20";
                                        $mcf_v = 20;
                                        $mcf_v = (float) $mcf_v;
                                    }
                                    elseif($row['mcofc_fair'] == "Unknown"){
                                        $mcf = "\n".$mcofc_fair.": £0";
                                        $mcf_v = 0;
                                        $mcf_v = (float) $mcf_v;
                                    }
                                    else {
                                        $mcf = "\n".$mcofc_fair.": £105";
                                    }
                                }
        
                                $mcf_v_sum += $mcf_v;
                                // calculate MCOFC FAIR
        
        
                                //collect the values for product Description
                                $ps = "<br>Part shape: ".$part_shape;
                                
                                if($row['width_inch'] == 0){
                                    $wdti = "";
                                    $wdt = "<br>Width (MM): ".$width;
                                } else {
                                    $wdti = "<br>Width (INCHES): ".$width_inch;
                                }
        
                                if($row['length_inch'] == 0){
                                    $lgti = "";
                                    $lgt = "<br>Length (MM): ".$length;
                                } else {
                                    $lgti = "<br>Length (INCHES): ".$length_inch;
                                }
        
                                if( $row['radius'] == 0 ){
                                    $rad = "";
                                } else {
                                    $rad = "<br>Radius (".$dimension_type."): ".$radius;
                                }
        
                                if(empty($row['pdf'])){
                                    $dra = "";
                                } else {
                                    $dra = "<br>PDF Drawing: ".$row['pdf'];
                                }
        
                                if(empty($row['dxf'])){
                                    $dxf = "";
                                } else {
                                    $dxf = "<br>DXF Drawing: ".$row['dxf'];
                                }
        
                                if(empty($row['mcofc_fair'])){
                                    $mcf = "";
                                    $mcf_v = 0;
                                    $mcf_v = (float) $mcf_v;
                                } else {
                                    if($row['mcofc_fair'] == "FAIR"){
                                        $mcf = "<br>".$mcofc_fair.": £95";
                                        $mcf_v = 95;
                                        $mcf_v = (float) $mcf_v;
                                        
                                    } 
                                    elseif($row['mcofc_fair'] == "MCOFC"){
                                        $mcf = "<br>".$mcofc_fair.": £10";
                                        $mcf_v = 10;
                                        $mcf_v = (float) $mcf_v;
                                    }
                                    elseif($row['mcofc_fair'] == "Materials Direct COFC"){
                                        $mcf = "<br>".$mcofc_fair.": £20";
                                        $mcf_v = 20;
                                        $mcf_v = (float) $mcf_v;
                                    }
                                    else {
                                        $mcf = "<br>".$mcofc_fair.": £105";
                                    }
                                }
                                if($md_title){
                                    $mdcfc = "<br>Add " . $md_title .": £". $md_value;
                                } else {
                                    $mdcfc = "";
                                }
        
                                $sch = '<br><br>Schedule: ' .$row['schedule'];
                                $str = "<br><br>Unit price includes any fair or manufacturers COFC";
                                //collect the values for product Description
        
                                //collect the values for the invoice numbers on merged dates
                                // $invoice_no_calc = $row['invoice_no'];
                                // $invoice_no_calc = substr($invoice_no_calc, 0, -2);
                                // $invoice_date = $row['date'];
                                // $invoice_date = str_replace("-", "", $invoice_date);
                                // $merged_invoice = $invoice_no_calc ."-". $invoice_date;
                                //collect the values for the invoice numbers on merged dates
        
                                // calculate subtotal display
                                $subtotal_display_2 = $cppnew + $mcf_v + $my_shipping_response - $tf_3 - $discount_code_value_new;
                                $subtotal_2_sum += $subtotal_display_2;
                                // calculate subtotal display
        
                                $temp_value = $tax_rate . ", " . $cppnew . ", " . $shipping_display_new . ", " . $tf_3 . ", " . $md_value . ", " . $discount_code_value_new;
        
                                // Order Details (First Table)
                                if (!$order_details_added) {
                                    $formatted_date_pdf = date('jS F Y', strtotime($row['date']));
                                    $order_details_html .= '<tr>';
                                    if($duplicate_date_count > 1){
                                        $order_details_html .= '<td>#'. $merged_invoice .'</td>';
                                    } else {
                                        $order_details_html .= '<td>#' . $row['invoice_no'] . '</td>';
                                    }
                                    $order_details_html .= '<td>' . $formatted_date_pdf . '</td>';
                                    $order_details_html .= '<td>#' . $order_no . '</td>';
                                    $order_details_html .= '<td>' . $row['customer_po'] . '</td>';
                                    //$order_details_html .= '<td>' . $row['schedule'] . '</td>';
                                    $order_details_html .= '</tr>';
                                    $order_details_added = true;
                                }
        
                                // Invoice Details (Second Table)
                                $invoice_details_html .= '<tr>';
                                $invoice_details_html .= '<td>' . $row['sku'] . '</td>';
                                $invoice_details_html .= '<td>' . $title . $ps . $dra . $dxf . $wdt . $wdti . $lgt . $lgti . $rad . $mcf . $scd . $sch . $str . '</td>';
                                //$invoice_details_html .= '<td>' . $row['title'] . '<br>Part shape: ' . $part_shape  . '<br>Width (MM): ' . $width . '<br>Length (MM): ' . $length . '<br><br>Schedule: ' .$row['schedule'] . '</td>';
        
                                $invoice_details_html .= '<td>' . $row['schedule_qty'] . '</td>';
                                $invoice_details_html .= '<td>' . number_format($row['cost_per_part_raw'], 4) . '</td>';
                                $invoice_details_html .= '<td>' . $subtotal_display . '</td>';
                                $invoice_details_html .= '<td>' . number_format($vat_display, 2) . '</td>';
                                $invoice_details_html .= '</tr>';
        
        
                            } // end foreach
        
        
        
        
                                    // Totals (merged dates)
                                    if($duplicate_date_count > 1){
        
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td>Parts Total</td>';
                                        $totals_html .= '<td>£'.number_format($subtotal_sum, 2).'</td>';
                                        $totals_html .= '</tr>';
                
                                        // if($mcf_v != 0){
                                            $totals_html .= '<tr>';
                                            $totals_html .= '<td>All COFCs & Fairs</td>';
                                            $totals_html .= '<td>£'.$mcf_v_sum.'</td>';
                                            $totals_html .= '</tr>';
                                        // }
                
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td>Shipping Total (ex. VAT)</td>';
                                        $msr = $my_shipping_response * $duplicate_date_count;
                                        $totals_html .= '<td>£'.$msr.'</td>';
                                        $totals_html .= '</tr>';
                
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td>Order Discounts</td>';
                                        $totals_html .= '<td>£-'.number_format($tf_3_sum, 2).'</td>';
                                        $totals_html .= '</tr>';
                
                                        if($discount_code_value_new != 0){
                                            $totals_html .= '<tr>';
                                            $totals_html .= '<td>Discount Code</td>';
                                            $totals_html .= '<td>£-'. number_format($discount_code_value_sum, 2) .'</td>';
                                            $totals_html .= '</tr>';
                                        }
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td><strong>Subtotal (ex. VAT)</strong></td>';
                                        $totals_html .= '<td><strong>£'.number_format($subtotal_2_sum, 2).'</strong></td>';
                                        $totals_html .= '</tr>';
                
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td><strong>VAT</strong></td>';
                                        $totals_html .= '<td><strong>£'.number_format($vat_sum, 2).'</strong></td>';
                                        $totals_html .= '</tr>';
                
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td><strong>Total (Inc. VAT)</strong></td>';
                                        $totals_html .= '<td><strong>£'.number_format($total_sum, 2).'</strong></td>';
                                        $totals_html .= '</tr>';
                
                                    } 
                                    // Totals (merged dates)
                                    else {
                                        // Totals (single order)
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td>Parts Total</td>';
                                        $totals_html .= '<td>£'.$subtotal_display.'</td>';
                                        $totals_html .= '</tr>';
                            
                                        if($mcf_v != 0){
                                            $totals_html .= '<tr>';
                                            $totals_html .= '<td>All COFCs & Fairs</td>';
                                            $totals_html .= '<td>£'.$mcf_v.'</td>';
                                            $totals_html .= '</tr>';
                                        }
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td>Shipping Total (ex. VAT)</td>';
                                        $totals_html .= '<td>£'.$my_shipping_response .'</td>';
                                        $totals_html .= '</tr>';
                            
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td>Order Discounts</td>';
                                        $totals_html .= '<td>£-'.number_format($tf_3, 2).'</td>';
                                        $totals_html .= '</tr>';
                            
                                        if($discount_code_value_new != 0){
                                            $totals_html .= '<tr>';
                                            $totals_html .= '<td>Discount Code</td>';
                                            $totals_html .= '<td>£-'. number_format($discount_code_value_new, 2) .'</td>';
                                            $totals_html .= '</tr>';
                                        }
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td><strong>Subtotal (ex. VAT)</strong></td>';
                                        $totals_html .= '<td><strong>£'.number_format($subtotal_display_2, 2).'</strong></td>';
                                        $totals_html .= '</tr>';
                            
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td><strong>VAT</strong></td>';
                                        $totals_html .= '<td><strong>£'.number_format($vat_display, 2).'</strong></td>';
                                        $totals_html .= '</tr>';
                            
                                        $totals_html .= '<tr>';
                                        $totals_html .= '<td><strong>Total (Inc. VAT)</strong></td>';
                                        $totals_html .= '<td><strong>£'.$newtotal.'</strong></td>';
                                        $totals_html .= '</tr>';
                                        // Totals (single order)
                                    }
        
        
                            $bank_details = '<tr style="background-color: #f2f2f2;">';
                            $bank_details .= '<td>Materials Direct Bank A/C details:<br>HSBC Bank<br>Sort Code: 40-33-33<br>Account Number: 42625172<br>Payment terms - 30 days net</td>';
                            $bank_details .= '</tr>';
        
        
                            // -------------------------------
                            // 🎯 Display Order Details Table
                            // -------------------------------
                            $html2 .= '<tr>';
                            $html2 .= '<td colspan="2" style="padding: 20px;">';
                            $html2 .= '<h3 style="margin-bottom: 10px;">Order Details</h3>';
                            $html2 .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
                            $html2 .= '<thead>';
                            $html2 .= '<tr style="background-color:rgb(244, 147, 2);">';
                            $html2 .= '<th><strong>Invoice No.</strong></th>';
                            $html2 .= '<th><strong>Despatch Date</strong></th>';
                            $html2 .= '<th><strong>MD Order No.</strong></th>';
                            $html2 .= '<th><strong>Customer PO Number</strong></th>';
                            // $html2 .= '<th><strong>Schedule</strong></th>';
                            $html2 .= '</tr>';
                            $html2 .= '</thead>';
                            $html2 .= '<tbody>' . $order_details_html . '</tbody>';
                            $html2 .= '</table>';
                            $html2 .= '</td>';
                            $html2 .= '</tr>';
        
                            // --------------------------
                            // 🎯 Display Invoice Table
                            // --------------------------
                            $html2 .= '<tr>';
                            $html2 .= '<td colspan="2" style="padding: 20px;">';
                            $html2 .= '<br>';
                            $html2 .= '<h2 style="margin-bottom: 10px; margin-top: 10px; text-align:center;">Invoice</h2>';
                            $html2 .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
                            $html2 .= '<thead>';
                            $html2 .= '<tr style="background-color:rgb(244, 147, 2);">';
                            $html2 .= '<th style="width: 10%;"><strong>Part No.</strong></th>';
                            $html2 .= '<th style="width: 45%;"><strong>Product Description</strong></th>';
                            $html2 .= '<th style="width: 8%;"><strong>Qty</strong></th>';
                            $html2 .= '<th style="width: 12%;"><strong>Unit Price</strong></th>';
                            $html2 .= '<th style="width: 13%;"><strong>Net Amount</strong></th>';
                            $html2 .= '<th style="width: 12%;"><strong>VAT (£)</strong></th>';
                            $html2 .= '</tr>';
                            $html2 .= '</thead>';
                            $html2 .= '<tbody>' . $invoice_details_html . '</tbody>';
                            $html2 .= '</table>';
                            $html2 .= '</td>';
                            $html2 .= '</tr>';  
                            
                            // ----------------------------
                            // 🎯 Display Order Total Table
                            // ----------------------------
        
                            $html2 .= '<tr>';
                            $html2 .= '<td colspan="2" style="padding: 20px;">';
        
                            $html2 .= '<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">';
                            $html2 .= '<tr>';
        
                            $html2 .= '<td class="padding:0 50px 0 0; vertical-align: bottom;">';
                            $html2 .= '<br><br><table id="totals" border="1" cellspacing="0" cellpadding="5" style="width: 95%; border-collapse: collapse; margin-left: auto; margin-right: 0;">';
                            $html2 .= '<tbody>' . $bank_details . '</tbody>';
                            $html2 .= '</table>';
                            $html2 .= '</td>';
        
                            $html2 .= '<td>';
                            $html2 .= '<br><br><table id="totals" border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: 0;">';
                            $html2 .= '<tbody>' . $totals_html . '</tbody>';
                            $html2 .= '</table>';
                            $html2 .= '</td>';
        
                            $html2 .= '</tr>';
                            $html2 .= '</table>'; // Close the container table
        
        
                            $html2 .= '</td>';
                            $html2 .= '</tr>';
        
                            // End HTML content
                            $html2 .= '</table>';
        
        
                            $pdf2->writeHTML($html2, true, false, true, false, '');
        
                            // Output PDF to file
                            $pdf2->Output($tempFilePath2, 'F');
        
                            /* INVOICE PDF */
        
        
        
        
        
        
                            // === Send email with attachment ===
        
        
                            //$message = 'Hi Andrew, Attached is your delivery note for scheduled delivery part 1 of 3.';
                           
                            foreach ($results as $row) {
                                    $schedule = $row['schedule'];
                                    $subject = 'Customer Invoice - Delivery Note ('.$schedule.')';
                                    $to = "andrewh@materials-direct.com";
                                    $firstname = $row['firstname'];
                                    $customer_po_no = $row['customer_po'];
                                    $order_no = $row['order_no'];
                                    $title = $row['title'];
                                    $part_shape = $row['part_shape'];
                                    $pdf_part_shape_link = $row['pdf_part_shape_link'];
                                    $pdf = $row['pdf'];
                                    $dxf_part_shape_link = $row['dxf_part_shape_link'];
                                    $dxf = $row['dxf'];
                                    $dxf = $row['dxf'];
                                    $width = $row['width'];
                                    $length = $row['length'];
                                    $width_inch = $row['width_inch'];
                                    $length_inch = $row['length_inch'];
                                    $dimension_type = $row['dimension_type'];
                                    $dimension_type = strtoupper($dimension_type);
                                    $discount_rate = $row['discount_rate'];
                                    $schedule_qty = str_replace(',', '', $row['schedule_qty']);
        
                                    $message = '<h2 style="display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Delivery Note</h2>';
                                    $message .= '<p>Hi '.$firstname.',<br>Attached is your delivery note for scheduled delivery '.$schedule.'</p>';
                                    $message .= '<h2 style="color: #ef9003; display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">
                                    Customer PO: '.$customer_po_no.'<br>MD Order: #'.$order_no.' <br>Order Date: '.$formatted_date_pdf.'</h2>';
        
                                    $message .= '<table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;">';
                                    $message .= '<thead>';
                                    $message .= '<tr>';
                                    $message .= '<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;">Product</th>';
                                    // $message .= '<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 120px; text-align: center;">Price</th>';
                                    $message .= '</tr>';
                                    $message .= '</thead>';
                                    $message .= '<tbody>';
                                    $message .= '<tr class="order_item">';
                                    $message .= '<td class="td" style="color: #636363; border: 1px solid #e5e5e5; text-align: left; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;">';
                                    $message .= $title;
        
                                    $message .= '<ul class="wc-item-meta" style="list-style-type: none; padding-left: 0;">';
                                    $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Part shape</strong> <p>'.$part_shape.'</p>';
                                    if(!empty($row['pdf_part_shape_link'])){
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Upload .PDF Drawing</strong> <p><a href="'.$pdf_part_shape_link.'">'.$pdf.'</a></p>';
                                    }
                                    if(!empty($row['dxf_part_shape_link'])){
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Upload .DXF Drawing</strong> <p><a href="'.$dxf_part_shape_link.'">'.$dxf.'</a></p>';
                                    }
                                    if($row['dimension_type'] == "mm"){
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Width ('.$dimension_type.')</strong> <p>'.$width.'</p>';
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Length ('.$dimension_type.')</strong> <p>'.$length.'</p>';
                                    } else {
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Width ('.$dimension_type.')</strong> <p>'.$width_inch.'</p>';
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Length ('.$dimension_type.')</strong> <p>'.$length_inch.'</p>';
                                    }
                                    
                                    
                                    if($radius != 0){
                                        $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Radius ('.$dimension_type.')</strong> <p>'.$radius.'</p>';
                                    }
                                    $message .= '<strong class="wc-item-meta-label" style="padding-right: 10px; float: left; margin-right: .25em; clear: both;">Total number of parts</strong> <p>'.$schedule_qty.'</p>';
        
                                    $message .= '<strong class="wc-item-meta-label aaa" style="width: 100%; padding-right: 10px; float: left; margin-right: .25em; margin-bottom: 0.25em; clear: both;">Scheduled Deliveries</strong>';
                                    $message .= '<p style="margin: 0; padding: 0;">Qty: '.$schedule_qty.'</p>';
                                    $message .= '<p style="margin: 0; padding: 0;">Dispatch Date: '.$formatted_date_pdf.'</p>';
                                    $message .= '<p>'.$schedule_qty.' parts to be dispatched in/on '.$formatted_date_pdf.' ('.$discount_rate.'% Discount)</p>';
        
                                    $message .= '</ul>';
        
                                    $message .= '</td>';
        
                                    $message .= '</tfoot>';
                                    $message .= '</table>';
        
                            }
        
        
                            $headers = array('Content-Type: text/html; charset=UTF-8');
        
                            // Attach PDF to email
                            $attachments = array($tempFilePath1, $tempFilePath2);
        
                            // Send email
                            $mail_sent = wp_mail($to, $subject, $message, $headers, $attachments);
        
                            if ($mail_sent) {
                                echo "Email with delivery note PDF sent successfully.";
                            } else {
                                echo "Error sending delivery note email.";
                            }
        
                           
        
                        }    
        
        
         
        
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
        
        
                } else {
                    echo "We are not on dispatch";
                }
                // if part is complete and marked as DESPATCH send the customer an email with despatch note PDF



    } else {
        echo "Somethings wrong";
        echo "Error updating status: " . $wpdb->last_error;
    }

    // Always exit to avoid further execution
    wp_die();
}