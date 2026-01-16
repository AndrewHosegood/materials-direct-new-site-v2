<?php

add_filter('manage_edit-shop_order_columns', 'add_split_schedule_status_column', 999);

function add_split_schedule_status_column($columns) {
    $columns['split_schedule_status'] = __('Split Schedule Status', 'woocommerce');
    return $columns;
}

add_action('manage_shop_order_posts_custom_column', 'render_split_schedule_status_column', 10, 2);

function render_split_schedule_status_column($column, $post_id) {
    if ($column === 'split_schedule_status') {
        $order = wc_get_order($post_id);
        if ($order && $order->get_status() === 'pending') { // 'pending' is the internal WooCommerce status for "Payment Pending"
            echo '<a href="/wp-admin/admin.php?page=view_admin&calendar__search='.esc_html($order->get_order_number()).'&Search=search">View Status</a>';
        }
    }
}

