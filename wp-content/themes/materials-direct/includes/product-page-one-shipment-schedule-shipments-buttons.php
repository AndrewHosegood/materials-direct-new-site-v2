<?php

function one_shipment_scheduled_shipment_buttons_shortcode() {

    ob_start();

	$allow_credit = (bool) get_field('credit_options_allow_user_credit_option', 'user_' . get_current_user_id());

	if( is_user_logged_in() ){
		if(empty($allow_credit)){ ?>
			<div class="product-page__button_holder-wrapper">
				<a class="product-page__discount-table-info" href="#" id="split_schedule_instructions_msg_3">
					What are scheduled shipments? 
				</a>

				<div class="product-page__button_holder">
					<a class="product-page__one-shipment-button selected has-tooltip"
					data-tooltip="You want a simple One Shipment delivery and your paying with your Credit Card or Direct Bank Transfer"
					id="regular_dispatch">
						<p class="product-page__delivery-options-button-text">Selected</p>
						One Shipment
					</a>

					<a class="has-tooltip product-page__delivery-options-button-disabled" id="split_schedule_dispatch_2" data-tooltip="Only available with a Credit Account. (Fill in the Credit Account Application at the top of the page)." disabled=""><p class="product-page__delivery-options-button-text">Disabled</p>Scheduled Shipments</a>
				</div>
			</div>
		<?php } else { ?>
			<p style="color: red;">WE ARE LOGGED IN WITH A CREDIT ACCOUNT</p>
		<?php }

	} else { ?>
		<div>
            <a class="product-page__discount-table-info" href="#" id="split_schedule_instructions_msg_3">
                What are scheduled shipments? 
            </a>

            <div class="product-page__button_holder">
                <a class="product-page__one-shipment-button selected has-tooltip"
                   data-tooltip="You want a simple One Shipment delivery and your paying with your Credit Card or Direct Bank Transfer"
                   id="regular_dispatch">
                    <p class="product-page__delivery-options-button-text">Selected</p>
                    One Shipment
                </a>

                <a href="/my-account/?redirect=<?php echo urlencode( get_permalink() ); ?>"
                   id="login-redirect-btn"
                   class="product-page__delivery-options-button has-tooltip"
                   data-tooltip="You want to split your scheduled delivery options over multiple dates. (Credit Account required)">
                    <p class="product-page__delivery-options-button-text">Login Required</p>
                    Scheduled Shipments
                </a>

				<!-- <a class="has-tooltip product-page__delivery-options-button-disabled" id="split_schedule_dispatch_2" data-tooltip="Only available with a Credit Account. (Fill in the Credit Account Application at the top of the page)." disabled=""><p class="product-page__delivery-options-button-text">Disabled</p>Scheduled Shipments</a> -->
            </div>
        </div>
	<?php }	



    return ob_get_clean();
}

// Register the shortcode
add_shortcode( 'md_shipping_options', 'one_shipment_scheduled_shipment_buttons_shortcode' );


// Redirect the user back to the pruct page after logging in
add_filter( 'woocommerce_login_redirect', 'md_custom_login_redirect', 10, 2 );
function md_custom_login_redirect( $redirect, $user ) {

    if ( isset($_GET['redirect']) && ! empty($_GET['redirect']) ) {
        return esc_url_raw( $_GET['redirect'] );
    }

    return $redirect; // default WC behavior
}