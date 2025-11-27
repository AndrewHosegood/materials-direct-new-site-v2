<?php

require_once('tcpdf_include.php');

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
		$img_file = K_PATH_IMAGES.'image_demo.jpg';
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
$pdf->setMargins(10, 30, 10);
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
$pdf->setFont('helvetica', '', 13);

// add a page
$pdf->AddPage();

// Print a text
$html = '

<div style="color:#444444; background-color:yellow;">eeee</div>


';
$pdf->writeHTML($html, true, false, true, false, '');


// add a page
// $pdf->AddPage();

// // // Print a text
// $html = '<span style="background-color:yellow;color:blue;">&nbsp;PAGE 2&nbsp;</span>';
// $pdf->writeHTML($html, true, false, true, false, '');

// --- example with background set on page ---

// remove default header
$pdf->setPrintHeader(false);

// add a page
// $pdf->AddPage();


// // -- set new background ---

// // get the current page break margin
// $bMargin = $pdf->getBreakMargin();
// // get current auto-page-break mode
// $auto_page_break = $pdf->getAutoPageBreak();
// // disable auto-page-break
// $pdf->setAutoPageBreak(false, 0);
// // set bacground image
// $img_file = K_PATH_IMAGES.'image_demo.jpg';
// $pdf->Image($img_file, null, 0, 210, 297, '', '', '', false, 300, 'C', false, false, 0);
// // restore auto-page-break status
// $pdf->setAutoPageBreak($auto_page_break, $bMargin);
// // set the starting point for the page content
// $pdf->setPageMark();


// // Print a text
// $html = '<span style="color:white;text-align:center;font-weight:bold;font-size:80pt;">MY TEXT HERE</span>';
// $pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_051.pdf', 'I');