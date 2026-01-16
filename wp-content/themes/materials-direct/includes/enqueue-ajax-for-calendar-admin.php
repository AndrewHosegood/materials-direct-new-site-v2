<?php
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
function enqueue_admin_scripts() {
    wp_enqueue_script('admin-ajax', admin_url('admin-ajax.php'), array('jquery'), null, true);
    wp_enqueue_script('custom-admin-script', '/wp-content/themes/materials-direct/js/custom-admin-script.js', array('jquery'), null, true);

    // Pass Ajax Url to script.js
    wp_localize_script('custom-admin-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}