<?php
// Override tax location to use session shipping country for cart and checkout
add_filter('woocommerce_get_tax_location', 'override_tax_location_with_session', 10, 2);
function override_tax_location_with_session($location, $tax_class) {
    if (is_cart() || is_checkout() || defined('DOING_AJAX') && DOING_AJAX) {
        $shipping_address = WC()->session->get('custom_shipping_address');
        if ($shipping_address && isset($shipping_address['country'])) {
            $country_codes = [
                'United Kingdom' => 'GB',
                'France' => 'FR',
                'Germany' => 'DE',
                'Monaco' => 'MC',
                'Poland' => 'PL',
                'Spain' => 'ES',
                'United States' => 'US',
            ];
            $country_code = isset($country_codes[$shipping_address['country']]) ? $country_codes[$shipping_address['country']] : $location[0];
            $location[0] = $country_code; // Country
            // Optionally set state, postcode, city from session if needed for more precise tax rules
            $location[1] = $shipping_address['county_state']; // State
            $location[2] = $shipping_address['zip_postal']; // Postcode
            $location[3] = $shipping_address['city']; // City

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("override_tax_location_with_session: Forced tax location country to {$country_code}");
            }
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
            $country_codes = [
                'United Kingdom' => 'GB',
                'France' => 'FR',
                'Germany' => 'DE',
                'Monaco' => 'MC',
                'Poland' => 'PL',
                'Spain' => 'ES',
            ];
            $country_code = isset($country_codes[$shipping_address['country']]) ? $country_codes[$shipping_address['country']] : '';
        }
        wp_localize_script('checkout-override', 'checkout_override_data', array(
            'shipping_country' => $country_code,
        ));
    }
}