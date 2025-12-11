<?php
function hide_credit_account_active() {
    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id); 
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false;

    // User logged out OR logged in but NO credit allowed
    if ( ! is_user_logged_in() || $allow_credit == 0 ) {
        echo '<style>#menu-item-1052 { display: none !important; }</style>';
    }

    // User logged in AND credit IS allowed
    if ( is_user_logged_in() && $allow_credit == 1 ) {
        echo '<style>#menu-item-1051 { display: none !important; }</style>';
    }

}
add_action('wp_head', 'hide_credit_account_active');