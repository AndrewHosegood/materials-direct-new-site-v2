<?php
if ( ! function_exists( 'custom_add_info_after_product_thumbnail' ) ) {
    add_action( 'woocommerce_after_shop_loop_item', 'custom_add_info_after_product_thumbnail', 5 );
    function custom_add_info_after_product_thumbnail() {
        global $product;

        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            return;
        }

        $product_link = get_permalink( $product->get_id() );
        $product_title = get_the_title( $product->get_id() );
        $product_sku = $product->get_sku();
        $short_desc_raw = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
        

        echo '</div>';
        echo '<div class="woocommerce-shop__info-card">';
        echo '<h4 class="woocommerce-shop__card-title"><a class="woocommerce-shop__card-link" href="' . esc_url( $product_link ) . '">' . esc_html( $product_title ) . '</a></h4>';
        
        if ( $product_sku ) {
            echo '<div class="woocommerce-shop__info-sku">';
            echo '<span class="woocommerce-shop__i-sku">SKU: ' . esc_html( $product_sku ) . '</span>';
            echo '</div>';
        }

        echo '</div>';

        // Short description block
        if ( $short_desc_raw ) {
            echo '<div class="woocommerce-shop__short-description">';
            echo $short_desc_raw;
            echo '</div>';
        }
    }
}