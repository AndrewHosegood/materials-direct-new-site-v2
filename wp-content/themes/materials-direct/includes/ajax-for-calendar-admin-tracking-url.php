<?php
add_action('wp_ajax_update_tracking_details_url', 'update_tracking_details_url');

function update_tracking_details_url() {

    global $wpdb;

    // Retrieve order ID and status from AJAX request
    $id = $_POST['id'];
    $tracking_number_details = $_POST['tracking_number_url'];

    // Update the status in the database
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $result = $wpdb->update(
        $table_name,
        array('shipment_tracking_url' => $tracking_number_details),
        array('id' => $id),
        array('%s'),       
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