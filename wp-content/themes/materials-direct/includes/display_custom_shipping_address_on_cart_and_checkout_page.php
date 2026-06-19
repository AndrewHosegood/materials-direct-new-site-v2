<?php
// Helper function which adds fallback logic in case the Shipping Address session value does not exists
function get_custom_shipping_address() {

    // First try the session
    $shipping_address = WC()->session->get('custom_shipping_address');

    if (!empty($shipping_address) && is_array($shipping_address)) {
        return $shipping_address;
    }

    // Session missing - recover from cart item
    if (WC()->cart && !WC()->cart->is_empty()) {

        foreach (WC()->cart->get_cart() as $cart_item) {

            if (
                !empty($cart_item['custom_inputs']['shipping_address']) &&
                is_array($cart_item['custom_inputs']['shipping_address'])
            ) {

                $shipping_address = $cart_item['custom_inputs']['shipping_address'];

                // Restore session for future requests
                WC()->session->set(
                    'custom_shipping_address',
                    $shipping_address
                );

                error_log('Recovered custom_shipping_address from cart item');

                return $shipping_address;
            }
        }
    }

    return false;
}

// DISPLAY SHIPPING ADDRESS ON CHECKOUT PAGE
add_action('woocommerce_review_order_before_payment', 'display_shipping_address_on_checkout', 5);
function display_shipping_address_on_checkout() {

    $shipping_address = get_custom_shipping_address();
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

// DISPLAY GLOBAL SHIPPING ADDRESS BELOW CART TABLES
add_action('woocommerce_before_cart_totals', 'display_global_shipping_address_cart', 1, 2);
function display_global_shipping_address_cart() {
    $shipping_address = get_custom_shipping_address();
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


