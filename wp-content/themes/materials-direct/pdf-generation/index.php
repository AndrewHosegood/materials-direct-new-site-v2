<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = 1;
//$id = $_GET['id'];
echo "ID = " .  $id;

global $wpdb;
//$table_name = $wpdb->prefix . 'split_schedule_orders';
//$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = $id");
//$sql = $wpdb->prepare("SELECT * FROM 'wp_split_schedule_orders' WHERE id = %d", $id);

// try {
//     $results = $wpdb->get_results($sql, ARRAY_A);
//     foreach ($results as $row) {
//         $title = $row['title'];
//         $invoice_no = $row['invoice_no'];
//         $firstname = $row['firstname'];
//         $lastname = $row['lastname'];
//         $company = $row['company'];
//         $order_no = $row['order_no'];
//         $customer_po_no = $row['customer_po'];
//         //$cost_per_part = $row['cost_per_part'];
//         $cost_per_part = preg_replace('/\(Average Price\)/', '', $row['cost_per_part']);
//         $status = $row['status'];
//         $schedule = $row['schedule'];
//         $schedule_qty = $row['schedule_qty'];
//         $part_shape = $row['part_shape'];
//         $part_shape_link = $row['part_shape_link'];
//         $pdf = $row['pdf'];
//         $dxf = $row['dxf'];
//         $dimension_type = $row['dimension_type'];
//         $width = $row['width'];
//         $length = $row['length'];
//         $radius = $row['radius'];
//         $qty = $row['qty'];
//         $date = $row['date'];
//         $notes = $row['notes'];
//         $shipping = $row['shipping'];
//         $sku = $row['sku'];
//         echo $title;
//         echo $order_no;
//         echo $schedule;
//         echo $schedule_qty;
//         echo $part_shape;
//         echo $part_shape_link;
//         echo $pdf;
//         echo $dxf;
//         echo $width;
//         echo $length;
//         echo $radius;
//         echo $qty;
//         echo $notes;
//         echo $shipping;
//         echo $sku;
//         $order = wc_get_order($order_no);

//         if(!empty($order->get_billing_first_name())){
//             $billing_firstname = $order->get_billing_first_name();
//         }

//         $total_price = $order->get_total();
//         $total_tax = $order->get_total_tax();
//         $date = new DateTime($date);
//         $formatted_date_pdf = $date->format('jS F Y');
//         $payment_method = $order->get_payment_method_title();
//         $billing_address_1 = "13 Buttermere Close";
//         $billing_address_2 = "Bletchley";
//         $billing_city = "Milton Keynes";
//         $billing_postcode = "MK2 3DG";

//         $pattern = '/[0-9]+(?:\.[0-9]+)?/';
//         preg_match($pattern, $shipping, $matches);
//         $shipping_new = $matches[0];
//         $subtotal = $total_price - $shipping_new - $total_tax;

//         // CONSTRUCT THE PDF
//         require_once('/Applications/MAMP/htdocs/materials-direct/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');

//         class MYPDF extends TCPDF {

//             public function Header() {
//                 $bMargin = $this->getBreakMargin();
//                 $auto_page_break = $this->AutoPageBreak;
//                 $this->setAutoPageBreak(false, 0);
//                 $img_file = K_PATH_IMAGES.'image_demo_delivery.jpg';
//                 $this->Image($img_file, null, 0, 210, 297, '', '', '', false, 300, 'C', false, false, 0);
//                 $this->setAutoPageBreak($auto_page_break, $bMargin);
//                 $this->setPageMark();
//             }
//         }

//         $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//         $pdf->setCreator(PDF_CREATOR);
//         $pdf->setAuthor('Nicola Asuni');
//         $pdf->setTitle('TCPDF Example 051');
//         $pdf->setSubject('TCPDF Tutorial');
//         $pdf->setKeywords('TCPDF, PDF, example, test, guide');
//         $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//         $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//         $pdf->setMargins(11, 41, 11);
//         $pdf->setHeaderMargin(0);
//         $pdf->setFooterMargin(0);
//         $pdf->setPrintFooter(false);
//         $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//         $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//         if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
//             require_once(dirname(__FILE__).'/lang/eng.php');
//             $pdf->setLanguageArray($l);
//         }

//         $pdf->setFont('helvetica', '', 9);

//         $pdf->AddPage();

//         $value_1 = $firstname ." ". $lastname ." ". $company . "\n".$billing_address_1."\n".$billing_address_2."\n".$billing_city."\n".$billing_postcode;
//         $value_2 = $firstname ." ". $lastname ." ". $company . "\n".$billing_address_1."\n".$billing_address_2."\n".$billing_city."\n".$billing_postcode;
//         $value_3 = "#" . $invoice_no; //invoice number
//         $value_4 = $formatted_date_pdf; //formatted date
//         $value_5 = $order_no;
//         $value_6 =  $customer_po_no;
//         $value_7 = $sku; //part no.
//         $value_8 = $title . "\nPart shape: ".$part_shape."\nUpload .PDF Drawing: ".$part_shape_link."\nWidth (MM): ".$width."\nLength (MM): $length";
//         $value_9 = $schedule_qty;
//         $value_10 = $cost_per_part; //unit price
//         $value_11 = $subtotal; //net amount
//         $value_12 = $total_tax; //VAT
//         $value_13 = "£" . $subtotal; //TOTAL ex VAT
//         $value_14 = $shipping; // Shipping total
//         $value_15 = "£" . $subtotal; // Subtotal ex VAT
//         $value_16 = "£" . $total_tax; // VAT
//         $value_17 = "£" . $total_price; // Total inc VAT


//         $pdf->MultiCell(0, 0, $value_1, 0, 'L', false, 1);
//         $pdf->SetXY(10, 76);

//         $pdf->MultiCell(0, 0, $value_2, 0, 'L', false, 1);
//         $pdf->SetXY(160, 75);

//         $pdf->Cell(0, 0, $value_3, 0, 1, 'L');
//         $pdf->SetXY(160, 80.5);

//         $pdf->Cell(0, 0, $value_4, 0, 1, 'L');
//         $pdf->SetXY(160, 86);

//         $pdf->Cell(0, 0, $value_5, 0, 1, 'L');
//         $pdf->SetXY(160, 93);

//         $pdf->Cell(0, 0, $value_6, 0, 1, 'L');
//         $pdf->SetXY(12.6, 126);

//         $pdf->Cell(0, 0, $value_7, 0, 1, 'L');
//         $pdf->SetXY(31.5, 126);
//         $pdf->MultiCell(0, 0, $value_8, 0, 'L', false, 1);
//         $pdf->SetXY(104, 126);
//         $pdf->Cell(0, 0, $value_9, 0, 1, 'L');
//         $pdf->SetXY(122.5, 126);
//         $pdf->Cell(0, 0, $value_10, 0, 1, 'L');
//         $pdf->SetXY(150, 126);
//         $pdf->Cell(0, 0, $value_11, 0, 1, 'L');
//         $pdf->SetXY(182.8, 126);
//         $pdf->Cell(0, 0, $value_12, 0, 1, 'L');
//         $pdf->SetXY(163, 234.8);
//         $pdf->Cell(0, 0, $value_13, 0, 1, 'R');
//         $pdf->SetXY(163, 242.3);
//         $pdf->Cell(0, 0, $value_14, 0, 1, 'R');
//         $pdf->SetFont('helvetica', 'B', 9);
//         $pdf->SetXY(163, 250.5);
//         $pdf->Cell(0, 0, $value_15, 0, 1, 'R');
//         $pdf->SetFont('helvetica', '', 9);
//         $pdf->SetXY(163, 258);
//         $pdf->Cell(0, 0, $value_16, 0, 1, 'R');
//         $pdf->SetXY(163, 266);
//         $pdf->Cell(0, 0, $value_17, 0, 1, 'R');

//         $pdf->SetFont('helvetica', '', 9);

//         $pdf->setPrintHeader(false);

//         //Close and output PDF document
//         $pdf->Output('example_051.pdf', 'I');


//         // END CONSTRUCT THE PDF
//     }
// } catch (Exception $e) {
//     // Handle any errors
//     echo "Error: " . $e->getMessage();
// }



        // CONSTRUCT THE PDF
        require_once('/Applications/MAMP/htdocs/materials-direct/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');

        class MYPDF extends TCPDF {

            public function Header() {
                $bMargin = $this->getBreakMargin();
                $auto_page_break = $this->AutoPageBreak;
                $this->setAutoPageBreak(false, 0);
                $img_file = K_PATH_IMAGES.'image_demo_delivery.jpg';
                $this->Image($img_file, null, 0, 210, 297, '', '', '', false, 300, 'C', false, false, 0);
                $this->setAutoPageBreak($auto_page_break, $bMargin);
                $this->setPageMark();
            }
        }

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('Nicola Asuni');
        $pdf->setTitle('TCPDF Example 051');
        $pdf->setSubject('TCPDF Tutorial');
        $pdf->setKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->setMargins(11, 41, 11);
        $pdf->setHeaderMargin(0);
        $pdf->setFooterMargin(0);
        $pdf->setPrintFooter(false);
        $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->setFont('helvetica', '', 9);

        $pdf->AddPage();

        $firstname = "Andrew";
        $lastname = "Hosegood"
        $company = "xxxxxx";
        $billing_address_1 = "xxxxx";
        $billing_address_2 = "xxxxx";
        $billing_city = "xxxxx";
        $billing_postcode = "xxxxx";
        $invoice_no = "xxxxxx";
        $formatted_date_pdf = "xxxxxx";

        $value_1 = $firstname ." ". $lastname ." ". $company . "\n".$billing_address_1."\n".$billing_address_2."\n".$billing_city."\n".$billing_postcode;
        $value_2 = $firstname ." ". $lastname ." ". $company . "\n".$billing_address_1."\n".$billing_address_2."\n".$billing_city."\n".$billing_postcode;
        $value_3 = "#" . $invoice_no; //invoice number
        $value_4 = $formatted_date_pdf; //formatted date
        $value_5 = $order_no;
        $value_6 =  $customer_po_no;
        $value_7 = $sku; //part no.
        $value_8 = $title . "\nPart shape: ".$part_shape."\nUpload .PDF Drawing: ".$part_shape_link."\nWidth (MM): ".$width."\nLength (MM): $length";
        $value_9 = $schedule_qty;
        $value_10 = $cost_per_part; //unit price
        $value_11 = $subtotal; //net amount
        $value_12 = $total_tax; //VAT
        $value_13 = "£" . $subtotal; //TOTAL ex VAT
        $value_14 = $shipping; // Shipping total
        $value_15 = "£" . $subtotal; // Subtotal ex VAT
        $value_16 = "£" . $total_tax; // VAT
        $value_17 = "£" . $total_price; // Total inc VAT


        $pdf->MultiCell(0, 0, $value_1, 0, 'L', false, 1);
        $pdf->SetXY(10, 76);

        $pdf->MultiCell(0, 0, $value_2, 0, 'L', false, 1);
        $pdf->SetXY(160, 75);

        $pdf->Cell(0, 0, $value_3, 0, 1, 'L');
        $pdf->SetXY(160, 80.5);

        $pdf->Cell(0, 0, $value_4, 0, 1, 'L');
        $pdf->SetXY(160, 86);

        $pdf->Cell(0, 0, $value_5, 0, 1, 'L');
        $pdf->SetXY(160, 93);

        $pdf->Cell(0, 0, $value_6, 0, 1, 'L');
        $pdf->SetXY(12.6, 126);

        $pdf->Cell(0, 0, $value_7, 0, 1, 'L');
        $pdf->SetXY(31.5, 126);
        $pdf->MultiCell(0, 0, $value_8, 0, 'L', false, 1);
        $pdf->SetXY(104, 126);
        $pdf->Cell(0, 0, $value_9, 0, 1, 'L');
        $pdf->SetXY(122.5, 126);
        $pdf->Cell(0, 0, $value_10, 0, 1, 'L');
        $pdf->SetXY(150, 126);
        $pdf->Cell(0, 0, $value_11, 0, 1, 'L');
        $pdf->SetXY(182.8, 126);
        $pdf->Cell(0, 0, $value_12, 0, 1, 'L');
        $pdf->SetXY(163, 234.8);
        $pdf->Cell(0, 0, $value_13, 0, 1, 'R');
        $pdf->SetXY(163, 242.3);
        $pdf->Cell(0, 0, $value_14, 0, 1, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(163, 250.5);
        $pdf->Cell(0, 0, $value_15, 0, 1, 'R');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(163, 258);
        $pdf->Cell(0, 0, $value_16, 0, 1, 'R');
        $pdf->SetXY(163, 266);
        $pdf->Cell(0, 0, $value_17, 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 9);

        $pdf->setPrintHeader(false);

        //Close and output PDF document
        $pdf->Output('example_051.pdf', 'I');