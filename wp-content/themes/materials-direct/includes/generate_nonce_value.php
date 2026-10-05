<?php
add_action('wp_ajax_get_custom_price_nonce', 'get_custom_price_nonce');
add_action('wp_ajax_nopriv_get_custom_price_nonce', 'get_custom_price_nonce');

function get_custom_price_nonce() {

    nocache_headers();

    header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
    header( 'Pragma: no-cache' );

    wp_send_json_success(
        array(
            'nonce' => wp_create_nonce( 'custom_price_nonce' ),
        )
    );
}