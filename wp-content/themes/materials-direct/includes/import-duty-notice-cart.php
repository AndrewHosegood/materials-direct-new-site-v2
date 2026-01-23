<?php
function import_duty_notice_cart() {
	$shipping_address = WC()->session->get('custom_shipping_address');
	if ($shipping_address) {
        $cart_country = $shipping_address['country'];
		if($cart_country != 'United Kingdom'){
			echo '<p class="currency_notice">Please note, for orders shipped outside of the UK import duties may apply.</p>';
		}
    }	
}

add_action('woocommerce_proceed_to_checkout', 'import_duty_notice_cart');