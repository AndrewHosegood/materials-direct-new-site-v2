<?php
add_action( 'woocommerce_thankyou', 'debug_display_order_object', 20 );

function debug_display_order_object( $order_id ) {
    if ( ! $order_id ) return;

    // Get the order object
    $order = wc_get_order( $order_id );

    if ( ! $order ) return;

    echo '<pre style="background:#f4f4f4; padding:15px; border:1px solid #ddd; overflow:auto;">';
    echo '<strong>WooCommerce Order Object:</strong><br><br>';
    print_r( $order );
    echo '</pre>';
}