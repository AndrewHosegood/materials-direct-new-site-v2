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

// Print a text
$html1 = '<div style="color:#444444; position:absolute; top:50px; left:50px;">Andrew Hosegood<br>13 Buttermere Close<br>Bletchley<br>Milton Keynes<br>MK2 3DG</div>';
$html2 = '<div style="color:#444444; position:absolute; top:100px; left:50px;">Andrew Hosegood<br>13 Buttermere Close<br>Bletchley<br>Milton Keynes<br>MK2 3DG</div>';
$html3 = '<div style="color:#444444; position:absolute; top:100px; left:50px;">XXXXXXXX</div>';


$pdf->writeHTML($html1, true, false, true, false, '');


$pdf->SetXY(10, 76);
$pdf->writeHTML($html2, true, false, true, false, '');


$pdf->SetXY(30, 113);
$pdf->writeHTML($html3, true, false, true, false, '');






// --- example with background set on page ---

// remove default header
$pdf->setPrintHeader(false);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_051.pdf', 'I');