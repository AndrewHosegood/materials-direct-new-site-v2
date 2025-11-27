<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('examples/tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->setAutoPageBreak(false, 0);
		// set bacground image
		$img_file = K_PATH_IMAGES.'image_demo_invoice.jpg';
		$this->Image($img_file, null, 0, 210, 297, '', '', '', false, 300, 'C', false, false, 0);
		// restore auto-page-break status
		$this->setAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 051');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(11, 41, 11);
$pdf->setHeaderMargin(0);
$pdf->setFooterMargin(0);

// remove default footer
$pdf->setPrintFooter(false);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFont('helvetica', '', 9);

// add a page
$pdf->AddPage();

$value_1 = "Jack Jones\n13 Buttermere Close\nBletchley\nMilton Keynes\nMK2 3DG";
$value_2 = "Jack Jones\n13 Buttermere Close\nBletchley\nMilton Keynes\nMK2 3DG";
$value_3 = "#00001-1";
$value_4 = "15th April 2024";
$value_5 = "38494";
$value_6 = "PO23001896";
$value_7 = "1035";
$value_8 = "Universal Science A3100 - 0.35mm\nPart shape: Custom Shape (Drawing)\nUpload .PDF Drawing: sample27.pdf\nWidth (MM): 4\nLength (MM): 10";
$value_9 = "100";
$value_10 = "0.032";
$value_11 = "240.66";
$value_12 = "54.73";
$value_13 = "£273.66";
$value_14 = "£33.00";
$value_15 = "£306.66";
$value_16 = "£61.33";
$value_17 = "£367.99";

// Invoice Address
$pdf->MultiCell(0, 0, $value_1, 0, 'L', false, 1);

// Delivery Address
$pdf->SetXY(10, 76);
$pdf->MultiCell(0, 0, $value_2, 0, 'L', false, 1);



// Invoice Number
$pdf->SetXY(160, 75);
$pdf->Cell(0, 0, $value_3, 0, 1, 'L');

// Despatch Date
$pdf->SetXY(160, 80.5);
$pdf->Cell(0, 0, $value_4, 0, 1, 'L');

// MD Order No.
$pdf->SetXY(160, 86);
$pdf->Cell(0, 0, $value_5, 0, 1, 'L');

// Customer PO Number
$pdf->SetXY(160, 93);
$pdf->Cell(0, 0, $value_6, 0, 1, 'L');



// Part No.
$pdf->SetXY(12.6, 126);
$pdf->Cell(0, 0, $value_7, 0, 1, 'L');

// Product Description
$pdf->SetXY(31.5, 126);
$pdf->MultiCell(0, 0, $value_8, 0, 'L', false, 1);

// QTY
$pdf->SetXY(104, 126);
$pdf->Cell(0, 0, $value_9, 0, 1, 'L');

// Unit Price
$pdf->SetXY(122.5, 126);
$pdf->Cell(0, 0, $value_10, 0, 1, 'L');

// Net Amount
$pdf->SetXY(150, 126);
$pdf->Cell(0, 0, $value_11, 0, 1, 'L');

// VAT (£)
$pdf->SetXY(182.8, 126);
$pdf->Cell(0, 0, $value_12, 0, 1, 'L');



// Total (ex. VAT)
$pdf->SetXY(163, 234.8);
$pdf->Cell(0, 0, $value_13, 0, 1, 'R');

// Shipping Total (ex. VAT)
$pdf->SetXY(163, 242.3);
$pdf->Cell(0, 0, $value_14, 0, 1, 'R');

// Subtotal (ex. VAT)
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(163, 250.5);
$pdf->Cell(0, 0, $value_15, 0, 1, 'R');
$pdf->SetFont('helvetica', '', 9);

// VAT
$pdf->SetXY(163, 258);
$pdf->Cell(0, 0, $value_16, 0, 1, 'R');

// TOTAL
$pdf->SetXY(163, 266);
$pdf->Cell(0, 0, $value_17, 0, 1, 'R');







// --- example with background set on page ---

// remove default header
$pdf->setPrintHeader(false);

// ---------------------------------------------------------

//Close and output PDF document
//$pdf->Output('example_051.pdf', 'I');

$pdf_filename = "38710";
$count = 1;

$tempFilePath = '/Applications/MAMP/htdocs/materials-direct/wp-content/themes/creative-mon/pdf-generation/pdf/'.$pdf_filename.'-'.$count.'.pdf'; // Specify the folder path and filename
$pdf->Output($tempFilePath, 'F'); // 'F' parameter saves the PDF to a file




//$pdfFilePath = '/https://staging2.materials-direct.com/public_html/pdf/example_051.pdf'; // Specify the folder path and filename
//$pdf->Output($pdfFilePath, 'F'); // 'F' parameter saves the PDF to a file
//$pdf->Output($_SERVER['DOCUMENT_ROOT'] . 'example_051.pdf', 'F');

// Send appropriate headers to force download
//  header('Content-Type: application/octet-stream');
//  header('Content-Disposition: attachment; filename="example_051.pdf"');
//  header('Content-Length: ' . filesize($pdfFilePath));
//  readfile($pdfFilePath);