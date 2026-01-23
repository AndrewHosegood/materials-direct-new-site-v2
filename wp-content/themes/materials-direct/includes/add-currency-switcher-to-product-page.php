<?php
add_action( 'woocommerce_single_product_summary', 'my_product_subtitle_under_title', 6 );
function my_product_subtitle_under_title() {
    
    if ( ! function_exists( 'get_field' ) ) {
        return; // ACF not active
    }

    // Get the ACF field (attached to the product)
    $subtitle = get_field( 'product_subtitle' );

	$currency = get_current_currency();

    if ( $subtitle ) {
        echo '<h2 class="product-page__subtitle">' . esc_html( $subtitle ) . '</h2>';
    }
	echo '<div class="product-page__currency-switcher">';
	echo '<p class="product-page__currency-switcher-text">To get an instant quote for your custom parts, please select your currency and follow steps 1 to 4.</p>';
	echo '<div class="product-page__currency-switcher-box">
		<a class="product-page__currency-switcher-link '.($currency === 'USD' ? 'active-currency' : '').'" href="?set_currency=USD">
			<div class="box-symbol">$</div>
		</a>
		<a class="product-page__currency-switcher-link '.($currency === 'GBP' ? 'active-currency' : '').'" href="?unset_currency=1">
			<div class="box-symbol ">£</div>
		</a>
		<a class="product-page__currency-switcher-link '.($currency === 'EUR' ? 'active-currency' : '').'" href="?set_currency=EUR">
			<div class="box-symbol ">€</div>
		</a>
	</div>';
	echo '</div>';
}