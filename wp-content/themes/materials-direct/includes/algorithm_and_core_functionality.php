<?php
// Codys Exponential Decay Function 
function exponentialDecay($A, $k, $t) {
    return $A * exp(-$k * $t);
}
// End Codys Exponential Decay Function 


// AJAX handler for file uploads
add_action('wp_ajax_upload_drawing', 'upload_drawing');
add_action('wp_ajax_nopriv_upload_drawing', 'upload_drawing');
function upload_drawing() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    if (empty($_FILES['file'])) {
        wp_send_json_error(['message' => 'No file uploaded.']);
    }

    $file = $_FILES['file'];
    $type = sanitize_text_field($_POST['type']);

    $allowed = [];
    if ($type === 'pdf') {
        $allowed = ['pdf' => 'application/pdf'];
    } elseif ($type === 'dxf') {
        $allowed = ['dxf' => 'application/dxf'];
    } else {
        wp_send_json_error(['message' => 'Invalid file type.']);
    }

    $uploaded = wp_handle_upload($file, ['test_form' => false, 'mimes' => $allowed]);

    if ($uploaded && !isset($uploaded['error'])) {
        // Move to specific folder
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/pdf-and-dxf-uploads/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $filename = wp_unique_filename($target_dir, $file['name']);
        $target_path = $target_dir . $filename;

        if (rename($uploaded['file'], $target_path)) {
            $relative_path = '/pdf-and-dxf-uploads/' . $filename;
            wp_send_json_success(['path' => $relative_path]);
        } else {
            wp_send_json_error(['message' => 'Failed to move file.']);
        }
    } else {
        wp_send_json_error(['message' => $uploaded['error'] ?? 'Invalid file type or upload error.']);
    }
}
// AJAX handler for file uploads



// 1. PRICE CALCULATION FUNCTION
function calculate_product_price($product_id, $width, $length, $qty, $discount_rate = 0, $shape_type = 'custom-shape-drawing') {

    if (!is_numeric($product_id) || !is_numeric($width) || !is_numeric($length) || !is_numeric($qty) || !is_numeric($discount_rate)) {
        return new WP_Error('invalid_input', 'Invalid input data');
    }

    $width = floatval($width);
    $length = floatval($length);
    $qty = intval($qty);
    $discount_rate = floatval($discount_rate);

    if ($width <= 0 || $length <= 0 || $qty < 1) {
        return new WP_Error('invalid_input', 'Width, Length, and Quantity must be positive');
    }

    // Validate discount rate (ensure it's one of the allowed values)
    $valid_discount_rates = [0, 0.015, 0.02, 0.025, 0.03, 0.035, 0.04, 0.05];
    if (!in_array($discount_rate, $valid_discount_rates)) {
        return new WP_Error('invalid_discount', 'Invalid discount rate');
    }

    // Retrieve product and stock quantity for blanket discount
    $product = wc_get_product($product_id);
    if (!$product) {
        return new WP_Error('invalid_product', 'Invalid product ID');
    }
    $stock_quantity = $product->get_stock_quantity();

    $shipments = WC()->session->get('custom_shipments', []);
    $is_split_schedule = !empty($shipments); 

    if ($stock_quantity <= 0) {
        if ($is_split_schedule == true) {
            $discount_rate = max($discount_rate, 0);
        } else {
            $discount_rate = max($discount_rate, 0.05);
        }
    }

    $cost_per_part = floatval(get_field('buy_cost', $product_id));
    $cost_per_cm2 = floatval(get_field('cost_per_cm', $product_id));
    if(get_field('sheets_gpm', $product_id)){
        $sheets_gpm = floatval(get_field('sheets_gpm', $product_id));
    } else {
        $sheets_gpm = 0.48679; // changed from 0.5 to allow for borders in sheets
    }
    if(get_field('rolls_gpm', $product_id)){
        $rolls_gpm = floatval(get_field('rolls_gpm', $product_id));
    } else {
        $rolls_gpm = 0.48679;// changed from 0.5 to allow for borders in rolls
    }
    if(get_field('roll_length', $product_id)){
        $roll_length = floatval(get_field('roll_length', $product_id));
    } else {
        $roll_length = 0;
    }
    $roll_length_v =   $roll_length / 1000;
    $item_border = floatval(get_field('border_around', $product_id));


    if ($shape_type === 'custom-shape-drawing') {
        $globalPriceAdjust = 1.0;
    } 
    elseif ($shape_type === 'stock-sheets') {      
        $globalPriceAdjust = floatval(get_field('global_adjust_sheets', 'options') ?: 1.0);
    }
    elseif ($shape_type === 'rolls') {      
        $globalPriceAdjust = floatval(get_field('global_adjust_rolls', 'options') ?: 1.0);
    } 
    else {
        $globalPriceAdjust = floatval(get_field('global_adjust_square_rectangle', 'options') ?: 1.0); // Circle Radius + Square Rectangle both use the same option
    }


    if($shape_type ==="stock-sheets"){
        $borderSize = 0;
    } 
    elseif($shape_type ==="rolls"){
        $borderSize = 0;
    }
    else {
        $borderSize = $item_border * 2;
    }
    
    $setLength = $length / 10;
    $setWidth = $width / 10;
    $maxSetWidth = $setWidth + $borderSize;
    $maxSetLength = $setLength + $borderSize;

    if($shape_type ==="stock-sheets" || $shape_type ==="rolls"){
        $ppp = $maxSetLength * $maxSetWidth * $cost_per_part;
    } else {
        $ppp = $maxSetLength * $maxSetWidth * $cost_per_cm2;
    }
    
    

    $totalSqMm = $setWidth * $setLength * 100;


    // Codys algorith
    $A = 0.68;      // Maximum Cost Factor possible
    $k = 0.0018;    // Decay Rate
    $t = $totalSqMm; // mm2 of part
    $costFactorResult = exponentialDecay($A, $k, $t);
    // End Codys algorith


    $finalPppOnAva = $ppp + $costFactorResult;

    
    if($shape_type ==="stock-sheets"){
        $finalPppOnAva = $finalPppOnAva / $sheets_gpm;
    }
    if($shape_type === "rolls"){
		$finalPppOnAva = $finalPppOnAva / $rolls_gpm;
	}


    // Apply discount rate
    $discountAmount = $finalPppOnAva * $discount_rate;
    $finalPppOnAva = $finalPppOnAva - $discountAmount;
    $adjustedPrice = $finalPppOnAva * $globalPriceAdjust;


    /* AH rolls fix 9.12.2025 */
    if($shape_type ==="rolls"){
        $total_price = $adjustedPrice * $qty * $roll_length_v;
    } else {
        $total_price = $adjustedPrice * $qty;
    }
    //$total_price = $adjustedPrice * $qty;
    /* AH rolls fix 9.12.2025 */


    return round($total_price, 2);
}
// 1. END PRICE CALCULATION FUNCTION


// 1B. ENQUEUE THE RESET BUTTON JAVASCTPT

add_action('wp_enqueue_scripts', 'enqueue_reset_shipments_script');
function enqueue_reset_shipments_script() {
    // Only enqueue on single product pages
    if (is_product()) {
        wp_enqueue_script(
            'reset-shipments',
            get_theme_file_uri('/js/reset-shipments.js'),
            ['jquery'], 
            '1.0.0',
            true 
        );

        // Pass ajaxurl to the script
        wp_localize_script(
            'reset-shipments',
            'resetShipmentsVars',
            [
                'ajaxurl' => admin_url('admin-ajax.php')
            ]
        );
    }
}

// 1B. ENQUEUE THE RESET BUTTON JAVASCTPT


// 2. HTML FORM WITH SPINNER
add_action('woocommerce_before_add_to_cart_button', 'custom_price_input_fields_prefill');
function custom_price_input_fields_prefill() {
    global $product;

    // Get the ACF field value
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product->get_id()) : false;
    $product_id = $product->get_id();
    $shipping_address = WC()->session->get('custom_shipping_address', []);
    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id); // get the ACF Users Credits options
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false; // get the ACF Users Credits options
    $shipments = WC()->session->get('custom_shipments', []); 
    
    if (is_user_logged_in() && $allow_credit && !is_admin()) {
        $custom_qty = WC()->session->get('custom_qty', 0); 
        $total_parts = array_sum(array_column($shipments, 'parts')); 
        $remaining_parts = max(0, $custom_qty - $total_parts); 
    }
    
    // Prefill shipping address fields from session
    $street_address = !empty($shipping_address['street_address']) ? esc_attr($shipping_address['street_address']) : '';
    $address_line2 = !empty($shipping_address['address_line2']) ? esc_attr($shipping_address['address_line2']) : '';
    $city = !empty($shipping_address['city']) ? esc_attr($shipping_address['city']) : '';
    $county_state = !empty($shipping_address['county_state']) ? esc_attr($shipping_address['county_state']) : '';
    $zip_postal = !empty($shipping_address['zip_postal']) ? esc_attr($shipping_address['zip_postal']) : '';
    $country = !empty($shipping_address['country']) ? esc_attr($shipping_address['country']) : 'United Kingdom';

    echo '<input type="hidden" name="allow_credit" value="' . ($allow_credit ? '1' : '0') . '">';

    // If is_product_single is true, skip the custom form and rely on default WooCommerce behavior
    if ($is_product_single) {
        // Output shipping address form for single products
        echo '<div id="shipping-address-form">
            <h3 class="product-page__subheading">Item(s) shipping address<span class="gfield_required gfield_required_asterisk">*</span></h3>
            <p class="address-lookup__text" style="">Please ensure your shipping address is entered correctly here. Your order may be cancelled if an incorrect country has been entered. See our <a target="_blank" href="/terms-and-conditions/#shipping">terms and conditions</a> for more information.</p>
            <label class="custom-price-calc__label"><input class="product-page__calc-input" type="text" id="input_street_address" name="custom_street_address" placeholder="Street Address" value="' . $street_address . '" required></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input" type="text" id="input_address_line2" name="custom_address_line2" placeholder="Address Line 2" value="' . $address_line2 . '"></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_city" name="custom_city" placeholder="City" value="' . $city . '" required></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_county_state" name="custom_county_state" placeholder="County/State" value="' . $county_state . '" required></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_zip_postal" name="custom_zip_postal" placeholder="ZIP/Postal Code" value="' . $zip_postal . '" required></label>
            <label class="custom-price-calc__label">
                <select id="input_country" class="product-page__calc-input product-page__calc-input-small" name="custom_country" required>
                    <option value="United Kingdom"' . selected($country, 'United Kingdom', false) . '>United Kingdom</option>
                    <option value="France"' . selected($country, 'France', false) . '>France</option>
                    <option value="Germany"' . selected($country, 'Germany', false) . '>Germany</option>
                    <option value="Monaco"' . selected($country, 'Monaco', false) . '>Monaco</option>
                    <option value="Poland"' . selected($country, 'Poland', false) . '>Poland</option>
                    <option value="Spain"' . selected($country, 'Spain', false) . '>Spain</option>
                    <option value="United States"' . selected($country, 'United States', false) . '>United States</option>
                    
                </select>
            </label>
        </div>';
        echo '<input type="hidden" id="custom_price" name="custom_price" value="">';
        echo '<div id="custom_price_display"></div>';
        return;
    }

    // Check stock quantity for backorder status
    $stock_quantity = $product->get_stock_quantity();
    $is_full_backorder = $stock_quantity <= 0;
    $roll_length = floatval(get_field('roll_length', $product_id) ?: 0);
    $roll_length_v = $roll_length / 1000;

    // Get the currency switcher ID
    if (isset($_GET['set_currency']) && $_GET['set_currency'] === 'USD') {
        $currency_switcher_id = '1384';
    } elseif (isset($_GET['set_currency']) && $_GET['set_currency'] === 'EUR') {
        $currency_switcher_id = '1386';
    } else {
        $currency_switcher_id = '1385';
    }

    
    $set_currency = $_GET['set_currency'] ?? '';

    // Existing form for non-single products
    echo '<div class="product-page__stages-heading"><h3 class="product-page__stages-heading-content one">Tell us what to manufacture and give us your delivery date</h3></div>
    
        <div id="custom-price-calc" class="custom-price-calc">

        <!-- Tabs -->
        <ul class="product-page__tabs">

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Custom Shape<br>(Drawing)
        <input class="product-page__tabs-input" name="tabs_input" type="radio" value="custom-shape-drawing" checked="checked" id="custom_drawing" tabindex="1">
        </label></li>

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Circle<br>Radius
        <input class="product-page__tabs-input" name="tabs_input" type="radio" value="circle-radius" id="circle-radius" tabindex="0">
        </label></li>

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Square<br>Rectangle
        <input class="product-page__tabs-input" name="tabs_input" type="radio" value="square-rectangle" id="square_rectangle" tabindex="0">
        </label></li>

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Stock<br>Sheets
        <input class="product-page__tabs-input" name="tabs_input" type="radio" value="stock-sheets" id="stock_sheets" tabindex="0">
        </label></li>

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Rolls
        <input class="product-page__tabs-input" name="tabs_input" type="radio" value="rolls" id="rolls" tabindex="0">
        </label></li>

        </ul>
        <!-- Tabs -->

        <!-- Price Inputs -->
        <div class="product-page__grey-panel">

        <p class="product-page__square-rectangle-message"><i class="fa-solid fa-circle-info product-page__square-rectangle-message-icon"></i> You are asking us to manufacture a <span id="tabs_status_message">custom shape</span><span id="tabs_status_message_2">. The roll length is <span id="tabs_status_message_3">'.$roll_length_v.'</span> metres</span>. Enter your values below</p>

        <!-- File Upload Fields -->
        <div id="pdf_upload_container">
        <label id="pdf_upload_label" class="product-page__file-upload-label">Upload .PDF Drawing</label>
        <input class="product-page__file-upload-input" type="file" id="uploadPdf" name="upload-pdf" accept=".pdf">
        </div>

        <p id="pdf_upload_text" class="product-page__file-upload-pdf-message">Uploading a <strong>.pdf</strong> is required for a custom shape, we also use <strong>.pdf</strong> to verify dimensions and tolerences of your custom parts</p>
        
        <label id="dxf_upload_label" class="product-page__file-upload-label">Upload .DXF Drawing</label>
        <input class="product-page__file-upload-input" type="file" id="uploadDxf" name="upload-dxf" accept=".dxf">
        <p id="dxf_upload_text" class="product-page__file-upload-pdf-message">Uploading a <strong>.dxf</strong> is the preferred file to manufacture your parts</p>
        
        <input type="hidden" id="pdf_path" name="pdf_path" value="">
        <input type="hidden" id="dxf_path" name="dxf_path" value="">
        
        <input type="hidden" id="currency_rate" name="currency_rate" value="'.esc_attr($set_currency).'">
        <input type="hidden" id="currency_id" name="currency_id" value="'.$currency_switcher_id.'">
        <input type="hidden" id="currency_rate_sum" name="currency_rate_sum" value="'.get_currency_rate().'">
        <input type="hidden" id="currency_rate_symbol" name="currency_rate_symbol" value="'.get_currency_symbol().'">

        <div id="drawing_guide" class="product-page__drawing-guide">
        <a href="/wp-content/uploads/2025/10/DrawinGuide2025.pdf" target="_blank" class="product-page__drawing-guide-btn">Download here</a>
        <p class="product-page__drawing-guide-text">Download the drawing guide to help you with your pad and gasket design</p>
        </div>

        <div id="choose_inches" class="product-page__input-wrap unstyled centered" style="width: 100%;">
            <input type="checkbox" id="use_inches" class="styled-checkbox" name="conversion_factor" value="25.4">
            <label>Choose Inches</label>
        </div>
        <div id="choose_inches_radius" class="product-page__input-wrap unstyled centered" style="width: 100%;">
            <input type="checkbox" id="use_inches_radius" class="styled-checkbox" name="conversion_factor_radius" value="25.4">
            <label>Choose Inches (Radius)</label>
        </div>

        <label id="cont_radius_inches" class="product-page__input-wrap-radius">Radius (INCHES): <input class="product-page__input" type="number" id="input_radius_inches" name="custom_radius_inches" min="0.01" step="any"></label> 
        <label class="product-page__input-wrap-radius">Radius (MM): <input class="product-page__input" type="number" id="input_radius" name="custom_radius" min="0.01" step="any"></label>

        <label id="cont_width_inches" class="product-page__input-wrap">Width (INCHES): <input class="product-page__input" type="number" id="input_width_inches" name="custom_width_inches" min="0.01" step="any"></label>
        <label id="cont_length_inches" class="product-page__input-wrap">Length (INCHES): <input class="product-page__input" type="number" id="input_length_inches" name="custom_length_inches" min="0.01" step="any"></label>

        <label id="cont_width_mm" class="product-page__input-wrap">Width (MM): <input class="product-page__input" type="number" id="input_width" name="custom_width" min="0.01" step="any" required></label>
        <label id="cont_length_mm" class="product-page__input-wrap"><span class="product-page__rolls-label-text-1">Length (MM):</span> <input class="product-page__input" type="number" id="input_length" name="custom_length" min="0.01" step="any" required></label>
        <label style="position:relative;" class="product-page__input-wrap part-qty"><span class="product-page__rolls-label-text-2">Total number of parts:</span> <input class="product-page__input" type="number" id="input_qty" name="custom_qty" value="1" min="1" step="1" required></label>
        </div>';


        // MOCOF FAIR CONTENT

        echo '<div id="cofc_hide_show"><div class="product-page__optional-fees" style="display:none;">

            <h4 class="product-page__optional-fees-title">Do you require these addons with your product?</h4>

            <div class="product-page__checkbox-label unstyled">
                <p class="product-page__checkbox-title">Add Manufacturers COFC</p>
                <input type="checkbox" name="add_manufacturers_COFC" value="10" class="styled-checkbox" id="add_manufacturers_COFC">
                <label>
                <span class="product-page__checkbox-heading">Manufacturers COFC <span class="product-page__checkbox-price">£10</span>
                    <span class="cfc__tooltip" data-tooltip="A Manufacturers Certificate of Conformity (MCOFC) is a document that manufacturers issue to confirm that a product has been made to a specific standard and meets quality and regulatory requirements.">?</span>
                </span>
                </label>
            </div><br>

            <div id="fair_label" class="product-page__checkbox-label unstyled">
                <p class="product-page__checkbox-title">Add First Article Inspection Report</p>
                <input type="checkbox" name="add_fair" value="95" class="styled-checkbox" id="add_fair">
                <label>
                <span class="product-page__checkbox-heading">FAIR <span class="product-page__checkbox-price">£95</span>
                    <span class="cfc__tooltip" data-tooltip="A First Article Inspection Report (FAIR) or ISIR is the first item we make for the customer and measure to confirm all dimensions meet the drawing and tolerances.">?</span>
                </span>
                </label>
            </div><br>

            <div class="product-page__checkbox-label unstyled">
                <p class="product-page__checkbox-title">Add Materials Direct COFC?</p>
                <input type="checkbox" name="add_materials_direct_COFC" value="12.50" class="styled-checkbox" id="add_materials_direct_COFC">
                <label>
                <span class="product-page__checkbox-heading">Materials Direct COFC <span class="product-page__checkbox-price">£12.50</span>
                    <span class="cfc__tooltip" data-tooltip="A certificate from Materials Direct confirming that the part meets the criteria ordered (RoHS and REACH compliant).">?</span>
                </span>
                </label>
            </div>

        </div></div>';
        // END MOCOF FAIR CONTENT

        // Display login buttons if the user is not logged in
        echo do_shortcode('[md_shipping_options]');
        // Display login buttons if the user is not logged in


        if($is_full_backorder != 1){
            echo ' <label id="despatched_within" class="custom-price-calc__label product-page__label">Despatched Within <span class="product-page__label-small-text">Only applies to available stock</span> 
            <select class="custom-price-calc__input product-page__calc-input" id="input_discount_rate" name="custom_discount_rate">
                <option value="0" selected="selected">24Hrs (working day)</option>
                <option value="0.015">48Hrs (working days) (1.5% Discount)</option>
                <option value="0.02">5 Days (working days) (2% Discount)</option>
                <option value="0.025">7 Days (working days) (2.5% Discount)</option>
                <option value="0.03">12 Days (working days) (3% Discount)</option>
                <option value="0.035">14 Days (working days) (3.5% Discount)</option>
                <option value="0.04">30 Days (working days) (4% Discount)</option>
                <option value="0.05">35 Days (working days) (5% Discount)</option>
            </select>
        </label>';
        } else {
            echo ' <label id="despatched_within" class="custom-price-calc__label product-page__label">Despatched Within <span class="product-page__label-small-text">Please allow 35 Days for complete order fulfillment with a 5% discount applied to the total order</span> 
            <select class="custom-price-calc__input product-page__calc-input" id="input_discount_rate" name="custom_discount_rate">
                <option value="0.05">35 Days (working days) (5% Discount)</option>
            </select>
        </label>';
        }
    
            

        // display the is shipment button -  if the user has a credit account
        if (is_user_logged_in() && $allow_credit && !is_admin()) {
                echo '<div id="shipments_display" style="display: none; padding: 0.4rem 0.99rem; background: #efefef; border: 2px solid #ddd;"><a href="#" id="add_shipments" class="product-page__shipments-btn">Add Shipment(s)</a>
                    <a id="reset_button" class="product-page__generate-price product-page__reset" href="#">Reset</a>
                    <div id="order_info_box" class="product-page__order-info-box delivery-options-active">';
                        echo '<p class="product-page__order-info-message-1">Click on Add Shipment(s) to select a lead time</p>';
                        echo '<p class="product-page__order-info-message-2">Remaining parts to assign to a delivery date:</p>
                        <p class="product-page__order-info-message-3"><strong id="parts_remaining">' . esc_html($remaining_parts) . '</strong></p>
                    </div>
                    <div class="delivery-options-shipment__outer">
                        <table class="delivery-options-shipment">
                            <thead>
                                <tr>
                                    <th class="delivery-options-shipment__title">Despatch Date</th>
                                    <th class="delivery-options-shipment__title">Total number of parts</th>
                                    <th class="delivery-options-shipment__title">All COFCs & FAIRs</th>
                                    <th class="delivery-options-shipment__title"><span class="screen-reader-text">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>';
                if (empty($shipments)) {
                    echo '<tr class="delivery-options-shipment__display">
                            <td class="delivery-options-shipment__display-inner" colspan="4">There are no <span>shipments.</span></td>
                        </tr>';
                } else {
                    foreach ($shipments as $index => $shipment) {
                        echo '<tr>
                            <td class="delivery-options-shipment__display-results">' . esc_html($shipment['date']) . '</td>
                            <td class="delivery-options-shipment__display-results">' . esc_html($shipment['parts']) . '</td>
                            <td class="delivery-options-shipment__display-results">' . esc_html($fee_display) . '</td>
                            <td class="delivery-options-shipment__display-results"><a href="#" class="delete-shipment delivery-options-shipment__delete" data-index="' . esc_attr($index) . '"><i class="fa-solid fa-trash-can"></i></a></td>
                        </tr>';
                    }
                }
                echo '</tbody></table></div>
                <h4 class="product-page__discount-table-title">Select a lead time and enjoy a discount</h4>
                <table class="product-page__discount-table-2">
                <thead>
                <tr>
                <th class="product-page__discount-table-heading">Lead Time</th>
                <th class="product-page__discount-table-heading">Discount</th>
                </tr></thead>
                <tbody>
                <tr>
                <td class="product-page__discount-table-content-2">24Hrs (working day)</td>
                <td class="product-page__discount-table-content-2"><b>N/A</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">48Hrs - 4 days</td>
                <td class="product-page__discount-table-content-2"><b>1.5% discount</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">5 days - 6 days</td>
                <td class="product-page__discount-table-content-2"><b>2% discount</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">7 days - 11 days</td>
                <td class="product-page__discount-table-content-2"><b>2.5% discount</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">12 days - 13 days</td>
                <td class="product-page__discount-table-content-2"><b>3% discount</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">14 days - 29 days</td>
                <td class="product-page__discount-table-content-2"><b>3.5% discount</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">30 days - 34 days</td>
                <td class="product-page__discount-table-content-2"><b>4% discount</b></td>
                </tr>
                <tr>
                <td class="product-page__discount-table-content-2">35 days+</td>
                <td class="product-page__discount-table-content-2"><b>5% discount</b></td>
                </tr>
                </tbody>
                </table>
                <a class="product-page__discount-table-info" href="#" id="split_schedule_instructions_msg_3">What are scheduled shipments?</a>
                </div>';
        }
        // display the is shipment button - if the user has a credit account

        // Display the Calculate Price button

        echo '<button type="button" class="product-page__generate-price" id="generate_price">Get Price</button>';

        // Display the Calculate Price button


        echo '<div id="price-spinner-overlay" style="display:none;">
            <div class="spinner-wrapper">
                <img src="' . esc_url(get_theme_file_uri('/images/loading_md.gif')) . '" alt="Loading...">
            </div>
        </div>
        <div id="custom_price_display"></div>
        <input type="hidden" id="custom_price" name="custom_price" value="">
        <input type="hidden" id="cpp" name="cost_per_part" value="">';


        echo '<div class="product-page__stages-heading"><h3 class="product-page__stages-heading-content two">Enter your delivery address</h3></div>';

        if(WC()->session->get('custom_shipping_address')){
            echo '<div id="shipping-address-form" class="shipping-address-form__hide">';
        } else {
            echo '<div id="shipping-address-form">';
        }
        
        echo '<input style="margin-top: 1rem;" name="address_lookup" id="address_lookup" type="text" value="" class="product-page__calc-input address-lookup__search-field" tabindex="41" placeholder="Start by entering your address details here..." aria-invalid="false" role="combobox" aria-describedby="pca-country-button-help-text pca-help-text" aria-autocomplete="list" aria-expanded="false" autocomplete="off">
            <h3 class="product-page__subheading">Item(s) shipping address<span class="gfield_required gfield_required_asterisk">*</span></h3>
            <p class="address-lookup__text" style="">Please ensure your shipping address is entered correctly here. Your order may be cancelled if an incorrect country has been entered. See our <a target="_blank" href="/terms-and-conditions/#shipping">terms and conditions</a> for more information.</p>
            <label class="custom-price-calc__label"><input class="product-page__calc-input" type="text" id="input_street_address" name="custom_street_address" placeholder="Street Address" value="' . $street_address . '" required></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input" type="text" id="input_address_line2" name="custom_address_line2" placeholder="Address Line 2" value="' . $address_line2 . '"></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_city" name="custom_city" placeholder="City" value="' . $city . '" required></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_county_state" name="custom_county_state" placeholder="County/State" value="' . $county_state . '" required></label>
            <label class="custom-price-calc__label"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_zip_postal" name="custom_zip_postal" placeholder="ZIP/ Postal Code" value="' . $zip_postal . '" required></label>
            <label class="custom-price-calc__label">
                <select id="input_country" class="product-page__calc-input product-page__calc-input-small" name="custom_country" required>
                    <option value="United Kingdom"' . selected($country, 'United Kingdom', false) . '>United Kingdom</option>
                    <option value="France"' . selected($country, 'France', false) . '>France</option>
                    <option value="Germany"' . selected($country, 'Germany', false) . '>Germany</option>
                    <option value="Monaco"' . selected($country, 'Monaco', false) . '>Monaco</option>
                    <option value="Poland"' . selected($country, 'Poland', false) . '>Poland</option>
                    <option value="Spain"' . selected($country, 'Spain', false) . '>Spain</option>
                    <option value="United States"' . selected($country, 'United States', false) . '>United States</option>
                </select>
            </label>
        </div>';
   
        

        if(WC()->session->get('custom_shipping_address')){
            $address = WC()->session->get('custom_shipping_address');
            echo '<div class="shipping-address-form__saved">';
            echo '<h3 class="product-page__subheading">Item(s) shipping address?<span class="gfield_required gfield_required_asterisk">*</span></h3>';
            echo '<p class="shipping-address-form__saved-content">';
            echo $address['street_address'] . "<br>";
            echo $address['address_line2'] . "<br>";
            echo $address['city'] . "<br>";
            echo $address['county_state'] . "<br>";
            echo $address['zip_postal'] . "<br>";
            echo $address['country'];
            echo '</p>';
            echo '<a class="shipping-address-form__saved-edit">Edit Address</a>';
            echo '</div>';
        }
       

    echo '</div>';
}
// 2. HTML FORM WITH SPINNER





// 3. SECURE PRICE CALCULATION IN PHP
add_action('wp_ajax_calculate_secure_price', 'calculate_secure_price');
add_action('wp_ajax_nopriv_calculate_secure_price', 'calculate_secure_price');

function calculate_secure_price() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    $product_id = intval($_POST['product_id']);
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;

    $roll_length = function_exists('get_field') ? floatval(get_field('roll_length', $product_id)) : false;

    $roll_length_v = ($roll_length > 0) ? $roll_length / 1000 : 0;

    //$currency_value = (float) get_field('currency_rate_to_gbp', 1350);

    // get the currency value
    //$usd_id = 1386;
    //$currency_symbol      = get_field('currency_symbol', $usd_id);
    //$currency_rate_to_gbp = get_field('currency_rate_to_gbp', $usd_id);
    // get the currency value

    if ($is_product_single) {
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Invalid product ID.']);
            return;
        }
        $price = $product->get_price(); // Get default WooCommerce price
        wp_send_json_success([
            'price' => round($price, 2),
            'per_part' => round($price, 2), 
            'sheets_required' => 1, // Default to 1 sheet for single products
            'stock_quantity' => $product->get_stock_quantity(),
            'is_backorder' => false, // Single products don't use sheets, so no backorder
            'border_around' => 0.2 // Default for single products (not used, but included for consistency)
        ]);
        return;
    }

    $width = floatval($_POST['width']);
    $length = floatval($_POST['length']);
    $qty = intval($_POST['qty']);
    $discount_rate = floatval($_POST['discount_rate']);
    $shape_type = sanitize_text_field($_POST['shape_type'] ?? 'custom-shape-drawing');
    $currency_rate = floatval($_POST['currency_rate']);
    $currency_symbol = $_POST['currency_symbol'];

    if (!is_numeric($product_id) || $width <= 0 || $length <= 0 || $qty < 1 || !is_numeric($discount_rate)) {
        wp_send_json_error(['message' => 'Invalid input values.']);
    }

    // Save custom_qty to session
    WC()->session->set('custom_qty', $qty);

    // Clear any existing shipments to reset for new calculation
    WC()->session->set('custom_shipments', []);


    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(['message' => 'Invalid product ID.']);
        return;
    }
    $sheet_length_mm = $product->get_length() * 10;
    $sheet_width_mm = $product->get_width() * 10;
    $stock_quantity = $product->get_stock_quantity();
    $border_around = function_exists('get_field') ? floatval(get_field('border_around', $product_id) ?: 0.2) : 0.2;
    //$border_around = ($shape_type === 'stock-sheets' || $shape_type === 'rolls') ? 0 : (get_field('border_around', $product_id) ?: 0.2); // ah fix for partial backorder

    if ($sheet_length_mm <= 0 || $sheet_width_mm <= 0) {
        wp_send_json_error(['message' => 'Invalid sheet dimensions for this product.']);
        return;
    }

    if (!empty($_POST['street_address'])) {
        $shipping_address = [
            'street_address' => sanitize_text_field($_POST['street_address']),
            'address_line2' => sanitize_text_field($_POST['address_line2']),
            'city'          => sanitize_text_field($_POST['city']),
            'county_state'  => sanitize_text_field($_POST['county_state']),
            'zip_postal'    => sanitize_text_field($_POST['zip_postal']),
            'country'       => sanitize_text_field($_POST['country']),
        ];
        WC()->session->set('custom_shipping_address', $shipping_address);
    }

    $total_price = calculate_product_price($product_id, $width, $length, $qty, $discount_rate, $shape_type, $product_id);

    if (is_wp_error($total_price)) {
        wp_send_json_error(['message' => $total_price->get_error_message()]);
        return;
    }
    $per_part_price = $total_price / $qty;


    // Dynamically calculate sheets required
    $sheet_result = calculate_sheets_required(
        $sheet_width_mm,
        $sheet_length_mm,
        $width,
        $length,
        $qty,
        $product_id
    );

    $sheets_required = $sheet_result['sheets_required'];
    $sheets_required_rolls = $sheet_result['sheets_required'] * $roll_length_v;

    /* AH rolls fix 9.12.2025 */
    if($shape_type === "rolls"){
        $is_backorder = $sheets_required_rolls > $stock_quantity;
    } else {
        $is_backorder = $sheets_required > $stock_quantity;
    }
    /* AH rolls fix 9.12.2025 */

    //$currency_rate_value  = get_currency_rate();
    //$currency_symbol_value = get_currency_symbol();

    
    // $is_backorder = $sheets_required > $stock_quantity;
    //error_log("is_backorder (1): " . $is_backorder);
    //error_log("Currency Rate Value: " . $currency_rate_value);
    //error_log("Currency Symbol Value: " . $currency_symbol_value);
    //error_log("Currency Value: " . $currency_rate_to_gbp);
    error_log("Currency Rate xxx: " . $currency_rate);
    error_log("Currency Symbol: " . $currency_symbol);


    // SEND DATA TO algorith-core-functionality.js
    wp_send_json_success([
        'price' => round($total_price, 2),
        'per_part' => $per_part_price,
        'sheets_required' => $sheets_required,
        'stock_quantity' => $stock_quantity,
        'is_backorder' => $is_backorder,
        'sheet_width_mm' => $sheet_width_mm,
        'sheet_length_mm' => $sheet_length_mm,
        'entered_quantity' => $qty,
        'discount_rate' => $discount_rate,
        'border_around' => $border_around,
        'roll_length' => $roll_length_v,
        'currency_rate'  => (float) $currency_rate,
        'currency_symbol'=> $currency_symbol,
    ]);
}
// 3. SECURE PRICE CALCULATION IN PHP


// NEW. SCHEDULED PRICE CALCULATION IN PHP
add_action('wp_ajax_calculate_scheduled_price', 'calculate_scheduled_price_func');
add_action('wp_ajax_nopriv_calculate_scheduled_price', 'calculate_scheduled_price_func');

function calculate_scheduled_price_func() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    $product_id = intval($_POST['product_id']);
    $width = floatval($_POST['width']);
    $length = floatval($_POST['length']);
    $qty = intval($_POST['qty']);

    if (!is_numeric($product_id) || $width <= 0 || $length <= 0 || $qty < 1) {
        wp_send_json_error(['message' => 'Invalid input values.']);
    }

    $shipments = WC()->session->get('custom_shipments', []);
    $custom_qty = WC()->session->get('custom_qty', 0);

    if ($qty != $custom_qty) {
        wp_send_json_error(['message' => 'Quantity mismatch.']);
    }

    $total_parts = array_sum(array_column($shipments, 'parts'));
    if ($total_parts != $qty) {
        wp_send_json_error(['message' => 'Shipments do not cover all parts.']);
    }
    $shape_type = sanitize_text_field($_POST['shape_type'] ?? 'custom-shape-drawing');

    // Calculate base price without any discount
    $base_total_price = calculate_product_price($product_id, $width, $length, $qty, 0, $shape_type);
    if (is_wp_error($base_total_price)) {
        wp_send_json_error(['message' => $base_total_price->get_error_message()]);
        return;
    }
    $per_part_base = $base_total_price / $qty;

    // Calculate total scheduled price
    $today = date('Y-m-d');
    $total_scheduled_price = 0;
    foreach ($shipments as $shipment) {
        $despatch_date = $shipment['date']; // dd/mm/yyyy
        list($dd, $mm, $yyyy) = explode('/', $despatch_date);
        $despatch_ymd = "$yyyy-$mm-$dd";

        /* new calendar days discounts */ 
        $today_timestamp   = strtotime($today);
        $despatch_timestamp = strtotime($despatch_ymd);
        $calendar_days      = floor(($despatch_timestamp - $today_timestamp) / 86400);

        if ($calendar_days <= 1) {
            $disc = 0;        // 24Hrs / next day
        } elseif ($calendar_days <= 4) {
            $disc = 0.015;    // 2–4 days (48Hrs–4 days)
        } elseif ($calendar_days <= 6) {
            $disc = 0.02;     // 5–6 days
        } elseif ($calendar_days <= 11) {
            $disc = 0.025;    // 7–11 days
        } elseif ($calendar_days <= 13) {
            $disc = 0.03;     // 12–13 days
        } elseif ($calendar_days <= 29) {
            $disc = 0.035;    // 14–29 days
        } elseif ($calendar_days <= 34) {
            $disc = 0.04;     // 30–34 days
        } else {
            $disc = 0.05;     // 35+ days
        }

        $portion_parts = $shipment['parts'];
        $portion_price = $per_part_base * $portion_parts;
        $portion_discount = $portion_price * $disc;
        $portion_final = $portion_price - $portion_discount;

        //new cofc delivery options
        $lead_time_label = get_shipment_lead_time_label($despatch_ymd);
        $enhanced_shipments[] = [
            'date' => $despatch_date,
            'parts' => $portion_parts,
            'lead_time_label' => $lead_time_label
        ];

        $total_scheduled_price += $portion_final;
        //new cofc delivery options

    }

    $total_optional_fees = 0; //new cofc delivery options
    foreach ($shipments as $s) { //new cofc delivery options
        $total_optional_fees += $s['total_fee'] ?? 0; //new cofc delivery options
    } 


    /* new calendar days discounts */ 

    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(['message' => 'Invalid product ID.']);
        return;
    }
    $sheet_length_mm = $product->get_length() * 10;
    $sheet_width_mm = $product->get_width() * 10;
    $stock_quantity = $product->get_stock_quantity();
    $border_around = function_exists('get_field') ? floatval(get_field('border_around', $product_id) ?: 0.2) : 0.2;

    if (!empty($_POST['street_address'])) {
        $shipping_address = [
            'street_address' => sanitize_text_field($_POST['street_address']),
            'address_line2' => sanitize_text_field($_POST['address_line2']),
            'city'          => sanitize_text_field($_POST['city']),
            'county_state'  => sanitize_text_field($_POST['county_state']),
            'zip_postal'    => sanitize_text_field($_POST['zip_postal']),
            'country'       => sanitize_text_field($_POST['country']),
        ];
        WC()->session->set('custom_shipping_address', $shipping_address);
    }

    $sheet_result = calculate_sheets_required($sheet_width_mm, $sheet_length_mm, $width, $length, $qty, $product_id);
    $sheets_required = $sheet_result['sheets_required'];
    $is_backorder = false; // No backorder for scheduled deliveries
    error_log("Per Part Base 2: " . $per_part_base);

    wp_send_json_success([
        'price' => round($total_scheduled_price, 2),
        'per_part' => round($total_scheduled_price / $qty, 6), // Higher precision for per_part
        'per_part_base' => $per_part_base,
        'total_optional_fees' => $total_optional_fees, //new cofc delivery options
        'sheets_required' => $sheets_required,
        'stock_quantity' => $stock_quantity,
        'is_backorder' => $is_backorder,
        'sheet_width_mm' => $sheet_width_mm,
        'sheet_length_mm' => $sheet_length_mm,
        'entered_quantity' => $qty,
        'discount_rate' => 0,
        'border_around' => $border_around,
        'shipments' => $enhanced_shipments
    ]);
}
// NEW. SCHEDULED PRICE CALCULATION IN PHP


// 3b NEW AJAX HANDLER FOR SAVING SHIPPING ADDRESS FOR SINGLE PRODUCTS
add_action('wp_ajax_save_single_product_shipping', 'save_single_product_shipping');
add_action('wp_ajax_nopriv_save_single_product_shipping', 'save_single_product_shipping');

function save_single_product_shipping() {
    // Verify nonce for security
    check_ajax_referer('custom_price_nonce', 'nonce');

    $product_id = intval($_POST['product_id']);
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;

    if (!$is_product_single) {
        wp_send_json_error(['message' => 'This action is only for single products.']);
        return;
    }

    // Validate and save shipping address to session
    if (!empty($_POST['street_address']) && !empty($_POST['city']) && !empty($_POST['county_state']) && !empty($_POST['zip_postal']) && !empty($_POST['country'])) {
        $shipping_address = [
            'street_address' => sanitize_text_field($_POST['street_address']),
            'address_line2' => sanitize_text_field($_POST['address_line2']),
            'city'          => sanitize_text_field($_POST['city']),
            'county_state'  => sanitize_text_field($_POST['county_state']),
            'zip_postal'    => sanitize_text_field($_POST['zip_postal']),
            'country'       => sanitize_text_field($_POST['country']),
        ];
        WC()->session->set('custom_shipping_address', $shipping_address);
        wp_send_json_success(['message' => 'Shipping address saved successfully.']);
    } 
    // else {
    //     wp_send_json_error(['message' => '<span>Please fill in all required shipping address fields.</span>']);
    // }
}
// 3b NEW AJAX HANDLER FOR SAVING SHIPPING ADDRESS FOR SINGLE PRODUCTS




// 4. ENQUEUE JS WITH NONCE
add_action('wp_enqueue_scripts', function() {
    if (is_product()) {
        wp_enqueue_script('custom-price-calc', get_stylesheet_directory_uri() . '/js/algorithm-core-functionality.js', ['jquery', 'jquery-ui-datepicker'], null, true);
        wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_localize_script('custom-price-calc', 'ajax_params', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'product_id' => get_the_ID(),
            'nonce' => wp_create_nonce('custom_price_nonce'),
        ]);
    }
});
// 4. ENQUEUE JS WITH NONCE






// HELPER FUNCTION TO GROUP SHIPPING DATA BY DISPATCH DATE
function group_shipping_by_date($cart) {

   //error_log("Group Shipping By Date Triggered");

    $shipping_by_date = [];

    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['custom_inputs']['total_del_weight'], $cart_item['custom_inputs']['shipping_address']['country'])) {
            $total_del_weight = floatval($cart_item['custom_inputs']['total_del_weight']);
            $country = $cart_item['custom_inputs']['shipping_address']['country'];

            if (isset($cart_item['custom_inputs']['scheduled_shipments'])) {
                // Scheduled delivery case
                $shipments = $cart_item['custom_inputs']['scheduled_shipments'];
                $qty = $cart_item['custom_inputs']['qty'];
                foreach ($shipments as $shipment) {
                    $date = $shipment['date'];
                    $portion_parts = $shipment['parts'];
                    $ratio = $portion_parts / $qty;
                    $portion_weight = $total_del_weight * $ratio;
                    //error_log("Shipping BY Date Quantity: " . $shipping_by_date[$date]['quantity']);
                    if (isset($shipping_by_date[$date])) {
                        $shipping_by_date[$date]['quantity'] += 1;
                        $shipping_by_date[$date]['total_del_weight'] += $portion_weight;
                    } else {
                        $shipping_by_date[$date] = [
                            'quantity' => 1,
                            'total_del_weight' => $portion_weight,
                            'country' => $country,
                        ];
                    }
                }
                //error_log("Scheduled Group Shipping BY Date Triggered");
                //error_log(print_r($shipping_by_date[$date], true));
            } elseif (isset($cart_item['custom_inputs']['shipments'])) {
                $shipments = $cart_item['custom_inputs']['shipments'];
                if (is_array($shipments)) {
                    // Partial backorder: Split into dispatch and backorder portions
                    if (isset($cart_item['custom_inputs']['backorder_data'])) {
                        $bd = $cart_item['custom_inputs']['backorder_data'];
                        $dispatch_parts = $bd['able_to_dispatch'];
                        $back_parts = $bd['parts_backorder'];
                        $total_parts = $dispatch_parts + $back_parts;
                        if ($total_parts <= 0) continue; // Invalid, skip

                        $dispatch_ratio = $dispatch_parts / $total_parts;
                        $back_ratio = $back_parts / $total_parts;
                        $ratios = [$dispatch_ratio, $back_ratio];

                        for ($i = 0; $i < 2; $i++) {
                            $date = $shipments[$i];
                            $portion_weight = $total_del_weight * $ratios[$i];

                            if (isset($shipping_by_date[$date])) {
                                $shipping_by_date[$date]['quantity'] += 1; // Count as separate shipment portion
                                $shipping_by_date[$date]['total_del_weight'] += $portion_weight;
                            } else {
                                $shipping_by_date[$date] = [
                                    'quantity' => 1,
                                    'total_del_weight' => $portion_weight,
                                    'country' => $country,
                                ];
                            }
                        }
                    }
                } else {
                    // Standard single date
                    $date = $shipments;
                    if (isset($shipping_by_date[$date])) {
                        $shipping_by_date[$date]['quantity'] += 1;
                        $shipping_by_date[$date]['total_del_weight'] += $total_del_weight;
                    } else {
                        $shipping_by_date[$date] = [
                            'quantity' => 1,
                            'total_del_weight' => $total_del_weight,
                            'country' => $country,
                        ];
                    }
                }
            }
        }
    }

    // Calculate final shipping costs after all aggregations
    foreach ($shipping_by_date as $date => &$data) {
        $data['final_shipping'] = calculate_shipping_cost($data['total_del_weight'], $data['country']);
    }

    return $shipping_by_date;
    //error_log("Shipping By Date: " . $shipping_by_date);
    //error_log("Final Shiiping By Date Triggered");
}
// HELPER FUNCTION TO GROUP SHIPPING DATA BY DISPATCH DATE






// HELPER FUNCTION TO CALCULATE SHIPPING COST BASED ON TOTAL DELIVERY WEIGHT
function calculate_shipping_cost($total_del_weight, $country) {
    // Define cost tiers for each country or group of countries
    $cost_tiers = [
        'United Kingdom' => [
            [0, 10, 13.29],
            [10, 15, 18.76],
            [15, 20, 24.23],
            [20, 25, 29.70],
            [25, 30, 35.71],
            [30, 35, 41.20],
            [35, 40, 46.69],
            [40, 45, 52.19],
            [45, 50, 57.68],
            [50, 60, 68.66],
            [60, 70, 79.63],
            [70, 80, 90.63],
            [80, 90, 101.60],
            [90, 100, 112.58],
            [100, 110, 123.57],
            [110, 120, 134.55],
            [120, 130, 145.53],
            [130, 140, 156.52],
            [140, 150, 167.50],
            [150, 160, 178.47],
            [160, 170, 189.47],
            [170, 180, 200.44],
            [180, 190, 211.42],
            [190, 200, 222.41],
            [200, 210, 233.39],
            [210, 220, 244.37],
            [220, 230, 255.36],
            [230, 240, 266.34],
            [240, 250, 277.31],
            [250, 260, 288.29],
            [260, 270, 299.28],
            [270, 280, 310.26],
            [280, 290, 321.23],
            [290, 299, 331.13],
            [299, PHP_INT_MAX, 341.13],
        ],
        'Europe_1' => [ // Shared tiers for France, Germany, Monaco
            [0, 1, 54.18],
            [1, 1.5, 61.86],
            [1.5, 2, 65.86],
            [2, 2.5, 69.54],
            [2.5, 3, 73.34],
            [3, 3.5, 77.46],
            [3.5, 4, 81.22],
            [4, 4.5, 85.20],
            [4.5, 5, 88.98],
            [5, 10, 92.94],
            [10, 26, 126.48],
            [26, 30, 202.10],
            [30, 50, 221.50],
            [50, 70, 322.42],
            [70, 100, 423.40],
            [100, PHP_INT_MAX, 603.40],
        ],
        'Europe_2' => [ // Shared tiers for Spain
            [0, 1, 58.56],
            [1, 1.5, 67.38],
            [1.5, 2, 72.70],
            [2, 2.5, 78.36],
            [2.5, 3, 83.92],
            [3, 3.5, 89.12],
            [3.5, 4, 94.50],
            [4, 4.5, 99.76],
            [4.5, 5, 105],
            [5, 10, 110.40],
            [10, 26, 160.26],
            [26, 30, 280.46],
            [30, 50, 308.68],
            [50, 70, 486.86],
            [70, 100, 665.10],
            [100, PHP_INT_MAX, 950.70],
        ],
        'Europe_3' => [ // Shared tiers for Poland
            [0, 1, 65.48],
            [1, 1.5, 73.24],
            [1.5, 2, 80.54],
            [2, 2.5, 86.92],
            [2.5, 3, 93.48],
            [3, 3.5, 97],
            [3.5, 4, 102.76],
            [4, 4.5, 108.32],
            [4.5, 5, 114.08],
            [5, 10, 119.72],
            [10, 26, 162.26],
            [26, 30, 286.88],
            [30, 50, 316.32],
            [50, 70, 494.98],
            [70, 100, 673.54],
            [100, PHP_INT_MAX, 962.14],
        ],
        'America_1' => [ // Shared tiers for USA
            [0, 1, 84.40],
            [1, 1.5, 89.12],
            [1.5, 2, 101.40],
            [2, 2.5, 106.70],
            [2.5, 3, 111.92],
            [3, 3.5, 117.24],
            [3.5, 4, 122.08],
            [4, 4.5, 126.96],
            [4.5, 5, 131.92],
            [5, 10, 136.76],
            [10, 26, 173.44],
            [26, 30, 301.82],
            [30, 50, 328.26],
            [50, 70, 496.86],
            [70, 100, 664.70],
            [100, PHP_INT_MAX, 935.90],
        ],
    ];

    // Map countries to cost tier groups
    $country_groups = [
        'United Kingdom' => 'United Kingdom',
        'France' => 'Europe_1',
        'Germany' => 'Europe_1',
        'Monaco' => 'Europe_1',
        'Poland' => 'Europe_3',
        'Spain' => 'Europe_2',
        'United States' => 'America_1',
    ];

    // Get the appropriate cost tier based on country
    switch ($country) {
        case 'United Kingdom':
        case 'France':
        case 'Germany':
        case 'Monaco':
        case 'Poland':
        case 'Spain':
        case 'United States':
            $tiers = $cost_tiers[$country_groups[$country]];
            break;
        default:
            return 0;
    }

    // Find the cost based on weight
    foreach ($tiers as $tier) {
        list($min_weight, $max_weight, $cost) = $tier;
        if ($total_del_weight >= $min_weight && $total_del_weight < $max_weight) {
            return $cost;
        }
    }

    return 0;
}
// HELPER FUNCTION TO CALCULATE SHIPPING COST BASED ON TOTAL DELIVERY WEIGHT




// 5. CREATE CART ITEM DATA AND STORE AS SESSION

add_filter('woocommerce_add_cart_item_data', 'add_custom_price_cart_item_data_secure', 10, 2);
function add_custom_price_cart_item_data_secure($cart_item_data, $product_id) {
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;
    $roll_length = floatval(get_field('roll_length', $product_id));
    $roll_length_v = ($roll_length > 0) ? $roll_length / 1000 : 0;
    $cart_item_data['custom_inputs'] = [];

    if (
        isset($_POST['custom_street_address']) &&
        isset($_POST['custom_city']) &&
        isset($_POST['custom_county_state']) &&
        isset($_POST['custom_zip_postal']) &&
        isset($_POST['custom_country'])
    ) {
        $cart_item_data['custom_inputs']['shipping_address'] = [
            'street_address' => sanitize_text_field($_POST['custom_street_address']),
            'address_line2' => sanitize_text_field($_POST['custom_address_line2']),
            'city' => sanitize_text_field($_POST['custom_city']),
            'county_state' => sanitize_text_field($_POST['custom_county_state']),
            'zip_postal' => sanitize_text_field($_POST['custom_zip_postal']),
            'country' => sanitize_text_field($_POST['custom_country']),
        ];
        WC()->session->set('custom_shipping_address', $cart_item_data['custom_inputs']['shipping_address']);
    }

    // Add file paths to cart data
    if (isset($_POST['pdf_path']) && !empty($_POST['pdf_path'])) {
        $cart_item_data['custom_inputs']['pdf_path'] = sanitize_text_field($_POST['pdf_path']);
    }
    if (isset($_POST['dxf_path']) && !empty($_POST['dxf_path'])) {
        $cart_item_data['custom_inputs']['dxf_path'] = sanitize_text_field($_POST['dxf_path']);
    }

    if (isset($_POST['cost_per_part']) && !empty($_POST['cost_per_part'])) {
        $cart_item_data['custom_inputs']['cost_per_part'] = sanitize_text_field($_POST['cost_per_part']);
    }
    error_log("Cost Per Part (Sent To Cart)" . $cart_item_data['custom_inputs']['cost_per_part']);

    $product = wc_get_product($product_id);
    if (!$product) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("add_custom_price_cart_item_data_secure: Invalid product ID {$product_id}");
        }
        return $cart_item_data;
    }

    $country = isset($cart_item_data['custom_inputs']['shipping_address']['country']) ? $cart_item_data['custom_inputs']['shipping_address']['country'] : 'United Kingdom';

    if ($is_product_single) {
        $product_weight = $product->get_weight();
        if (!is_numeric($product_weight) || $product_weight <= 0) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("add_custom_price_cart_item_data_secure: Invalid weight for single product ID {$product_id}");
            }
            $total_del_weight = 0;
        } else {
            $total_del_weight = floatval($product_weight);
        }

        $final_shipping = calculate_shipping_cost($total_del_weight, $country);
        $despatch_notes = 'Single product to be despatched in 24Hrs (working day)';
        $shipments = date('d/m/Y', strtotime('+1 days'));

        $cart_item_data['custom_inputs'] = array_merge($cart_item_data['custom_inputs'], [
            'price' => floatval($product->get_price()),
            'qty' => 1,
            'sheets_required' => 1,
            'despatch_notes' => $despatch_notes,
            'shipments' => $shipments,
            'total_del_weight' => $total_del_weight,
            'final_shipping' => $final_shipping,
        ]);

        return $cart_item_data;
    }

    if (
        !isset($_POST['custom_width']) ||
        !isset($_POST['custom_length']) ||
        !isset($_POST['custom_qty']) ||
        !isset($_POST['custom_price']) ||
        !isset($_POST['custom_discount_rate'])
    ) {
        return $cart_item_data;
    }

    $shape_type = sanitize_text_field($_POST['tabs_input'] ?? $_POST['shape_type'] ?? 'custom-shape-drawing');
    if (!in_array($shape_type, ['custom-shape-drawing', 'square-rectangle', 'circle-radius', 'stock-sheets', 'rolls'])) {
        $shape_type = 'custom-shape-drawing';
    }

    $sheet_length_mm = $product->get_length() * 10; 
    $sheet_width_mm = $product->get_width() * 10;
    $part_width_mm = floatval($_POST['custom_width']);
    $part_width_inches = floatval($_POST['custom_width_inches']);
    $part_length_mm = floatval($_POST['custom_length']);
    $part_length_inches = floatval($_POST['custom_length_inches']);
    $custom_radius_inches = floatval($_POST['custom_radius_inches']);
    

    $quantity = intval($_POST['custom_qty']);
    $product_weight = $product->get_weight();
    $discount_rate = isset($_POST['custom_discount_rate']) ? floatval($_POST['custom_discount_rate']) : 0;

    $discount_labels = [
        '0' => '24Hrs (working day)',
        '0.015' => '48Hrs (working days) (1.5% Discount)',
        '0.02' => '5 Days (working days) (2% Discount)',
        '0.025' => '7 Days (working days) (2.5% Discount)',
        '0.03' => '12 Days (working days) (3% Discount)',
        '0.035' => '14 Days (working days) (3.5% Discount)',
        '0.04' => '30 Days (working days) (4% Discount)',
        '0.05' => '35 Days (working days) (5% Discount)',
    ];

    $delivery_time = isset($discount_labels[(string)$discount_rate]) ? $discount_labels[(string)$discount_rate] : 'Unknown';

    if($delivery_time === "24Hrs (working day)"){
        $shipments = date('d/m/Y', strtotime('+1 days'));
    } elseif($delivery_time === "48Hrs (working days) (1.5% Discount)"){
        $shipments = date('d/m/Y', strtotime('+2 days'));
    } elseif($delivery_time === "5 Days (working days) (2% Discount)"){
        $shipments = date('d/m/Y', strtotime('+5 days'));
    } elseif($delivery_time === "7 Days (working days) (2.5% Discount)"){
        $shipments = date('d/m/Y', strtotime('+7 days'));
    } elseif($delivery_time === "12 Days (working days) (3% Discount)"){
        $shipments = date('d/m/Y', strtotime('+12 days'));
    } elseif($delivery_time === "14 Days (working days) (3.5% Discount)"){
        $shipments = date('d/m/Y', strtotime('+14 days'));
    } elseif($delivery_time === "30 Days (working days) (4% Discount)"){
        $shipments = date('d/m/Y', strtotime('+30 days'));
    } else {
        $shipments = date('d/m/Y', strtotime('+35 days'));
    }

    if ($part_width_mm <= 0 || $part_length_mm <= 0 || $quantity < 1 || !is_numeric($product_weight)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("add_custom_price_cart_item_data_secure: Invalid inputs - Width: $part_width_mm, Length: $part_length_mm, Qty: $quantity, Weight: $product_weight");
        }
        return $cart_item_data;
    }

    $sheet_result = calculate_sheets_required(
        $sheet_width_mm,
        $sheet_length_mm,
        $part_width_mm,
        $part_length_mm,
        $quantity,
        $product_id
    );

    $totalSqMm = $part_length_mm * $part_width_mm;
    $totalSqCm = $totalSqMm / 100;
    $total_del_weight = $totalSqCm * floatval($product_weight) * $quantity * 1.03;
    $final_shipping = calculate_shipping_cost($total_del_weight, $country);

    if($shape_type === "rolls"){
        $stock_quantity = $product->get_stock_quantity() / $roll_length_v;
    } else {
        $stock_quantity = $product->get_stock_quantity(); 
    }

    $sheets_required = $sheet_result['sheets_required'];
    $is_backorder_raw = $sheets_required > $stock_quantity;

    // Force clear scheduled session if no credit (prevent UI/session artifacts for non-credit users)
    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id);
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false;
    if (!$allow_credit) {
        WC()->session->set('custom_shipments', []);
        WC()->session->set('custom_qty', null);
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("add_custom_price_cart_item_data_secure: Forced clear custom_shipments for non-credit user on product $product_id");
        }
    }

    $shipments_session = WC()->session->get('custom_shipments', []);
    $is_scheduled = $allow_credit && !empty($shipments_session) && array_sum(array_column($shipments_session, 'parts')) == $quantity;

    $despatch_string = ''; // thurdsay retrieve discount rate
    $despatch_notes = '';
    $backorder_data = [];
    $is_backorder = false;
    $server_total_price = 0;

    // Calculate base price without discount for potential splits
    $base_total_price_no_disc = calculate_product_price($product_id, $part_width_mm, $part_length_mm, $quantity, 0, $shape_type);
    if (is_wp_error($base_total_price_no_disc)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("add_custom_price_cart_item_data_secure: Error calculating base price for product ID $product_id: " . $base_total_price_no_disc->get_error_message());
        }
        return $cart_item_data;
    }

    if ($is_scheduled) {
        $cart_item_data['custom_inputs']['is_scheduled'] = $is_scheduled; // wednesday amend
        $cart_item_data['custom_inputs']['roll_length'] = $roll_length_v; // wednesday amend
        $despatch_dates = '';
        $despatch_string = ''; 
        $despatch_notes = '';
        $scheduled_shipments = $shipments_session;
        $enhanced_shipments = [];

        foreach ($scheduled_shipments as $s) {
            $despatch_date = $s['date']; // dd/mm/yyyy
            list($dd, $mm, $yyyy) = explode('/', $despatch_date);
            $despatch_ymd = "$yyyy-$mm-$dd";

            $lead_time_label = get_shipment_lead_time_label($despatch_ymd);

            error_log("Lead Time Label: " . $lead_time_label);

            $discount_label = get_shipment_lead_time_discount($despatch_ymd); // thurdsay retrieve discount rate

            error_log("Discount Label: " . $discount_label); // thurdsay retrieve discount rate

            // new code for new teusday cofc delivery options
            $feeAppendix = '';
            if (!empty($s['fees'])) {
                $feeLabels = [
                    'add_manufacturers_COFC_ss' => 'Manufacturers COFC',
                    'add_fair_ss' => 'First Article Inspection Report',
                    'add_materials_direct_COFC_ss' => 'Materials Direct COFC'
                ];
                $feeParts = [];
                foreach ($s['fees'] as $feeKey => $feeValue) {
                    if ($feeValue > 0) {
                        $label = isset($feeLabels[$feeKey]) ? $feeLabels[$feeKey] : $feeKey;
                        $feeParts[] = $label . ' £' . number_format($feeValue, 2);
                    }
                }
                if (!empty($feeParts)) {
                    $feeAppendix = ' - ' . implode(' - ', $feeParts);
                }
            }
            // new code for new teusday cofc delivery options

            $formatted_line = number_format($s['parts']) . " parts to be despatched on {$despatch_date} {$lead_time_label}" . $feeAppendix;

            $formatted_line_discount = number_format($s['parts']) . ", " . $despatch_date .", ". $discount_label .", ". implode(', ', $feeParts) . ", ";
            //$formatted_line_date = $despatch_date . ", "; 

            error_log("Formatted Line Discount: " . $formatted_line_discount);
            

            //$formatted_line = number_format($s['parts']) . " parts to be despatched on {$despatch_date} {$lead_time_label}";
            $despatch_notes .= $formatted_line . "\n";
            $despatch_string .= $formatted_line_discount; // thurdsay retrieve discount rate
            $ah_despatch_date .= $formatted_line_date;
            //error_log("AH Despatch Dates: " . $ah_despatch_date);
            $enhanced_shipments[] = [
                'date'            => $despatch_date,
                'parts'           => $s['parts'],
                'lead_time_label' => $lead_time_label
            ];

        }

        $per_part_base = $base_total_price_no_disc / $quantity;
        $today = date('Y-m-d');
        $server_total = 0;
        foreach ($scheduled_shipments as $index => $shipment) { 
            list($dd, $mm, $yyyy) = explode('/', $shipment['date']);
            $despatch_ymd = "$yyyy-$mm-$dd";

            /* new calendar days discounts */ 
            $today_timestamp = strtotime($today);
            $despatch_timestamp = strtotime($despatch_ymd);
            $calendar_days = ($despatch_timestamp - $today_timestamp) / (60 * 60 * 24);

            if ($calendar_days <= 1) $disc = 0; // 24Hr (Next day)
            elseif ($calendar_days <= 4) $disc = 0.015; // 2–4 days (48Hr)
            elseif ($calendar_days <= 6) $disc = 0.02; // 5–6 days (5 Days)
            elseif ($calendar_days <= 12) $disc = 0.025; // 7–12 days (7 Days)
            elseif ($calendar_days <= 13) $disc = 0.03; // 13 days (12 Days)
            elseif ($calendar_days <= 29) $disc = 0.035; // 14–29 days (14–15 Days)
            elseif ($calendar_days <= 35) $disc = 0.04; // 30–35 days (30 Days)
            else $disc = 0.05; // 36+ days (35 Days)
            
            /* new calendar days discounts */ 

            $portion_parts = $shipment['parts'];
            $portion_price = $per_part_base * $portion_parts;
            $portion_discount = $portion_price * $disc;
            $portion_final = $portion_price - $portion_discount;
            $server_total += $portion_final;

        }
        $server_total_price = $server_total;

        //new cofc delivery options
        $total_optional_fees = 0;

        foreach ($scheduled_shipments as $shipment) {
            $total_optional_fees += $shipment['total_fee'] ?? 0;
        }

        // Store in cart data (for display and fee hook)
        $cart_item_data['custom_inputs']['total_optional_fees'] = $total_optional_fees; 
        $cart_item_data['custom_inputs']['optional_fees_per_shipment'] = []; 
        foreach ($scheduled_shipments as $s) {
            $cart_item_data['custom_inputs']['optional_fees_per_shipment'][] = [
                'date' => $s['date'],
                'fee' => $s['total_fee'] ?? 0,
                'fees' => $s['fees'] ?? [] // new teusday cofc delivery options
            ];
        }
        //new cofc delivery options

        $server_price_per_sheet = $sheets_required > 0 ? $server_total_price / $sheets_required : $server_total_price;

        $client_price = floatval($_POST['custom_price']);
        if (abs($server_price_per_sheet - $client_price) > 0.01) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("add_custom_price_cart_item_data_secure: Scheduled price mismatch for product ID $product_id. Client price per sheet: $client_price, Server price per sheet: $server_price_per_sheet, Total price: $server_total_price, Shape Type: $shape_type");
            }
        }

        $cart_item_data['custom_inputs']['price'] = $server_price_per_sheet;
        $cart_item_data['custom_inputs']['total_price'] = $server_total_price;

        $is_backorder = false;
        $backorder_data = [];
        $shipments = ''; 
        $despatch_notes = $despatch_notes;
    } else {
        // Non-scheduled: Unified handling for backorder and instock
        $border = floatval(get_field('border_around', $product_id) * 10);
        $v1 = $part_width_mm + (2 * $border);
        $v2 = $part_length_mm + (2 * $border);
        $parts_per_row = floor($sheet_width_mm / $v1);
        $parts_per_column = floor($sheet_length_mm / $v2);
        $calculated_parts_per_sheet = $parts_per_row * $parts_per_column;

        $server_total_price = calculate_product_price($product_id, $part_width_mm, $part_length_mm, $quantity, $discount_rate, $shape_type);
        if (is_wp_error($server_total_price)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("add_custom_price_cart_item_data_secure: Error calculating price for product ID $product_id: " . $server_total_price->get_error_message());
            }
            return $cart_item_data;
        }

        if ($stock_quantity <= 0) {
            // Full backorder
            $discount_rate = 0.05;
            $server_total_price = calculate_product_price($product_id, $part_width_mm, $part_length_mm, $quantity, $discount_rate, $shape_type);
            $despatch_notes = sprintf('%d parts to be despatched in 35 Days (working days) (5%% Discount)', $quantity);
            $shipments = date('d/m/Y', strtotime('+35 days'));
            $is_backorder = true;
        } elseif ($is_backorder_raw) {
            // Partial backorder - Recalc with split discounts
            $sheets_backorder = $sheets_required - $stock_quantity;
            $parts_from_stock = $stock_quantity * $calculated_parts_per_sheet;
            $able_to_dispatch = min($parts_from_stock, $quantity);
            $parts_backorder = $quantity - $able_to_dispatch;

            // Split price: normal discount on dispatch, 5% on backorder
            $per_part_base = $base_total_price_no_disc / $quantity;
            $dispatch_price = $able_to_dispatch * $per_part_base * (1 - $discount_rate);
            $backorder_price = $parts_backorder * $per_part_base * 0.95; // 5% discount
            $server_total_price = $dispatch_price + $backorder_price;

            $despatch_notes = sprintf(
                '%d parts to be despatched in %s, %d parts to be despatched in 35 days (5%% discount)',
                $able_to_dispatch,
                $delivery_time,
                $parts_backorder
            );
            $shipments_dispatch = $shipments;
            $shipments_backorder = date('d/m/Y', strtotime('+35 days'));
            $shipments = [$shipments_dispatch, $shipments_backorder];

            $backorder_data = [
                'backorder_total' => $server_total_price,
                'parts_backorder' => $parts_backorder,
                'able_to_dispatch' => $able_to_dispatch,
                'parts_per_sheet' => $calculated_parts_per_sheet,
            ];
            $is_backorder = true;

            // Validate client-sent backorder data if present
            if (isset($_POST['custom_parts_per_sheet'])) {
                $client_parts_per_sheet = intval($_POST['custom_parts_per_sheet']);
                $client_parts_backorder = intval($_POST['custom_parts_backorder']);
                $client_able_to_dispatch = intval($_POST['custom_able_to_dispatch']);
                if (
                    $calculated_parts_per_sheet != $client_parts_per_sheet ||
                    $parts_backorder != $client_parts_backorder ||
                    $able_to_dispatch != $client_able_to_dispatch
                ) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("add_custom_price_cart_item_data_secure: Backorder data validation failed. Client parts_per_sheet: {$client_parts_per_sheet}, Server: $calculated_parts_per_sheet, etc.");
                    }
                    $is_backorder = false;
                    $backorder_data = [];
                    $despatch_notes = sprintf('%d parts to be despatched in %s', $quantity, $delivery_time); // Fallback
                }
            }
        } else {
            // Instock
            if ($stock_quantity <= 0) {
                $delivery_time = '35 Days (working days) (5% Discount)';
                $shipments = date('d/m/Y', strtotime('+35 days'));
            }
            $despatch_notes = sprintf(
                '%d parts to be despatched in %s',
                $quantity,
                $delivery_time
            );
        }

        // Price per sheet for cart
        $server_price_per_sheet = $sheets_required > 0 ? $server_total_price / $sheets_required : $server_total_price;
        $client_price = floatval($_POST['custom_price']);
        if (abs($server_price_per_sheet - $client_price) > 0.01) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("add_custom_price_cart_item_data_secure: Price mismatch for product ID $product_id. Client price per sheet: $client_price, Server price per sheet: $server_price_per_sheet, Total price: $server_total_price, Shape Type: $shape_type");
            }
        }
        $cart_item_data['custom_inputs']['price'] = $server_price_per_sheet;
        $cart_item_data['custom_inputs']['total_price'] = $server_total_price;
    }

    // Collect the dates from scheduled orders V1
    /*
    $current_dates = [];

    if ($is_scheduled) {
        foreach ($scheduled_shipments as $s) {
            $current_dates[] = $s['date'];
        }
    } else {
        if (is_array($shipments)) {
            $current_dates = $shipments; 
        } elseif (is_string($shipments) && !empty($shipments)) {
            $current_dates = [$shipments]; 
        }
    }

    $all_dates = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
        if (isset($cart_item['custom_inputs']['is_scheduled']) && $cart_item['custom_inputs']['is_scheduled']) {
            // Scheduled item – dates stored in scheduled_shipments
            if (isset($cart_item['custom_inputs']['scheduled_shipments'])) {
                $all_dates = array_merge($all_dates, array_column($cart_item['custom_inputs']['scheduled_shipments'], 'date'));
            }
        } else {
            // Non-scheduled item – dates stored in shipments
            $item_shipments = $cart_item['custom_inputs']['shipments'] ?? '';
            if (is_array($item_shipments)) {
                $all_dates = array_merge($all_dates, $item_shipments);
            } elseif (is_string($item_shipments) && !empty($item_shipments)) {
                $all_dates[] = $item_shipments;
            }
        }
    }

    $all_dates = array_merge($all_dates, $current_dates);

    $ah_despatch_date = implode(', ', $all_dates);

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        WC()->cart->cart_contents[$cart_item_key]['custom_inputs']['despatch_date'] = $ah_despatch_date;
    }

    $cart_item_data['custom_inputs']['despatch_date'] = $ah_despatch_date;

    error_log("All Combined Despatch Dates (across whole cart): " . $ah_despatch_date);
    */
    // Collect the dates from scheduled orders V1


    // Collect the dates from scheduled orders v2
    $current_dates = [];

    if ($is_scheduled) {
        // Scheduled: dates come from the session / enhanced_shipments
        if (!empty($enhanced_shipments)) {
            $current_dates = array_column($enhanced_shipments, 'date');
        }
    } else {
        // Non-scheduled: use the $shipments value that was set earlier
        if (is_array($shipments)) {
            $current_dates = $shipments; // partial or full backorder (array of dates)
        } elseif (is_string($shipments) && !empty(trim($shipments))) {
            $current_dates = [trim($shipments)]; // single date string
        }
        // If no date (very rare edge case), $current_dates remains empty
    }

    // Optional debug to confirm current item's dates are captured
    error_log("Current item despatch dates: " . print_r($current_dates, true));



    // Collect ALL existing despatch dates from items already in the cart
    $all_dates = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
        if (isset($cart_item['custom_inputs']['is_scheduled']) && $cart_item['custom_inputs']['is_scheduled']) {
            // Scheduled item – pull dates from scheduled_shipments
            if (isset($cart_item['custom_inputs']['scheduled_shipments'])) {
                $all_dates = array_merge($all_dates, array_column($cart_item['custom_inputs']['scheduled_shipments'], 'date'));
            }
        } else {
            // Non-scheduled item – pull dates from shipments
            $item_shipments = $cart_item['custom_inputs']['shipments'] ?? '';
            if (is_array($item_shipments)) {
                $all_dates = array_merge($all_dates, $item_shipments);
            } elseif (is_string($item_shipments) && !empty(trim($item_shipments))) {
                $all_dates[] = trim($item_shipments);
            }
        }
    }

    // Add the current item's dates
    $all_dates = array_merge($all_dates, $current_dates);

    // Safety: remove any empty/invalid entries
    $all_dates = array_filter($all_dates, function($date) {
        return !empty(trim($date));
    });

    // Count occurrences
    $counts = array_count_values($all_dates);

    // Build aggregated array
    $aggregated = [];
    foreach ($counts as $date => $qty) {
        $aggregated[$date] = ['qty' => (int)$qty];
    }

    // Sort chronologically
    uksort($aggregated, function($a, $b) {
        $da = DateTime::createFromFormat('d/m/Y', $a);
        $db = DateTime::createFromFormat('d/m/Y', $b);
        if (!$da || !$db) {
            return strcmp($a, $b);
        }
        return $da <=> $db;
    });

    // Update ALL cart items (existing + current) with the full aggregated array
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        WC()->cart->cart_contents[$cart_item_key]['custom_inputs']['despatch_date'] = $aggregated;
    }
    $cart_item_data['custom_inputs']['despatch_date'] = $aggregated;

    // Debug
    error_log("All collected dates (raw): " . print_r($all_dates, true));
    error_log("Aggregated Despatch Dates (across whole cart): " . print_r($aggregated, true));
    // Collect the dates from scheduled orders v2


    error_log("is_backorder (2): " . $is_backorder);
    error_log("Stock Quantity: (from add_custom_price_cart_item_data_secure)" . $stock_quantity);
    error_log("Total Del Weight: " . $total_del_weight);

    $cart_item_data['custom_inputs'] = array_merge($cart_item_data['custom_inputs'], [
        'width' => floatval($_POST['custom_width']),
        'width_inches' => floatval($_POST['custom_width_inches']),
        'length' => floatval($_POST['custom_length']),
        'length_inches' => floatval($_POST['custom_length_inches']),
        'custom_radius_inches' => floatval($_POST['custom_radius_inches']),
        'custom_radius' => floatval($_POST['custom_radius']),
        'qty' => intval($_POST['custom_qty']),
        'shape_type' => $shape_type,
        'discount_rate' => floatval($_POST['custom_discount_rate']),
        'sheets_required' => $sheet_result['sheets_required'],
        'final_shipping' => $final_shipping,
        'shipments' => $shipments,
        'total_del_weight' => $total_del_weight,
        'despatch_notes' => $despatch_notes,
        'despatch_string' =>  $despatch_string, // thurdsay retrieve discount rate
        'is_backorder' => $is_backorder,
        'backorder_data' => $backorder_data,
        'stock_quantity' => $stock_quantity,
        'allow_credit' => $allow_credit,
        'despatch_date' => $aggregated,  // this will now be the combined list
    ]);


    if ($is_scheduled) {
        $cart_item_data['custom_inputs']['is_scheduled'] = true;
        //$cart_item_data['custom_inputs']['scheduled_shipments'] = $scheduled_shipments;
        $cart_item_data['custom_inputs']['scheduled_shipments'] = $enhanced_shipments;
        error_log("Enhanced Shipments:\n" . print_r($enhanced_shipments, true));
        error_log("Lead Time Label:\n" . $lead_time_label);
    }

    // Clear custom_shipments and custom_qty sessions for scheduled orders
    if ($is_scheduled && !empty($shipments_session)) {
        WC()->session->set('custom_shipments', []);
        WC()->session->set('custom_qty', null);
    }
    // echo "<pre>";
    // print_r($cart_item_data['custom_inputs']);
    // echo "</pre>";

    return $cart_item_data;
}
// 5. END CREATE CART ITEM DATA AND STORE AS SESSION




// REGISTER CUSTOM SHIPPING METHOD
add_action('woocommerce_shipping_init', 'init_custom_shipping_method');
function init_custom_shipping_method() {
    if (!class_exists('WC_Custom_Shipping_Method')) {
        class WC_Custom_Shipping_Method extends WC_Shipping_Method {
            public function __construct($instance_id = 0) {
                $this->id = 'custom_shipping_method';
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Custom Shipping', 'woocommerce');
                $this->method_description = __('Custom shipping method for calculated shipping costs', 'woocommerce');
                $this->supports = ['shipping-zones', 'instance-settings'];
                $this->init();
            }

            public function init() {
                $this->enabled = 'yes';
                $this->title = __('Shipping Total', 'woocommerce');
            }

            public function calculate_shipping($package = []) {
                $cart = WC()->cart;
                $shipping_by_date = group_shipping_by_date($cart);

                // Sum the shipping costs
                $total_shipping = 0;
                foreach ($shipping_by_date as $date => $data) {
                    $total_shipping += floatval($data['final_shipping']);
                }

                if ($total_shipping > 0) {
                    $this->add_rate([
                        'id' => $this->id . ':' . $this->instance_id,
                        'label' => $this->title,
                        'cost' => $total_shipping,
                        'taxes'     => '',
                        'calc_tax' => 'per_order',
                        'package' => $package,
                    ]);
                }
            }
        }
    }
}
// END REGISTER CUSTOM SHIPPING METHOD


// ADD CUSTOM SHIPPING METHOD TO WOOCOMMERCE
add_filter('woocommerce_shipping_methods', 'add_custom_shipping_method');
function add_custom_shipping_method($methods) {
    $methods['custom_shipping_method'] = 'WC_Custom_Shipping_Method';
    return $methods;
}
// END ADD CUSTOM SHIPPING METHOD TO WOOCOMMERCE


// DISPLAY SHIPMENTS SECTION ABOVE CART TOTALS
add_action('woocommerce_before_cart_totals', 'display_shipments_section_cart');
function display_shipments_section_cart() {
    $cart = WC()->cart;
    $shipping_by_date = group_shipping_by_date($cart);

    if (!empty($shipping_by_date)) {
        echo '<div class="shipments-section" style="margin-bottom: 20px;">';
        echo '<p class="cart_totals__shipment"><strong>Shipments:</strong></p>';

        foreach ($shipping_by_date as $date => $data) {
            $shipping_cost = floatval($data['final_shipping']);
            $shipping_rate = get_currency_rate();
            $currency_symbol = get_currency_symbol();
            if ($shipping_cost > 0) {
                //$formatted_cost = wc_price($shipping_cost);
                $formatted_cost = round($shipping_cost * $shipping_rate, 2);
                echo '<p class="cart_totals__shipment-details">Dispatch ' . esc_html($date) .'('. $currency_symbol . '' . $formatted_cost . ')</p>';
            }
        }
        echo '</div>';
    }
}
// DISPLAY SHIPMENTS SECTION ABOVE CART TOTALS


// DISPLAY SHIPPING ADDRESS ON CHECKOUT PAGE
add_action('woocommerce_review_order_before_payment', 'display_shipping_address_on_checkout');
function display_shipping_address_on_checkout() {

    $shipping_address = WC()->session->get('custom_shipping_address');
    if ($shipping_address) {
        echo '<div class="custom-shipping-address">';
        echo '<h3>Shipping Details</h3>';
        echo '<p><strong>Shipping Address: </strong>';
        echo '' . esc_html($shipping_address['street_address']) . ', ';
        if (!empty($shipping_address['address_line2'])) {
            echo '' . esc_html($shipping_address['address_line2']) . ', ';
        }
        echo '' . esc_html($shipping_address['city']) . ', ' . esc_html($shipping_address['county_state']) . ', ' . esc_html($shipping_address['zip_postal']) . ', ';
        echo '' . esc_html($shipping_address['country']) . '';
        echo '</p>';
        echo '</div>';
    }
}
// DISPLAY SHIPPING ADDRESS ON CHECKOUT PAGE


// DISPLAY SHIPMENTS SECTION ABOVE CHECKOUT TOTALS
add_action('woocommerce_review_order_before_payment', 'display_shipments_section_checkout');
function display_shipments_section_checkout() {
    $cart = WC()->cart;
    $shipping_by_date = group_shipping_by_date($cart);

    if (!empty($shipping_by_date)) {
        echo '<div class="shipments-section" style="margin-bottom: 20px;">';
        echo '<p class="cart_totals__shipment"><strong>Shipments:</strong></p>';
        foreach ($shipping_by_date as $date => $data) {
            $shipping_cost = floatval($data['final_shipping']);
            if ($shipping_cost > 0) {
                $formatted_cost = wc_price($shipping_cost);
                echo '<p class="cart_totals__shipment-details">Dispatch ' . esc_html($date) . ' (' . $formatted_cost . ')</p>';
            }
        }
        echo '</div>';
    }
}
// DISPLAY SHIPMENTS SECTION ABOVE CHECKOUT TOTALS





// ENSURE SHIPPING RATE IS ADDED TO ORDER

add_action('woocommerce_checkout_create_order', 'add_custom_shipping_to_order', 20, 2);
function add_custom_shipping_to_order($order, $data) {
    $cart = WC()->cart;
    $shipping_by_date = group_shipping_by_date($cart);

    // Sum the shipping costs
    $shipping_meta = [];
    $total_shipping = 0;

    foreach ($shipping_by_date as $date => $data) {

        $final_shipping = floatval($data['final_shipping']);
        error_log("Shipping (3 new): " . $final_shipping);

        $total_shipping += floatval($data['final_shipping']);

        $shipping_meta[$date] = $final_shipping;
    }

    if ($total_shipping > 0) {
        // Create a new shipping item
        $shipping_item = new WC_Order_Item_Shipping();
        $shipping_item->set_method_id('custom_shipping_method');
        $shipping_item->set_method_title('Shipping Total');
        $shipping_item->set_total($total_shipping);

        // Add meta safely now
        foreach ($shipping_meta as $date => $amount) {
            $shipping_item->add_meta_data(
                'ah_shipping_cost',
                wc_format_decimal($amount),
                false
            );
        }

        // Add the shipping item to the order
        $order->add_item($shipping_item);
    }
    // Update shipping address from session
    $shipping_address = WC()->session->get('custom_shipping_address');
    if ($shipping_address && is_array($shipping_address)) {
        $order->set_shipping_first_name(isset($data['billing_first_name']) ? $data['billing_first_name'] : '');
        $order->set_shipping_last_name(isset($data['billing_last_name']) ? $data['billing_last_name'] : '');
        $order->set_shipping_company(isset($data['billing_company']) ? $data['billing_company'] : '');
        $order->set_shipping_address_1($shipping_address['street_address']);
        $order->set_shipping_address_2(!empty($shipping_address['address_line2']) ? $shipping_address['address_line2'] : '');
        $order->set_shipping_city($shipping_address['city']);
        $order->set_shipping_state($shipping_address['county_state']);
        $order->set_shipping_postcode($shipping_address['zip_postal']);
        
        // Map full country names to ISO country codes for WooCommerce
        $country_codes = [
            'United Kingdom' => 'GB',
            'France' => 'FR',
            'Germany' => 'DE',
            'Monaco' => 'MC',
            'Poland' => 'PL',
            'Spain' => 'ES',
            'United States' => 'US',
        ];
        $country_code = isset($country_codes[$shipping_address['country']]) ? $country_codes[$shipping_address['country']] : '';
        if ($country_code) {
            $order->set_shipping_country($country_code);
        }
    }

    // Debugging log to confirm shipping item addition
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("add_custom_shipping_to_order: Added shipping item with total {$total_shipping} for order ID {$order->get_id()}");
        if ($shipping_address && is_array($shipping_address)) {
            error_log("add_custom_shipping_to_order: Shipping address set to " . print_r($shipping_address, true));
        }
    }
}

// ENSURE SHIPPING RATE IS ADDED TO ORDER



// SAVE ALL RELEVANT DATA TO ORDER ITEM META

add_action('woocommerce_checkout_create_order_line_item', 'save_sheets_required_to_order_item', 10, 4);

function save_sheets_required_to_order_item($item, $cart_item_key, $values, $order) {
    if (isset($values['custom_inputs']['sheets_required'])) {
        $item->add_meta_data('sheets_required', $values['custom_inputs']['sheets_required'], true);
    }
    if (isset($values['custom_inputs']['shipping_address'])) {
        $item->add_meta_data('custom_shipping_address', $values['custom_inputs']['shipping_address'], true);
    }
    if (isset($values['custom_inputs']['shape_type'])) {
        $item->add_meta_data('shape_type', $values['custom_inputs']['shape_type'], true);
    }
    if (isset($values['custom_inputs']['width'])) {
        $item->add_meta_data('width', $values['custom_inputs']['width'], true);
    }
    if (isset($values['custom_inputs']['width_inches'])) {
        $item->add_meta_data('width_inches', $values['custom_inputs']['width_inches'], true);
    }
    if (isset($values['custom_inputs']['length'])) {
        $item->add_meta_data('length', $values['custom_inputs']['length'], true);
    }
    if (isset($values['custom_inputs']['length_inches'])) {
        $item->add_meta_data('length_inches', $values['custom_inputs']['length_inches'], true);
    }
    if (isset($values['custom_inputs']['custom_radius'])) {
        $item->add_meta_data('custom_radius', $values['custom_inputs']['custom_radius'], true);
    }
    if (isset($values['custom_inputs']['custom_radius_inches'])) {
        $item->add_meta_data('custom_radius_inches', $values['custom_inputs']['custom_radius_inches'], true);
    }
    if (isset($values['custom_inputs']['qty'])) {
        $item->add_meta_data('qty', $values['custom_inputs']['qty'], true);
    }
    if (isset($values['custom_inputs']['despatch_notes'])) {
        $item->add_meta_data('despatch_notes', $values['custom_inputs']['despatch_notes'], true);
    }
    if (isset($values['custom_inputs']['despatch_string'])) {
        $item->add_meta_data('despatch_string', $values['custom_inputs']['despatch_string'], true);
    }
    if (isset($values['custom_inputs']['total_del_weight'])) {
        $item->add_meta_data('total_del_weight', $values['custom_inputs']['total_del_weight'], true);
    }
    if (isset($values['custom_inputs']['is_backorder'])) {
        $item->add_meta_data('is_backorder', $values['custom_inputs']['is_backorder'], true);
    }
    if (isset($values['custom_inputs']['per_part'])) {
        $item->add_meta_data('per_part', $values['custom_inputs']['per_part'], true);
    }
    if (isset($values['custom_inputs']['cost_per_part'])) {
        $item->add_meta_data('cost_per_part', $values['custom_inputs']['cost_per_part'], true);
    }
    if (isset($values['custom_inputs']['price'])) {
        $item->add_meta_data('price', $values['custom_inputs']['price'], true);
    }
    if (isset($values['custom_inputs']['is_scheduled'])) {
        $item->add_meta_data('is_scheduled', $values['custom_inputs']['is_scheduled'], true);
    }
    if (isset($values['custom_inputs']['roll_length'])) {
        $item->add_meta_data('roll_length', $values['custom_inputs']['roll_length'], true);
    }
    if (isset($values['custom_inputs']['stock_quantity'])) {
        $item->add_meta_data('stock_quantity', $values['custom_inputs']['stock_quantity'], true);
    }
    if (isset($values['custom_inputs']['despatch_date'])) {
        $item->add_meta_data('despatch_date', $values['custom_inputs']['despatch_date'], true);
    }
    if (isset($values['custom_inputs']['is_backorder']) && $values['custom_inputs']['is_backorder']) {
        $backorder_data = $values['custom_inputs']['backorder_data'];
        $item->add_meta_data('parts_backorder', $backorder_data['parts_backorder'], true);
        $item->add_meta_data('able_to_dispatch', $backorder_data['able_to_dispatch'], true);
        $item->add_meta_data('parts_per_sheet', $backorder_data['parts_per_sheet'], true);
    }
    // Handle shipments explicitly
    if (isset($values['custom_inputs']['shipments'])) {
        $shipments = $values['custom_inputs']['shipments'];
        $item->add_meta_data('shipments', is_array($shipments) ? implode(', ', $shipments) : $shipments, true);
    }
    if (isset($values['custom_inputs']['scheduled_shipments'])) {
        $scheduled_shipments = $values['custom_inputs']['scheduled_shipments'];
        $meta_value = [];
        foreach ($scheduled_shipments as $s) {
            $meta_value[] = $s['parts'] . ' parts on ' . $s['date'];
        }
        $item->add_meta_data('scheduled_shipments', implode("\n", $meta_value), true);
    }
    // Add file paths to order meta
    if (isset($values['custom_inputs']['pdf_path'])) {
        $item->add_meta_data('pdf_path', $values['custom_inputs']['pdf_path'], true);
    }
    if (isset($values['custom_inputs']['dxf_path'])) {
        $item->add_meta_data('dxf_path', $values['custom_inputs']['dxf_path'], true);
    }
}
// SAVE ALL RELEVANT DATA TO ORDER ITEM META


// SHOW DATA IN CART AND CHECKOUT
add_filter('woocommerce_get_item_data', 'show_custom_input_details_in_cart', 10, 2);
function show_custom_input_details_in_cart($item_data, $cart_item) {
    $product_id = $cart_item['product_id'];
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;
    $roll_length = floatval(get_field('roll_length', $product_id));
    $roll_length_v = ($roll_length > 0) ? $roll_length / 1000 : 0;

    if ($is_product_single) {
        return $item_data; // Skip custom inputs for single products
    }

    if (!empty($cart_item['custom_inputs'])) {


        // Part Shape
        if (isset($cart_item['custom_inputs']['shape_type']) && !empty($cart_item['custom_inputs']['shape_type'])) {
            $ps_string = $cart_item['custom_inputs']['shape_type'];
            $ps_string = str_replace('-', ' ', $ps_string);
            $ps_string = ucwords($ps_string);
            $item_data[] = [
                'name' => 'Part shape',
                'value' => $ps_string
            ];
        }

        // PDF Drawing (if present)
        if (isset($cart_item['custom_inputs']['pdf_path']) && !empty($cart_item['custom_inputs']['pdf_path'])) {
            $item_data[] = [
                'name' => 'PDF Drawing',
                'value' => '<a href="/wp-content/uploads' . esc_url($cart_item['custom_inputs']['pdf_path']) . '" target="_blank">' . esc_html(basename($cart_item['custom_inputs']['pdf_path'])) . '</a>'
            ];
        }

        // DXF Drawing (if present, optional)
        if (isset($cart_item['custom_inputs']['dxf_path']) && !empty($cart_item['custom_inputs']['dxf_path'])) {
            $item_data[] = [
                'name' => 'DXF Drawing',
                'value' => '<a href="/wp-content/uploads' . esc_url($cart_item['custom_inputs']['dxf_path']) . '" target="_blank">' . esc_html(basename($cart_item['custom_inputs']['dxf_path'])) . '</a>'
            ];
        }

        // Dynamically Display Radius Value (MM)
        // ONLY when shape is circle-radius AND custom_radius_inches is 0 or empty
        if (
            isset($cart_item['custom_inputs']['shape_type']) &&
            $cart_item['custom_inputs']['shape_type'] === 'circle-radius' &&
            (
                !isset($cart_item['custom_inputs']['custom_radius_inches']) ||
                (float) $cart_item['custom_inputs']['custom_radius_inches'] === 0.0
            )
        ) {
            if (isset($cart_item['custom_inputs']['width']) && (float) $cart_item['custom_inputs']['width'] > 0) {
                $width_value = (float) $cart_item['custom_inputs']['width'] / 2;

                $item_data[] = [
                    'name'  => 'Radius (MM)',
                    'value' => $width_value
                ];
            }
        }


        // Dynamically Display Radius Value
        // if ($cart_item['custom_inputs']['shape_type'] == "circle-radius") {
        
        // $width_value = $cart_item['custom_inputs']['width'] / 2;
        //     $item_data[] = [
        //         'name' => 'Radius (MM)',
        //         'value' => $width_value
        //     ];

        // }

        // Width
        if (isset($cart_item['custom_inputs']['width'])) {
            $item_data[] = [
                'name' => 'Width (MM)',
                'value' => $cart_item['custom_inputs']['width']
            ];
        }

        // Width Inches
        if (
            isset($cart_item['custom_inputs']['width_inches']) &&
            (float) $cart_item['custom_inputs']['width_inches'] > 0
        ) {
            $item_data[] = [
                'name'  => 'Width (INCHES)',
                'value' => $cart_item['custom_inputs']['width_inches']
            ];
        }

        // Length
        if (isset($cart_item['custom_inputs']['length'])) {
            $item_data[] = [
                'name' => 'Length (MM)',
                'value' => $cart_item['custom_inputs']['length']
            ];
        }

        // Length Inches
        if (
            isset($cart_item['custom_inputs']['length_inches']) &&
            (float) $cart_item['custom_inputs']['length_inches'] > 0
        ) {
            $item_data[] = [
                'name'  => 'Length (INCHES)',
                'value' => $cart_item['custom_inputs']['length_inches']
            ];
        }

        // Custom Radius Inches
        if (
            isset($cart_item['custom_inputs']['custom_radius_inches']) &&
            (float) $cart_item['custom_inputs']['custom_radius_inches'] > 0
        ) {
            $item_data[] = [
                'name'  => 'Radius (INCHES)',
                'value' => $cart_item['custom_inputs']['custom_radius_inches']
            ];
        }


        if ($cart_item['custom_inputs']['shape_type'] === "rolls") {

        if (!empty($cart_item['custom_inputs']['qty']) && $roll_length_v > 0) {

                $qty = floatval($cart_item['custom_inputs']['qty']);
                $parts = $qty / $roll_length_v;

                $item_data[] = [
                    'name'  => 'Total number of rolls',
                    'value' => $cart_item['custom_inputs']['qty']
                ];
            }

        } else {

            if (!empty($cart_item['custom_inputs']['qty'])) {

                $item_data[] = [
                    'name'  => 'Total number of parts',
                    'value' => $cart_item['custom_inputs']['qty']
                ];
            }
        }


        // Despatch Notes
        if (isset($cart_item['custom_inputs']['despatch_notes'])) {
            $item_data[] = [
                'name' => 'Despatch Notes',
                'value' => '<br>' . esc_html($cart_item['custom_inputs']['despatch_notes'])
            ];
        }

        // Total Del Weights
        if (isset($cart_item['custom_inputs']['total_del_weight'])) {
            $item_data[] = [
                'name' => 'Customer Shipping Weight(s)',
                'value' => round((float)$cart_item['custom_inputs']['total_del_weight'], 3) . "kg"
            ];
            // echo "<pre>";
            // print_r($cart_item['custom_inputs']);
            // echo "</pre>";
        }

    }
    return $item_data;
}
// SHOW DATA IN CART AND CHECKOUT





add_filter('woocommerce_get_cart_item_from_session', function($item, $values) {
    if (isset($values['custom_inputs'])) {
        $item['custom_inputs'] = $values['custom_inputs'];
    }
    return $item;
}, 10, 2);




add_action('woocommerce_before_calculate_totals', 'apply_secure_custom_price');

function apply_secure_custom_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    if (did_action('woocommerce_before_calculate_totals') >= 2) {
        return;
    }

    // Log the current shipping country and tax rate
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $shipping_country = WC()->customer->get_shipping_country();
        $tax_rates = WC_Tax::find_rates(['country' => $shipping_country]);
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];
        $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;

        // Skip custom pricing for products where is_product_single is true
        if ($is_product_single) {
            continue; // Use default WooCommerce price
        }

        if (isset($cart_item['custom_inputs'])) {
            if (isset($cart_item['custom_inputs']['is_scheduled']) && $cart_item['custom_inputs']['is_scheduled']) {
                $price_per_sheet = floatval($cart_item['custom_inputs']['price']);
                if ($price_per_sheet > 0) {
                    $cart_item['data']->set_price($price_per_sheet);
                    continue;
                }
            }
            $width = $cart_item['custom_inputs']['width'];
            $length = $cart_item['custom_inputs']['length'];
            $qty = $cart_item['custom_inputs']['qty'];
            $discount_rate = isset($cart_item['custom_inputs']['discount_rate']) ? $cart_item['custom_inputs']['discount_rate'] : 0;
            $sheets_required = isset($cart_item['custom_inputs']['sheets_required']) ? intval($cart_item['custom_inputs']['sheets_required']) : 1;
            $is_backorder = isset($cart_item['custom_inputs']['is_backorder']) ? $cart_item['custom_inputs']['is_backorder'] : false;
            $shape_type = $cart_item['custom_inputs']['shape_type'] ?? 'custom-shape-drawing'; 

            if (!in_array($shape_type, ['custom-shape-drawing', 'square-rectangle', 'circle-radius', 'stock-sheets', 'rolls'])) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("apply_secure_custom_price: Invalid shape_type ($shape_type) for cart item key $cart_item_key. Defaulting to custom-shape-drawing.");
                }
                $shape_type = 'custom-shape-drawing';
            }

            if ($is_backorder && !empty($cart_item['custom_inputs']['backorder_data'])) {
                //error_log('We triggered the backorder');
                $backorder_data = $cart_item['custom_inputs']['backorder_data'];
                // Use the backorder total if valid
                if (isset($backorder_data['backorder_total']) && $backorder_data['backorder_total'] > 0) {
                    $total_price = $backorder_data['backorder_total'];
                    $price_per_sheet = $sheets_required > 0 ? $total_price / $sheets_required : $total_price;
                } else {
                    $total_price = calculate_product_price($product_id, $width, $length, $qty, $discount_rate, $shape_type);
                    $price_per_sheet = $sheets_required > 0 ? $total_price / $sheets_required : $total_price;
                }
            } else {
                //error_log('We triggered the else');
                // Use stored price per sheet if available, otherwise recalculate
                $price_per_sheet = isset($cart_item['custom_inputs']['price']) ? floatval($cart_item['custom_inputs']['price']) : 0;
                if ($price_per_sheet <= 0) {
                    $total_price = calculate_product_price($product_id, $width, $length, $qty, $discount_rate, $shape_type);
                    try {
                        $price_per_sheet = $sheets_required > 0 ? $total_price / $sheets_required : $total_price;
                    } catch (TypeError $e) {
                        error_log('Price calculation TypeError: ' . $e->getMessage());
                    }
                }
            }

          if (!is_wp_error($price_per_sheet)) {
                $cart_item['data']->set_price($price_per_sheet);
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("apply_secure_custom_price: Error calculating price for product ID $product_id: " . $price_per_sheet->get_error_message());
                }
            }
        }
    }
}









// DISPLAY SHIPPING ADDRESS ON THANKYOU PAGE
add_action('woocommerce_thankyou', 'display_shipping_address_on_thankyou', 10, 1);
function display_shipping_address_on_thankyou($order_id) {

    $order = wc_get_order($order_id);
    $shipping_address = false;
    $despatch_notes = false;

    foreach ($order->get_items() as $item_id => $item) {
        $custom_shipping_address = $item->get_meta('custom_shipping_address');
        $item_despatch_notes = $item->get_meta('despatch_notes');
        if ($custom_shipping_address && is_array($custom_shipping_address)) {
            $shipping_address = $custom_shipping_address;
        }
        if ($item_despatch_notes) {
            $despatch_notes = $item_despatch_notes;
        }
        // Break after the first item with data to avoid duplicates
        if ($shipping_address || $despatch_notes) {
            break;
        }
    }
/*
if ($shipping_address || $despatch_notes) {
        echo '<div class="custom-order-details">';
        echo '<h2 class="woocommerce-column__title">Order Details</h2>';

        // Display Despatch Notes
        if ($despatch_notes) {
            echo '<p><strong>Despatch Notes:</strong> ' . esc_html($despatch_notes) . '</p>';
        }

        // Display Shipping Address
        if ($shipping_address) {
            echo '<h3>Shipping Details</h3>';
            echo '<address>';
            echo esc_html($shipping_address['street_address']) . '<br>';
            if (!empty($shipping_address['address_line2'])) {
                echo esc_html($shipping_address['address_line2']) . '<br>';
            }
            echo esc_html($shipping_address['city']) . '<br>';
            echo esc_html($shipping_address['county_state']) . '<br>';
            echo esc_html($shipping_address['zip_postal']) . '<br>';
            echo esc_html($shipping_address['country']) . '<br>';
            echo '</address>';
        }

        echo '</div>';
    }
        */
}
// DISPLAY SHIPPING ADDRESS ON THANKYOU PAGE




// SAVE SHIPPING ADDRESS TO ORDER META AND DISPLAY IN EMAILS
/*
add_action('woocommerce_checkout_create_order_line_item', 'save_shipping_address_to_order_item', 10, 4);
function save_shipping_address_to_order_item($item, $cart_item_key, $values, $order) {
    if (isset($values['custom_inputs']['shipping_address'])) {
        $shipping_address = $values['custom_inputs']['shipping_address'];
        $item->add_meta_data('custom_shipping_address', $shipping_address, true);
    }
}
    */
// SAVE SHIPPING ADDRESS TO ORDER META AND DISPLAY IN EMAILS




// DISPLAY SHIPPING ADDRESS/NOTES IN ORDER EMAILS

add_action('woocommerce_email_customer_details', 'add_custom_shipping_address_below_billing', 25, 4);
function add_custom_shipping_address_below_billing($order, $sent_to_admin, $plain_text, $email) {
    // Loop through order items to find the first shipping address
    $shipping_address = null;
    $despatch_notes = null;

    foreach ($order->get_items() as $item_id => $item) {
        $meta_address = $item->get_meta('custom_shipping_address');
        $meta_despatch_notes = $item->get_meta('despatch_notes');
        if (!empty($meta_address['street_address'])) {
            $shipping_address = $meta_address;
        }
        if ($meta_despatch_notes) {
            $despatch_notes = $meta_despatch_notes;
        }
        if ($shipping_address || $despatch_notes) {
            break; // Only show first item with data
        }
    }

    if (!$shipping_address && !$despatch_notes) return;

    if ($plain_text) {
        echo "\nShipping Address:\n";
        echo $shipping_address['street_address'] . "\n";
        if (!empty($shipping_address['address_line2'])) {
            echo $shipping_address['address_line2'] . "\n";
        }
        echo $shipping_address['city'] . ', ' . $shipping_address['county_state'] . ', ' . $shipping_address['zip_postal'] . "\n";
        echo $shipping_address['country'] . "\n";
    } else {

echo '<div class="custom-order-details" style="margin-top:10px;">';
        if ($despatch_notes) {
            echo '<h3>Despatch Notes</h3>';
            echo '<p>' . esc_html($despatch_notes) . '</p>';
        }
        if ($shipping_address) {
            echo '<h3>Shipping Address</h3>';
            echo esc_html($shipping_address['street_address']) . '<br>';
            if (!empty($shipping_address['address_line2'])) {
                echo esc_html($shipping_address['address_line2']) . '<br>';
            }
            echo esc_html($shipping_address['city']) . ', ' . esc_html($shipping_address['county_state']) . ', ' . esc_html($shipping_address['zip_postal']) . '<br>';
            echo esc_html($shipping_address['country']) . '<br>';
        }
        echo '</div>';

    }
}

// DISPLAY SHIPPING ADDRESS/NOTES IN ORDER EMAILS




// DISPLAY GLOBAL SHIPPING ADDRESS BELOW CART TABLES
add_action('woocommerce_before_cart_totals', 'display_global_shipping_address_cart', 1, 2);
function display_global_shipping_address_cart() {
    $shipping_address = WC()->session->get('custom_shipping_address');
    if (!$shipping_address) return;

    echo '<div class="global-shipping-address" style="margin-top:20px;">';
    echo '<h3 class="global-shipping-address-title">Shipping Details</h3>';
    echo '<p><strong>Shipping Address: </strong><p class="global-shipping-address-list">';
    echo esc_html($shipping_address['street_address']) . '<br>';
    if (!empty($shipping_address['address_line2'])) {
        echo esc_html($shipping_address['address_line2']) . '<br>';
    }
    echo esc_html($shipping_address['city']) . '<br>' . esc_html($shipping_address['county_state']) . '<br>' . esc_html($shipping_address['zip_postal']) . '<br>';
    echo esc_html($shipping_address['country']) . '';
    echo '</p></p>';
    echo '</div>';
}
// DISPLAY GLOBAL SHIPPING ADDRESS BELOW CART TABLES




// SAVE THE WP SESSION SHIPPING ADDRESS TO THE ORDERS PAGE
add_action('woocommerce_checkout_create_order', 'update_order_shipping_fields', 20, 2);
function update_order_shipping_fields($order, $data) {
    // Try to get shipping address from session
    $shipping_address = WC()->session->get('custom_shipping_address');

    if ($shipping_address && is_array($shipping_address)) {
        // Map your custom fields to WooCommerce shipping fields
        $order->set_shipping_first_name(isset($data['billing_first_name']) ? $data['billing_first_name'] : '');
        $order->set_shipping_last_name(isset($data['billing_last_name']) ? $data['billing_last_name'] : '');
        $order->set_shipping_company(isset($data['billing_company']) ? $data['billing_company'] : '');
        $order->set_shipping_address_1($shipping_address['street_address']);
        $order->set_shipping_address_2(!empty($shipping_address['address_line2']) ? $shipping_address['address_line2'] : '');
        $order->set_shipping_city($shipping_address['city']);
        $order->set_shipping_state($shipping_address['county_state']);
        $order->set_shipping_postcode($shipping_address['zip_postal']);
        $order->set_shipping_country($shipping_address['country']);
    }
}
// SAVE THE WP SESSION SHIPPING ADDRESS TO THE ORDERS PAGE




// CLEAR SESSION AFTER ORDER IS PLACED

add_action('woocommerce_checkout_order_processed', 'clear_custom_shipping_session', 10, 1);
function clear_custom_shipping_session($order_id) {
    WC()->session->set('custom_shipping_address', null);
    WC()->session->set('custom_qty', null); 
    WC()->session->set('custom_shipments', null); 
}

// CLEAR SESSION AFTER ORDER IS PLACED




// CALCULATE SHEETS REQUIRED

function calculate_sheets_required($sheet_width, $sheet_length, $part_width, $part_length, $quantity, $product_id) {

    $border_cm = 0.2;
    
    if ($product_id && function_exists('get_field')) {
        $acf_border = get_field('border_around', $product_id); // i need to conditionally set $acf_border to '0' if stock sheets is clicked
        if (is_numeric($acf_border) && $acf_border > 0) {
            $border_cm = floatval($acf_border);
        } 
        // else {
        //      $border_cm = 0;
        // }
    }
    
    $border_mm = $border_cm * 10; // cm → mm

    $edge_margin = $border_mm;
    $gap         = $border_mm;

    // Calculate max parts per row (width-wise)
    $max_parts_per_row = 1;
    while (true) {
        $total_width = (2 * $edge_margin) + ($max_parts_per_row * $part_width) + (($max_parts_per_row - 1) * $gap);
        if ($total_width > $sheet_width) break;
        $max_parts_per_row++;
    }
    $max_parts_per_row--; // Last valid count

    // Calculate max parts per column (length-wise)
    $max_parts_per_column = 1;
    while (true) {
        $total_length = (2 * $edge_margin) + ($max_parts_per_column * $part_length) + (($max_parts_per_column - 1) * $gap);
        if ($total_length > $sheet_length) break;
        $max_parts_per_column++;
    }
    $max_parts_per_column--; // Last valid count

    // Calculate parts per sheet
    $parts_per_sheet = $max_parts_per_row * $max_parts_per_column;

    if ($parts_per_sheet <= 0) {
        return [
            'sheets_required' => 0,
            'parts_per_sheet' => 0,
            'max_columns' => 0,
            'max_rows' => 0
        ];
    }

    // Calculate required sheets
    $sheets_required = ceil($quantity / $parts_per_sheet);

    return [
        'sheets_required' => $sheets_required,
        'parts_per_sheet' => $parts_per_sheet,
        'max_columns' => $max_parts_per_row,
        'max_rows' => $max_parts_per_column
    ];
}

// CALCULATE SHEETS REQUIRED



// DISPLAY CUSTOM INPUTS ON PRODUCT PAGE
add_action('woocommerce_before_single_product_summary', 'display_custom_inputs_on_product_page', 10);
function display_custom_inputs_on_product_page() {
    global $product;

    $product_id = $product->get_id();
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;

    // Get product weight (in kg)
    $product_weight = $product->get_weight();
    $weight_unit = get_option('woocommerce_weight_unit');

    if ($is_product_single) {
        return;
    }

    $length = $product->get_length();
    $width  = $product->get_width();

    if (!is_numeric($length) || !is_numeric($width)) {
        return; // dimensions missing → do nothing
    }

    //$sheet_length_mm = $product->get_length() * 10; // cm → mm
    //$sheet_width_mm = $product->get_width() * 10;   // cm → mm
    $sheet_length_mm = (float) $length * 10;
    $sheet_width_mm  = (float) $width  * 10;

    $part_length_mm = isset($_POST['custom_length']) ? floatval($_POST['custom_length']) : 0;
    $part_width_mm = isset($_POST['custom_width']) ? floatval($_POST['custom_width']) : 0;
    $quantity = isset($_POST['custom_qty']) ? intval($_POST['custom_qty']) : 0;
    $stock_quantity = $product->get_stock_quantity();
    $discount_rate = isset($_POST['custom_discount_rate']) ? floatval($_POST['custom_discount_rate']) : 0;
    $is_backorder = isset($_POST['is_backorder']) ? floatval($_POST['is_backorder']) : 0;

    $country = isset($_POST['custom_country']) ? sanitize_text_field($_POST['custom_country']) : 'United Kingdom';

    $discount_labels = [
        '0' => '24Hrs (working day)',
        '0.015' => '48Hrs (working days) (1.5% Discount)',
        '0.02' => '5 Days (working days) (2% Discount)',
        '0.025' => '7 Days (working days) (2.5% Discount)',
        '0.03' => '12 Days (working days) (3% Discount)',
        '0.035' => '14 Days (working days) (3.5% Discount)',
        '0.04' => '30 Days (working days) (4% Discount)',
        '0.05' => '35 Days (working days) (5% Discount)',
    ];

    $delivery_time = isset($discount_labels[(string)$discount_rate]) ? $discount_labels[(string)$discount_rate] : 'Unknown';

    if($delivery_time === "24Hrs (working day)"){
        $shipments = date('d/m/Y', strtotime(' + 1 days'));
    } elseif($delivery_time === "48Hrs (working days) (1.5% Discount)"){
        $shipments = date('d/m/Y', strtotime(' + 2 days'));
    } elseif($delivery_time === "5 Days (working days) (2% Discount)"){
        $shipments = date('d/m/Y', strtotime(' + 5 days'));
    } elseif($delivery_time === "7 Days (working days) (2.5% Discount)"){
        $shipments = date('d/m/Y', strtotime(' + 7 days'));
    } elseif($delivery_time === "12 Days (working days) (3% Discount)"){
        $shipments = date('d/m/Y', strtotime(' + 12 days'));
    } elseif($delivery_time === "14 Days (working days) (3.5% Discount)"){
        $shipments = date('d/m/Y', strtotime(' + 14 days'));
    } elseif($delivery_time === "30 Days (working days) (4% Discount)"){
        $shipments = date('d/m/Y', strtotime(' + 30 days'));
    } else {
        $shipments = date('d/m/Y', strtotime(' + 35 days'));
    }


    // Only calculate and display if valid inputs are provided
    if ($part_width_mm > 0 && $part_length_mm > 0 && $quantity > 0) {
        // Call calculate_sheets_required function
        $result = calculate_sheets_required(
            $sheet_width_mm,
            $sheet_length_mm,
            $part_width_mm,
            $part_length_mm,
            $quantity,
            $product_id
        );

        // Calculate total delivery weight
        $sheets = $result['sheets_required'];
        if (!is_numeric($product_weight) || $product_weight <= 0) {
            $total_del_weight = new WP_Error('invalid_weight', 'Invalid or missing product weight');
        } else {
            $totalSqMm = $part_length_mm * $part_width_mm;
            $totalSqCm = $totalSqMm / 100;
            $total_del_weight = $totalSqCm * floatval($product_weight) * $quantity * 1.03;
            $final_shipping = calculate_shipping_cost($total_del_weight, $country);
        }




        // Output styled results
        echo '<div class="custom-product-info" style="margin-bottom: 20px;">';
        echo '<h3>Product Details</h3>';
        echo '<p><strong>Sheet Size (mm):</strong> ' . esc_html($sheet_width_mm) . ' x ' . esc_html($sheet_length_mm) . '</p>';
        echo '<p><strong>Part Size (mm):</strong> ' . esc_html($part_width_mm) . ' x ' . esc_html($part_length_mm) . '</p>';
        echo '<p><strong>Quantity Needed:</strong> ' . esc_html($quantity) . '</p>';
        echo '<p><strong>Delivery Time:</strong> ' . esc_html($delivery_time) . '</p>';
        echo '<p><strong>Shipments:</strong> ' . esc_html($shipments) . '</p>';
        echo '<p><strong>Final Shipping:</strong> ' . esc_html($final_shipping) . '</p>';
        echo '<p><strong>Sheets Required:</strong> ' . esc_html($result['sheets_required']) . '</p>';
        echo '<p><strong>Parts Per Sheet:</strong> ' . esc_html($result['parts_per_sheet']) . '</p>';
        echo '<p><strong>Max Columns:</strong> ' . esc_html($result['max_columns']) . '</p>';
        echo '<p><strong>Max Rows:</strong> ' . esc_html($result['max_rows']) . '</p>';
        echo '<p><strong>Total Delivery Weight:</strong> ' . esc_html($total_del_weight) . ' ' . esc_html($weight_unit) . '</p>';
        echo '<p><strong>Discount Rate:</strong> ' . esc_html($discount_rate) . '</p>';
        echo '<p><strong>Stock Quantity:</strong> ' . esc_html($stock_quantity) . '</p>';
        echo '<p><strong>ON Backorder?:</strong> ' . esc_html($is_backorder) . '</p>';
        echo '</div>';
    }
}

// DISPLAY CUSTOM INPUTS ON PRODUCT PAGE


// ADD HIDDEN FIELD FOR IS_PRODUCT_SINGLE
add_action('woocommerce_before_single_product', 'add_is_product_single_hidden_field');
function add_is_product_single_hidden_field() {
    global $product;
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product->get_id()) : false;
    echo '<input type="hidden" name="is_product_single" value="' . ($is_product_single ? '1' : '0') . '">';
}
// ADD HIDDEN FIELD FOR IS_PRODUCT_SINGLE



// DYNAMICALLY SET THE CUSTOMERS PRODUCT PAGE SHIPPING ADDRESS FOR TAX CALCULATIONS BASED ON SESSION
add_action('template_redirect', 'set_custom_shipping_country_for_tax_calculation');
function set_custom_shipping_country_for_tax_calculation() {
    if (is_cart() || is_checkout()) {
        $shipping_address = WC()->session->get('custom_shipping_address');
        if ($shipping_address && isset($shipping_address['country'])) {
            $custom_country = $shipping_address['country'];
            // Map full country names to ISO country codes for WooCommerce
            $country_codes = [
                'United Kingdom' => 'GB',
                'France' => 'FR',
                'Germany' => 'DE',
                'Monaco' => 'MC',
                'Poland' => 'PL',
                'Spain' => 'ES',
                'United States' => 'US',
            ];
            $country_code = isset($country_codes[$custom_country]) ? $country_codes[$custom_country] : '';
            if ($country_code) {
                WC()->customer->set_shipping_country($country_code);
                // Optionally set other address fields for more precise tax rules if needed
                WC()->customer->set_shipping_address($shipping_address['street_address']);
                WC()->customer->set_shipping_address_2($shipping_address['address_line2']);
                WC()->customer->set_shipping_city($shipping_address['city']);
                WC()->customer->set_shipping_state($shipping_address['county_state']);
                WC()->customer->set_shipping_postcode($shipping_address['zip_postal']);
            }
        } else {
                // Fallback to store base country only if no session data
                $store_country = WC()->countries->get_base_country();
                WC()->customer->set_shipping_country($store_country);
        }
    }
}
// DYNAMICALLY SET THE CUSTOMERS PRODUCT PAGE SHIPPING ADDRESS FOR TAX CALCULATIONS BASED ON SESSION



// HANDLE MODAL SUBMISSION FOR DELIVERY OPTIONS VIA AJAX
add_action('wp_ajax_save_shipment', 'save_shipment_callback');
add_action('wp_ajax_nopriv_save_shipment', 'save_shipment_callback');

function save_shipment_callback() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    $despatch_date = sanitize_text_field($_POST['despatch_date'] ?? '');
    $parts         = intval($_POST['shipment_parts'] ?? 0);

    $fees = [
        'add_manufacturers_COFC_ss' => isset($_POST['add_manufacturers_COFC_ss']) ? floatval($_POST['add_manufacturers_COFC_ss']) : 0, //new cofc delivery options
        'add_fair_ss' => isset($_POST['add_fair_ss']) ? floatval($_POST['add_fair_ss']) : 0, //new cofc delivery options
        'add_materials_direct_COFC_ss' => isset($_POST['add_materials_direct_COFC_ss']) ? floatval($_POST['add_materials_direct_COFC_ss']) : 0, //new cofc delivery options
    ];

    $total_shipment_fee = array_sum($fees); //new cofc delivery options

    if (!$despatch_date || $parts < 1) {
        wp_send_json_error(['message' => 'Invalid despatch date or parts.']);
    }

    // Get current shipments
    $shipments = WC()->session->get('custom_shipments', []);
    $custom_qty = WC()->session->get('custom_qty', 0);

    if ($custom_qty <= 0) {
        wp_send_json_error(['message' => 'No total quantity set.']);
    }

    $total_scheduled = array_sum(array_column($shipments, 'parts'));
    $remaining = $custom_qty - $total_scheduled;

    if ($parts > $remaining) {
        wp_send_json_error(['message' => "Only $remaining parts remaining."]);
    }

    // Add new shipment
    $shipments[] = [
        'date'  => $despatch_date,
        'parts' => $parts,
        'fees' => $fees, //new cofc delivery options
        'total_fee'   => $total_shipment_fee //new cofc delivery options
    ];

    WC()->session->set('custom_shipments', $shipments);

    WC()->session->set('custom_shipments_last_activity', time()); //Update the last activity timestamp to reset the 15-minute timer

    $new_total = array_sum(array_column($shipments, 'parts'));
    $remaining = $custom_qty - $new_total;
    $total_fees = array_sum(array_column($shipments, 'total_fee')); //new cofc delivery options

    // Build enhanced table HTML
    ob_start();
    ?>
    <div class="delivery-options-shipment__outer">
        <table class="delivery-options-shipment">
            <thead>
                <tr>
                    <th class="delivery-options-shipment__title">Despatch Date</th>
                    <th class="delivery-options-shipment__title">Total number of parts</th>
                    <th class="delivery-options-shipment__title">All COFCs & FAIRs</th> <!-- NEW -->
                    <th class="delivery-options-shipment__title"><span class="screen-reader-text">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($shipments)) { ?>
                    <tr class="delivery-options-shipment__display">
                        <td class="delivery-options-shipment__display-inner" colspan="4">There are no <span>shipments.</span></td>
                    </tr>
                <?php } else { ?>
                <?php //error_log("Shipments: " . print_r($shipments)) ?>
                        <?php foreach ($shipments as $index => $shipment) {
                            //error_log("Shipment: " . print_r($shipment));
                            $despatch_ymd = date('Y-m-d', strtotime(str_replace('/', '-', $shipment['date'])));
                            $lead_time_label = get_shipment_lead_time_label($despatch_ymd);
                            $fee_display = $shipment['total_fee'] > 0 ? '+ £' . number_format($shipment['total_fee'], 2) : 'None';
                        ?>
                            <tr>
                                <td class="delivery-options-shipment__display-results">
                                    <?php echo esc_html($shipment['date'] . ' ' . $lead_time_label); ?>
                                </td>
                                <td class="delivery-options-shipment__display-results">
                                    <?php echo esc_html($shipment['parts']); ?>
                                </td>
                                <td class="delivery-options-shipment__display-results"> 
                                        <?php echo esc_html($fee_display); ?>
                                </td>
                                <td class="delivery-options-shipment__display-results">
                                    <a href="#" class="delete-shipment delivery-options-shipment__delete" data-index="<?php echo $index; ?>"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        <?php $custom_shipments = WC()->session->get( 'custom_shipments', [] ); echo "<pre class='aaa'>"; print_r($custom_shipments); echo "</pre>"; ?>
    </div>
    <?php
    $table_html = ob_get_clean();

    wp_send_json_success([
        'table_html'       => $table_html,
        'remaining_parts'  => $remaining
    ]);
}

// HANDLE MODAL SUBMISSION FOR DELIVERY OPTIONS VIA AJAX


// HANDLE DELIVERY OPTIONS DELETE AJAX SHIPPING

add_action('wp_ajax_delete_shipment', 'delete_shipment');
add_action('wp_ajax_nopriv_delete_shipment', 'delete_shipment');

function delete_shipment() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    $index = intval($_POST['index']);
    $custom_qty = WC()->session->get('custom_qty', 0);
    $shipments = WC()->session->get('custom_shipments', []);

    if (!isset($shipments[$index])) {
        wp_send_json_error(['message' => 'Invalid shipment index.']);
        return;
    }

    // Remove the shipment
    array_splice($shipments, $index, 1);

    // Save updated shipments to session
    WC()->session->set('custom_shipments', $shipments);

    // Calculate remaining parts
    $total_parts = array_sum(array_column($shipments, 'parts'));
    $remaining_parts = max(0, $custom_qty - $total_parts);

    // Generate updated table HTML
    ob_start();
    ?>
    <div class="delivery-options-shipment__outer">
        <table class="delivery-options-shipment">
            <thead>
                <tr>
                    <th class="delivery-options-shipment__title">Despatch Date</th>
                    <th class="delivery-options-shipment__title">Total number of parts</th>
                    <th class="delivery-options-shipment__title">All COFC's & FAIR's</th> <!-- NEW -->
                    <th class="delivery-options-shipment__title"><span class="screen-reader-text">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($shipments)) { ?>
                    <tr class="delivery-options-shipment__display">
                        <td class="delivery-options-shipment__display-inner" colspan="4">There are no <span>shipments.</span></td>
                    </tr>
                <?php } else { ?>
                <?php //error_log("Shipments: " . print_r($shipments)) ?>
                        <?php foreach ($shipments as $index => $shipment) {
                            //error_log("Shipment: " . print_r($shipment));
                            $despatch_ymd = date('Y-m-d', strtotime(str_replace('/', '-', $shipment['date'])));
                            $lead_time_label = get_shipment_lead_time_label($despatch_ymd);
                            $fee_display = $shipment['total_fee'] > 0 ? '+ £' . number_format($shipment['total_fee'], 2) : 'None';
                        ?>
                            <tr>
                                <td class="delivery-options-shipment__display-results">
                                    <?php echo esc_html($shipment['date'] . ' ' . $lead_time_label); ?>
                                </td>
                                <td class="delivery-options-shipment__display-results">
                                    <?php echo esc_html($shipment['parts']); ?>
                                </td>
                                <td class="delivery-options-shipment__display-results"> <!-- NEW -->
                                        <?php echo esc_html($fee_display); ?>
                                </td>
                                <td class="delivery-options-shipment__display-results">
                                    <a href="#" class="delete-shipment delivery-options-shipment__delete" data-index="<?php echo $index; ?>"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>    
    <?php
    $table_html = ob_get_clean();

    wp_send_json_success([
        'table_html' => $table_html,
        'remaining_parts' => $remaining_parts
    ]);
}

// HANDLE DELIVERY OPTIONS DELETE AJAX SHIPPING


// GET THE remaining_parts VALUE FOR LATER USE ON THE DELIVERY OPTIONS MODAL
add_action('wp_ajax_get_remaining_parts', 'get_remaining_parts_callback');
add_action('wp_ajax_nopriv_get_remaining_parts', 'get_remaining_parts_callback'); // If needed for guests
function get_remaining_parts_callback() {
    check_ajax_referer('custom_price_nonce', 'nonce'); // Security

    $custom_qty = WC()->session->get('custom_qty', 0);
    $shipments = WC()->session->get('custom_shipments', []);
    $total_parts = array_sum(array_column($shipments, 'parts'));
    $remaining_parts = max(0, $custom_qty - $total_parts);

    wp_send_json_success(['remaining_parts' => $remaining_parts]);
}
// GET THE remaining_parts VALUE FOR LATER USE ON THE DELIVERY OPTIONS MODAL


// HANDLE TEMP PDF DELETE
function delete_temp_pdf() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    if (empty($_POST['pdf_path'])) {
        wp_send_json_error(['message' => 'No PDF path provided.']);
        return;
    }

    $pdf_path = sanitize_text_field($_POST['pdf_path']);
    $upload_dir = wp_upload_dir();
    $full_path = $upload_dir['basedir'] . $pdf_path;

    if (file_exists($full_path)) {
        if (unlink($full_path)) {
            wp_send_json_success(['message' => 'Temporary PDF deleted successfully.']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete temporary PDF.']);
        }
    } else {
        wp_send_json_success(['message' => 'PDF file does not exist.']);
    }
}
add_action('wp_ajax_delete_temp_pdf', 'delete_temp_pdf');
// HANDLE TEMP PDF DELETE



// RESET BUTTON

add_action('wp_ajax_reset_custom_shipments', 'reset_custom_shipments_callback');
add_action('wp_ajax_nopriv_reset_custom_shipments', 'reset_custom_shipments_callback');

function reset_custom_shipments_callback() {
    if (function_exists('WC') && WC()->session) {
        WC()->session->__unset('custom_shipments');
        WC()->session->__unset('custom_qty');
    }

    wp_send_json_success(['message' => 'Session cleared.']);
}

// RESET BUTTON


// Delivery options lead time helper
/**
 * Return formatted lead-time + discount string for a despatch date.
 */
function get_shipment_lead_time_label( $despatch_ymd ) {
    $today            = date( 'Y-m-d' );
    $today_ts         = strtotime( $today );
    $despatch_ts      = strtotime( $despatch_ymd );
    $calendar_days    = floor( ( $despatch_ts - $today_ts ) / 86400 );

    if ( $calendar_days <= 1 ) {
        $label = '24Hrs (working day)';
        $disc  = 0;
    } elseif ( $calendar_days <= 4 ) {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.015;
    } elseif ( $calendar_days <= 6 ) {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.02;
    } elseif ( $calendar_days <= 11 ) {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.025;
    } elseif ( $calendar_days <= 13 ) {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.03;
    } elseif ( $calendar_days <= 29 ) {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.035;
    } elseif ( $calendar_days <= 34 ) {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.04;
    } else {
        $label = $calendar_days . ' Days (working days)';
        $disc  = 0.05;
    }

    if ( $disc > 0 ) {
        $label .= ' (' . ( $disc * 100 ) . '% Discount)';
    }

    return $label;
}
// Delivery options lead time helper


// Delivery Options Discount helper
function get_shipment_lead_time_discount( $despatch_ymd ) {
    $today_d            = date( 'Y-m-d' );
    $today_ts_d        = strtotime( $today_d );
    $despatch_ts_d     = strtotime( $despatch_ymd );
    $calendar_days_d    = floor( ( $despatch_ts_d - $today_ts_d ) / 86400 );

    if ( $calendar_days_d <= 1 ) {
        $disc_d  = 0;
    } elseif ( $calendar_days_d <= 4 ) {
        $disc_d  = 0.015;
    } elseif ( $calendar_days_d <= 6 ) {
        $disc_d  = 0.02;
    } elseif ( $calendar_days_d <= 11 ) {
        $disc_d  = 0.025;
    } elseif ( $calendar_days_d <= 13 ) {
        $disc_d  = 0.03;
    } elseif ( $calendar_days_d <= 29 ) {
        $disc_d  = 0.035;
    } elseif ( $calendar_days_d <= 34 ) {
        $disc_d  = 0.04;
    } else {
        $disc_d  = 0.05;
    }

    return $disc_d;
}
// Delivery Options Discount helper

// make sure scheduled delivery 'custom_shipment' session is destroyed after 15 minutes
add_action('init', 'cleanup_expired_custom_shipments');

function cleanup_expired_custom_shipments() {
    if (!function_exists('WC') || !WC()->session) {
        return;
    }

    $last_activity = WC()->session->get('custom_shipments_last_activity');

    // If timestamp exists and is older than 15 minutes (900 seconds)
    if ($last_activity && (time() - $last_activity > 600)) {
        WC()->session->set('custom_shipments', []);
        WC()->session->__unset('custom_shipments_last_activity');
    }
}
// make sure scheduled delivery 'custom_shipment' session is destroyed after 15 minutes