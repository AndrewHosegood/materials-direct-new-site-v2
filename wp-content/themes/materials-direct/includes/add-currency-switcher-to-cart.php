<?php
/**
 * Inject currency switcher at the top of the cart page
 */
function inject_cart_currency_switcher() {

	$currency = get_current_currency();
	//echo "Currency cart page: " . $currency . "<br>";

    echo '<div class="cart__currency-switcher" style="float: right;">';
    echo '<div class="cart__currency-switcher-box">';

    echo '<a class="cart__currency-switcher-link ' . ($currency === 'USD' ? 'active-currency' : '') . '" href="?set_currency=USD">
            <div class="box-symbol">$</div>
          </a>';

    echo '<a class="cart__currency-switcher-link ' . ($currency === 'GBP' ? 'active-currency' : '') . '" href="?set_currency=GBP">
            <div class="box-symbol">£</div>
          </a>';

    echo '<a class="cart__currency-switcher-link ' . ($currency === 'EUR' ? 'active-currency' : '') . '" href="?set_currency=EUR">
            <div class="box-symbol">€</div>
          </a>';

    echo '</div>';

    echo '<p class="cart__currency-switcher-text">
            Please note, transaction will be made in GBP. 
            Currencies displayed are for indication purposes. 
            You may also incur a foreign transaction fee.
          </p>';

    echo '</div>';
}

// Hook into WooCommerce cart page (top of cart)
add_action('woocommerce_before_cart', 'inject_cart_currency_switcher');

// Hook into WooCommerce checkout page (top of checkout, directly mirroring the cart position)
add_action('woocommerce_before_checkout_form', 'inject_cart_currency_switcher');