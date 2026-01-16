<?php
/*
Template Name: PDF Generation For Admin (New Version 4)
*/

$domain = $_SERVER['HTTP_HOST'];
//require_once('/home/customer/www/materials-direct.com/public_html/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');

if($domain == "localhost:8888"){
    require_once('/Applications/MAMP/htdocs/materials-direct-new/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');
} 
elseif($domain == "newbuild.staging-materials-direct.co.uk"){
    require_once('/kunden/homepages/2/d4298640024/htdocs/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');
}
else {
    require_once('/home/customer/www/materials-direct.com/public_html/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');
}

date_default_timezone_set('Europe/London');

$pdf_date = date('jS F Y');

$id = isset($_GET['id']) ? absint($_GET['id']) : 0;
//$order_no = isset($_GET['order_no']) ? sanitize_text_field($_GET['order_no']) : '';
$order_no = isset($_GET['order_no']) ? intval($_GET['order_no']) : 0;
$new_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
$is_merged = isset($_GET['is_merged']) ? sanitize_text_field($_GET['is_merged']) : '';
$pdf_title = isset($_GET['pdf_title']) ? sanitize_text_field($_GET['pdf_title']) : 'Invoice';


$order = wc_get_order($order_no);
$v_order = wc_get_order($id);

$totals_html = '';
$total_sum = 0;
$subtotal_sum = 0;
$vat_sum = 0;
$discount_code_value_sum = 0;
$tf_3_sum = 0;
$shipping_display_sum = 0;
$mcf_v_sum = 0;
$subtotal_2_sum = 0;

//echo $id . "<br>";
//echo $order_no . "<br>";
//echo $new_date . "<br>";

//print_r($order);

global $wpdb;



// Get the voucher discount rate
//$voucher_discount = $order->get_meta('_voucher_discount'); // Retrieve the meta value
//$voucher_discount = $order->get_meta('_voucher_discount') ?: 0;
// Get the voucher discount rate

if ( $order instanceof WC_Order ) {
    $voucher_discount = $order->get_meta('_voucher_discount') ?: 0;
} else {
    // Optional logging for debug
    error_log("PDF Invoice: Order not found for order_no = $order_no");
}



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


$table_name = $wpdb->prefix . 'split_schedule_orders';


/* Old query */ 
//$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
/* Old query */ 



if($is_merged == 1){
    $sql = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE order_no = %d AND is_merged = %s AND date = %s",
        $order_no,
        $is_merged,
        $new_date
    );
} else {
    $sql = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE order_no = %s AND date = %s",
        $order_no,
        $new_date
    );
}





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
        $Billing_address = $billing_firstname . " " . $billing_lastname . "\n" .
            $billing_address_1 . ", " . $billing_address_2 . "\n" .
            $billing_city . "\n" . $billing_postcode . "\n" . $billing_state . "\n" . $billing_country;

        $shipping_address = $shipping_firstname . " " . $shipping_lastname . "\n" .
            $shipping_address_1 . ", " . $shipping_address_2 . "\n" .
            $shipping_city . "\n" . $shipping_postcode . "\n" . $shipping_state . "\n" . $shipping_country;

        $logo_path = get_stylesheet_directory_uri() . '/pdf-generation/examples/images/logo_example.jpg';


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

                // Add text to bottom-right
                $this->SetFont('helvetica', '', 8);
                $this->SetX(35); // Start text to the right of the logo

                $bank_details = "\nBank name: HSBC\n" .
                                "Acc Name: UNIVERSAL SCIENCE (UK) LIMITED T/A Materials Direct\n" .
                                "Sort code: 40-33-33, Acc No: 42625172, IBAN: GB04HBUK40333342625172\n" .
                                "A Division of Universal Science (UK) Ltd | VAT number: GB222089038 | Company number: 07702000";
                $this->MultiCell(170, 20, $bank_details, 0, 'L', 0, 1, '', '', true);

            }
        }


        // Create new PDF document
        $pdf = new MDPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setIsoLogoPath(get_stylesheet_directory() . '/pdf-generation/examples/images/iso.png');

        $pdf->SetLeftMargin(10);
        //$pdf->SetMargins(20, 20, 20); 

        // Set document information
        $pdf->SetCreator('Andrew Hosegood');
        $pdf->SetAuthor('Materials Direct');
        $pdf->SetTitle('Materials-Direct-INVOICE');
        $pdf->SetSubject('Order Date Report');
        $pdf->SetKeywords('Order, PDF, Dates');

        // Disable header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // Set default font
        $pdf->SetFont('helvetica', '', 7.8);

        // Add a single page
        $pdf->AddPage();


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
        $html .= '<p>' . nl2br($Billing_address) . '</p><br>';
        $html .= '</td>';

        $html .= '<td style="width: 60%; padding: 20px;">';
        $html .= '<br><br>';
        $html .= '<p style="line-height: 0.1;"><strong>Delivery Address</strong></p>';
        $html .= '<p>' . nl2br($shipping_address) . '</p><br>';
        $html .= '</td>';
        $html .= '</tr>';

        // --------------------------
        // 🎯 Buffer Order and Invoice Content
        // --------------------------
        $order_details_html = '';  // Store order details
        $invoice_details_html = ''; // Store invoice details
        $order_details_added = false;

        // Loop through results and store the HTML in variables
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
          	$radius = $row['radius'];
            $width_inch = $row['width_inch'];
            $length_inch = $row['length_inch'];
            $voucher_code = $row['voucher_code'];
            $voucher_percent = $row['voucher_percent'];
            $shipping_weights = $row['shipping_weights'];
            $total_shipping_duplicates = $row['shipping_duplicates'];
            $meta_qty = $row['meta_shipping_qty'];
            $meta_shipping_total = $row['meta_shipping_total'];

            $mcofc_fair = $row['mcofc_fair'];
            $mcofc_fair_string = $row['mcofc_fair_string'];

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

            $mcofc_fair_value = $row['mcofc_fair_value'];
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
            if($row['repayment_terms'] == 0){
                $repayment_terms = 30;
            } else {
                $repayment_terms = $row['repayment_terms'];
            }
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


            // if($rolls_value == "Rolls"){
            //     $schedule_qty = $schedule_qty * $rolls_length;
            // }



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
            // if($row['mcofc_fair'] === "Manufacturers COFC"){
            //     $mcofc_fair_value_display = 10;
            // }
            // elseif($row['mcofc_fair'] === "FAIR"){
            //     $mcofc_fair_value_display = 95;
            // }
            // elseif($row['mcofc_fair'] === "Materials Direct COFC"){
            //     $mcofc_fair_value_display = 12.50;
            // }
            // else {
            //     $mcofc_fair_value_display = 0;
            // }

            $mcofc_fair_value_display = $mcofc_fair_value;
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



            // Get the MCOFC and FAIR values
            // echo "<pre>";
            // print_r($mcofc_fair);
            // echo "</pre>";



            // $mcofc_items = array_map(function($item) use (&$mcf_v) {
            //     $item = trim($item);
            //     if ($item === 'Manufacturers COFC') {
            //         $cost = ' - £10';
            //         $cost_value = 10.00;
            //     } elseif ($item === 'FAIR') {
            //         $cost = ' - £95';
            //         $cost_value = 95.00;
            //     } elseif($item === "Materials Direct COFC") {
            //         $cost = ' - £12.50'; 
            //         $cost_value = 12.50;
            //     } else {
            //         $cost = ' - £0'; 
            //         $cost_value = 0;
            //     }
            //     $mcf_v += $cost_value; // Accumulate the cost for this row
            //     return '(' . $item . $cost . ')';
            // }, explode(',', $mcofc_fair_string));

            // $mcf = "<br>" . implode('<br>', $mcofc_items);
            // Get the MCOFC and FAIR values





            //$subtotal_display


            $vat_amount = $cppnew + $mcofc_fair_value_display + $my_shipping_response - $tf_3 - $voucher_percent;


            if($country == "United Kingdom"){
                $vat_display_top = ($cppnew * $tax_rate) / 100;
                $vat_display = ($vat_amount * $tax_rate) / 100;
                $vat_sum_top += $vat_display_top;
                $vat_sum += $vat_display;
            } else {
                $vat_display_top = 0;
                $vat_display = 0;
                $vat_sum += $vat_display;
            }
            // calculate VAT

            // echo $cppnew;
            // echo "<br>";
            // echo $tax_rate;



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
                    $mcf = "\n".$row['mcofc_fair'].": £95";
                    $mcf_v = 95;
                    $mcf_v = (float) $mcf_v;
                    
                } 
                elseif($row['mcofc_fair'] == "Manufacturers COFC"){
                    $mcf = "\n".$row['mcofc_fair'].": £10";
                    $mcf_v = 10;
                    $mcf_v = (float) $mcf_v;
                }
                elseif($row['mcofc_fair'] == "Materials Direct COFC"){
                    $mcf = "\n".$row['mcofc_fair'].": £12.50";
                    $mcf_v = 12.50;
                    $mcf_v = (float) $mcf_v;
                }
                elseif($row['mcofc_fair'] == "Unknown"){
                    $mcf = "\n".$row['mcofc_fair'].": £0";
                    $mcf_v = 0;
                    $mcf_v = (float) $mcf_v;
                }
                else {
                    $mcf = "\n".$row['mcofc_fair'].": £105";
                }
            }

            //$mcf_v_sum += $mcf_v;
            $mcf_v_sum += $mcofc_fair_value;
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

            //if(empty($row['pdf'])){
                //$dra = "";
            //} else {
                //$dra = "<br>PDF Drawing: ".$row['pdf'];
            //}

            //if(empty($row['dxf'])){
                //$dxf = "";
            //} else {
                //$dxf = "<br>DXF Drawing: ".$row['dxf'];
            //}

            if(empty($row['pdf'])){
                $dra = "";
            } else {
                if (filter_var($row['pdf'], FILTER_VALIDATE_URL)) {
                    $dra_c = basename(parse_url($row['pdf'], PHP_URL_PATH));
                    $dra = "<br>PDF Drawing: ".$dra_c;
                } else {
                    $dra = "<br>PDF Drawing: ".$row['pdf'];
                }
                
            }

            if(empty($row['dxf'])){
                $dxf = "";
            } else {
                if (filter_var($row['dxf'], FILTER_VALIDATE_URL)) {
                     $dxf_c = basename(parse_url($row['dxf'], PHP_URL_PATH));
                     $dxf = "<br>DXF Drawing: ".$dxf_c;
                } else {
                    $dxf = "<br>DXF Drawing: ".$row['dxf'];
                }
            }

            if(empty($row['mcofc_fair_string'])){
                $mcf = "";
                $mcf_v = 0;
                $mcf_v = (float) $mcf_v;
            } else {
                if($row['mcofc_fair_string'] == "First Article Inspection Report"){
                    $mcf = "<br>".$mcofc_fair.": £95";
                    $mcf_v = 95;
                    $mcf_v = (float) $mcf_v;
                    
                } 
                elseif($row['mcofc_fair_string'] == "Manufacturers COFC"){
                    $mcf = "<br>".$mcofc_fair.": £10";
                    $mcf_v = 10;
                    $mcf_v = (float) $mcf_v;
                }
                elseif($row['mcofc_fair_string'] == "Materials Direct COFC"){
                    $mcf = "<br>".$mcofc_fair.": £12.50";
                    $mcf_v = 12.50;
                    $mcf_v = (float) $mcf_v;
                }
                elseif($row['mcofc_fair_string'] == "Unknown"){
                    $mcf = "<br>All COFC's: £0";
                    $mcf_v = 0;
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
            $str = "<br><br>Unit price does not include any fair or manufacturers COFC";
            //collect the values for product Description

            //collect the values for the invoice numbers on merged dates
            $invoice_no_calc = $row['invoice_no'];
            $invoice_no_calc = substr($invoice_no_calc, 0, -2);
            $invoice_date = $row['date'];
            $invoice_date = str_replace("-", "", $invoice_date);
            $merged_invoice = $invoice_no_calc ."-". $invoice_date;
            //collect the values for the invoice numbers on merged dates

            // calculate subtotal display
            $subtotal_display_2 = $cppnew + $mcofc_fair_value + $my_shipping_response - $tf_3 - $discount_code_value_new;
            $subtotal_2_sum += $subtotal_display_2;
            // calculate subtotal display

            $temp_value = $tax_rate . ", " . $cppnew . ", " . $shipping_display_new . ", " . $tf_3 . ", " . $md_value . ", " . $discount_code_value_new;

            // Order Details (First Table)
            if (!$order_details_added) {
                $formatted_date_pdf = date('jS F Y', strtotime($row['date']));
                $order_details_html .= '<tr>';

                if($pdf_title != "Order Acknowledgement"){
                    if($duplicate_date_count > 1){
                        $order_details_html .= '<td>#'. $merged_invoice .'</td>';
                    } else {
                        $order_details_html .= '<td>#' . $row['invoice_no'] . '</td>';
                    }
                }

                $order_details_html .= '<td>' . $formatted_date_pdf . '</td>';
                $order_details_html .= '<td>'.$pdf_date.'</td>';
                $order_details_html .= '<td>#' . $order_no . '</td>';
                $order_details_html .= '<td>' . $row['customer_po'] . '</td>';
                //$order_details_html .= '<td>' . $row['schedule'] . '</td>';
                $order_details_html .= '</tr>';
                $order_details_added = true;
            }

            // Invoice Details (Second Table)
            $invoice_details_html .= '<tr>';
            $invoice_details_html .= '<td>' . $row['sku'] . '</td>';
            //$invoice_details_html .= '<td>' . $title . $ps . $dra . $dxf . $wdt . $wdti . $lgt . $lgti . $rad . "<br>" . $mcofc_fair_formatted . $scd . $sch . $str .'New Total: '. $newtotal . '<br>cppnew: ' .$cppnew. '<br>My Shipping Response: ' .$my_shipping_response. '<br>Vat Display: ' .$vat_display. '<br>tf_3: ' .$tf_3. '<br>md_value: ' .$md_value. '<br>mcofc_fair_value_display: ' .$mcofc_fair_value_display. '<br>mcf_v: ' .$mcf_v. '<br>MCOFC Fair Value: ' .$mcofc_fair_value. '<br>Discount Code Value New: ' . $discount_code_value_new .  '</td>';
            //$invoice_details_html .= '<td>' . $title . $ps . $dra . $dxf . $wdt . $wdti . $lgt . $lgti . $rad . $mcf . $scd . $sch . $str . "<br>MCOFC Fair: " . $mcofc_fair_string . '</td>';
            //$invoice_details_html .= '<td>' . $title . $ps . $dra . $dxf . $wdt . $lgt . $wdti . $lgti . $rad . "<br>" . $mcofc_fair_formatted . $sch . $str .  '</td>';
            // $rolls_value = $row['rolls_value'];
            // $rolls_length = $row['rolls_length'];
            $invoice_details_html .= '<td>' . $title . $ps . $dra . $dxf . $wdt . $lgt . $wdti . $lgti . $rad . "<br>" . $mcofc_fair_formatted . $sch . $str . "<br>total_final: " . $total_final . "<br> cppnew: " . $cppnew . "<br>my_shipping_response: " . $my_shipping_response . "<br>vat_display: " .$vat_display. "<br>tf_3: " .$tf_3. "<br>md_value: " .$md_value. "<br>mcofc_fair_value_display: " .$mcofc_fair_value_display. "<br>voucher_percent: " .$voucher_percent. "<br>total_1: " .$total_1. "<br>discount_amount: " .$discount_amount. "<br>on_backorder: " .$row['on_backorder']. "<br>Rolls Value: " .$rolls_value. "<br>Rolls Length: " .$rolls_length.  '</td>'; 
            //$invoice_details_html .= '<td>' . $row['title'] . '<br>Part shape: ' . $part_shape  . '<br>Width (MM): ' . $width . '<br>Length (MM): ' . $length . '<br><br>Schedule: ' .$row['schedule'] . '</td>';

            $invoice_details_html .= '<td>' . $row['schedule_qty'] . '</td>';
            $invoice_details_html .= '<td>' . number_format($row['cost_per_part_raw'], 4) . '</td>';
            $invoice_details_html .= '<td>' . $subtotal_display . '</td>';
            $invoice_details_html .= '<td>' . number_format($vat_display_top, 2) . '</td>';
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
                        $totals_html .= '<td>£'.number_format($msr, 2).'</td>';
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
                        //$totals_html .= '<td>£'.$mcf_v.'</td>';
                        $totals_html .= '<td>£'.$mcofc_fair_value.'</td>';
                        $totals_html .= '</tr>';
                    }
                    $totals_html .= '<tr>';
                    $totals_html .= '<td>Shipping Total (ex. VAT)</td>';
                    $totals_html .= '<td>£'.number_format($my_shipping_response, 2) .'</td>';
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
        $bank_details .= '<td>Materials Direct Bank A/C details:<br>HSBC Bank<br>Sort Code: 40-33-33<br>Account Number: 42625172<br>Payment terms - '.$repayment_terms.' days net</td>';
        $bank_details .= '</tr>';



        // -------------------------------
        // 🎯 Display Order Details Table
        // -------------------------------
        $html .= '<tr>';
        $html .= '<td colspan="2" style="padding: 20px;">';
        $html .= '<h3 style="margin-bottom: 10px;">Order Details</h3>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f2f2f2;">';
        if($pdf_title != "Order Acknowledgement"){
            $html .= '<th><strong>Invoice No.</strong></th>';
        }
        $html .= '<th><strong>Scheduled Date</strong></th>';
        $html .= '<th><strong>Dispatch Date</strong></th>';
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
        $html .= '<h2 style="margin-bottom: 10px; margin-top: 10px; text-align:center;">'.$pdf_title.'</h2>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color:rgb(244, 147, 2);">';
        $html .= '<th style="width: 10%;"><strong>Part No.</strong></th>';
        $html .= '<th style="width: 45%;"><strong>Product Description</strong></th>';
        $html .= '<th style="width: 8%;"><strong>Qty</strong></th>';
        $html .= '<th style="width: 12%;"><strong>Unit Price</strong></th>';
        $html .= '<th style="width: 13%;"><strong>Net Amount</strong></th>';
        $html .= '<th style="width: 12%;"><strong>VAT (£)</strong></th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>' . $invoice_details_html . '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';

        // ----------------------------
        // 🎯 Display Order Total Table
        // ----------------------------

        $html .= '<tr>';
        $html .= '<td colspan="2" style="padding: 20px;">';

        $html .= '<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">';
        $html .= '<tr>';

        $html .= '<td class="padding:0 50px 0 0; vertical-align: bottom;">';
        $html .= '<br><br><table id="totals" border="1" cellspacing="0" cellpadding="5" style="width: 95%; border-collapse: collapse; margin-left: auto; margin-right: 0;">';
        $html .= '<tbody>' . $bank_details . '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        
        $html .= '<td>';
        $html .= '<br><br><table id="totals" border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: 0;">';
        $html .= '<tbody>' . $totals_html . '</tbody>';
        $html .= '</table>';
        $html .= '</td>';

        $html .= '</tr>';
        $html .= '</table>'; // Close the container table


        $html .= '</td>';
        $html .= '</tr>';


        // --------------------------
        // 🎯 Display disclaimer
        // --------------------------


        // End HTML content
        $html .= '</table>';


        // echo $html;

        // exit;

        // Output the HTML content as a single page
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF (display in browser)
        $pdf->Output($order_no . '-' .$date . '.pdf', 'I');
    } else {
        echo 'No records found for the given order number.';
    }
} catch (Exception $e) {
    echo 'Error generating PDF: ' . $e->getMessage();
}
?>
