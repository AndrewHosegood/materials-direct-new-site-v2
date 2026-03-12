<?php
add_action('admin_enqueue_scripts', 'crediting_gateway_admin_script');

function crediting_gateway_admin_script($hook) {

    $screen = get_current_screen();

    if (!$screen) {
        return;
    }

    // WooCommerce Orders list
    if ($screen->id === 'edit-shop_order') {
        wp_enqueue_script(
            'crediting-gateway-script',
            get_stylesheet_directory_uri() . '/js/crediting-gateway.js',
            array('jquery'),
            '1.0',
            true
        );
    }

    // WooCommerce Order detail page
    if ($screen->id === 'shop_order') {
        wp_enqueue_script(
            'crediting-gateway-script',
            get_stylesheet_directory_uri() . '/js/crediting-gateway.js',
            array('jquery'),
            '1.0',
            true
        );
    }
}