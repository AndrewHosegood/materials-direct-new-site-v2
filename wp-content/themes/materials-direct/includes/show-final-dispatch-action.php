<?php
add_action('wp_ajax_show_final_dispatch_action', 'show_final_dispatch_action');
function show_final_dispatch_action() {
    global $wpdb; // Include global $wpdb variable
    $order_no = $_POST['orderNo']; // Correct variable name
    $make_active = 1;
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $result = $wpdb->update(
        $table_name,
        array('make_active' => $make_active),
        array('order_no' => $order_no),
        array('%d'),
        array('%d')
    );

    if (false !== $result) {
        echo 'Success';
    } else {
        echo 'Fail: ' . $wpdb->last_error;
    }
    
    wp_die();
}
