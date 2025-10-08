<?php
add_action( 'woocommerce_thankyou', 'debug_display_order_object', 20 );

function debug_display_order_object( $order_id ) {
    if ( ! $order_id ) return;

    $order = wc_get_order( $order_id );

    echo '<h3>Debug Order Meta (Order Post Meta)</h3>';
    echo '<ul style="font-family:monospace;">';
    $meta = get_post_meta( $order_id );
    foreach ( $meta as $key => $values ) {
        echo '<li><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( maybe_serialize( $values[0] ) ) . '</li>';
    }
    echo '</ul>';

    echo '<h3>Debug Item Meta (Per Line Item)</h3>';
    foreach ( $order->get_items() as $item_id => $item ) {
        echo '<h4>Item: ' . esc_html( $item->get_name() ) . '</h4>';
        echo '<ul style="font-family:monospace;">';
        foreach ( $item->get_meta_data() as $meta_obj ) {
            $meta_key   = $meta_obj->key;
            $meta_value = $meta_obj->value;
            echo '<li><strong>' . esc_html( $meta_key ) . ':</strong> ' . esc_html( maybe_serialize( $meta_value ) ) . '</li>';
        }
        echo '</ul>';
    }
}
