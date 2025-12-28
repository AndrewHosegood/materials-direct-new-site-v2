<?php
add_action( 'woocommerce_email_footer', 'bbloomer_remove_get_the_app_ad', 8 );
  
function bbloomer_remove_get_the_app_ad() {
   $mailer = WC()->mailer()->get_emails();
   $object = $mailer['WC_Email_New_Order'];
   remove_action( 'woocommerce_email_footer', array( $object, 'mobile_messaging' ), 9 );
}

