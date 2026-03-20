<?php
/**
 * Add two new sorting options to WooCommerce dropdown
 */
add_filter( 'woocommerce_catalog_orderby', 'add_custom_stock_sorting_options' );
function add_custom_stock_sorting_options( $options ) {
    $new_options = [
        'instock_first'   => __( 'Sort by In Stock', 'woocommerce' ),
        'backorder_first' => __( 'Sort by Backorder', 'woocommerce' ),
    ];
    // Put new options at the top
    return array_merge( $new_options, $options );
}


/**
 * Custom sorting logic using posts_clauses (most reliable for non-alphabetic priority)
 */
add_filter( 'posts_clauses', 'custom_stock_sort_posts_clauses', 100, 2 );
function custom_stock_sort_posts_clauses( $clauses, $query ) {
    global $wpdb;

    if ( ! is_admin() && $query->is_main_query() && $query->is_archive() && $query->get( 'post_type' ) === 'product' ) {
        $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : '';

        if ( in_array( $orderby_value, [ 'instock_first', 'backorder_first' ], true ) ) {
            $stock_priority = [
                'instock'     => 10,
                'onbackorder' => 5,
                'outofstock'  => 0,
            ];

            if ( $orderby_value === 'backorder_first' ) {
                $stock_priority = [
                    'onbackorder' => 10,
                    'instock'     => 5,
                    'outofstock'  => 0,
                ];
            }

            // CASE expression for priority
            $case = "CASE {$wpdb->postmeta}.meta_value ";
            foreach ( $stock_priority as $status => $prio ) {
                $case .= "WHEN '$status' THEN $prio ";
            }
            $case .= "ELSE 0 END";

            // Modify ORDER BY
            $clauses['orderby'] = "$case DESC, {$wpdb->posts}.post_title ASC";

            // Join meta table if not already joined
            if ( strpos( $clauses['join'], "meta_key = '_stock_status'" ) === false ) {
                $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_stock_status') ";
            }

            // Remove any conflicting orderby that might come from WooCommerce
            remove_all_filters( 'woocommerce_get_catalog_ordering_args' ); // optional - prevents conflict
        }
    }

    return $clauses;
}


/**
 * Keep custom orderby selected / default handling
 */
add_filter( 'woocommerce_default_catalog_orderby_options', 'add_custom_stock_sorting_options' );
add_filter( 'woocommerce_default_catalog_orderby', 'keep_custom_stock_orderby_default' );
function keep_custom_stock_orderby_default( $default ) {
    if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], [ 'instock_first', 'backorder_first' ] ) ) {
        return wc_clean( $_GET['orderby'] );
    }
    return $default;
}



/**
 * Remove unwanted default sorting options from the dropdown
 */
add_filter( 'woocommerce_catalog_orderby', 'remove_specific_woocommerce_sorting_options' );
function remove_specific_woocommerce_sorting_options( $options ) {
    // Remove these two options
    unset( $options['rating'] );     // "Sort by average rating"
    unset( $options['date'] );       // "Sort by latest"
	unset( $options['price'] );        // "Sort by price: low to high"
    unset( $options['price-desc'] );   // "Sort by price: high to low"
    unset( $options['popularity'] );   // "Sort by popularity"
    

    // Optional: also remove others if you ever want to
    // unset( $options['price'] );        // "Sort by price: low to high"
    // unset( $options['price-desc'] );   // "Sort by price: high to low"
    // unset( $options['menu_order'] );   // "Default sorting"

    return $options;
}
