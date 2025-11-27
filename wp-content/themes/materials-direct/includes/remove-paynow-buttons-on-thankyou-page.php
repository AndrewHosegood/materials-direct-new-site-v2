<?php
/**
 * Remove the 'Actions' row (pay/cancel buttons) on the Thank You page.
 */
add_filter( 'woocommerce_my_account_my_orders_actions', '__return_empty_array' );

add_filter( 'woocommerce_available_order_actions', function( $actions ) {
    return array(); // Remove all actions (pay, cancel, view, etc.)
});