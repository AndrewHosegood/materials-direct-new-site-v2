<?php
add_filter('woocommerce_available_payment_gateways', 'ah_payment_gateway_disable_items');

function ah_payment_gateway_disable_items($available_gateways) {

    error_log("Triggered 1");

    // Admin safety
    if (is_admin() && !defined('DOING_AJAX')) {
        return $available_gateways;
        error_log("Triggered 2");
    }

    // if (!WC()->cart || WC()->cart->is_empty()) {
    //     return $available_gateways;
    // }

    // Don't access the cart until WooCommerce has finished loading it.
    if ( ! did_action( 'wp_loaded' ) ) {
        return $available_gateways;
        error_log("Triggered 3");
    }

    if ( ! WC()->cart ) {
        return $available_gateways;
        error_log("Triggered 4");
    }

    if ( WC()->cart->is_empty() ) {
        return $available_gateways;
        error_log("Triggered 5");
    }

    // Determine if cart contains a credit-enabled item
    $has_credit_item = false;

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

        error_log("Triggered 6");

        $allow_credit_custom = !empty($cart_item['custom_inputs']['allow_credit']);
        $allow_credit_capture = !empty($cart_item['cart_metadata']['allow_credit']);


        if ($allow_credit_custom || $allow_credit_capture) {

            $has_credit_item = true;

            break;

            error_log("Triggered 7");
        }
    }

    /*
     * CREDIT ACCOUNT ORDER
     */
    if ($has_credit_item) {

        error_log("has_credit_item == TRUE");

        // Allow ONLY crediting gateway
        foreach ($available_gateways as $gateway_id => $gateway) {
            error_log($gateway_id);
            if ($gateway_id !== 'crediting_gateway') {
                unset($available_gateways[$gateway_id]);
            }
        }

        return $available_gateways;
    }

    /*
     * NON-CREDIT ORDER
     */
    // Remove credit gateway
    unset($available_gateways['crediting_gateway']);

    // Remove COD (per your rules)
    unset($available_gateways['cod']);

    // Stripe + BACS remain enabled
    return $available_gateways;
}
