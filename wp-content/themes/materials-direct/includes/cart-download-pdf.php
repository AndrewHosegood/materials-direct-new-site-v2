<?php
// COLLECT THE DATA FOR INCLUDING IN THE PDF
function add_download_pdf_button_to_cart() {

    $cart = WC()->cart;

    // Get COFC fee
    $target_fee_label = "All COFC's & FAIR's";

    $fees = WC()->cart->get_fees();

    if (!empty($fees)) {
        foreach ($fees as $fee) {
            if ($fee->name === $target_fee_label) {
                $target_fee_value = (float) $fee->amount;
                break;
            }
        }
    } 
    // End get COFC fee





    $feeAmount = 0;
    $shipment_details_encoded = 0;
    $currency_rate = 0;
    $shipping_total = WC()->cart->get_shipping_total();

    echo '<a href="' . esc_url(add_query_arg([
            'download_cart_pdf' => 'true',
            'fee' => $feeAmount,
            'cfc_fee' => $target_fee_value,
            'currency_rate' => $currency_rate,
            'shipment' => $shipment_details_encoded
        ])) . '" class="button" style="border-radius: 0.25rem; margin-bottom:1rem;">Download PDF Quote</a>';
}

add_action('woocommerce_before_cart_totals', 'add_download_pdf_button_to_cart', 20);


function get_cart_data() { 

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return []; 
    }

    $domain = $_SERVER['HTTP_HOST'];

    $results = []; 

    if ( WC()->cart->get_cart_contents_count() > 0 ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

            // echo "<pre>";
            // print_r($cart_item);
            // echo "</pre>";

            $shipping = isset($cart_item['custom_inputs']['shipping_address']) ? $cart_item['custom_inputs']['shipping_address'] : [];

            $address_1 = implode(', ', array_filter([
                $shipping['street_address'] ?? '',
                $shipping['address_line2'] ?? '',
                $shipping['city'] ?? '',
                $shipping['county_state'] ?? '',
                $shipping['zip_postal'] ?? '',
                $shipping['country'] ?? '',
            ]));

            $product = $cart_item['data'];
            $product_name = $cart_item['data']->get_name();
            $thumb_image = wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' );
            $price = isset($cart_item['custom_inputs']['price']) ? $cart_item['custom_inputs']['price'] : '';
            $part_shape = isset($cart_item['custom_inputs']['shape_type']) ? $cart_item['custom_inputs']['shape_type'] : '';
            $value1 = ""; //?
            $pdf_link = "";
            $dxf_link = "";
            $value3 = isset($cart_item['custom_inputs']['width']) ? $cart_item['custom_inputs']['width'] : '';
            $value4 = isset($cart_item['custom_inputs']['length']) ? $cart_item['custom_inputs']['length'] : '';
            $value5 = $value4 / 2;
            $value6 = isset($cart_item['custom_inputs']['despatch_notes']) ? $cart_item['custom_inputs']['despatch_notes'] : '';
            $value7 = isset($cart_item['custom_inputs']['total_del_weight']) ? $cart_item['custom_inputs']['total_del_weight'] : '';
            $value8 = isset($cart_item['custom_inputs']['qty']) ? $cart_item['custom_inputs']['qty'] : '';
            $shipments_date = isset($cart_item['custom_inputs']['scheduled_shipments']) ? $cart_item['custom_inputs']['scheduled_shipments'] : '';

            //echo "Shipments Date: " . $shipments_date;
            
            $value9 = "";
            $value10 = "";
            $value11 = "";
            $value12 = "";
            $value13 = "";

            $results[] = [
                "product"                           => $product_name,
                "thumb_image"                       => $thumb_image ? $thumb_image : 'https://via.placeholder.com/150', // Fallback to a placeholder image
                "Price"                             => $price * $quantity, // Calculate price based on quantity
                "Part Shape"                        => $part_shape,
                "Roll Length (Metres)"              => $value1,
                "Upload .PDF Drawing"               => $pdf_link,
                "Upload .DXF Drawing"               => $dxf_link,
                "Width"                             => $value3,
                "Length"                            => $value4,
                "Radius"                            => $value5,
                "Notes"                             => $value6,
                "Shipping Weights"                  => $value7,
                "Total Number of Parts"             => $value8,
                "Manufacturers COFC"                => $value9,
                "First Article Inspection Report"   => $value10,
                "Width (Inch)"                      => $value11,
                "Length (Inch)"                     => $value12,
                "Radius (Inch)"                     => $value13,
                "Address-1"                         => $address_1,
                "shipping_address"                  => $shipping,
                "shipment_date"                     => $shipments_date,
            ];
        }
    }

    // Return the results array
    // echo "<pre>";
    // print_r($results); 
    // echo "</pre>";
    return $results;

}
// COLLECT THE DATA FOR INCLUDING IN THE PDF

// GENERATE THE PDF

function generate_cart_pdf() {

    // echo $_GET['currency_rate'];

    // exit;

    $domain = $_SERVER['HTTP_HOST'];

    if (!isset($_GET['download_cart_pdf'])) {
        return;
    }

    if (isset($_GET['fee'])) {
        $fee =  $_GET['fee'];
    }

    $shipment_details = isset($_GET['shipment']) ? urldecode($_GET['shipment']) : '';





    if (!WC()->cart || WC()->cart->is_empty()) {
        wp_die('Your cart is empty. Add items before downloading the PDF.');
    }



    // Load TCPDF based on environment

    if($domain == "materials-direct.com"){
        require_once('/home/customer/www/materials-direct.com/public_html/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php'); //live
    } elseif($domain == "staging-materials-direct.co.uk"){
        require_once('/homepages/2/d4298640024/htdocs/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php'); //staging
    } else {
        require_once('/Applications/MAMP/htdocs/materials-direct-new/wp-content/themes/creative-mon/pdf-generation/examples/tcpdf_include.php');
    }

    class MYPDF extends TCPDF {
        public function Header() {
            $this->SetY(15); 
            $this->SetFont('helvetica', 'B', 17);

            $image_file = K_PATH_IMAGES . 'logo_example.jpg';
            if (file_exists($image_file)) {
                $this->Image($image_file, 10, 10, 36, 15); 
            }

            $this->Cell(0, 13, 'Purchase Order Summary', 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln(35); 

        }
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $uk_date_time = new DateTime('now', new DateTimeZone('Europe/London'));
            $current_date_time = $uk_date_time->format('d/m/Y, h:ia');
            $this->SetX(10);
            $this->Cell(0, 10, $current_date_time, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            // Right-aligned page number
            $this->SetX(-8);
            $this->Cell(11, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        }
    }

    $pdf = new MYPDF();
    //TCPDF_STATIC::$tcpdf_remote = true;
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->setAuthor('Andrew Hosegood');
    $pdf->SetTitle('Cart Summary');
    $pdf->setSubject('Materials Direct');
    $pdf->SetMargins(10, 33, 10);
    $pdf->setHeaderMargin(17);
    //$pdf->SetCellPadding(0.6);
    $pdf->SetCellPaddings(0, 0.6, 0, 0.6);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);


    // Fetch Cart Items

    $cart_data = get_cart_data();

    if ( empty( $cart_data ) ) {
        return 'Your cart is empty.';
    }


    $first_item = reset($cart_data);

    // Extract the raw shipping address from custom_inputs
    $shipping_raw = $first_item['shipping_address'] ?? [];

    // Build the full formatted shipping address string
    $shipping_address_full = implode(', ', array_filter([
        $shipping_raw['street_address'] ?? '',
        $shipping_raw['address_line2'] ?? '',
        $shipping_raw['city'] ?? '',
        $shipping_raw['county_state'] ?? '',
        $shipping_raw['zip_postal'] ?? '',
        $shipping_raw['country'] ?? '',
    ]));


    $item_count = 0;

    foreach ( $cart_data as $item ) { 



        if ($item_count > 0 && $item_count % 2 == 0) {
            $pdf->AddPage(); 
        }



        if (isset($item['Part Shape'])) {
            $part_shape = $item['Part Shape'];
            $value1 = $item['Roll Length (Metres)'];
            $pdf_link = $item['Upload .PDF Drawing'];
            $dxf_link = $item['Upload .DXF Drawing'];
            $value3 = $item['Width'];
            $value4 = $item['Length'];
            $value4b = $item['Radius'];
            $value5 = $item['Notes'];
            $value6 = $item['Shipping Weights'];
            $value7 = $item['Total Number of Parts'];
            $value8 = $item['Manufacturers COFC'];
            $value9 = $item['First Article Inspection Report'];
            $value10 = $item['Manufacturers COFC.'];
            $value11 = $item['First Article Inspection Report.'];
            $value12 = $item['Width (Inch)'];
            $value13 = $item['Length (Inch)'];
            $value14 = $item['Radius (Inch)'];
            $value15 = $item['shipment_date'];

            // echo "<pre>";
            // print_r($value15);
            // echo "</pre>";
            
        } else {
            // In case there's no Gravity Form data, set defaults
            $part_shape = 'N/A';
            $value1 = 'N/A';
            $pdf_link = 'N/A';
            $dxf_link = 'N/A';
            $value3 = 'N/A';
            $value4 = 'N/A';
            $value4b = 'N/A';
            $value5 = 'N/A';
            $value6 = 'N/A';
            $value7 = 'N/A';
            $value8 = 'N/A';
            $value9 = 'N/A';
            $value10 = 'N/A';
            $value11 = 'N/A';
            $value12 = 'N/A';
            $value13 = 'N/A';
            $value14 = 'N/A';
            $value15 = 'N/A';
        }

        if($value8 == "Add MCOFC"){
            $value8b = "MCOFC £10";
        } else {
            $value8b = "None";
        }

        if($value9 == "Add FAIR"){
            $value9b = "FAIR £95";
        } else {
            $value9b = "None";
        }

        if($value10 == "10"){
            $value10b = "MCOFC £10";
        }

        if($value11 == "95"){
            $value11b = "FAIR £95";
        }

        if($part_shape == "Sheets"){
            $part_shape_b = "Stock Sheets";
        } else {
            $part_shape_b = $part_shape;
        }

        $currency_rate = (float) $_GET['currency_rate'];

        $pdf->SetDrawColor(220, 220, 220); 
        $pdf->SetLineWidth(0.3); 
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); 
        $pdf->Ln(6);


        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->SetDrawColor(220, 220, 220); 
        $pdf->SetLineWidth(0.5); 
        $pdf->Rect($x, $y, 21, 21);


        
        if (!empty($item['thumb_image'])) {
            $pdf->Image($item['thumb_image'], $pdf->GetX(), $pdf->GetY(), 21, 21, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $pdf->Ln(24); // Move cursor down after image
        }




        $pdf->SetTextColor(241, 144, 0); // Set text color to orange (RGB: 255, 165, 0)
        $pdf->SetFont('helvetica', 'B', 13); // Set bold font
        $pdf->Write(0, esc_html($item['product']), '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetTextColor(0, 0, 0); // Reset text color back to black

        $pdf->SetFont('helvetica', 'B', 10); // Set bold font
        $pdf->Write(0, "Part Shape: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10); // Reset to normal font
        $pdf->Write(0, esc_html($part_shape_b), '', 0, 'L', true, 0, false, false, 0);

        if(!empty($pdf_link)){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Upload .PDF Drawing: ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($pdf_link), '', 0, 'L', true, 0, false, false, 0);
        }

        if(!empty($dxf_link)){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Upload .DXF Drawing: ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($dxf_link), '', 0, 'L', true, 0, false, false, 0);
        }

        if(!empty($value4b)){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Radius (MM): ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($value4b), '', 0, 'L', true, 0, false, false, 0);
        }


        if(!empty($value14)){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Radius (INCH): ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($value14), '', 0, 'L', true, 0, false, false, 0);
        }


        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Width (MM): ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html($value3), '', 0, 'L', true, 0, false, false, 0);

        if(!empty($value12)){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Width (INCH): ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($value12), '', 0, 'L', true, 0, false, false, 0);
        }

        if($part_shape != "Rolls"){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Length (MM): ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($value4), '', 0, 'L', true, 0, false, false, 0);
        }
        
        if(!empty($value13)){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Length (INCH): ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($value13), '', 0, 'L', true, 0, false, false, 0);
        }
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Total Number of Parts: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html($value7), '', 0, 'L', true, 0, false, false, 0);

        if($value1 != 0){
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Write(0, "Roll Length (Metres): ", '', 0, 'L', false, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, esc_html($value1), '', 0, 'L', true, 0, false, false, 0);
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Despatch Notes: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html($value5), '', 0, 'L', true, 0, false, false, 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Customer Shipping Weights: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html($value6), '', 0, 'L', true, 0, false, false, 0);

        // $pdf->SetFont('helvetica', 'B', 10);
        // $pdf->Write(0, "Total: ", '', 0, 'L', false, 0, false, false, 0);
        // $pdf->SetFont('helvetica', '', 10);
        // $pdf->Write(0, html_entity_decode(strip_tags(wc_price($item['Price'] * $currency_rate))), '', 0, 'L', true, 0, false, false, 0);


        $pdf->Ln(5);

        $item_count++;
    }

    if ( ! WC()->cart ) {
        wc_load_cart();
    }

    $cart = WC()->cart;



    $subtotal = $cart->get_subtotal(); 
    $discount = $cart->get_discount_total();  
    $shipping_total = $cart->get_shipping_total();  
    $tax_total = $cart->get_taxes_total();  
    $grand_total = $cart->get_total();  

    $value_shipping_2 = $shipment_details;
    //$value_shipping_3 = wp_strip_all_tags(wc_price($subtotal));
    $value_shipping_3 = (float) preg_replace('/[^0-9.]/', '', wp_strip_all_tags(wc_price($subtotal)));
    //$value_shipping_4 = wp_strip_all_tags(wc_price($discount));
    $value_shipping_4 = (float) preg_replace('/[^0-9.]/', '', wp_strip_all_tags(wc_price($discount)));
    //$value_shipping_5 = wp_strip_all_tags(wc_price($custom_shipping_total)); 
    $value_shipping_5 = (float) preg_replace('/[^0-9.]/', '', wp_strip_all_tags(wc_price($custom_shipping_total)));
    //$value_shipping_6 = wp_strip_all_tags(wc_price($tax_total));
    $value_shipping_6 = (float) preg_replace('/[^0-9.]/', '', wp_strip_all_tags(wc_price($tax_total)));
    //$value_shipping_7 = $grand_total; 
    //$value_shipping_7 = wp_strip_all_tags($grand_total);
    $value_shipping_7 = (float) preg_replace('/[^0-9.]/', '', wp_strip_all_tags($grand_total));

    // Get the user details
    if ( is_user_logged_in() ) {

        $user_id = get_current_user_id();
        $user = get_userdata( $user_id );
        $company_name = get_user_meta( $user_id, 'billing_company', true );
        $email = $user->user_email;
        $telephone = get_user_meta( $user_id, 'billing_phone', true );
        $address = array_filter([
            get_user_meta( $user_id, 'billing_address_1', true ),
            get_user_meta( $user_id, 'billing_address_2', true ),
            get_user_meta( $user_id, 'billing_city', true ),
            get_user_meta( $user_id, 'billing_state', true ),
            get_user_meta( $user_id, 'billing_postcode', true ),
            get_user_meta( $user_id, 'billing_country', true )
        ]);
        $formatted_address = implode(', ', $address);

    }
    // Get the user details



     if ($item_count > 0 && $item_count % 2 == 0) {
        $pdf->AddPage(); 
    }

    if ( is_user_logged_in() ) {
        $pdf->SetDrawColor(220, 220, 220); 
        $pdf->SetLineWidth(0.3); 
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); 

        $pdf->Ln(4);
    }
    if ( is_user_logged_in() ) {
        $pdf->SetFont('helvetica', '', 14);
        $pdf->Write(0, "Company Order Details: ", '', 0, 'L', true, 0, false, false, 0);
        $pdf->Ln(3);
    }
    // Display the details from the user profile
    if ( is_user_logged_in() ) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Company Name: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html( $company_name ), '', 0, 'L', true, 0, false, false, 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Address: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html( $formatted_address ), '', 0, 'L', true, 0, false, false, 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Email: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html( $email ), '', 0, 'L', true, 0, false, false, 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, "Telephone: ", '', 0, 'L', false, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, esc_html( $telephone ), '', 0, 'L', true, 0, false, false, 0);
    }
    // Display the details from the user profile

    $pdf->Ln(4);

    $pdf->SetDrawColor(220, 220, 220); 
    $pdf->SetLineWidth(0.3); 
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); 

    $pdf->Ln(4);

    $pdf->SetFont('helvetica', '', 14);
    $pdf->Write(0, "Shipping Details: ", '', 0, 'L', true, 0, false, false, 0);
    $pdf->Ln(3);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Write(0, "Shipping Address: ", '', 0, 'L', false, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Write(
    0,
    esc_html($shipping_address_full),
    '',
    0,
    'L',
    true,
    0,
    false,
    false,
    0
);


    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Write(0, "Shipments: ", '', 0, 'L', false, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 10);
    $shipment_custom_display = "£" .number_format(WC()->cart->get_shipping_total(), 2);
    $pdf->Write(0, esc_html($shipment_custom_display), '', 0, 'L', true, 0, false, false, 0);

    $pdf->Ln(3);

    $pdf->SetFont('helvetica', '', 14);
    $pdf->Write(0, "Basket totals: ", '', 0, 'L', true, 0, false, false, 0);


    $pdf->Ln(3);


    $table_html = '<table cellspacing="0" cellpadding="3" border="0" style="background: red; border: 1px solid #ccc;">
        <tbody>
            <tr>
                <th style="border: 1px solid #ccc;">Subtotal</th>
                <td style="border: 1px solid #ccc;">£'.esc_html(number_format($value_shipping_3, 2)).'</td>
            </tr>
            <tr>
                <th style="border: 1px solid #ccc;">Discount</th>
                <td style="border: 1px solid #ccc;">-£'.esc_html(number_format($value_shipping_4, 2)).'</td>
            </tr>
            <tr>
                <th style="border: 1px solid #ccc;">All COFCs & FAIRs</th>
                <td style="border: 1px solid #ccc;">£'.number_format(esc_html($_GET['cfc_fee']), 2).'</td>
            </tr>
            <tr>
                <th style="border: 1px solid #ccc;">Shipping Total</th>
                <td style="border: 1px solid #ccc;">£'.number_format(WC()->cart->get_shipping_total(), 2).'</td>
            </tr>
            <tr>
                <th style="border: 1px solid #ccc;">VAT</th>
                <td style="border: 1px solid #ccc;">£'.esc_html(number_format($value_shipping_6, 2)).'</td>
            </tr>
            <tr>
                <th style="border: 1px solid #ccc;">Total after discount</th>
                <td style="border: 1px solid #ccc;">£'.esc_html(number_format($value_shipping_7, 2)).'</td>
            </tr>
        </tbody>
    </table>';


    $pdf->SetFont('helvetica', '', 10);
    $pdf->writeHTML($table_html, true, false, false, false, '');
    



    

    // Output PDF
    $pdf->Output('cart-summary.pdf', 'D'); // 'D' forces download
    exit;
}

add_action('init', 'generate_cart_pdf');

// GENERATE THE PDF