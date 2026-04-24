<?php
function calendar_admin_scripts($hook) {
    // Only load on your custom page
    if (isset($_GET['page']) && $_GET['page'] === 'view_admin') {

        // jQuery UI Datepicker (comes with WordPress)
        wp_enqueue_script('jquery-ui-datepicker');

        // Optional: jQuery UI theme (required for styling)
        wp_enqueue_style(
            'jquery-ui-css',
            'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css'
        );

        // Your custom JS (init datepicker)
        wp_enqueue_script(
            'calendar-datepicker-init',
            get_stylesheet_directory_uri() . '/js/delivery-options-datepicker.js',
            array('jquery', 'jquery-ui-datepicker'),
            '1.0',
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'calendar_admin_scripts');