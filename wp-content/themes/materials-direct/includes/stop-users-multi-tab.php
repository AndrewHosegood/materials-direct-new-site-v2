<?php
add_action('wp_enqueue_scripts', function () {

    if (!is_product()) {
        return;
    }

    // Get current user
    $user_id = get_current_user_id();

    if (!$user_id) {
        return; // not logged in
    }

    // ACF field
    $allow_credit = (bool) get_field('credit_options_allow_user_credit_option', 'user_' . $user_id);

    // WooCommerce session
    if (!WC()->session) {
        return;
    }


    // Only proceed if BOTH conditions are true
    if ($allow_credit) {
    //if ($allow_credit) {

    wp_enqueue_script(
        'wc-page-lock',
        get_stylesheet_directory_uri() . '/js/page_lock.js',
        [],
        '1.0',
        true
    );

    }

});