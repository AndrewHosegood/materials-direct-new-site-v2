<?php
function add_credit_status_to_product_page() {
	global $product;
    $credit_limit_remaining = (float) get_field('credit_options_credit_limit_remaining', 'user_' . get_current_user_id());
    $original_credit_allowence = (float) get_field('credit_options_original_credit_allowence', 'user_' . get_current_user_id());
    $allow_credit = (bool) get_field('credit_options_allow_user_credit_option', 'user_' . get_current_user_id());
	$is_product_single = function_exists('get_field') ? get_field('is_product_single', $product->get_id()) : false;
    
	if(!$is_product_single){
		if($allow_credit){

			if ($credit_limit_remaining <= 16.50) {
				echo '<p style="margin: 17px 0 0 0;" class="ran-out-of-credits">'
					. 'You currently have <strong>&pound;' . esc_html(number_format($credit_limit_remaining, 2)) . ' of credit.</strong> '
					. 'You do not have enough funds to fulfill your order. Please call the accounts department at Materials Direct on '
					. '<strong>01908 222211</strong> or email <strong>info@materials-direct.com</strong>.</p>';
			} 

			if ($credit_limit_remaining > 16.50) {
				echo '<p style="margin: 17px 0 0 0;" class="ran-out-of-credits-blue">'
					. 'You currently have <strong>&pound;' . esc_html(number_format($credit_limit_remaining, 2)) . ' of credit.</strong> '
					. 'Always make sure you have enough funds to fulfill your order. Please pay all outstanding invoices or call the accounts department at Materials Direct on 01908 222211 or email info@materials-direct.com to increase your credit limit.</strong>'.$is_product_single.'</p>';
			}
			
		}
	}
    

}
add_action('woocommerce_before_add_to_cart_button', 'add_credit_status_to_product_page', 0);