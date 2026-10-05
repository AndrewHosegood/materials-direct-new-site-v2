<?php
/**
 * Send the WooCommerce Admin New Order email for
 * Credit Account orders after the checkout order
 * has been fully constructed.
 *
 * The order remains Pending payment.
 */
add_action( 'woocommerce_checkout_order_processed', 'md_credit_account_admin_new_order_email', 20, 3 );

function md_credit_account_admin_new_order_email( $order_id, $posted_data, $order ) {

    if ( ! $order instanceof WC_Order ) {
        $order = wc_get_order( $order_id );
    }

    if ( ! $order ) {
        return;
    }

    // Only affect Credit Account orders.
    if ( 'crediting_gateway' !== $order->get_payment_method() ) {
        return;
    }

    // Get WooCommerce email objects.
    $mailer = WC()->mailer();

    if ( ! $mailer ) {
        return;
    }

    $emails = $mailer->get_emails();

    // Trigger the standard WooCommerce "New Order" email.
    if ( isset( $emails['WC_Email_New_Order'] ) ) {

        $emails['WC_Email_New_Order']->trigger( $order_id, $order );
    }
}