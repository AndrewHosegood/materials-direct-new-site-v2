<?php
add_action('woocommerce_cart_emptied', function() {
    WC()->session->__unset('custom_shipping_address');
    // Optional: also clear related sessions if needed
    // WC()->session->__unset('custom_qty');
    // WC()->session->__unset('custom_shipments');
}, 10);

add_action('woocommerce_cart_item_removed', function($cart_item_key, $cart) {
    if (WC()->cart->is_empty()) {
        WC()->session->__unset('custom_shipping_address');
    }
}, 10, 2);

// Extra safety: check on cart page load / AJAX updates
add_action('template_redirect', function() {
    if (is_cart() && WC()->cart->is_empty()) {
        WC()->session->__unset('custom_shipping_address');
    }
});