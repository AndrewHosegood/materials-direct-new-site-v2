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
        $product_terms    = get_the_terms( $product->get_id(), 'product_cat' );
        

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
 * Add soft-border div + add custom class to WooCommerce loop <img> tags
 */
add_filter( 'woocommerce_product_get_image', 'inject_soft_border_and_add_img_class', 10, 5 );
function inject_soft_border_and_add_img_class( $image, $product, $size, $attr, $placeholder ) {

    // Prevent injection on the cart page (basket)
    if ( is_cart() ) {
        return $image;
    }

    // Only modify images on loop pages
    if ( ! in_the_loop() ) {
        return $image;
    }

    /* -------------------------------------------------
        1. Ensure <img> tag has class "woocommerce-shop__img"
       ------------------------------------------------- */
    if ( strpos( $image, 'class="' ) !== false ) {
        // Append our class to existing class attribute
        $image = preg_replace(
            '/class="([^"]*)"/',
            'class="$1 woocommerce-shop__img"',
            $image,
            1
        );
    } else {
        // No class attribute → add one
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

    //$image = preg_replace( '/<img/i', $soft_border . '<img', $image, 1 );
    if ( strpos( $image, 'woocommerce-shop__soft-border' ) === false ) {
        $image = preg_replace( '/<img/i', $soft_border . '<img', $image, 1 );
    }

    return $image;
}





/**
 * 3. Inject category hover image immediately after opening <li> tag
 *    Uses the WooCommerce hook that fires right after <li> opens
 */



add_action( 'woocommerce_before_shop_loop_item', 'inject_cat_hover_image' );
function inject_cat_hover_image() {

    if ( is_shop() || is_product_category() || is_product_tag() || is_product() ) {
        echo '<img class="woocommerce-shop__cat-hover-image" src="/wp-content/uploads/2025/11/category_hover_with_text.jpg" alt="">';
    }
}

/**
 * 4. Inject orange links on category cards
 */

add_action( 'woocommerce_before_shop_loop_item', 'inject_cat_hover_links', 5 );
function inject_cat_hover_links() {

    if ( is_shop() || is_product_category() || is_product_tag() || is_product() ) {

        global $product;
        $product_id = $product->get_id();

        // Default: empty link
        $download_file = '';

        // Get the repeater "download_items"
        if ( have_rows('download_items', $product_id) ) {
            while ( have_rows('download_items', $product_id) ) {
                the_row();
                $download_file = get_sub_field('download_file');
                // If you only need the FIRST item, break here:
                break;
            }
        }

        echo '<div class="woocommerce-shop__image-links double">';

        echo '<a class="woocommerce-shop__image-links-link" rel="nofollow" href="' . get_the_permalink() . '">
                Order<br>Custom Parts
              </a>';

        // Only print the download link if ACF value exists
        if ( $download_file ) {
            echo '<a class="woocommerce-shop__image-links-link" target="_blank" href="' . esc_url( $download_file ) . '">
                    Product<br>Data Sheet
                  </a>';
        }
        
        echo '</div>';
    }
}
