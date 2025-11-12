<?php
function mytheme_replace_wc_product_gallery() {
    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
    add_action( 'woocommerce_before_single_product_summary', 'mytheme_static_product_image', 20 );
}

add_action( 'wp', 'mytheme_replace_wc_product_gallery' );


function mytheme_static_product_image() {
    global $product;

    if ( ! $product ) {
        return;
    }

    $image_id  = $product->get_image_id();
    $image_url = wp_get_attachment_image_url( $image_id, 'large' );

    $swap_image_url = "/wp-content/uploads/2025/11/product_hover_with_text.jpg";

    if ( $image_url ) {
        echo '<div class="single-product__gallery-image-col product-image-static images">';
        echo '<img class="single-product__gallery-image" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
        echo '<img class="single-product__gallery-image-zoom" src="' . esc_url( $swap_image_url ) . '" alt="Custom pads and gaskets priced instantly and made to order within 24 hours!" />';
        echo '</div>';
    } else {
        echo wc_placeholder_img( 'large' );
    }
}