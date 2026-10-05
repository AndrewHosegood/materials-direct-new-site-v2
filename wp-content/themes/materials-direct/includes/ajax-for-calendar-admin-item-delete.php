<?php

add_action('wp_ajax_delete_split_schedule_order', 'delete_split_schedule_order');

function delete_split_schedule_order() {

    error_log('DELETE AJAX HANDLER REACHED');

    // Users allowed to delete shipment rows
    // allow andrewh@materials-direct.com
    // allow andrew.hosegood@sky.com
    $allowed_delete_users = array(1, 2236);

    // Security check
    if (!in_array(get_current_user_id(), $allowed_delete_users, true)) {
        wp_send_json_error('Permission denied');
    }

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (!$id) {
        wp_send_json_error('Invalid ID');
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'split_schedule_orders';

    $deleted = $wpdb->delete(
        $table_name,
        array(
            'id' => $id
        ),
        array(
            '%d'
        )
    );

    if ($deleted === false) {
        wp_send_json_error('Database delete failed');
    } else {
         wp_send_json_success('Shipment deleted');
    }

}