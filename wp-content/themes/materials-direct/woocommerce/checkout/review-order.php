<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>

<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-name">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>

					<td class="product-total">
						<?php 
                        //echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                        // Custom values
                        $raw = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						$raw = preg_replace("/[^0-9.]/", "", $raw) * get_currency_rate();
						$raw = number_format((float)$raw, 2, '.', '');
						echo get_currency_symbol().$raw;
                        // Custom values
                         ?>
					</td>

				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<!-- <tr class="cart-subtotal">
			<th><?php //esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php //wc_cart_totals_subtotal_html(); ?></td>
		</tr> -->
    
    <!-- Custom Cart Subtotal -->    
    <?php
	foreach( WC()->cart->get_cart() as $cart_item ) {

		$ship = (float)$cart_item['_gravity_form_lead'][76];
		$newCustomShipping += $ship;

	}
	$amount2 = floatval( preg_replace( '#[^\d.]#', '',  WC()->cart->get_cart_total() ) );
	$productsPurchased = $amount2; //$amount2 - $newCustomShipping;
	$productsPurchased = number_format($productsPurchased, 2);
	$newCustomShipping = number_format($newCustomShipping, 2);
	?>

	<tr class="cart-subtotal">
		<th><?php esc_html_e( 'Products purchased', 'woocommerce' ); ?></th>
		<td><?php echo get_currency_symbol().$productsPurchased; ?></td>
	</tr>
    <!-- Custom Cart Subtotal -->

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td>
                    <?php //wc_cart_totals_coupon_html( $coupon ); ?>
                        <!-- Custom Coupon Values -->
                        <?php 
                        //wc_cart_totals_coupon_html( $coupon ); 
                        ob_start();
                        wc_cart_totals_coupon_html( $coupon );
                        $coupon_html = ob_get_clean();

                        // Use regex to extract the numeric value from the captured HTML
                        preg_match("/-?[\d,\.]+/", $coupon_html, $matches);
                        $coupon_value_str = $matches[0];

                        // Remove any commas (for thousands) and convert to a float
                        $coupon_value_float = floatval(str_replace(',', '', $coupon_value_str));

                        // Apply the currency conversion
                        $coupon_final_value = $coupon_value_float * get_currency_rate();

                        // Output the final converted value with the currency symbol
                        echo "-" . get_currency_symbol() . number_format($coupon_final_value, 2, '.', '');
                        ?>
                        <!-- Custom Coupon Values -->
                </td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td>
                            <?php //echo wp_kses_post( $tax->formatted_amount ); ?>
                            <!-- Custom tax values -->
                            <?php
                            $raw = $tax->formatted_amount;
                            $raw = preg_replace("/[^0-9.]/", "", $raw) * get_currency_rate();
                            $raw = number_format((float)$raw, 2, '.', '');
                            echo get_currency_symbol().$raw;
                            ?>
                            <!-- Custom tax values -->
                        </td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php //wc_cart_totals_taxes_total_html(); ?>
                        <!-- Custom tax values -->
                        <?php
                            $raw = WC()->cart->get_taxes_total();
                            $raw = preg_replace("/[^0-9.]/", "", $raw) * get_currency_rate();
                            $raw = number_format((float)$raw, 2, '.', '');
                            echo get_currency_symbol().$raw;
                        ?>
                        <!-- Custom tax values -->
                    </td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php //wc_cart_totals_order_total_html(); ?>
                <?php
                    $raw = WC()->cart->get_total();
                    $raw = preg_replace("/[^0-9.]/", "", $raw) * get_currency_rate();
                    $raw = number_format((float)$raw, 2, '.', '');

					if (get_current_currency() !== 'GBP') {
						echo get_currency_symbol().$raw . ' <br/><small class="currency-disclaimer"><strong>(Payment will be made in GBP for a total of: '.WC()->cart->get_total().')</strong></small>';
					} else {
						echo get_currency_symbol().$raw;
					}
                    
                ?>
            </td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
