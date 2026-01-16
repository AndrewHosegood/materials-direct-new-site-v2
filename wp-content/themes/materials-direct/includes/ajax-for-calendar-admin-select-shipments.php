<?php
add_action('wp_ajax_update_order_status_checkbox_select', 'update_order_status_checkbox_select');

function update_order_status_checkbox_select() {

    global $wpdb;

    // Retrieve order ID and status from AJAX request
    $id = $_POST['id'];
    $is_merged = $_POST['is_merged'];

    echo "ID = " .  $id;
    echo "IS MERGED = " .  $is_merged;

    // Update the status in the database
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $result = $wpdb->update(
        $table_name,
        array('is_merged' => $is_merged),
        array('id' => $id),
        array('%d'),       
        array('%d') 
    );

    // Check if update was successful
    if ($result !== false) {

        echo "Status updated successfully";

    } else {
        echo "Somethings wrong";
        echo "Error updating status: " . $wpdb->last_error;
    }

    // Always exit to avoid further execution
    wp_die();
}