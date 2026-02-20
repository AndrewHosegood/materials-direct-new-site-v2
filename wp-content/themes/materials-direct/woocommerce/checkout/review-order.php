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
// Custom logic
$currency_rate   = get_currency_rate();
$currency_symbol = get_currency_symbol();
$is_gbp          = ( get_current_currency() === 'GBP' );
// End custom logic
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

				$line_subtotal_raw = (float) $cart_item['line_subtotal']; // excl. tax, before discounts
                $converted_subtotal = $line_subtotal_raw * $currency_rate;

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
						echo $currency_symbol . number_format( $converted_subtotal, 2, '.', '' );
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
	// Custom Logic
	$cart_subtotal_raw = (float) WC()->cart->get_subtotal(); // excl. tax
	$products_purchased_converted = $cart_subtotal_raw * $currency_rate;
	// End custom logic
	?>


	<tr class="cart-subtotal products-purchased">
            <th><?php esc_html_e( 'Products purchased', 'woocommerce' ); ?></th>
            <td><?php echo $currency_symbol . number_format( $products_purchased_converted, 2, '.', '' ); ?></td>
    </tr>
    <!-- Custom Cart Subtotal -->

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<?php
				$discount_raw = WC()->cart->get_coupon_discount_amount( $code ); // already negative or zero
				$converted_discount = abs( $discount_raw ) * $currency_rate; // we show positive value with -
            ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td>
                    <?php //wc_cart_totals_coupon_html( $coupon ); ?>
                        <!-- Custom Coupon Values -->
						<?php echo '-' . $currency_symbol . number_format( $converted_discount, 2, '.', '' ); ?>
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
				<td>
					<?php //wc_cart_totals_fee_html( $fee ); ?>
					<?php
                    $fee_amount_converted = (float) $fee->amount * $currency_rate;
                    echo $currency_symbol . number_format( $fee_amount_converted, 2, '.', '' );
                    ?>
				</td>
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
                            $tax_amount_raw = (float) $tax->amount;
                            $converted_tax  = $tax_amount_raw * $currency_rate;
                            echo $currency_symbol . number_format( $converted_tax, 2, '.', '' );
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
                        $taxes_total_raw = (float) WC()->cart->get_taxes_total( false, false );
                        $converted_taxes = $taxes_total_raw * $currency_rate;
                        echo $currency_symbol . number_format( $converted_taxes, 2, '.', '' );
                        ?>
                        <!-- Custom tax values -->
                    </td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<?php
        // custom logic
        $total_raw = method_exists( WC()->cart, 'get_total' ) && is_numeric( WC()->cart->get_total( 'edit' ) )
            ? (float) WC()->cart->get_total( 'edit' )
            : (float) WC()->cart->get_total();

        $converted_total = $total_raw * $currency_rate;
        $formatted_total = $currency_symbol . number_format( $converted_total, 2, '.', '' );

        $base_total_formatted = WC()->cart->get_total(); 
		// end // custom logic
        ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php //wc_cart_totals_order_total_html(); ?>
				<?php
					echo $formatted_total;

					if ( ! $is_gbp ) {
						echo '<br><small class="currency-disclaimer"><strong>';
						echo esc_html__( '(Payment will be made in GBP for a total of: ', 'woocommerce' );
						echo $base_total_formatted;
						echo ')</strong></small>';
					}
                ?>
            </td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>

<!-- Convert shipping to current currency rate -->
 <!--
<script>
	let shipping_rate = <?php //echo json_encode(get_currency_rate()); ?>;
	let currency_symbol = <?php //echo json_encode(get_currency_symbol()); ?>;
    let $priceEl = jQuery('#shipping_method .woocommerce-Price-amount bdi');



    if ($priceEl.length) {

		alert("we have something!");

        let currentPrice = parseFloat(
            $priceEl.text().replace('£', '').trim()
        );

        if (!isNaN(currentPrice)) {

            let newPrice = (currentPrice * shipping_rate).toFixed(2);

            $priceEl.html(
                '<span class="woocommerce-Price-currencySymbol">'+currency_symbol+'</span>' + newPrice
            );
        }
    } else {
		alert("we DONT have anything");
	}
</script>
-->
<!-- End convert shipping to current currency rate -->
