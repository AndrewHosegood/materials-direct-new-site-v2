<?php
// Override tax location to use session shipping country for cart and checkout
add_filter('woocommerce_get_tax_location', 'override_tax_location_with_session', 10, 2);
function override_tax_location_with_session($location, $tax_class) {
    if (is_cart() || is_checkout() || defined('DOING_AJAX') && DOING_AJAX) {
        $shipping_address = WC()->session->get('custom_shipping_address');
        if ($shipping_address && isset($shipping_address['country'])) {
            /*
            $country_codes = [
                'United Kingdom' => 'GB',
                'Albania' => 'AL',
                'Andorra' => 'AD',
                'Angola' => 'AO',
                'Australia' => 'AU',
                'Austria' => 'AT',
                'Bahrain' => 'BH',
                'Bangladesh' => 'BD',
                'Belgium' => 'BE',
                'Bhutan' => 'BT',
                'Brunei' => 'BN',
                'Bosnia & Herzegovina' => 'BA',
                'Bulgaria' => 'BG',
                'Canada' => 'CA',
                'Cambodia' => 'KH',
                'China' => 'CN',
                'Croatia' => 'HR',
                'Cyprus' => 'CY',
                'Czechia' => 'CZ',
                'Denmark' => 'DK',
                'Estonia' => 'EE',
                'Faroe Islands' => 'FO',     
                'Finland' => 'FI',  
                'France' => 'FR',
                'Germany' => 'DE', 
                'Gibraltar' => 'GI',
                'Greece' => 'GR',
                'Greenland' => 'GL',
                'Guernsey' => 'GG',
                'Hungary' => 'HU',
                'Hong Kong SAR China' => 'HK',
                'Iceland' => 'IS',
                'India' => 'IN',
                'Indonesia' => 'ID',
                'Ireland' => 'IE',
                'Israel' => 'IL',
                'Italy' => 'IT',
                'Japan' => 'JP',
                'Jersey' => 'JE',
                'Jordan' => 'JO',
                'Kuwait' => 'KW',
                'Laos' => 'LA',
                'Latvia' => 'LV',
                'Liechtenstein' => 'LI',
                'Lithuania' => 'LT',
                'Luxembourg' => 'LU',
                'Malaysia' => 'MY',
                'Malta' => 'MT',
                'Mexico' => 'MX',
                'Monaco' => 'MC',
                'Montenegro' => 'ME',
                'Myanmar (Burma)' => 'MM',
                'Nepal' => 'NP',
                'Netherlands' => 'NL',
                'New Zealand' => 'NZ',
                'North Macedonia' => 'MK',
                'Norway' => 'NO',
                'Oman' => 'OM',
                'Pakistan' => 'PK',
                'Philippines' => 'PH',
                'Poland' => 'PL',
                'Portugal' => 'PT',
                'Qatar'   => 'QA',    
                'Romania' => 'RO',      
                'San Marino' => 'SM',
                'Saudi Arabia' => 'SA',
                'Serbia' => 'RS',
                'Singapore' => 'SG',
                'Slovakia' => 'SK',     
                'Slovenia' => 'SI',
                'South Africa'   => 'ZA',
                'South Korea'   => 'KR',
                'Spain' => 'ES',
                'Sri Lanka' => 'LK',
                'Sweden' => 'SE',
                'Switzerland' => 'CH',
                'Taiwan' => 'TW',
                'Thailand' => 'TH',
                'Turkey' => 'TR',
                'United Arab Emirates' => 'AE',
                'United States' => 'US',
                'Vatican City' => 'VA',
                'Vietnam' => 'VN',
                'Zimbabwe' => 'ZW'
            ];
            */
            $country_data = get_country_data();

            $country_codes = [];
            foreach ($country_data as $country_name => $data) {
                $country_codes[$country_name] = $data['code'];
            }

            $country_code = isset($country_codes[$shipping_address['country']]) ? $country_codes[$shipping_address['country']] : $location[0];
            $location[0] = $country_code; // Country
            // Optionally set state, postcode, city from session if needed for more precise tax rules
            $location[1] = $shipping_address['county_state']; // State
            $location[2] = $shipping_address['zip_postal']; // Postcode
            $location[3] = $shipping_address['city']; // City

        }
    }
    return $location;
}

// Enqueue JavaScript on checkout page to override browser auto-fill and trigger update
add_action('wp_enqueue_scripts', 'enqueue_checkout_override_script');
function enqueue_checkout_override_script() {
    if (is_checkout()) {
        wp_enqueue_script('checkout-override', get_stylesheet_directory_uri() . '/js/checkout-override.js', array('jquery'), null, true);
        $shipping_address = WC()->session->get('custom_shipping_address');
        $country_code = '';
        if ($shipping_address && isset($shipping_address['country'])) {
            /*
            $country_codes = [
                'United Kingdom' => 'GB',
                'Albania' => 'AL',
                'Andorra' => 'AD',
                'Angola' => 'AO',
                'Australia' => 'AU',
                'Austria' => 'AT',
                'Bahrain' => 'BH',
                'Bangladesh' => 'BD',
                'Belgium' => 'BE',
                'Bhutan' => 'BT',
                'Brunei' => 'BN',
                'Bosnia & Herzegovina' => 'BA',
                'Bulgaria' => 'BG',
                'Canada' => 'CA',
                'Cambodia' => 'KH',
                'China' => 'CN',
                'Croatia' => 'HR',
                'Cyprus' => 'CY',
                'Czechia' => 'CZ',
                'Denmark' => 'DK',
                'Estonia' => 'EE',
                'Faroe Islands' => 'FO',     
                'Finland' => 'FI',  
                'France' => 'FR',
                'Germany' => 'DE', 
                'Gibraltar' => 'GI',
                'Greece' => 'GR',
                'Greenland' => 'GL',
                'Guernsey' => 'GG',
                'Hungary' => 'HU',
                'Hong Kong SAR China' => 'HK',
                'Iceland' => 'IS',
                'India' => 'IN',
                'Indonesia' => 'ID',
                'Ireland' => 'IE',
                'Israel' => 'IL',
                'Italy' => 'IT',
                'Japan' => 'JP',
                'Jersey' => 'JE',
                'Jordan' => 'JO',
                'Kuwait' => 'KW',
                'Laos' => 'LA',
                'Latvia' => 'LV',
                'Liechtenstein' => 'LI',
                'Lithuania' => 'LT',
                'Luxembourg' => 'LU',
                'Malaysia' => 'MY',
                'Malta' => 'MT',
                'Mexico' => 'MX',
                'Monaco' => 'MC',
                'Montenegro' => 'ME',
                'Myanmar (Burma)' => 'MM',
                'Nepal' => 'NP',
                'Netherlands' => 'NL',
                'New Zealand' => 'NZ',
                'North Macedonia' => 'MK',
                'Norway' => 'NO',
                'Oman' => 'OM',
                'Pakistan' => 'PK',
                'Philippines' => 'PH',
                'Poland' => 'PL',
                'Portugal' => 'PT',
                'Qatar'   => 'QA',    
                'Romania' => 'RO',      
                'San Marino' => 'SM',
                'Saudi Arabia' => 'SA',
                'Serbia' => 'RS',
                'Singapore' => 'SG',
                'Slovakia' => 'SK',     
                'Slovenia' => 'SI',
                'South Africa'   => 'ZA',
                'South Korea'   => 'KR',
                'Spain' => 'ES',
                'Sri Lanka' => 'LK',
                'Sweden' => 'SE',
                'Switzerland' => 'CH',
                'Taiwan' => 'TW',
                'Thailand' => 'TH',
                'Turkey' => 'TR',
                'United Arab Emirates' => 'AE',
                'United States' => 'US',
                'Vatican City' => 'VA',
                'Vietnam' => 'VN',
                'Zimbabwe' => 'ZW'
            ];
            */
            $country_data = get_country_data();

            $country_codes = [];
            foreach ($country_data as $country_name => $data) {
                $country_codes[$country_name] = $data['code'];
            }
            $country_code = isset($country_codes[$shipping_address['country']]) ? $country_codes[$shipping_address['country']] : '';
        }
        wp_localize_script('checkout-override', 'checkout_override_data', array(
            'shipping_country' => $country_code,
        ));
    }
}