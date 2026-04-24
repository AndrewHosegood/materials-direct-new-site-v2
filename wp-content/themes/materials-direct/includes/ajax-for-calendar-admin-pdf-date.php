<?php
add_action('wp_ajax_update_pdf_despatch_date', 'update_pdf_despatch_date_callback');
function update_pdf_despatch_date_callback() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    
    $id                = intval($_POST['id']);
    $pdf_despatch_date = sanitize_text_field($_POST['pdf_despatch_date']);

    // Update the row
    $result = $wpdb->update(
        $table_name,
        array('pdf_despatch_date' => $pdf_despatch_date),
        array('id' => $id),
        array('%s'),   // format for pdf_despatch_date
        array('%d')    // format for id
    );

    if ($result !== false) {
        wp_send_json_success('PDF Despatch Date updated successfully');
    } else {
        wp_send_json_error('Failed to update PDF Despatch Date');
    }

    wp_die(); // Always end AJAX callbacks with wp_die()
}