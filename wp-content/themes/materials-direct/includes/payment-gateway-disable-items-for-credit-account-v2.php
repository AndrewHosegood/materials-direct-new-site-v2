<?php
add_filter('woocommerce_available_payment_gateways', 'ah_payment_gateway_disable_items');

function ah_payment_gateway_disable_items($available_gateways) {

    // Admin safety
    if (is_admin() && !defined('DOING_AJAX')) {
        return $available_gateways;
    }

    if (!WC()->cart || WC()->cart->is_empty()) {
        return $available_gateways;
    }

    // Determine if cart contains a credit-enabled item
    $has_credit_item = false;

    foreach (WC()->cart->get_cart() as $cart_item) {
        if (!empty($cart_item['custom_inputs']['allow_credit'])) {
            $has_credit_item = true;
            break;
        }
    }

    /*
     * CREDIT ACCOUNT ORDER
     */
    if ($has_credit_item) {

        // Allow ONLY crediting gateway
        foreach ($available_gateways as $gateway_id => $gateway) {
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
