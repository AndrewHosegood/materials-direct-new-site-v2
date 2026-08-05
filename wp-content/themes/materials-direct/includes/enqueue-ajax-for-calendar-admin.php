<?php
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

function enqueue_admin_scripts($hook) {

    if ($hook !== 'toplevel_page_custompage' && $hook !== 'calendar_page_view_admin') {
        return;
    }

    wp_enqueue_script('custom-admin-script', get_template_directory_uri() . '/js/custom-admin-script.js', ['jquery'], null, true);

    wp_localize_script('custom-admin-script', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php'),]);
}
/*
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
function enqueue_admin_scripts() {
    wp_enqueue_script('admin-ajax', admin_url('admin-ajax.php'), array('jquery'), null, true);
    wp_enqueue_script('custom-admin-script', '/wp-content/themes/materials-direct/js/custom-admin-script.js', array('jquery'), null, true);

    // Pass Ajax Url to script.js
    wp_localize_script('custom-admin-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
    */