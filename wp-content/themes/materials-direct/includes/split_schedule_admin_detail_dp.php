<?php
function shipment_details_content() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'split_schedule_orders';

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (!$id) {
        echo '<div class="wrap">';
        echo '<h1>Shipment Details</h1>';
        echo '<p>Invalid shipment ID.</p>';
        echo '</div>';
        return;
    }

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        )
    );

    if (!$row) {
        echo '<div class="wrap">';
        echo '<h1>Shipment Details</h1>';
        echo '<p>Shipment not found.</p>';
        echo '</div>';
        return;
    }

    echo '<div class="wrap">';

    echo '<h1>Shipment Details</h1>';

    echo '<p><strong>Order Number:</strong> ' . esc_html($row->order_no) . '</p>';

    echo '<p>';
    echo '<a href="' . esc_url(admin_url('admin.php?page=view_admin')) . '">Return</a>';
    echo '</p>';

    echo '</div>';
}