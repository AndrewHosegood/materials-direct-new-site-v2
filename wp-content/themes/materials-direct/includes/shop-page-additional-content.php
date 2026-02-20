<?php

// Global flag to detect when we are inside the cross-sells loop
global $is_cross_sells_image_modify;
$is_cross_sells_image_modify = false;

add_action( 'woocommerce_before_cross_sells', function() {
    global $is_cross_sells_image_modify;
    $is_cross_sells_image_modify = true;
} );

add_action( 'woocommerce_after_cross_sells', function() {
    global $is_cross_sells_image_modify;
    $is_cross_sells_image_modify = false;
} );

// Hide the default WooCommerce loop title only in cross-sells
add_action( 'woocommerce_before_cross_sells', function() {
    remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
} );

add_action( 'woocommerce_after_cross_sells', function() {
    add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
} );

// Your existing function (unchanged)
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
        $product_terms = get_the_terms( $product->get_id(), 'product_cat' );
       
        echo '</div>';
        echo '<div class="woocommerce-shop__info-card">';
        echo '<h4 class="woocommerce-shop__card-title"><a class="woocommerce-shop__card-link" href="' . esc_url( $product_link ) . '">' . esc_html( $product_title ) . '</a></h4>';
       
        if ( $product_sku ) {
            echo '<div class="woocommerce-shop__info-sku">';
            echo '<span class="woocommerce-shop__i-sku">SKU: ' . esc_html( $product_sku ) . '</span>';
            echo '</div>';
        }
        // Categories list
        if ( ! is_wp_error( $product_terms ) && ! empty( $product_terms ) ) {
            echo '<div class="woocommerce-shop__i-cat">';
            $cats = [];
            foreach ( $product_terms as $term ) {
                $cats[] = '<a class="woocommerce-shop__i-cat-link" href="' . esc_url( get_term_link( $term ) ) . '" rel="tag">' . esc_html( $term->name ) . '</a>';
            }
            echo implode( ', ', $cats );
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

/**
 * Updated image filter – now applies to cross-sells while still skipping cart item thumbnails
 */
add_filter( 'woocommerce_product_get_image', 'inject_soft_border_and_add_img_class', 10, 5 );
function inject_soft_border_and_add_img_class( $image, $product, $size, $attr, $placeholder ) {
    global $is_cross_sells_image_modify;

    // Skip cart item thumbnails, but allow cross-sells
    if ( is_cart() && ! $is_cross_sells_image_modify ) {
        return $image;
    }

    // Only apply in main shop loop OR when explicitly in cross-sells
    if ( ! in_the_loop() && ! $is_cross_sells_image_modify ) {
        return $image;
    }

    /* -------------------------------------------------
        1. Ensure <img> tag has class "woocommerce-shop__img"
       ------------------------------------------------- */
    if ( strpos( $image, 'class="' ) !== false ) {
        $image = preg_replace(
            '/class="([^"]*)"/',
            'class="$1 woocommerce-shop__img"',
            $image,
            1
        );
    } else {
        $image = preg_replace(
            '/<img/',
            '<img class="woocommerce-shop__img"',
            $image,
            1
        );
    }

    /* -------------------------------------------------
        2. Inject <div class="soft-border"></div> before <img>
       ------------------------------------------------- */
    $soft_border = '<div class="woocommerce-shop__soft-border"></div>';
    if ( strpos( $image, 'woocommerce-shop__soft-border' ) === false ) {
        $image = preg_replace( '/<img/i', $soft_border . '<img', $image, 1 );
    }

    return $image;
}

/**
 * Updated – now also shows on cart page cross-sells
 */
add_action( 'woocommerce_before_shop_loop_item', 'inject_cat_hover_image' );
function inject_cat_hover_image() {
    if ( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() ) {
        echo '<img class="woocommerce-shop__cat-hover-image" src="/wp-content/uploads/2025/11/category_hover_with_text.jpg" alt="">';
    }
}

/**
 * Updated – now also shows on cart page cross-sells
 */
add_action( 'woocommerce_before_shop_loop_item', 'inject_cat_hover_links', 5 );
function inject_cat_hover_links() {
    if ( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() ) {
        global $product;
        $product_id = $product->get_id();
        $download_file = '';

        if ( have_rows('download_items', $product_id) ) {
            while ( have_rows('download_items', $product_id) ) {
                the_row();
                $download_file = get_sub_field('download_file');
                break;
            }
        }

        echo '<div class="woocommerce-shop__image-links double">';
        echo '<a class="woocommerce-shop__image-links-link" rel="nofollow" href="' . get_the_permalink() . '">
                Order<br>Custom Parts
              </a>';

        if ( $download_file ) {
            echo '<a class="woocommerce-shop__image-links-link" target="_blank" href="' . esc_url( $download_file ) . '">
                    Product<br>Data Sheet
                  </a>';
        }
       
        echo '</div>';
    }
}

/*
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
        $product_terms    = get_the_terms( $product->get_id(), 'product_cat' );
        

        echo '</div>';
        echo '<div class="woocommerce-shop__info-card">';
        echo '<h4 class="woocommerce-shop__card-title"><a class="woocommerce-shop__card-link" href="' . esc_url( $product_link ) . '">' . esc_html( $product_title ) . '</a></h4>';
        
        if ( $product_sku ) {
            echo '<div class="woocommerce-shop__info-sku">';
            echo '<span class="woocommerce-shop__i-sku">SKU: ' . esc_html( $product_sku ) . '</span>';
            echo '</div>';
        }


        if ( ! is_wp_error( $product_terms ) && ! empty( $product_terms ) ) {
            echo '<div class="woocommerce-shop__i-cat">';
            $cats = [];

            foreach ( $product_terms as $term ) {
                $cats[] = '<a class="woocommerce-shop__i-cat-link" href="' . esc_url( get_term_link( $term ) ) . '" rel="tag">' . esc_html( $term->name ) . '</a>';
            }

            echo implode( ', ', $cats );
            echo '</div>';
        }

        echo '</div>';


        if ( $short_desc_raw ) {
            echo '<div class="woocommerce-shop__short-description">';
            echo $short_desc_raw;
            echo '</div>';
        }
    }
}



add_filter( 'woocommerce_product_get_image', 'inject_soft_border_and_add_img_class', 10, 5 );
function inject_soft_border_and_add_img_class( $image, $product, $size, $attr, $placeholder ) {

    if ( is_cart() ) {
        return $image;
    }

    if ( ! in_the_loop() ) {
        return $image;
    }


    if ( strpos( $image, 'class="' ) !== false ) {
        $image = preg_replace(
            '/class="([^"]*)"/',
            'class="$1 woocommerce-shop__img"',
            $image,
            1
        );
    } else {
        $image = preg_replace(
            '/<img/',
            '<img class="woocommerce-shop__img"',
            $image,
            1
        );
    }


    $soft_border = '<div class="woocommerce-shop__soft-border"></div>';


    if ( strpos( $image, 'woocommerce-shop__soft-border' ) === false ) {
        $image = preg_replace( '/<img/i', $soft_border . '<img', $image, 1 );
    }

    return $image;
}







add_action( 'woocommerce_before_shop_loop_item', 'inject_cat_hover_image' );
function inject_cat_hover_image() {

    if ( is_shop() || is_product_category() || is_product_tag() || is_product() ) {
        echo '<img class="woocommerce-shop__cat-hover-image" src="/wp-content/uploads/2025/11/category_hover_with_text.jpg" alt="">';
    }
}



add_action( 'woocommerce_before_shop_loop_item', 'inject_cat_hover_links', 5 );
function inject_cat_hover_links() {

    if ( is_shop() || is_product_category() || is_product_tag() || is_product() ) {

        global $product;
        $product_id = $product->get_id();

        $download_file = '';

        if ( have_rows('download_items', $product_id) ) {
            while ( have_rows('download_items', $product_id) ) {
                the_row();
                $download_file = get_sub_field('download_file');
                break;
            }
        }

        echo '<div class="woocommerce-shop__image-links double">';

        echo '<a class="woocommerce-shop__image-links-link" rel="nofollow" href="' . get_the_permalink() . '">
                Order<br>Custom Parts
              </a>';

        if ( $download_file ) {
            echo '<a class="woocommerce-shop__image-links-link" target="_blank" href="' . esc_url( $download_file ) . '">
                    Product<br>Data Sheet
                  </a>';
        }
        
        echo '</div>';
    }
}
    */
