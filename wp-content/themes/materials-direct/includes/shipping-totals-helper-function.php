<?php
/**
 * Custom helper: Output shipping row with forced correct formatting
 */
function md_custom_shipping_totals_row() {
    if ( ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) {
        return;
    }

    $shipping_methods = WC()->cart->get_shipping_methods();
    if ( empty( $shipping_methods ) ) {
        // Fallback: show calculator if no methods
        ?>
        <tr class="shipping">
            <th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
            <td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>">
                <?php woocommerce_shipping_calculator(); ?>
            </td>
        </tr>
        <?php
        return;
    }

    $currency_rate   = get_currency_rate();
    $currency_symbol = get_currency_symbol();

    foreach ( $shipping_methods as $method ) {
        $raw_cost       = (float) $method->cost;
        $converted_cost = $raw_cost * $currency_rate;
        $price_display  = $currency_symbol . number_format( $converted_cost, 2, '.', '' );

        ?>
        <tr class="shipping">
            <th><?php echo esc_html( $method->get_label() ); ?></th>
            <td data-title="<?php echo esc_attr( $method->get_label() ); ?>">
                <?php echo $price_display; ?>
            </td>
        </tr>
        <?php
    }
}