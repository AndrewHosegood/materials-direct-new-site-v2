<?php
add_action( 'woocommerce_email_footer', 'bbloomer_remove_get_the_app_ad', 8 );
  
function bbloomer_remove_get_the_app_ad() {
   $mailer = WC()->mailer()->get_emails();
   $object = $mailer['WC_Email_New_Order'];
   remove_action( 'woocommerce_email_footer', array( $object, 'mobile_messaging' ), 9 );
}




add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'remove_order_item_meta_string', 10, 2 );

function remove_order_item_meta_string( $formatted_meta, $item ) {
    // Loop through formatted meta and unset specific keys.
    foreach ( $formatted_meta as $key => $meta ) {
        if ( in_array( $meta->key, array( 'despatch_string', 'Cost Per Part', 'price', 'is_scheduled', 'stock_quantity', 'scheduled_shipments' ) ) ) {
            unset( $formatted_meta[ $key ] );
        }
    }

    return $formatted_meta;
}


add_filter( 'woocommerce_order_get_payment_method_title', 'custom_style_payment_method_title_in_emails', 10, 2 );

function custom_style_payment_method_title_in_emails( $title, $order ) {
    // Only apply in email notifications (customer and admin emails).
    if ( did_action( 'woocommerce_email_order_details' ) === 0 ) {
        return $title;
    }

    return '<div style="font-size:10px; line-height:12px; max-width:130px;">' . $title . '</div>';
}