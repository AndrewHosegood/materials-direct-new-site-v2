<?php
add_action( 'woocommerce_before_add_to_cart_button', 'show_stock_sheet_size', 2 );
function show_stock_sheet_size() {
    global $product;

    if ( ! $product ) {
        return;
    }

    // Get shipping dimensions
    $width  = $product->get_width();
    $length = $product->get_length();

    // Is the product a single product?
    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product->get_id()) : false;

    if (!$is_product_single) {
        // Only display if both width and length exist
        if ( $width && $length ) {
            // Format as cm (WooCommerce stores dimensions in the store unit, e.g. cm/mm/inch)
            echo '<p class="product-page__stock-sheet-size">';
            echo 'Stock sheet size: ' . esc_html( $width ) . 'cm x ' . esc_html( $length ) . 'cm';
            echo '</p>';
        }
    }

}