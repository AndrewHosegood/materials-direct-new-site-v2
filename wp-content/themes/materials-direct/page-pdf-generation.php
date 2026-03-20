<?php
/*
Template Name: PDF Generation (Delivery Note)
*/

$domain = $_SERVER['HTTP_HOST'];

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
$order_no = isset($_GET['order_no']) ? sanitize_text_field($_GET['order_no']) : '';
$new_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
$is_merged = isset($_GET['is_merged']) ? sanitize_text_field($_GET['is_merged']) : '';

// $id = $_GET['id'];
// $order_no = $_GET['order_no'];
// $new_date = $_GET['date'];
$order = wc_get_order($order_no);
$totals_html = '';



global $wpdb;


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
        $value_1 = $billing_firstname . " " . $billing_lastname . "\n" .
            $billing_address_1 . ", " . $billing_address_2 . "\n" .
            $billing_city . "\n" . $billing_postcode . "\n" . $billing_state . "\n" . $billing_country;

        $value_2 = $shipping_firstname . " " . $shipping_lastname . "\n" .
            $shipping_address_1 . ", " . $shipping_address_2 . "\n" .
            $shipping_city . "\n" . $shipping_postcode . "\n" . $shipping_state . "\n" . $shipping_country;

        $logo_path = get_stylesheet_directory_uri() . '/pdf-generation/examples/images/logo_example.jpg';
        //$iso_logo_path = get_stylesheet_directory_uri() . '/pdf-generation/examples/images/iso.png';



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
        
        

        // Create new PDF document
        $pdf = new MDPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setIsoLogoPath(get_stylesheet_directory() . '/pdf-generation/examples/images/iso.png');


        $pdf->SetLeftMargin(10);
        //$pdf->SetMargins(20, 20, 20); 

        // Set document information
        $pdf->SetCreator('Andrew Hosegood');
        $pdf->SetAuthor('Materials Direct');
        $pdf->SetTitle('Materials-Direct-DELIVERY NOTE');
        $pdf->SetSubject('Order Date Report');
        $pdf->SetKeywords('Order, PDF, Dates');

        // Disable header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // Set default font
        $pdf->SetFont('helvetica', '', 8.8);

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

        // Loop through results and store the HTML in variables
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
            $width_inch = $row['width_inch'];
            $length_inch = $row['length_inch'];
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
          
			/*
            $lgt = "<br>Length (MM): ".$length;
            $lgti = "<br>Length (INCHES): ".$length_inch;
            $wdt = "<br>Width (MM): ".$width;
            $wdti = "<br>Width (INCHES): ".$width_inch;
			*/

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
                $order_details_html .= '<td>'.$pdf_date.'</td>';
                $order_details_html .= '<td>#' . $order_no . '</td>';
                $order_details_html .= '<td>' . $row['customer_po'] . '</td>';
                //$order_details_html .= '<td>' . $row['schedule'] . '</td>';
                $order_details_html .= '</tr>';
                $order_details_added = true;
            }



            // Invoice Details (Second Table) $duplicate_date_count
            $invoice_details_html .= '<tr style="height: 500px;">';
            $invoice_details_html .= '<td style="vertical-align:top;">' . $row['sku'] . '</td>';
            $invoice_details_html .= '<td style="vertical-align:top;">' . $row['title'] . '<br>Part shape: ' . $part_shape  . $wdt . $lgt . $wdti  . $lgti . '<br><br>Schedule: ' .$row['schedule'] . $complete . '</td>';
            $invoice_details_html .= '<td style="vertical-align:top;">' . $row['schedule_qty'] . '</td>';
            $invoice_details_html .= '</tr>';

        }
        


        // -------------------------------
        // 🎯 Display Order Details Table
        // -------------------------------
        $html .= '<tr>';
        $html .= '<td colspan="2" style="padding: 20px;">';
        $html .= '<h3 style="margin-bottom: 10px;">Order Details</h3>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th><strong>Invoice No...</strong></th>';
        $html .= '<th><strong>Scheduled Date</strong></th>';
        $html .= '<th><strong>Dispatch Date</strong></th>';
        $html .= '<th><strong>MD Order No.</strong></th>';
        $html .= '<th><strong>Customer PO Number</strong></th>';
        //$html .= '<th><strong>Schedule</strong></th>';
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

        // echo $html;

        // exit;

        // Output the HTML content as a single page
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF (display in browser)
        $pdf->Output('order_details_' . $order_no . '.pdf', 'I');
    } else {
        echo 'No records found for the given order number.';
    }
} catch (Exception $e) {
    echo 'Error generating PDF: ' . $e->getMessage();
}
?>
