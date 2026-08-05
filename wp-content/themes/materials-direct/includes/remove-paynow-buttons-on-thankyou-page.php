<?php
/**
 * Remove the 'Actions' row (pay) on the Thank You page.
 */
add_filter( 'woocommerce_my_account_my_orders_actions', function( $actions, $order ) {

    // Remove the Pay action
    if ( isset( $actions['pay'] ) ) {
        unset( $actions['pay'] );
    }

    // Remove the Cancel action
    if ( isset( $actions['cancel'] ) ) {
        unset( $actions['cancel'] );
    }

    return $actions;

}, 10, 2 );