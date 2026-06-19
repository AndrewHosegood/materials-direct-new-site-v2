<?php
// Get country data for select country and shipping calculations
function get_country_data() {
    return [

        // UNITED KINGDOM
        'United Kingdom' => ['code' => 'GB', 'group' => 'United Kingdom'],

        // EUROPE 1
        'Belgium' => ['code' => 'BE', 'group' => 'Europe_1'],
        'France' => ['code' => 'FR', 'group' => 'Europe_1'],
        'Germany' => ['code' => 'DE', 'group' => 'Europe_1'],
        'Monaco' => ['code' => 'MC', 'group' => 'Europe_1'],
        'Netherlands' => ['code' => 'NL', 'group' => 'Europe_1'],
        'Luxembourg' => ['code' => 'LU', 'group' => 'Europe_1'],
        'Ireland' => ['code' => 'IE', 'group' => 'Europe_1'],
        'Guernsey' => ['code' => 'GG', 'group' => 'Europe_1'],
        'Jersey' => ['code' => 'JE', 'group' => 'Europe_1'],

        // EUROPE 2
        'Andorra' => ['code' => 'AD', 'group' => 'Europe_2'],
        'Austria' => ['code' => 'AT', 'group' => 'Europe_2'],
        'Bulgaria' => ['code' => 'BG', 'group' => 'Europe_2'],
        'Croatia' => ['code' => 'HR', 'group' => 'Europe_2'],
        'Cyprus' => ['code' => 'CY', 'group' => 'Europe_2'],
        'Czechia' => ['code' => 'CZ', 'group' => 'Europe_2'],
        'Denmark' => ['code' => 'DK', 'group' => 'Europe_2'],
        'Estonia' => ['code' => 'EE', 'group' => 'Europe_2'],
        'Finland' => ['code' => 'FI', 'group' => 'Europe_2'],
        'Gibraltar' => ['code' => 'GI', 'group' => 'Europe_2'],
        'Greece' => ['code' => 'GR', 'group' => 'Europe_2'],
        'Hungary' => ['code' => 'HU', 'group' => 'Europe_2'],
        'Italy' => ['code' => 'IT', 'group' => 'Europe_2'],
        'Latvia' => ['code' => 'LV', 'group' => 'Europe_2'],
        'Lithuania' => ['code' => 'LT', 'group' => 'Europe_2'],
        'Malta' => ['code' => 'MT', 'group' => 'Europe_2'],
        'Poland' => ['code' => 'PL', 'group' => 'Europe_2'],
        'Portugal' => ['code' => 'PT', 'group' => 'Europe_2'],
        'Romania' => ['code' => 'RO', 'group' => 'Europe_2'],
        'San Marino' => ['code' => 'SM', 'group' => 'Europe_2'],
        'Slovakia' => ['code' => 'SK', 'group' => 'Europe_2'],
        'Slovenia' => ['code' => 'SI', 'group' => 'Europe_2'],
        'Spain' => ['code' => 'ES', 'group' => 'Europe_2'],
        'Sweden' => ['code' => 'SE', 'group' => 'Europe_2'],
        'Vatican City' => ['code' => 'VA', 'group' => 'Europe_2'],

        // EUROPE 3
        'Albania' => ['code' => 'AL', 'group' => 'Europe_3'],
        'Bosnia & Herzegovina' => ['code' => 'BA', 'group' => 'Europe_3'],
        'Faroe Islands' => ['code' => 'FO', 'group' => 'Europe_3'],
        'Greenland' => ['code' => 'GL', 'group' => 'Europe_3'],
        'Iceland' => ['code' => 'IS', 'group' => 'Europe_3'],
        'Israel' => ['code' => 'IL', 'group' => 'Europe_3'],
        'Liechtenstein' => ['code' => 'LI', 'group' => 'Europe_3'],
        'Montenegro' => ['code' => 'ME', 'group' => 'Europe_3'],
        'North Macedonia' => ['code' => 'MK', 'group' => 'Europe_3'],
        'Norway' => ['code' => 'NO', 'group' => 'Europe_3'],
        'Serbia' => ['code' => 'RS', 'group' => 'Europe_3'],
        'Switzerland' => ['code' => 'CH', 'group' => 'Europe_3'],
        'Turkey' => ['code' => 'TR', 'group' => 'Europe_3'],

        // AMERICA 1
        'United States' => ['code' => 'US', 'group' => 'America_1'],
        'Canada' => ['code' => 'CA', 'group' => 'America_1'],
        'Mexico' => ['code' => 'MX', 'group' => 'America_1'],

        // MIDDLE EAST
        'Bahrain' => ['code' => 'BH', 'group' => 'middle_east'],
        'Jordan' => ['code' => 'JO', 'group' => 'middle_east'],
        'Kuwait' => ['code' => 'KW', 'group' => 'middle_east'],
        'Oman' => ['code' => 'OM', 'group' => 'middle_east'],
        'Qatar' => ['code' => 'QA', 'group' => 'middle_east'],
        'Saudi Arabia' => ['code' => 'SA', 'group' => 'middle_east'],
        'United Arab Emirates' => ['code' => 'AE', 'group' => 'middle_east'],

        // AUSTRALASIA
        'Australia' => ['code' => 'AU', 'group' => 'australasia'],
        'New Zealand' => ['code' => 'NZ', 'group' => 'australasia'],
        'China' => ['code' => 'CN', 'group' => 'australasia'],
        'Hong Kong SAR China' => ['code' => 'HK', 'group' => 'australasia'],
        'India' => ['code' => 'IN', 'group' => 'australasia'],
        'Japan' => ['code' => 'JP', 'group' => 'australasia'],
        'Malaysia' => ['code' => 'MY', 'group' => 'australasia'],
        'Singapore' => ['code' => 'SG', 'group' => 'australasia'],
        'South Korea' => ['code' => 'KR', 'group' => 'australasia'],
        'South Africa' => ['code' => 'ZA', 'group' => 'australasia'],
        'Taiwan' => ['code' => 'TW', 'group' => 'australasia'],
        'Thailand' => ['code' => 'TH', 'group' => 'australasia'],

        // ASIA
        'Bangladesh' => ['code' => 'BD', 'group' => 'asia'],
        'Bhutan' => ['code' => 'BT', 'group' => 'asia'],
        'Brunei' => ['code' => 'BN', 'group' => 'asia'],
        'Cambodia' => ['code' => 'KH', 'group' => 'asia'],
        'Indonesia' => ['code' => 'ID', 'group' => 'asia'],
        'Laos' => ['code' => 'LA', 'group' => 'asia'],
        'Myanmar (Burma)' => ['code' => 'MM', 'group' => 'asia'],
        'Nepal' => ['code' => 'NP', 'group' => 'asia'],
        'Pakistan' => ['code' => 'PK', 'group' => 'asia'],
        'Philippines' => ['code' => 'PH', 'group' => 'asia'],
        'Sri Lanka' => ['code' => 'LK', 'group' => 'asia'],
        'Vietnam' => ['code' => 'VN', 'group' => 'asia'],

        // REST OF WORLD
        'Afghanistan' => ['code' => 'AF', 'group' => 'rest_of_world'],
        'Algeria' => ['code' => 'DZ', 'group' => 'rest_of_world'],
        'American Samoa' => ['code' => 'AS', 'group' => 'rest_of_world'],
        'Angola' => ['code' => 'AO', 'group' => 'rest_of_world'],
        'Anguilla' => ['code' => 'AI', 'group' => 'rest_of_world'],
        'Antigua & Barbuda' => ['code' => 'AG', 'group' => 'rest_of_world'],
        'Argentina' => ['code' => 'AR', 'group' => 'rest_of_world'],
        'Armenia' => ['code' => 'AM', 'group' => 'rest_of_world'],
        'Aruba' => ['code' => 'AW', 'group' => 'rest_of_world'],
        'Azerbaijan' => ['code' => 'AZ', 'group' => 'rest_of_world'],
        'Bahamas' => ['code' => 'BS', 'group' => 'rest_of_world'],
        'Barbados' => ['code' => 'BB', 'group' => 'rest_of_world'],
        'Belarus' => ['code' => 'BY', 'group' => 'rest_of_world'],
        'Belize' => ['code' => 'BZ', 'group' => 'rest_of_world'],
        'Benin' => ['code' => 'BJ', 'group' => 'rest_of_world'],
        'Bermuda' => ['code' => 'BM', 'group' => 'rest_of_world'],
        'Bolivia' => ['code' => 'BO', 'group' => 'rest_of_world'],
        'Botswana' => ['code' => 'BW', 'group' => 'rest_of_world'],
        'Brazil' => ['code' => 'BR', 'group' => 'rest_of_world'],
        'British Virgin Islands' => ['code' => 'VG', 'group' => 'rest_of_world'],
        'Burkina Faso' => ['code' => 'BF', 'group' => 'rest_of_world'],
        'Burundi' => ['code' => 'BI', 'group' => 'rest_of_world'],
        'Cameroon' => ['code' => 'CM', 'group' => 'rest_of_world'],
        'Cape Verde' => ['code' => 'CV', 'group' => 'rest_of_world'],
        'Cayman Islands' => ['code' => 'KY', 'group' => 'rest_of_world'],
        'Central African Republic' => ['code' => 'CF', 'group' => 'rest_of_world'],
        'Chad' => ['code' => 'TD', 'group' => 'rest_of_world'],
        'Chile' => ['code' => 'CL', 'group' => 'rest_of_world'],
        'Colombia' => ['code' => 'CO', 'group' => 'rest_of_world'],
        'Comoros' => ['code' => 'KM', 'group' => 'rest_of_world'],
        'Congo - Brazzaville' => ['code' => 'CG', 'group' => 'rest_of_world'],
        'Congo - Kinshasa' => ['code' => 'CD', 'group' => 'rest_of_world'],
        'Cook Islands' => ['code' => 'CK', 'group' => 'rest_of_world'],
        'Costa Rica' => ['code' => 'CR', 'group' => 'rest_of_world'],
        'Cote dIvoire' => ['code' => 'CI', 'group' => 'rest_of_world'],
        'Cuba' => ['code' => 'CU', 'group' => 'rest_of_world'],
        'Curaçao' => ['code' => 'CW', 'group' => 'rest_of_world'],
        'Djibouti' => ['code' => 'DJ', 'group' => 'rest_of_world'],
        'Dominica' => ['code' => 'DM', 'group' => 'rest_of_world'],
        'Dominican Republic' => ['code' => 'DO', 'group' => 'rest_of_world'],
        'Ecuador' => ['code' => 'EC', 'group' => 'rest_of_world'],
        'Egypt' => ['code' => 'EG', 'group' => 'rest_of_world'],
        'El Salvador' => ['code' => 'SV', 'group' => 'rest_of_world'],
        'Equatorial Guinea' => ['code' => 'GQ', 'group' => 'rest_of_world'],
        'Eritrea' => ['code' => 'ER', 'group' => 'rest_of_world'],
        'Ethiopia' => ['code' => 'ET', 'group' => 'rest_of_world'],
        'Falkland Islands' => ['code' => 'FK', 'group' => 'rest_of_world'],
        'Fiji' => ['code' => 'FJ', 'group' => 'rest_of_world'],
        'French Guiana' => ['code' => 'GF', 'group' => 'rest_of_world'],
        'Gabon' => ['code' => 'GA', 'group' => 'rest_of_world'],
        'Gambia' => ['code' => 'GM', 'group' => 'rest_of_world'],
        'Georgia' => ['code' => 'GE', 'group' => 'rest_of_world'],
        'Ghana' => ['code' => 'GH', 'group' => 'rest_of_world'],
        'Grenada' => ['code' => 'GD', 'group' => 'rest_of_world'],
        'Guadeloupe' => ['code' => 'GP', 'group' => 'rest_of_world'],
        'Guam' => ['code' => 'GU', 'group' => 'rest_of_world'],
        'Guatemala' => ['code' => 'GT', 'group' => 'rest_of_world'],
        'Guinea' => ['code' => 'GN', 'group' => 'rest_of_world'],
        'Guinea-Bissau' => ['code' => 'GW', 'group' => 'rest_of_world'],
        'Guyana' => ['code' => 'GY', 'group' => 'rest_of_world'],
        'Haiti' => ['code' => 'HT', 'group' => 'rest_of_world'],
        'Honduras' => ['code' => 'HN', 'group' => 'rest_of_world'],
        'Iran' => ['code' => 'IR', 'group' => 'rest_of_world'],
        'Iraq' => ['code' => 'IQ', 'group' => 'rest_of_world'],
        'Jamaica' => ['code' => 'JM', 'group' => 'rest_of_world'],
        'Kazakhstan' => ['code' => 'KZ', 'group' => 'rest_of_world'],
        'Kenya' => ['code' => 'KE', 'group' => 'rest_of_world'],
        'Kiribati' => ['code' => 'KI', 'group' => 'rest_of_world'],
        'Kyrgyzstan' => ['code' => 'KG', 'group' => 'rest_of_world'],
        'Lebanon' => ['code' => 'LB', 'group' => 'rest_of_world'],
        'Lesotho' => ['code' => 'LS', 'group' => 'rest_of_world'],
        'Liberia' => ['code' => 'LR', 'group' => 'rest_of_world'],
        'Libya' => ['code' => 'LY', 'group' => 'rest_of_world'],
        'Madagascar' => ['code' => 'MG', 'group' => 'rest_of_world'],
        'Malawi' => ['code' => 'MW', 'group' => 'rest_of_world'],
        'Maldives' => ['code' => 'MV', 'group' => 'rest_of_world'],
        'Mali' => ['code' => 'ML', 'group' => 'rest_of_world'],
        'Marshall Islands' => ['code' => 'MH', 'group' => 'rest_of_world'],
        'Martinique' => ['code' => 'MQ', 'group' => 'rest_of_world'],
        'Mauritania' => ['code' => 'MR', 'group' => 'rest_of_world'],
        'Mauritius' => ['code' => 'MU', 'group' => 'rest_of_world'],
        'Mayotte' => ['code' => 'YT', 'group' => 'rest_of_world'],
        'Micronesia' => ['code' => 'FM', 'group' => 'rest_of_world'],
        'Moldova' => ['code' => 'MD', 'group' => 'rest_of_world'],
        'Mongolia' => ['code' => 'MN', 'group' => 'rest_of_world'],
        'Montserrat' => ['code' => 'MS', 'group' => 'rest_of_world'],
        'Morocco' => ['code' => 'MA', 'group' => 'rest_of_world'],
        'Mozambique' => ['code' => 'MZ', 'group' => 'rest_of_world'],
        'Namibia' => ['code' => 'NA', 'group' => 'rest_of_world'],
        'Nauru' => ['code' => 'NR', 'group' => 'rest_of_world'],
        'New Caledonia' => ['code' => 'NC', 'group' => 'rest_of_world'],
        'Nicaragua' => ['code' => 'NI', 'group' => 'rest_of_world'],
        'Niger' => ['code' => 'NE', 'group' => 'rest_of_world'],
        'Nigeria' => ['code' => 'NG', 'group' => 'rest_of_world'],
        'Niue' => ['code' => 'NU', 'group' => 'rest_of_world'],
        'North Korea' => ['code' => 'KP', 'group' => 'rest_of_world'],
        'Northern Mariana Islands' => ['code' => 'MP', 'group' => 'rest_of_world'],
        'Palau' => ['code' => 'PW', 'group' => 'rest_of_world'],
        'Panama' => ['code' => 'PA', 'group' => 'rest_of_world'],
        'Papua New Guinea' => ['code' => 'PG', 'group' => 'rest_of_world'],
        'Paraguay' => ['code' => 'PY', 'group' => 'rest_of_world'],
        'Peru' => ['code' => 'PE', 'group' => 'rest_of_world'],
        'Puerto Rico' => ['code' => 'PR', 'group' => 'rest_of_world'],
        'Reunion' => ['code' => 'RE', 'group' => 'rest_of_world'],
        'Russia' => ['code' => 'RU', 'group' => 'rest_of_world'],
        'Rwanda' => ['code' => 'RW', 'group' => 'rest_of_world'],
        'Samoa' => ['code' => 'WS', 'group' => 'rest_of_world'],
        'Sao Tome & Principe' => ['code' => 'ST', 'group' => 'rest_of_world'],
        'Senegal' => ['code' => 'SN', 'group' => 'rest_of_world'],
        'Seychelles' => ['code' => 'SC', 'group' => 'rest_of_world'],
        'Sierra Leone' => ['code' => 'SL', 'group' => 'rest_of_world'],
        'Sint Maarten' => ['code' => 'SX', 'group' => 'rest_of_world'],
        'Solomon Islands' => ['code' => 'SB', 'group' => 'rest_of_world'],
        'Somalia' => ['code' => 'SO', 'group' => 'rest_of_world'],
        'South Sudan' => ['code' => 'SS', 'group' => 'rest_of_world'],
        'St. Barthelemy' => ['code' => 'BL', 'group' => 'rest_of_world'],
        'St. Helena' => ['code' => 'SH', 'group' => 'rest_of_world'],
        'St. Kitts & Nevis' => ['code' => 'KN', 'group' => 'rest_of_world'],
        'St. Lucia' => ['code' => 'LC', 'group' => 'rest_of_world'],
        'St. Vincent & Grenadines' => ['code' => 'VC', 'group' => 'rest_of_world'],
        'Sudan' => ['code' => 'SD', 'group' => 'rest_of_world'],
        'Suriname' => ['code' => 'SR', 'group' => 'rest_of_world'],
        'Syria' => ['code' => 'SY', 'group' => 'rest_of_world'],
        'Tajikistan' => ['code' => 'TJ', 'group' => 'rest_of_world'],
        'Tanzania' => ['code' => 'TZ', 'group' => 'rest_of_world'],
        'Timor-Leste' => ['code' => 'TL', 'group' => 'rest_of_world'],
        'Togo' => ['code' => 'TG', 'group' => 'rest_of_world'],
        'Tonga' => ['code' => 'TO', 'group' => 'rest_of_world'],
        'Trinidad & Tobago' => ['code' => 'TT', 'group' => 'rest_of_world'],
        'Tunisia' => ['code' => 'TN', 'group' => 'rest_of_world'],
        'Turkmenistan' => ['code' => 'TM', 'group' => 'rest_of_world'],
        'Turks & Caicos Islands' => ['code' => 'TC', 'group' => 'rest_of_world'],
        'Tuvalu' => ['code' => 'TV', 'group' => 'rest_of_world'],
        'U.S. Virgin Islands' => ['code' => 'VI', 'group' => 'rest_of_world'],
        'Uganda' => ['code' => 'UG', 'group' => 'rest_of_world'],
        'Ukraine' => ['code' => 'UA', 'group' => 'rest_of_world'],
        'Uruguay' => ['code' => 'UY', 'group' => 'rest_of_world'],
        'Uzbekistan' => ['code' => 'UZ', 'group' => 'rest_of_world'],
        'Vanuatu' => ['code' => 'VU', 'group' => 'rest_of_world'],
        'Venezuela' => ['code' => 'VE', 'group' => 'rest_of_world'],
        'Yemen' => ['code' => 'YE', 'group' => 'rest_of_world'],
        'Zambia' => ['code' => 'ZM', 'group' => 'rest_of_world'],
        'Zimbabwe' => ['code' => 'ZW', 'group' => 'rest_of_world'],
    ];
}
// Get country data for select country and shipping calculations



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

    // new
    // Load the product once — this is the reliable way
    $product = wc_get_product($product_id);
    if (!$product) {
        return new WP_Error('invalid_product', 'Invalid product ID');
    }

    // Now safely get shipping dimensions
    $shipping_length = $product->get_length();


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
        $sheets_gpm = 0.5; 
    }
    if(get_field('rolls_gpm', $product_id)){
        $rolls_gpm = floatval(get_field('rolls_gpm', $product_id));
    } else {
        $rolls_gpm = 0.5;
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
        /* new rolls calculation */
        $shipping_length_value = 100 / $shipping_length;
        $finalPppOnAva = $shipping_length_value * ($finalPppOnAva / $rolls_gpm);
         /* new rolls calculation */
        
		//$finalPppOnAva = $finalPppOnAva / $rolls_gpm;
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

    $clear_nonce = wp_create_nonce('clear_custom_shipping_address_nonce');  // unique action name to avoid conflicts

    // Get the ACF field value
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product->get_id()) : false;
    $product_id = $product->get_id();
    $shipping_address = WC()->session->get('custom_shipping_address', []);
    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id); // get the ACF Users Credits options
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false; // get the ACF Users Credits options
    $current_user = wp_get_current_user(); // Get the current user role
    $is_creditor = in_array('creditor', (array) $current_user->roles, true); // Get the current user role
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
        if(WC()->session->get('custom_shipping_address')){
            echo '<div id="shipping-address-form-single" class="shipping-address-form__hide">';
        } else {
            echo '<div id="shipping-address-form-single">';
        }

        echo '<h3 class="product-page__subheading">Item(s) shipping address<span class="gfield_required gfield_required_asterisk">*</span></h3>
            <p class="address-lookup__text" style="">Please ensure your shipping address is entered correctly here. Your order may be cancelled if an incorrect country has been entered. See our <a target="_blank" href="/terms-and-conditions/#shipping">terms and conditions</a> for more information.</p>
            <label class="custom-price-calc__label product-page__address-1"><input class="product-page__calc-input" type="text" id="input_street_address" name="custom_street_address" placeholder="Street Address" value="' . $street_address . '" required></label>
            <label class="custom-price-calc__label product-page__address-2"><input class="product-page__calc-input" type="text" id="input_address_line2" name="custom_address_line2" placeholder="Address Line 2" value="' . $address_line2 . '"></label>
            <label class="custom-price-calc__label product-page__address-3"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_city" name="custom_city" placeholder="City" value="' . $city . '" required></label>
            <label class="custom-price-calc__label product-page__address-4"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_county_state" name="custom_county_state" placeholder="County/State" value="' . $county_state . '" required></label>
            <label class="custom-price-calc__label product-page__address-5"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_zip_postal" name="custom_zip_postal" placeholder="ZIP/Postal Code" value="' . $zip_postal . '" required></label>';
        
            echo '<label class="custom-price-calc__label product-page__address-6">';

            echo  '<select id="input_country" class="product-page__calc-input product-page__calc-input-small" name="custom_country" required>';

            $country_data = get_country_data();
            ksort($country_data); // Optional: sort alphabetically for nicer UX
            foreach ($country_data as $country_name => $data) {
                echo '<option value="' . esc_attr($country_name) . '" ' . selected($country, $country_name, false) . '>' 
                    . esc_html($country_name) . 
                '</option>';
            }
            echo '</select>';
            
            if (WC()->session->get('custom_shipping_address')) {
                echo '<input type="hidden" id="saved_country" name="custom_country" value="' . esc_attr($country) . '">';
            }


            echo '</label>';
            echo '</div>';

            if(WC()->session->get('custom_shipping_address')){
                $address = WC()->session->get('custom_shipping_address');
                echo '<div class="shipping-address-form__saved">';
                echo '<h3 class="product-page__subheading">Item(s) shipping address?<span class="gfield_required gfield_required_asterisk">*</span></h3>';
                echo '<p class="shipping-address-form__saved-content">';
                if($address['street_address']){ echo $address['street_address'] . "<br>"; }
                if($address['address_line2']){ echo $address['address_line2'] . "<br>"; }
                if($address['city']){ echo $address['city'] . "<br>"; }
                if($address['county_state']){ echo $address['county_state'] . "<br>"; }
                if($address['zip_postal']){ echo $address['zip_postal'] . "<br>"; }
                if($address['country']){ echo $address['country'] . "<br>"; }
                echo '</p>';
                echo '<a class="shipping-address-form__clear-address" id="clear-shipping-address">Clear Address</a>';
                echo '</div>';
            }
        ?>    
        <?php if (WC()->session->get('custom_shipping_address')) : ?>
        <script>
            jQuery(document).ready(function($) {
                $(document).on('click', '#clear-shipping-address', function(e) {
                    e.preventDefault();
                    if (!confirm('Are you sure you want to clear the saved shipping address?')) {
                        return;
                    }

                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'clear_custom_shipping_address',
                            nonce: '<?php echo esc_js($clear_nonce); ?>'
                        },
                        success: function(response) {

                            if (response.success) {
                                $('input[type="hidden"][name="custom_country"]').remove();
                                $('.shipping-address-form__saved').remove();
                                $('#input_street_address').val('');
                                $('#input_address_line2').val('');
                                $('#input_city').val('');
                                $('#input_county_state').val('');
                                $('#input_zip_postal').val('');
                                $('#input_country').val('United Kingdom');
                                $('#shipping-address-form-single').removeClass('shipping-address-form__hide');
                            } else {
                                alert('Error clearing address. Please try again.');
                            }
                        },
                        error: function() {
                            alert('Ajax error. Please refresh the page.');
                        }
                    });
                });
            });
        </script>
        <?php endif; ?>
        <script>
            // force the selecet menu in the DOM to use the selected country instead of United Kingdowm
            jQuery(function($){
                var savedCountry = $('#saved_country').val();
                if(savedCountry){
                    $('#input_country').val(savedCountry);
                }
            });
        </script>
        <?php
        echo '<input type="hidden" id="custom_price" name="custom_price" value="">';
        echo '<div id="custom_price_display"></div>';
        return;
    }

    // Check stock quantity for backorder status
    $stock_quantity = $product->get_stock_quantity();
    $is_full_backorder = $stock_quantity <= 0;

    //$roll_length_v = $roll_length / 1000;

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

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Simple<br>Circle
        <input class="product-page__tabs-input" name="tabs_input" type="radio" value="circle-radius" id="circle-radius" tabindex="0">
        </label></li>

        <li class="product-page__tabs-list"><label class="product-page__tabs-label">Simple Square<br>Rectangle
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

        <p class="product-page__square-rectangle-message"><i class="fa-solid fa-circle-info product-page__square-rectangle-message-icon"></i> You are asking us to manufacture a <span id="tabs_status_message">custom shape</span><span id="tabs_status_message_2">. Enter your values below</p>';
        

        echo '<!-- File Upload Fields -->
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
            <label for="use_inches">Choose Inches</label>
        </div>
        <div id="choose_inches_radius" class="product-page__input-wrap unstyled centered" style="width: 100%;">
            <input type="checkbox" id="use_inches_radius" class="styled-checkbox" name="conversion_factor_radius" value="25.4">
            <label for="use_inches_radius">Choose Inches (Radius)</label>
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

        $no_cofc = function_exists('get_field') ? get_field('no_cofc', $product->get_id()) : '';
        $is_cofc_disabled = ($no_cofc === 'yes');
        $cofc_disabled_attr = $is_cofc_disabled ? 'disabled="disabled"' : '';
        $cofc_wrapper_class = $is_cofc_disabled ? 'cofc-disabled' : '';

        $no_fair = function_exists('get_field') ? get_field('no_fair', $product->get_id()) : '';
        $is_fair_disabled = ($no_fair === 'yes');
        $fair_disabled_attr = $is_fair_disabled ? 'disabled="disabled"' : '';
        $fair_wrapper_class = $is_fair_disabled ? 'fair-disabled' : '';

        $no_mcofc = function_exists('get_field') ? get_field('no_mcofc', $product->get_id()) : '';
        $is_mcofc_disabled = ($no_mcofc === 'yes');
        $mcofc_disabled_attr = $is_mcofc_disabled ? 'disabled="disabled"' : '';
        $mcofc_wrapper_class = $is_mcofc_disabled ? 'mcofc-disabled' : '';

        echo '<div id="cofc_hide_show"><div class="product-page__optional-fees" style="display:none;">

            <h4 class="product-page__optional-fees-title">Do you require these addons with your product?</h4>

            <div class="product-page__checkbox-label unstyled '.$cofc_wrapper_class.'">
                <p class="product-page__checkbox-title">Add Manufacturers COFC</p>';

                if($no_cofc === 'yes'){
                    echo '<p class="product-page__cofc-not-available">Not available for this product</p>';
                }

        echo '<input type="checkbox" name="add_manufacturers_COFC" value="10" class="styled-checkbox" id="add_manufacturers_COFC" '.$cofc_disabled_attr.'>
                <label for="add_manufacturers_COFC">
                <span class="product-page__checkbox-heading">Manufacturers COFC <span class="product-page__checkbox-price">£10</span>
                    <span class="cfc__tooltip" data-tooltip="A Manufacturers Certificate of Conformity (MCOFC) is a document that manufacturers issue to confirm that a product has been made to a specific standard and meets quality and regulatory requirements.">?</span>
                </span>
                </label>
            </div><br>

            <div id="fair_label" class="product-page__checkbox-label unstyled '.$fair_wrapper_class.'">
                <p class="product-page__checkbox-title">Add First Article Inspection Report</p>';

                if($no_fair === 'yes'){
                    echo '<p class="product-page__cofc-not-available">Not available for this product</p>';
                }

        echo '<input type="checkbox" name="add_fair" value="95" class="styled-checkbox" id="add_fair" '.$fair_disabled_attr.'>
                <label for="add_fair">
                <span class="product-page__checkbox-heading">FAIR <span class="product-page__checkbox-price">£95</span>
                    <span class="cfc__tooltip" data-tooltip="A First Article Inspection Report (FAIR) or ISIR is the first item we make for the customer and measure to confirm all dimensions meet the drawing and tolerances.">?</span>
                </span>
                </label>
            </div><br>

            <div class="product-page__checkbox-label unstyled '.$mcofc_wrapper_class.'">
                <p class="product-page__checkbox-title">Add Materials Direct COFC?</p>';

                if($no_mcofc === 'yes'){
                    echo '<p class="product-page__cofc-not-available">Not available for this product</p>';
                }

        echo '<input type="checkbox" name="add_materials_direct_COFC" value="12.50" class="styled-checkbox" id="add_materials_direct_COFC" '.$mcofc_disabled_attr.'>
                <label for="add_materials_direct_COFC">
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
        if ( is_user_logged_in() && $allow_credit && !is_admin() ) {
                $post_id = get_the_ID();
                $sold_as_roll_length_value = get_field('sold_as_roll_length', $post_id);
                if (!empty($sold_as_roll_length_value)) {
                    $sold_as_roll_length = $sold_as_roll_length_value;
                }
                echo '<div id="shipments_display" style="display: none; padding: 0.4rem 0.99rem; background: #efefef; border: 2px solid #ddd;">
                    <a href="#" id="add_shipments" data-id="'.$sold_as_roll_length.'" class="product-page__shipments-btn">Add Shipment(s)</a>
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
            <label class="custom-price-calc__label product-page__address-1"><input class="product-page__calc-input" type="text" id="input_street_address" name="custom_street_address" placeholder="Street Address" value="' . $street_address . '" required></label>
            <label class="custom-price-calc__label product-page__address-2"><input class="product-page__calc-input" type="text" id="input_address_line2" name="custom_address_line2" placeholder="Address Line 2" value="' . $address_line2 . '"></label>
            <label class="custom-price-calc__label product-page__address-3"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_city" name="custom_city" placeholder="City" value="' . $city . '" required></label>
            <label class="custom-price-calc__label product-page__address-4"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_county_state" name="custom_county_state" placeholder="County/State" value="' . $county_state . '" required></label>
            <label class="custom-price-calc__label product-page__address-5"><input class="product-page__calc-input product-page__calc-input-small" type="text" id="input_zip_postal" name="custom_zip_postal" placeholder="ZIP/ Postal Code" value="' . $zip_postal . '" required></label>';
            
        echo '<label class="custom-price-calc__label product-page__address-6">'; 
            
            
            echo '<select id="input_country" class="product-page__calc-input product-page__calc-input-small" name="custom_country" required>';
            $country_data = get_country_data();
            
            ksort($country_data); 
            
            foreach ($country_data as $country_name => $data) {
                echo '<option value="' . esc_attr($country_name) . '" ' . selected($country, $country_name, false) . '>' 
                    . esc_html($country_name) . 
                '</option>';
            }
            
            echo '</select>';

            if (WC()->session->get('custom_shipping_address')) {
                echo '<input type="hidden" id="saved_country" name="custom_country" value="' . esc_attr($country) . '">';
            }
        

        echo '</label>';
        echo '</div>';


        if(WC()->session->get('custom_shipping_address')){
            $address = WC()->session->get('custom_shipping_address');
            echo '<div class="shipping-address-form__saved">';
            echo '<h3 class="product-page__subheading">Item(s) shipping address?<span class="gfield_required gfield_required_asterisk">*</span></h3>';
            echo '<p class="shipping-address-form__saved-content">';
                if($address['street_address']){ echo $address['street_address'] . "<br>"; }
                if($address['address_line2']){ echo $address['address_line2'] . "<br>"; }
                if($address['city']){ echo $address['city'] . "<br>"; }
                if($address['county_state']){ echo $address['county_state'] . "<br>"; }
                if($address['zip_postal']){ echo $address['zip_postal'] . "<br>"; }
                if($address['country']){ echo $address['country'] . "<br>"; }
            echo '</p>';
            echo '<a class="shipping-address-form__clear-address" id="clear-shipping-address">Clear Address</a>';
            echo '</div>';
        }
       

    echo '</div>';
    ?>
    <?php if (WC()->session->get('custom_shipping_address')) : ?>
    <script>
    jQuery(document).ready(function($) {
        $(document).on('click', '#clear-shipping-address', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to clear the saved shipping address?')) {
                return;
            }

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'clear_custom_shipping_address',
                    nonce: '<?php echo esc_js($clear_nonce); ?>'
                },
                success: function(response) {

                    if (response.success) {
                        $('input[type="hidden"][name="custom_country"]').remove();
                        $('.shipping-address-form__saved').remove();
                        $('#input_street_address').val('');
                        $('#input_address_line2').val('');
                        $('#input_city').val('');
                        $('#input_county_state').val('');
                        $('#input_zip_postal').val('');
                        $('#input_country').val('United Kingdom');
                        $('#shipping-address-form').removeClass('shipping-address-form__hide');
                    } else {
                        alert('Error clearing address. Please try again.');
                    }
                },
                error: function() {
                    alert('Ajax error. Please refresh the page.');
                }
            });
        });
    });
    </script>
    <?php endif; ?>
    <script>
        // force the selecet menu in the DOM to use the selected country instead of United Kingdowm
        jQuery(function($){
            var savedCountry = $('#saved_country').val();
            if(savedCountry){
                $('#input_country').val(savedCountry);
            }
        });
    </script>
<?php
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

    if ($is_product_single) {
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Invalid product ID.']);
            return;
        }
        $price = $product->get_price(); 
        wp_send_json_success([
            'price' => round($price, 2),
            'per_part' => round($price, 2), 
            'sheets_required' => 1, 
            'stock_quantity' => $product->get_stock_quantity(),
            'is_backorder' => false, 
            'border_around' => 0.2 
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

    if (!is_numeric($product_id) || $width <= 0 || $length <= 0 || $qty < 1) {
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



    // Dynamically calculate sheets required
    $sheet_result = calculate_sheets_required(
        $sheet_width_mm,
        $sheet_length_mm,
        $width,
        $length,
        $qty,
        $shape_type,
        $product_id
    );

    $sheets_required = $sheet_result['sheets_required'];
    

    /* If rolls are selected apply fix  */
    $sheets_required_rolls = $sheet_result['sheets_required'] * $roll_length_v;
    if($shape_type === "rolls"){
        $is_backorder = $sheets_required_rolls > $stock_quantity;
    } else {
        $is_backorder = $sheets_required > $stock_quantity;
    }
    /* If rolls are selected apply fix  */

    // === ROLLS FULL BACKORDER LOGIC (NEW) ===
    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id);
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false;

    $is_full_backorder_rolls = false;

    if ($shape_type === 'rolls' && $is_backorder) {
        $is_full_backorder_rolls = true;
        $discount_rate = 0.05;   // Blanket 5% discount for Rolls full backorder
    }
    // === ROLLS FULL BACKORDER LOGIC (NEW) ===

    $total_price = calculate_product_price($product_id, $width, $length, $qty, $discount_rate, $shape_type);

    if (is_wp_error($total_price)) {
        wp_send_json_error(['message' => $total_price->get_error_message()]);
        return;
    }

    $per_part_price = $total_price / $qty;

    // SEND DATA TO algorith-core-functionality.js
    wp_send_json_success([
        'price' => round($total_price, 2),
        'per_part' => $per_part_price,
        'sheets_required' => $sheets_required,
        'stock_quantity' => $stock_quantity,
        'is_backorder' => $is_backorder,
        'is_full_backorder_rolls' => $is_full_backorder_rolls,
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

    $total_optional_fees = 0; 
    foreach ($shipments as $s) { 
        $total_optional_fees += $s['total_fee'] ?? 0; 
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

    $sheet_result = calculate_sheets_required($sheet_width_mm, $sheet_length_mm, $width, $length, $qty, $shape_type, $product_id);
    $sheets_required = $sheet_result['sheets_required'];
    $is_backorder = false; // No backorder for scheduled deliveries


    wp_send_json_success([
        'price' => round($total_scheduled_price, 2),
        'per_part' => round($total_scheduled_price / $qty, 6), 
        'per_part_base' => $per_part_base,
        'total_optional_fees' => $total_optional_fees, 
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

                    if (isset($shipping_by_date[$date])) {
                        $shipping_by_date[$date]['quantity'] += 1;
                        $shipping_by_date[$date]['total_del_weight'] += $portion_weight;
                        $shipping_by_date[$date]['country'] = $country;
                    } else {
                        $shipping_by_date[$date] = [
                            'quantity' => 1,
                            'total_del_weight' => $portion_weight,
                            'country' => $country,
                        ];
                    }
                }

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
                                $shipping_by_date[$date]['country'] = $country;
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
                        $shipping_by_date[$date]['country'] = $country;
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
        'Europe_1' => [ // Shared tiers for France, Germany, Monaco etc
            [0, 0.5, 38.01],
            [0.5, 1, 39.83],
            [1, 1.5, 41.66],
            [1.5, 2.0, 43.42],
            [2.0, 2.5, 45.01],
            [2.5, 3, 46.59],
            [3, 3.5, 48.16],
            [3.5, 4, 49.75],
            [4, 4.5, 51.31],
            [4.5, 5, 52.86],
            [5, 5.5, 54.41],
            [5.5, 6, 55.94],
            [6, 6.5, 57.49],
            [6.5, 7, 59.04],
            [7, 7.5, 60.57],
            [7.5, 8, 62.12],
            [8, 8.5, 63.65],
            [8.5, 9, 65.20],
            [9, 9.5, 66.75],
            [9.5, 10, 67.78],
            [10, 15, 78.10],
            [15, 20, 88.61],
            [20, 25, 100.71],
            [25, 30, 114.54],
            [30, 35, 129.27],
            [35, 40, 143.99],
            [40, 45, 158.72],
            [45, 50, 173.43],
            [50, 60, 202.89],
            [60, 70, 233.82],
            [70, 80, 278.13],
            [80, 90, 322.45],
            [90, 100, 366.76],
            [100, 110, 411.08],
            [110, 120, 455.39],
            [120, 130, 499.71],
            [130, 140, 544.02],
            [140, 150, 588.34],
            [150, 160, 632.65],
            [160, 170, 676.97],
            [170, 180, 721.28],
            [180, 190, 765.59],
            [190, 200, 809.91],
            [200, 210, 854.22],
            [210, 220, 898.54],
            [220, 230, 942.85],
            [230, 240, 987.17],
            [240, 250, 1031.48],
            [250, 260, 1075.80],
            [260, 270, 1120.11],
            [270, 280, 1164.43],
            [280, 290, 1208.74],
            [290, 299, 1248.63],
            [299, PHP_INT_MAX, 1288.74],
        ],
        'Europe_2' => [ // Shared tiers for Spain, Sweden, Vatican City etc
            [0, 0.5,  43.34],
            [0.5, 1, 47.38],
            [1, 1.5, 51.41],
            [1.5, 2, 52.94],
            [2, 2.5, 55.14],
            [2.5, 3, 57.33],
            [3, 3.5, 59.53],
            [3.5, 4, 61.71],
            [4, 4.5, 63.90],
            [4.5, 5, 65.73],
            [5, 5.5, 67.54],
            [5.5, 6, 69.35],
            [6, 6.5, 71.18],
            [6.5, 7, 72.99],
            [7, 7.5, 74.80],
            [7.5, 8, 76.63],
            [8, 8.5, 78.44],
            [8.5, 9, 80.27],
            [9, 9.5, 82.08],
            [9.5, 10, 83.35],
            [10, 15, 96.15],
            [15, 20, 109.18],
            [20, 25, 124.36],
            [25, 30, 142.18],
            [30, 35, 162.98],
            [35, 40, 184.77],
            [40, 45, 204.59],
            [45, 50, 225.38],
            [50, 60, 266.99],
            [60, 70, 309.74],
            [70, 80, 362.67],
            [80, 90, 415.61],
            [90, 100, 468.55],
            [100, 110,521.49],
            [110, 120, 574.43],
            [120, 130, 627.37],
            [130, 140, 680.31],
            [140, 150, 733.25],
            [150, 160, 786.18],
            [160, 170, 839.12],
            [170, 180, 892.06],
            [180, 190, 945.00],
            [190, 200, 997.94],
            [200, 210, 1050.88],
            [210, 220, 1103.82],
            [220, 230, 1156.75],
            [230, 240, 1209.69],
            [240, 250, 1262.63],
            [250, 260, 1315.57],
            [260, 270, 1368.51],
            [270, 280, 1421.45],
            [280, 290, 1474.39],
            [290, 299, 1522.02],
            [299,  PHP_INT_MAX, 1575.39],
        ],
        'Europe_3' => [ // Shared tiers for Greenland, Iceland, Isreal etc
            [0, 0.5, 45.47],
            [0.5, 1, 49.90],
            [1, 1.5, 54.32],
            [1.5, 2, 57.72],
            [2, 2.5, 61.25],
            [2.5, 3, 64.77],
            [3, 3.5, 68.28],
            [3.5, 4, 71.81],
            [4, 4.5, 75.34],
            [4.5, 5, 78.16],
            [5, 5.5, 80.99],
            [5.5, 6, 83.83],
            [6, 6.5, 86.65],
            [6.5, 7, 89.49],
            [7, 7.5, 92.33],
            [7.5, 8, 95.14],
            [8, 8.5, 97.98],
            [8.5, 9, 100.82],
            [9, 9.5, 103.66],
            [9.5, 10, 106.10],
            [10, 15, 130.69],
            [15, 20, 154.65],
            [20, 25, 173.08],
            [25, 30, 195.29],
            [30, 35, 223.46],
            [35, 40, 251.63],
            [40, 45, 279.81],
            [45, 50, 307.98],
            [50, 60, 364.32],
            [60, 70, 422.15],
            [70, 80, 493.23],
            [80, 90, 564.33],
            [90, 100, 635.41],
            [100, 110, 706.51],
            [110, 120, 777.60],
            [120, 130, 848.68],
            [130, 140, 919.78],
            [140, 150, 990.86],
            [150, 160, 1061.97],
            [160, 170, 1133.05],
            [170, 180, 1204.13],
            [180, 190, 1275.23],
            [190, 200, 1346.31],
            [200, 210, 1417.42],
            [210, 220, 1488.50],
            [220, 230, 1559.58],
            [230, 240, 1630.68],
            [240, 250, 1701.77],
            [250, 260, 1772.87],
            [260, 270, 1843.95],
            [270, 280, 1915.03],
            [280, 290, 1986.13],
            [290, 299, 2050.10],
            [299, PHP_INT_MAX, 2120.29],
        ],
        'America_1' => [ // Shared tiers for USA etc
            [0, 0.5, 37.97],
            [0.5, 1, 41.79],
            [1, 1.5, 45.64],
            [1.5, 2, 51.02],
            [2, 2.5, 54.15],
            [2.5, 3, 57.29],
            [3, 3.5, 60.41],
            [3.5, 4, 63.54],
            [4, 4.5, 66.68],
            [4.5, 5, 69.81],
            [5, 5.5, 72.97],
            [5.5, 6, 76.12],
            [6, 6.5, 79.26],
            [6.5, 7, 82.41],
            [7, 7.5, 85.57],
            [7.5, 8, 88.70],
            [8, 8.5, 91.86],
            [8.5, 9, 95.01],
            [9, 9.5, 98.15],
            [9.5, 10, 101.30],
            [10, 15, 132.83],
            [15, 20, 163.30],
            [20, 25, 184.30],
            [25, 30, 209.78],
            [30, 35, 242.59],
            [35, 40, 275.41],
            [40, 45, 308.21],
            [45, 50, 341.02],
            [50, 60, 406.65],
            [60, 70, 473.98],
            [70, 80, 556.57],
            [80, 90, 639.17],
            [90, 100, 721.75],
            [100, 110, 804.33],
            [110, 120, 886.93],
            [120, 130, 969.51],
            [130, 140, 1052.11],
            [140, 150, 1134.69],
            [150, 160, 1217.27],
            [160, 170, 1299.87],
            [170, 180, 1382.45],
            [180, 190, 1465.05],
            [190, 200, 1547.63],
            [200, 210, 1630.22],
            [210, 220, 1712.82],
            [220, 230, 1795.40],
            [230, 240, 1878.00],
            [240, 250, 1960.58],
            [250, 260, 2043.16],
            [260, 270, 2125.76],
            [270, 280, 2208.34],
            [280, 290, 2290.94],
            [290, 299, 2365.27],
            [299, PHP_INT_MAX, 2439.60],
        ],
        'middle_east' => [ // Shared tiers for Middle East etc
            [0, 0.5, 51.48],
            [0.5, 1, 55.66],
            [1, 1.5, 59.85],
            [1.5, 2, 63.52],
            [2, 2.5, 67.37],
            [2.5, 3, 71.21],
            [3, 3.5, 75.04],
            [3.5, 4, 78.89],
            [4, 4.5, 82.71],
            [4.5, 5, 86.43],
            [5, 5.5, 90.12],
            [5.5, 6, 93.84],
            [6, 6.5, 97.55],
            [6.5, 7, 101.25],
            [7, 7.5, 104.96],
            [7.5, 8, 108.66],
            [8, 8.5, 112.37],
            [8.5, 9, 116.07],
            [9, 9.5, 119.78],
            [9.5, 10, 123.63],
            [10, 15, 162.14],
            [15, 20, 199.47],
            [20, 25, 226.35],
            [25, 30, 258.50],
            [30, 35, 298.26],
            [35, 40, 338.00],
            [40, 45, 377.76],
            [45, 50, 417.52],
            [50, 60, 497.02],
            [60, 70, 578.61],
            [70, 80, 678.81],
            [80, 90, 779.02],
            [90, 100, 879.22],
            [100, 110, 979.42],
            [110, 120, 1079.64],
            [120, 130, 1179.85],
            [130, 140, 1280.05],
            [140, 150, 1380.25],
            [150, 160, 1480.45],
            [160, 170, 1580.67],
            [170, 180, 1680.88],
            [180, 190, 1781.08],
            [190, 200, 1881.28],
            [200, 210, 1981.49],
            [210, 220, 2081.71],
            [220, 230, 2181.91],
            [230, 240, 2282.11],
            [240, 250, 2382.31],
            [250, 260, 2482.52],
            [260, 270, 2582.74],
            [270, 280, 2682.94],
            [280, 290, 2783.14],
            [290, 299, 2873.32],
            [299, PHP_INT_MAX, 2963.50],
        ],
        'australasia' => [ // Shared tiers for Australasia etc
            [0, 0.5, 56.15],
            [0.5, 1, 58.93],
            [1, 1.5, 61.71],
            [1.5, 2, 65.50],
            [2, 2.5, 69.50],
            [2.5, 3, 73.49],
            [3, 3.5, 77.49],
            [3.5, 4, 81.48],
            [4, 4.5, 85.47],
            [4.5, 5, 89.53],
            [5, 5.5, 93.58],
            [5.5, 6, 97.63],
            [6, 6.5, 101.68],
            [6.5, 7, 105.73],
            [7, 7.5, 109.78],
            [7.5, 8, 113.83],
            [8, 8.5, 117.88],
            [8.5, 9, 121.93],
            [9, 9.5, 125.98],
            [9.5, 10, 130.24],
            [10, 15, 172.85],
            [15, 20, 215.02],
            [20, 25, 253.14],
            [25, 30, 296.65],
            [30, 35, 342.57],
            [35, 40, 388.47],
            [40, 45, 434.39],
            [45, 50, 480.31],
            [50, 60, 572.13],
            [60, 70, 665.63],
            [70, 80, 774.27],
            [80, 90, 882.90],
            [90, 100, 991.52],
            [100, 110, 1100.14],
            [110, 120, 1208.76],
            [120, 130, 1317.40],
            [130, 140, 1426.02],
            [140, 150, 1534.64],
            [150, 160, 1643.26],
            [160, 170, 1751.89],
            [170, 180, 1860.53],
            [180, 190, 1969.15],
            [190, 200, 2077.77],
            [200, 210, 2186.39],
            [210, 220, 2295.01],
            [220, 230, 2403.65],
            [230, 240, 2512.27],
            [240, 250, 2620.89],
            [250, 260, 2729.51],
            [260, 270, 2838.14],
            [270, 280, 2946.78],
            [280, 290, 3055.40],
            [290, 299, 3153.15],
            [299, PHP_INT_MAX, 3203.34],
        ],
        'asia' => [ // Shared tiers for Australasia etc
            [0, 0.5, 62.88],
            [0.5, 1, 67.39],
            [1, 1.5, 72.89],
            [1.5, 2, 78.42],
            [2, 2.5, 83.87],
            [2.5, 3, 89.32],
            [3, 3.5, 94.77],
            [3.5, 4, 100.24],
            [4, 4.5, 105.69],
            [4.5, 5, 110.92],
            [5, 5.5, 116.16],
            [5.5, 6, 121.39],
            [6, 6.5, 126.63],
            [6.5, 7, 131.88],
            [7, 7.5, 137.11],
            [7.5, 8, 142.35],
            [8, 8.5, 147.60],
            [8.5, 9, 152.82],
            [9, 9.5, 158.07],
            [9.5, 10, 163.17],
            [10, 15, 214.11],
            [15, 20, 265.07],
            [20, 25, 316.21],
            [25, 30, 376.19],
            [30, 35, 445.85],
            [35, 40, 515.54],
            [40, 45, 585.20],
            [45, 50, 654.88],
            [50, 60, 794.21],
            [60, 70, 935.48],
            [70, 80, 1093.90],
            [80, 90, 1252.35],
            [90, 100, 1410.79],
            [100, 110, 1569.23],
            [110, 120, 1727.67],
            [120, 130, 1886.10],
            [130, 140, 2044.54],
            [140, 150, 2202.98],
            [150, 160, 2361.43],
            [160, 170, 2519.87],
            [170, 180, 2678.29],
            [180, 190, 2836.74],
            [190, 200, 2995.18],
            [200, 210, 3153.62],
            [210, 220, 3312.06],
            [220, 230, 3470.49],
            [230, 240, 3628.93],
            [240, 250, 3787.37],
            [250, 260, 3945.82],
            [260, 270, 4104.26],
            [270, 280, 4262.68],
            [280, 290, 4421.13],
            [290, 299, 4563.72],
            [299, PHP_INT_MAX, 4706.31],
        ],
        'rest_of_world' => [ // Shared tiers for rest_of_world
            [0, 0.5, 67.55],
            [0.5, 1, 73.71],
            [1, 1.5, 79.87],
            [1.5, 2, 86.03],
            [2, 2.5, 92.10],
            [2.5, 3, 98.15],
            [3, 3.5, 104.20],
            [3.5, 4, 110.26],
            [4, 4.5, 116.31],
            [4.5, 5, 122.19],
            [5, 5.5, 128.07],
            [5.5, 6, 133.95],
            [6, 6.5, 139.83],
            [6.5, 7, 145.71],
            [7, 7.5, 151.59],
            [7.5, 8, 157.47],
            [8, 8.5, 163.35],
            [8.5, 9, 169.23],
            [9, 9.5, 175.11],
            [9.5, 10, 180.47],
            [10, 15, 234.04],
            [15, 20, 287.73],
            [20, 25, 342.53],
            [25, 30, 406.52],
            [30, 35, 479.77],
            [35, 40, 553.04],
            [40, 45, 626.30],
            [45, 50, 699.57],
            [50, 60, 846.10],
            [60, 70, 994.60],
            [70, 80, 1160.84],
            [80, 90, 1327.09],
            [90, 100, 1493.33],
            [100, 110, 1659.58],
            [110, 120, 1825.81],
            [120, 130, 1992.05],
            [130, 140, 2158.30],
            [140, 150, 2324.54],
            [150, 160, 2490.79],
            [160, 170, 2657.01],
            [170, 180, 2823.26],
            [180, 190, 2989.50],
            [190, 200, 3155.75],
            [200, 210, 3321.99],
            [210, 220, 3488.22],
            [220, 230, 3654.47],
            [230, 240, 3820.71],
            [240, 250, 3986.96],
            [250, 260, 4153.20],
            [260, 270, 4319.43],
            [270, 280, 4485.67],
            [280, 290, 4651.92],
            [290, 299, 4801.53],
            [299, PHP_INT_MAX, 4951.93],
        ],
    ];


    $country_data = get_country_data();

    // Get country info or fallback
    $country_info = $country_data[$country] ?? [
        'group' => 'rest_of_world'
    ];

    $group = $country_info['group'];

    // Make sure the group exists in your tiers
    if (!isset($cost_tiers[$group])) {
        return 0;
    }

    $tiers = $cost_tiers[$group];



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



// VALIDATION HELPER FUNCTION TO CHECK COUNTRY EXISTS IN ARRAY 
function is_valid_country($country) {
    $countries = get_country_data();
    return isset($countries[$country]);
}
// VALIDATION HELPER FUNCTION TO CHECK COUNTRY EXISTS IN ARRAY 


// 5. CREATE CART ITEM DATA AND STORE AS SESSION

add_filter('woocommerce_add_cart_item_data', 'add_custom_price_cart_item_data_secure', 10, 2);

function add_custom_price_cart_item_data_secure($cart_item_data, $product_id) {
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product_id) : false;
    $roll_length = floatval(get_field('roll_length', $product_id));
    $roll_length_v = ($roll_length > 0) ? $roll_length / 1000 : 0;

    $cart_item_data['custom_inputs'] = [];

    /* Begin collection of $_POST values for custom shipping address */
    if (
        isset($_POST['custom_street_address']) &&
        isset($_POST['custom_city']) &&
        isset($_POST['custom_county_state']) &&
        isset($_POST['custom_zip_postal']) &&
        isset($_POST['custom_country'])
    ) {
        $raw_country = sanitize_text_field($_POST['custom_country']);
        $country = is_valid_country($raw_country) ? $raw_country : 'United Kingdom';

        $cart_item_data['custom_inputs']['shipping_address'] = [
            'street_address' => sanitize_text_field($_POST['custom_street_address']),
            'address_line2' => sanitize_text_field($_POST['custom_address_line2']),
            'city' => sanitize_text_field($_POST['custom_city']),
            'county_state' => sanitize_text_field($_POST['custom_county_state']),
            'zip_postal' => sanitize_text_field($_POST['custom_zip_postal']),
            'country' => $country,
        ];
        /* We store the shipping address as a WP session for use in the add_custom_shipping_to_order() function */
        /* Later in the add_custom_shipping_to_order() function the session value is then saved as order meta */
        WC()->session->set('custom_shipping_address', $cart_item_data['custom_inputs']['shipping_address']);

        // Update all existing cart items to use the latest address
        if (WC()->cart && !WC()->cart->is_empty()) {

            $new_address = $cart_item_data['custom_inputs']['shipping_address'];

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

                WC()->cart->cart_contents[$cart_item_key]['custom_inputs']['shipping_address']
                    = $new_address;
            }

            WC()->cart->set_session();

        }
    }
    /* End collection of $_POST values for custom shipping address */

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

    $product = wc_get_product($product_id);
    if (!$product) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("add_custom_price_cart_item_data_secure: Invalid product ID {$product_id}");
        }
        return $cart_item_data;
    }

    //$country = isset($cart_item_data['custom_inputs']['shipping_address']['country']) ? $cart_item_data['custom_inputs']['shipping_address']['country'] : 'United Kingdom';
    $country = $cart_item_data['custom_inputs']['shipping_address']['country'] ?? 'United Kingdom';

    if ($is_product_single) {
        $product_weight = $product->get_weight();
        if (!is_numeric($product_weight) || $product_weight <= 0) {
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
        $shape_type,
        $product_id
    );

    $totalSqMm = $part_length_mm * $part_width_mm;
    $totalSqCm = $totalSqMm / 100;
    $total_del_weight_calc = $totalSqCm * floatval($product_weight) * $quantity * 1.03;

    if($shape_type === "rolls"){
        $stock_quantity = $product->get_stock_quantity() / $roll_length_v;
        $total_del_weight = $total_del_weight_calc * $roll_length_v;
    } else {
        $stock_quantity = $product->get_stock_quantity();
        $total_del_weight = $total_del_weight_calc;
    }

    $final_shipping = calculate_shipping_cost($total_del_weight, $country);

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
    $shipments_count = count($shipments_session);
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
        $cart_item_data['custom_inputs']['is_scheduled'] = $is_scheduled; 
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
            $discount_label = get_shipment_lead_time_discount($despatch_ymd); 

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
            

            //$formatted_line = number_format($s['parts']) . " parts to be despatched on {$despatch_date} {$lead_time_label}";
            $despatch_notes .= $formatted_line . "\n";
            $despatch_string .= $formatted_line_discount; // thurdsay retrieve discount rate
            //$ah_despatch_date .= $formatted_line_date;
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
            
            if ($calendar_days <= 1) {
                $disc = 0;           // 24Hrs
            } elseif ($calendar_days <= 4) {
                $disc = 0.015;       // 48Hrs
            } elseif ($calendar_days <= 6) {
                $disc = 0.02;        // 5 Days
            } elseif ($calendar_days <= 12) {
                $disc = 0.025;       // 7 Days
            } elseif ($calendar_days <= 13) {
                $disc = 0.03;        // 12 Days
            } elseif ($calendar_days <= 29) {
                $disc = 0.035;       // 14 Days
            } elseif ($calendar_days <= 34) {
                $disc = 0.04;        // 30–34 Days
            } else {
                $disc = 0.05;        // 35 Days and above ← this was the bug
            }
            
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
        $cart_item_data['custom_inputs']['shipments_count'] = $shipments_count;
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


        $is_full_backorder_rolls = isset($_POST['is_full_backorder_rolls']) && ($_POST['is_full_backorder_rolls'] === '1' || $_POST['is_full_backorder_rolls'] === true || $_POST['is_full_backorder_rolls'] === 'true');

        $server_total_price = 0;
        $despatch_notes = '';
        $is_backorder = false;
        $backorder_data = [];

        if ($is_full_backorder_rolls && $shape_type === 'rolls') {
            // Trust the exact discounted price the user saw on the product page
            $server_total_price = floatval($_POST['custom_price']) / $quantity;

            $despatch_notes = sprintf(
                '%d rolls to be despatched in 35 Days (working days) (5%% discount)',
                $quantity
            );
            $shipments = date('d/m/Y', strtotime('+35 days'));
            $is_backorder = true;

            $backorder_data = [
                'is_full_backorder_rolls' => true,
                'parts_backorder' => $quantity,
                'able_to_dispatch' => 0,
                'discount_rate' => 0.05
            ];


        }
        elseif ($stock_quantity <= 0) {
            // Full backorder
            $discount_rate = 0.05;
            $server_total_price = calculate_product_price($product_id, $part_width_mm, $part_length_mm, $quantity, $discount_rate, $shape_type);
            $despatch_notes = sprintf('%d parts to be despatched in 35 Days (working days) (5%% Discount)', $quantity);
            $shipments = date('d/m/Y', strtotime('+35 days'));
            $is_backorder = true;


        } elseif ($is_backorder_raw) {

            $server_total_price = floatval($_POST['custom_price']) * $sheets_required;

            $dispatch_days = 1; // 24 hr default
            if ($discount_rate == 0.015) $dispatch_days = 2;
            elseif ($discount_rate == 0.02) $dispatch_days = 5;
            elseif ($discount_rate == 0.025) $dispatch_days = 7;
            elseif ($discount_rate == 0.03) $dispatch_days = 12;
            elseif ($discount_rate == 0.035) $dispatch_days = 14;
            elseif ($discount_rate == 0.04) $dispatch_days = 30;
            elseif ($discount_rate == 0.05) $dispatch_days = 35;

            $shipments_dispatch = date('d/m/Y', strtotime('+' . $dispatch_days . ' days'));
            $shipments_backorder = date('d/m/Y', strtotime('+35 days'));
            $shipments = [$shipments_dispatch, $shipments_backorder];

            // Still calculate the split numbers only for despatch_notes and backorder_data
            $calculated_parts_per_sheet = $sheet_result['parts_per_sheet'];
            $parts_from_stock = $stock_quantity * $calculated_parts_per_sheet;
            $able_to_dispatch = min($parts_from_stock, $quantity);
            $parts_backorder = $quantity - $able_to_dispatch;

            $despatch_notes = sprintf(
                '%d parts to be despatched in %s, %d parts to be despatched in 35 days (5%% discount)',
                $able_to_dispatch,
                $delivery_time,
                $parts_backorder
            );

            $backorder_data = [
                'backorder_total' => $server_total_price,
                'parts_backorder' => $parts_backorder,
                'able_to_dispatch' => $able_to_dispatch,
                'parts_per_sheet' => $calculated_parts_per_sheet,
            ];

            $is_backorder = true;

            // Optional validation (keeps your existing safety check)
            if (isset($_POST['custom_parts_per_sheet'])) {
                $client_parts_per_sheet = intval($_POST['custom_parts_per_sheet']);
                $client_parts_backorder = intval($_POST['custom_parts_backorder']);
                $client_able_to_dispatch = intval($_POST['custom_able_to_dispatch']);
                if ($calculated_parts_per_sheet != $client_parts_per_sheet ||
                    $parts_backorder != $client_parts_backorder ||
                    $able_to_dispatch != $client_able_to_dispatch) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("Backorder data validation failed for product $product_id");
                    }
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
            $server_total_price = calculate_product_price($product_id, $part_width_mm, $part_length_mm, $quantity, $discount_rate, $shape_type);
        }






        // Price per sheet for cart
        if($is_full_backorder_rolls && $shape_type === 'rolls'){
            $cart_item_data['custom_inputs']['price'] = $server_total_price;
            $cart_item_data['custom_inputs']['total_price'] = $server_total_price;
            $cart_item_data['custom_inputs']['is_full_backorder_rolls'] = true;
        }
        else {
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
    }



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

    // Collect the dates from scheduled orders v2

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
        'roll_length' => $roll_length_v,
        'despatch_date' => $aggregated,  // this will now be the combined list
    ]);


    if ($is_scheduled) {
        $cart_item_data['custom_inputs']['is_scheduled'] = true;
        //$cart_item_data['custom_inputs']['scheduled_shipments'] = $scheduled_shipments;
        $cart_item_data['custom_inputs']['scheduled_shipments'] = $enhanced_shipments;
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

                // === DETECT RESTORED CART & GET UNIQUE CAPTURED TOTAL ===
                $has_restored = false;
                $captured_shipping_data = [];

                foreach ($cart->get_cart() as $item) {
                    if (!empty($item['restored_from_capture'])) {
                        $has_restored = true;

                        if (!empty($item['restored_shipments']) && is_array($item['restored_shipments'])) {
                            foreach ($item['restored_shipments'] as $date => $data) {
                                // Only add once per unique date (prevents double-counting)
                                if (!isset($captured_shipping_data[$date])) {
                                    $captured_shipping_data[$date] = floatval($data['final_shipping'] ?? 0);
                                }
                            }
                        }
                    }
                }

                $total_shipping = 0;

                if ($has_restored && !empty($captured_shipping_data)) {
                    $total_shipping = array_sum($captured_shipping_data);
                } else {
                    // Normal cart: live calculation
                    $shipping_by_date = group_shipping_by_date($cart);
                    $total_shipping = 0;
                    foreach ($shipping_by_date as $date => $data) {
                        $total_shipping += floatval($data['final_shipping'] ?? 0);
                        // if ($total_shipping > 0) {
                        //     $clean_cost = round(floatval($total_shipping), 2);
                        // }
                    }
                }

                // BEGIN OVERSIZE SURCHARGE LOGIC 
                $oversize_surcharge = 0;

                foreach ($cart->get_cart() as $cart_item) {
                    $product_id = $cart_item['product_id'];
                    
                    // Check ACF field - if checked, skip surcharge for this product
                    $disable_oversize_surcharge = get_field('disable_oversize_surcharge', $product_id);
                    if ($disable_oversize_surcharge) {
                        continue;
                    }

                    // Get dimensions - support both normal carts and restored carts
                    $width = 0;
                    $length = 0;

                    if (!empty($cart_item['custom_inputs'])) {
                        // Normal cart
                        $width  = floatval($cart_item['custom_inputs']['width'] ?? 0);
                        $length = floatval($cart_item['custom_inputs']['length'] ?? 0);
                        $final_shipment_count = !empty($cart_item['custom_inputs']['shipments_count']) ? (int)$cart_item['custom_inputs']['shipments_count'] : 1;
                    } elseif (!empty($cart_item['cart_metadata'])) {
                        // Restored cart
                        $width  = floatval($cart_item['cart_metadata']['Width'] ?? $cart_item['cart_metadata']['Width (MM)'] ?? 0);
                        $length = floatval($cart_item['cart_metadata']['Length'] ?? $cart_item['cart_metadata']['Length (MM)'] ?? 0);
                        $final_shipment_count = floatval($cart_item['cart_metadata']['shipments_count'] ?? $cart_item['cart_metadata']['shipments_count'] ?? 1);
                    }

                    if ($width >= 1000 || $length >= 1000) {
                        $oversize_surcharge += 30 * $final_shipment_count;
                        error_log("Oversize Surcharge: " . $oversize_surcharge);
                    }
                }
                // END OVERSIZE SURCHARGE LOGIC 

                // Add the surcharge to the total
                if ($oversize_surcharge > 0) {
                    $total_shipping += $oversize_surcharge;
                    error_log("Added oversize surcharge: £{$oversize_surcharge} | New total: £{$total_shipping}");
                }
                // END NEW: OVERSIZE SURCHARGE LOGIC 


                // === ADD THE RATE ===
                if ($total_shipping > 0) {
                    $this->add_rate([
                        'id'       => $this->id . ':' . $this->instance_id,
                        'label'    => $this->title,
                        'cost'     => $total_shipping,
                        'taxes'    => '',
                        'calc_tax' => 'per_order',
                        'package'  => $package,
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




// NEW - DISPLAY SHIPMENTS SECTION ABOVE CART TOTALS

add_action('woocommerce_before_cart_totals', 'display_shipments_section_cart', 5);

function display_shipments_section_cart() {
    $cart = WC()->cart;

    // Check if this cart contains any restored item
    $has_restored = false;
    $restored_shipments = [];

    foreach ($cart->get_cart() as $item) {
        if (!empty($item['restored_from_capture'])) {
            $has_restored = true;
            if (!empty($item['restored_shipments']) && is_array($item['restored_shipments'])) {
                $restored_shipments = array_merge($restored_shipments, $item['restored_shipments']);
            }
        }
    }

    // For restored carts: use captured data
    if ($has_restored && !empty($restored_shipments)) {
        $shipping_by_date = $restored_shipments;
    } else {
        // Normal cart: calculate live
        $shipping_by_date = group_shipping_by_date($cart);
    }

    if (empty($shipping_by_date)) {
        error_log('display_shipments_section_cart: No shipments data available');
        return;
    }

    echo '<div class="shipments-section" style="margin-bottom: 20px;">';
    echo '<p class="cart_totals__shipment"><strong>Shipments:</strong></p>';

    foreach ($shipping_by_date as $date => $data) {
        $shipping_cost = floatval($data['final_shipping'] ?? 0);
        $shipping_rate = get_currency_rate();
        $currency_symbol = get_currency_symbol();

        if ($shipping_cost > 0) {
            $formatted_cost = round($shipping_cost * $shipping_rate, 2);
            $line = 'Dispatch ' . esc_html($date) . '(' . $currency_symbol . $formatted_cost . ')';

            // Optional: show parts/lead time if captured
            // if (!empty($data['parts'])) {
            //     $line .= ' - ' . number_format($data['parts']) . ' parts';
            // }
            if (!empty($data['lead_time_label'])) {
                $line .= ' ' . esc_html($data['lead_time_label']);
            }

            echo '<p class="cart_totals__shipment-details">' . $line . '</p>';
        }
    }

    echo '</div>';
}

// NEW - DISPLAY SHIPMENTS SECTION ABOVE CART TOTALS






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
            $shipping_rate = get_currency_rate();
            $currency_symbol = get_currency_symbol();
            if ($shipping_cost > 0) {
                $formatted_cost = round($shipping_cost * $shipping_rate, 2);
                echo '<p class="cart_totals__shipment-details">Dispatch ' . esc_html($date) .'('. $currency_symbol . '' . $formatted_cost . ')</p>';
            }
        }
        echo '</div>';
    }
}
// DISPLAY SHIPMENTS SECTION ABOVE CHECKOUT TOTALS





// ENSURE SHIPPING RATE IS ADDED TO ORDER
 /* This function saves the custom shipping address session value as order meta ensuring that the session value is always only temporary */
add_action('woocommerce_checkout_create_order', 'add_custom_shipping_to_order', 20, 2);
function add_custom_shipping_to_order($order, $data) {

    $cart = WC()->cart;
    $shipping_by_date = group_shipping_by_date($cart);


    // Sum the shipping costs
    $shipping_meta = [];
    $total_shipping = 0;

    foreach ($shipping_by_date as $date => $data) {

        $final_shipping = floatval($data['final_shipping']);

        $total_shipping += floatval($data['final_shipping']);

        $shipping_meta[$date] = $final_shipping;

    }

    // Update shipping address from session

    /* We now get the $cart_item shipping address values from the cart object (instead of using sessions) - This is more robust */
    /* We then store the $cart_item['custom_inputs']['shipping_address'] value as $shipping_address  */
    /* We then resave the $cart_item['custom_inputs']['shipping_address'] value into the WC()->session */
    /* The WC()->session shipping address value is then sent to the cart as the 'custom shipping address' value */
    /* This solution ensures that if ever the WC()->session shipping address value is empty the shipping address will still remain */
    $shipping_address = null;

    if (WC()->cart) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            if (!empty($cart_item['custom_inputs']['shipping_address']) && is_array($cart_item['custom_inputs']['shipping_address'])) {
                $shipping_address = $cart_item['custom_inputs']['shipping_address'];
                // If ever the session is empty we restore the session again here
                WC()->session->set(
                    'custom_shipping_address',
                    $shipping_address
                );
                break;
            }
        }
    }

    // Fallback to session
    if (empty($shipping_address) && WC()->session) {
        $shipping_address = WC()->session->get('custom_shipping_address');
    }


    if ($shipping_address && is_array($shipping_address)) {
        $order->set_shipping_first_name(isset($data['billing_first_name']) ? $data['billing_first_name'] : '');
        $order->set_shipping_last_name(isset($data['billing_last_name']) ? $data['billing_last_name'] : '');
        $order->set_shipping_company(isset($data['billing_company']) ? $data['billing_company'] : '');
        $order->set_shipping_address_1($shipping_address['street_address']);
        $order->set_shipping_address_2(!empty($shipping_address['address_line2']) ? $shipping_address['address_line2'] : '');
        $order->set_shipping_city($shipping_address['city']);
        $order->set_shipping_state($shipping_address['county_state']);
        $order->set_shipping_postcode($shipping_address['zip_postal']);
        
        $country_data = get_country_data();
        $country_name = $shipping_address['country'] ?? '';

        $country_code = $country_data[$country_name]['code'] ?? '';

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
        if ($cart_item['custom_inputs']['shape_type'] !== "rolls") {
            if (isset($cart_item['custom_inputs']['length'])) {
                $item_data[] = [
                    'name' => 'Length (MM)',
                    'value' => $cart_item['custom_inputs']['length']
                ];
            }
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

        // Roll Length
        if ($cart_item['custom_inputs']['shape_type'] === "rolls") {
            if (
                isset($cart_item['custom_inputs']['roll_length']) &&
                (float) $cart_item['custom_inputs']['roll_length'] > 0
            ) {
                $item_data[] = [
                    'name'  => 'Roll Length (Metres)',
                    'value' => $cart_item['custom_inputs']['roll_length']
                ];
            }
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

            $shape_type = $cart_item['custom_inputs']['shape_type'] ?? 'custom-shape-drawing';
            

            if (!in_array($shape_type, ['custom-shape-drawing', 'square-rectangle', 'circle-radius', 'stock-sheets', 'rolls'])) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("apply_secure_custom_price: Invalid shape_type ($shape_type) for cart item key $cart_item_key. Defaulting to custom-shape-drawing.");
                }
                $shape_type = 'custom-shape-drawing';
            }

            $is_full_backorder_rolls = !empty($cart_item['custom_inputs']['is_full_backorder_rolls']);

            // === NEW: Respect Rolls full backorder total price ===
            if ($is_full_backorder_rolls && $shape_type === 'rolls') {
                $final_price = floatval($cart_item['custom_inputs']['total_price'] ?? $cart_item['custom_inputs']['price'] ?? 0);
                if ($final_price > 0) {
                    $cart_item['data']->set_price($final_price);
                    continue;   // Skip all further recalculation
                }
            }

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



            if ($is_backorder && !empty($cart_item['custom_inputs']['backorder_data'])) {
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





// SAVE SESSION 'custom_shipping_address' AS POST META  
add_action('woocommerce_checkout_create_order', 'save_custom_address_to_order', 10, 2);

function save_custom_address_to_order($order, $data) {
    if (isset(WC()->session) && WC()->session) {
        $address = WC()->session->get('custom_shipping_address');
        if ($address) {
            $order->update_meta_data('_custom_shipping_address', $address);
        }
    }
}
// SAVE SESSION 'custom_shipping_address' AS POST META  




// CLEAR SESSION AFTER ORDER IS PLACED

add_action('woocommerce_checkout_order_processed', 'clear_custom_shipping_session', 10, 1);
function clear_custom_shipping_session($order_id) {
    //WC()->session->set('custom_shipping_address', null);
    WC()->session->set('custom_qty', null); 
    WC()->session->set('custom_shipments', null); 
}

// CLEAR SESSION AFTER ORDER IS PLACED




// CALCULATE SHEETS REQUIRED

function calculate_sheets_required($sheet_width, $sheet_length, $part_width, $part_length, $quantity, $shape_type, $product_id) {

    $border_cm = 0;
    
    if ($product_id && function_exists('get_field')) {

        if($shape_type === "stock-sheets"){
          	$acf_border = 0;
        } 
        elseif($shape_type === "rolls"){
            $acf_border = 0;
        }
        else {
        	$acf_border = get_field('border_around', $product_id);
        }

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
            $shape_type,
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

            $country_data = get_country_data();
            $country_info = $country_data[$custom_country] ?? null;

            $country_code = $country_info['code'] ?? '';

                //If valid country code found
                if ($country_code) {
                    WC()->customer->set_shipping_country($country_code);

                    WC()->customer->set_shipping_address($shipping_address['street_address']);
                    WC()->customer->set_shipping_address_2($shipping_address['address_line2']);
                    WC()->customer->set_shipping_city($shipping_address['city']);
                    WC()->customer->set_shipping_state($shipping_address['county_state']);
                    WC()->customer->set_shipping_postcode($shipping_address['zip_postal']);
                } else {
                    // NEW: fallback if country not found in mapping
                    $store_country = WC()->countries->get_base_country();
                    WC()->customer->set_shipping_country($store_country);
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
                        <?php foreach ($shipments as $index => $shipment) {
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
                        <?php foreach ($shipments as $index => $shipment) {
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

// Make sure scheduled delivery 'custom_shipment' session is destroyed after 15 minutes
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
// Make sure scheduled delivery 'custom_shipment' session is destroyed after 15 minutes


// Delivery options partial backorder - enforce 35 day jquery datepicker
add_action('wp_ajax_save_partial_backorder_data', 'save_partial_backorder_data_callback');
add_action('wp_ajax_nopriv_save_partial_backorder_data', 'save_partial_backorder_data_callback');

function save_partial_backorder_data_callback() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    $is_partial = filter_var($_POST['is_partial_backorder'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $able       = intval($_POST['able_to_dispatch'] ?? 0);
    $back       = intval($_POST['parts_backorder'] ?? 0);

    WC()->session->set('is_partial_backorder', $is_partial);
    WC()->session->set('able_to_dispatch', $able);
    WC()->session->set('parts_backorder', $back);

    wp_send_json_success();
}


add_action('wp_ajax_get_current_shipments', 'get_current_shipments_callback');
add_action('wp_ajax_nopriv_get_current_shipments', 'get_current_shipments_callback');

function get_current_shipments_callback() {
    check_ajax_referer('custom_price_nonce', 'nonce');

    $shipments   = WC()->session->get('custom_shipments', []);
    $custom_qty  = WC()->session->get('custom_qty', 0);
    $total_parts = array_sum(array_column($shipments, 'parts'));
    $remaining   = max(0, $custom_qty - $total_parts);

    wp_send_json_success([
        'shipments'            => $shipments,
        'custom_qty'           => $custom_qty,
        'remaining_parts'      => $remaining,
        'custom_shipments_parts' => WC()->session->get('custom_shipments_parts', 0),
        'total_fees'           => array_sum(array_column($shipments, 'total_fee')),
        // NEW fields for modal validation
        'is_partial_backorder' => WC()->session->get('is_partial_backorder', false),
        'able_to_dispatch'     => WC()->session->get('able_to_dispatch', 0),
        'parts_backorder'      => WC()->session->get('parts_backorder', 0),
        'total_ordered_qty'    => $custom_qty
    ]);
}
// Delivery options partial backorder - enforce 35 day jquery datepicker