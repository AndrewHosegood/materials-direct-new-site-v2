<?php
add_action('template_redirect', 'clear_custom_sessions_on_product_page');

function clear_custom_sessions_on_product_page() {

    if (!is_product()) {
        return;
    }

    if (function_exists('WC') && WC()->session) {

        if (WC()->session->get('custom_shipments')) {
            WC()->session->__unset('custom_shipments');
        }

        if (WC()->session->get('custom_qty')) {
            WC()->session->__unset('custom_qty');
        }
    }
}
