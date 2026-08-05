<?php
add_filter( 'woocommerce_email_order_number', function( $order_number, $order ) {
    if ( is_a( $order, 'WC_Order' ) ) {
        return $order->get_order_number();
    }
    return $order_number;
}, 10, 2 );