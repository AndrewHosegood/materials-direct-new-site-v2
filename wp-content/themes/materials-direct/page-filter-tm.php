<?php
/*
Template Name: Replace TM Symbol - All Products
*/

if ( ! current_user_can( 'administrator' ) ) {
    wp_die( 'Access denied.' );
}

get_header();

echo '<pre>';

if ( isset( $_GET['replace_tm'] ) && $_GET['replace_tm'] === '1' ) {

    global $wpdb;

    // Get all WooCommerce product IDs containing the exact HTML entity
    $product_ids = $wpdb->get_col(
        "
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_type = 'product'
        AND post_title LIKE '%&#x2122;%'
        "
    );

    if ( empty( $product_ids ) ) {

        echo "No products found containing &#x2122;.\n";

    } else {

        foreach ( $product_ids as $product_id ) {

            // Replace ONLY the exact trademark HTML entity
            $wpdb->query(
                $wpdb->prepare(
                    "
                    UPDATE {$wpdb->posts}
                    SET post_title = REPLACE(post_title, %s, %s)
                    WHERE ID = %d
                    ",
                    '&#x2122;',
                    '™',
                    $product_id
                )
            );

            // Clear the cache for this product
            clean_post_cache( $product_id );
        }

        echo "Finished successfully.\n\n";
        echo "Products updated: " . count( $product_ids ) . "\n\n";
        echo "Only &#x2122; was replaced with ™.\n";
        echo "No other characters were changed.\n";
    }

} else {

    echo "Nothing has been changed.\n\n";
    echo "To update all products, use:\n";
    echo "?replace_tm=1\n";
}

echo '</pre>';

get_footer();