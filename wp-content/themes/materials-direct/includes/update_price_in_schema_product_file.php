<?php
add_filter( 'rank_math/json_ld', 'md_force_schema_product_from_acf', 20, 2 );

function md_force_schema_product_from_acf( $data, $jsonld ) {

    // Only run on staging
    if ( strpos( home_url(), 'materials-direct.com' ) === false ) {
        return $data;
    }

    // Get current product ID
    $product_id = get_the_ID();
    if ( ! $product_id ) {
        return $data;
    }

    // Pull ACF fields
    $acf_price  = get_field( '100_x_100mm_price', $product_id );
    $acf_title  = get_field( 'google_product_title', $product_id );

    foreach ( $data as &$entity ) {
        if ( isset( $entity['@type'] ) && $entity['@type'] === 'Product' ) {

            // Override Product Title
            if ( ! empty( $acf_title ) ) {
                $entity['name'] = $acf_title;
            }

            // Override Product Price
            if ( ! empty( $acf_price ) ) {
                if ( isset( $entity['offers']['price'] ) ) {
                    $entity['offers']['price'] = $acf_price;
                }

                if ( isset( $entity['offers']['priceSpecification']['price'] ) ) {
                    $entity['offers']['priceSpecification']['price'] = $acf_price;
                }
            }
        }
    }

    return $data;
}