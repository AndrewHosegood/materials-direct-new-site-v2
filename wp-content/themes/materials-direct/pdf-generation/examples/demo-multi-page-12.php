<?php
require_once('tcpdf_include.php');

// Product array
$products = [
    [
        "product" => "Universal Science T-Pad 1500 - A0 - 0.23mm",
        "thumb_image" => "http://localhost:8888/wp-content/uploads/2022/02/UniSci_Tpad1500_web-150x150.jpg",
        "Price" => 1276.32,
        "Part Shape" => "Square / Rectangle",
        "Width" => 4,
        "Length" => 10
    ],
    [
        "product" => "Universal Science™ T-Pad® 6500 - A0 - 0.2mm",
        "thumb_image" => "http://localhost:8888/wp-content/uploads/2019/08/Unisci_TPAD6500_22web-150x150.jpg",
        "Price" => 665.5,
        "Part Shape" => "Square / Rectangle",
        "Width" => 3,
        "Length" => 6
    ],
    [
        "product" => "Universal Science A3200 - 0.45mm",
        "thumb_image" => "http://localhost:8888/wp-content/uploads/2022/02/Sarcon_GRAE_web-1-150x150.jpg",
        "Price" => 3018.02,
        "Part Shape" => "Square / Rectangle",
        "Width" => 5,
        "Length" => 11
    ],
    [
        "product" => "Universal Science A3200 - 0.45mm",
        "thumb_image" => "http://localhost:8888/wp-content/uploads/2022/02/Sarcon_GRAE_web-1-150x150.jpg",
        "Price" => 3018.02,
        "Part Shape" => "Square / Rectangle",
        "Width" => 5,
        "Length" => 11
    ],
    [
        "product" => "Universal Science A3200 - 0.45mm",
        "thumb_image" => "http://localhost:8888/wp-content/uploads/2022/02/Sarcon_GRAE_web-1-150x150.jpg",
        "Price" => 3018.02,
        "Part Shape" => "Square / Rectangle",
        "Width" => 5,
        "Length" => 11
    ]
];

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    // Page header
    public function Header() {
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->SetX($this->GetX() + 0);
        $this->Image($image_file, $this->GetX(), 8, 30, '', 'JPG', '', 'T', false, 400, '', false, false, 0, false, false, false);
        $this->setFont('helvetica', 'B', 20);
        $this->SetY($this->GetY() + 7);
        $this->Cell(0, 45, 'Product Information', 0, false, 'R', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        $this->setY(-15);
        $this->setFont('helvetica', 'I', 8);
        $this->Cell(0, 0, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Andrew Hosegood');
$pdf->setTitle('Product Details');
$pdf->setSubject('Product PDF');
$pdf->setKeywords('TCPDF, PDF, products');

// Set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set font
$pdf->setFont('helvetica', 'N', 9);

// Loop through each product and generate a PDF page for each
foreach ($products as $product) {
    // Add a page
    $pdf->AddPage();

// Fetch the image and store it locally 
$image = $product['thumb_image'];
//$image = "";
    // Generate HTML content for the product
    $html = <<<EOD
<table border="1" cellpadding="5" style="border-color: #666666;">
    <tr bgcolor="#e6e6e6" style="border-color: #666666;">
        <th><b>Image</b></th>
        <th><b>product</b></th>
        <th><b>Price</b></th>
        <th><b>Part Shape</b></th>
        <th><b>Width</b></th>
        <th><b>Length</b></th>
    </tr>
    <tr bgcolor="#f8f8f8" style="border-color: #666666;">
        <td><img src="{$image}"></td>
        <td>{$product['product']}</td>
        <td>{$product['Price']}</td>
        <td>{$product['Part Shape']}</td>
        <td>{$product['Width']}</td>
        <td>{$product['Length']}</td>
    </tr>
</table>
<br>
<p>Materials Direct UK Head Office, 76 Burners Ln, Kiln Farm, Milton Keynes MK11 3HD. Telephone: +44 (0)1908 222 211. Email: info@materials-direct.com</p>
EOD;

    // Print product details
    $pdf->writeHTML($html, true, false, true, false, '');
}

// Close and output PDF document
$pdf->Output('products.pdf', 'I');

// Clean up temporary image files
unlink($imageFile);

?>
