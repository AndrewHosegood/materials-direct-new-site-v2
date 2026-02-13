<?php
add_action('woocommerce_thankyou', 'conditionally_add_styling_is_scheduled', 10, 1);

function conditionally_add_styling_is_scheduled( $order_id ) {

    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );

    foreach ( $order->get_items() as $item ) {

        $is_scheduled = (int) $item->get_meta( 'is_scheduled' );

        if ( $is_scheduled === 1 ) {

            echo '<style>
                .woocommerce-order-received .woocommerce-thankyou-order-received {
                    margin-top: 4rem !important;
                }
            </style>';

            break; // stop once found
        }
    }
}