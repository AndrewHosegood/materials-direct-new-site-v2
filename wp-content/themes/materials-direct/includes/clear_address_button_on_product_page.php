<?php
add_action('wp_ajax_clear_custom_shipping_address', 'clear_custom_shipping_address_callback');
add_action('wp_ajax_nopriv_clear_custom_shipping_address', 'clear_custom_shipping_address_callback');

function clear_custom_shipping_address_callback() {
    // Log everything for debugging
    error_log('Clear address AJAX called');
    error_log('POST data: ' . print_r($_POST, true));

    // Verify nonce – action name MUST match wp_create_nonce()
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'clear_custom_shipping_address_nonce')) {
        error_log('Nonce verification failed');
        wp_send_json_error(['message' => 'Security check failed'], 403);
    }

    WC()->session->__unset('custom_shipping_address');

    error_log('Custom shipping address cleared successfully');

    wp_send_json_success([
        'message' => 'Address cleared successfully',
        'reload'  => true
    ]);
}