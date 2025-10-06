<?php
function custom_hide_despatched_within_css() {
    // Simulate the credit setting from your code context
    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id); // Adjust this to how you store it
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false;

    // Conditions: only inject if allow_credit is 0/false and user is logged in
    if ( ! is_user_logged_in() || $allow_credit != 0 ) {
        return;
    }

    // Check if this is a single product page
    if ( is_product() ) {
        echo '<style>#despatched_within { display: none !important; }</style>';
    }
}
add_action('wp_head', 'custom_hide_despatched_within_css');