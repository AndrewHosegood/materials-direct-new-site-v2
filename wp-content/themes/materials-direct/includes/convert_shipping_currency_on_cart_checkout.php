<?php
add_filter( 'woocommerce_cart_shipping_method_full_label', function( $label, $method ) {
    $current_currency = get_current_currency();

    if ( $current_currency === 'GBP' ) {
        return $label; // Keep default for base currency
    }

    $rate           = (float) get_currency_rate();
    $original_cost  = (float) $method->cost;
    $converted_cost = $original_cost * $rate;

    // Decide label prefix (method title)
    $method_title = $method->get_label();           // Usually the admin-set title, e.g. "Custom Shipping Method1"
    if ( empty( $method_title ) ) {
        $method_title = $method->get_method_title(); // Fallback
    }

    // Format converted price with YOUR currency symbol & settings
    // wc_price() doesn't natively support arbitrary currency, so we build it manually but mimic WC format
    $price_html = sprintf(
        '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">%1$s</span>%2$s</bdi></span>',
        esc_html( get_currency_symbol() ),
        number_format( $converted_cost, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() )
    );

    // For free shipping or zero
    if ( 0 === $converted_cost ) {
        $price_html = apply_filters( 'woocommerce_free_shipping_label', esc_html__( 'Free!', 'woocommerce' ) );
    }

    // Rebuild full label (most common pattern: "Title: Price" or just "Price")
    $full_label = $method_title . ': ' . $price_html;

    // Optional: If your shipping methods use no colon or different separator, adjust here
    // $full_label = $method_title . ' — ' . $price_html;

    return $full_label;

}, 100, 2 );