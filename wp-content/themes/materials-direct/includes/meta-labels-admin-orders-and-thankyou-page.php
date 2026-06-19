<?php

add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'custom_order_item_meta_logic', 10, 2 );
function custom_order_item_meta_logic( $formatted_meta, $item ) {

    // Keys to hide for a regular cart
    $keys_to_hide_frontend = array(
        'despatch_string',
        'Customer Shipping Weight(s)',
        'cost_per_part',
        'price',
        'is_scheduled',
        'stock_quantity',
        'scheduled_shipments',
        'shipments',
        'conversion_factor',
    );

    // Keys to hide for a restored capture cart
    $captured_cart_hidden_keys = [
        'Sheets Required',
        'shipping_total_raw',
        'on_backorder',
        'raw_date',
        'discount_raw_new',
        'cost_per_part_raw',
        '_Shipping Total',
    ];

    $shape_type_is_rolls = false;

    foreach ( $formatted_meta as $meta ) {
        if ( $meta->key === 'shape_type' && $meta->value === 'Rolls' ) {
            $shape_type_is_rolls = true;
            break;
        }
    }

    foreach ( $formatted_meta as $meta_id => $meta ) {

        $raw_key = $meta->key;
        $display_key = $meta->display_key;

        $value = isset( $meta->value ) && is_numeric( $meta->value )
            ? (float) $meta->value
            : null;
        
        /*
         * ---- FRONTEND HIDING (Restored Capture carts) ----
         */
        if ( in_array( $raw_key, $captured_cart_hidden_keys, true ) ) {
            unset( $formatted_meta[$meta_id] );
            continue;
        }    

        /*
         * ---- FRONTEND HIDING (Regular carts) ----
         */
        
        if ( $raw_key === 'cost_per_part' ) {
            unset( $formatted_meta[ $meta_id ] );
            continue;
        }
        if ( ! is_admin() && in_array( $raw_key, $keys_to_hide_frontend, true ) ) {
            unset( $formatted_meta[ $meta_id ] );
            continue;
        }


        /*
         * ---- ZERO VALUE REMOVALS ----
         */
        if ( in_array( $raw_key, array(
            'custom_radius',
            'custom_radius_inches',
            'length_inches',
            'width_inches'
        ), true ) ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }
        }

        /*
         * ---- LABEL RENAMES ----
         */
        switch ( $raw_key ) {

            case 'custom_radius':
                $formatted_meta[ $meta_id ]->display_key = 'Radius (MM)';
                break;

            case 'custom_radius_inches':
                $formatted_meta[ $meta_id ]->display_key = 'Radius (INCHES)';
                break;

            case 'length_inches':
                $formatted_meta[ $meta_id ]->display_key = 'Length (INCHES)';
                break;

            case 'width_inches':
                $formatted_meta[ $meta_id ]->display_key = 'Width (INCHES)';
                break;

            case 'cost_per_part':
                $formatted_meta[ $meta_id ]->display_key = 'Cost Per Part';
                break;
        }

        /*
         * ---- FORMAT SHIPPING WEIGHT ----
         */
        if ( $raw_key === 'Customer Shipping Weight(s)' && is_numeric( $meta->value ) ) {
            $weight = round( (float) $meta->value, 3 );
            $formatted_meta[ $meta_id ]->display_value = $weight . 'kg';
        }

        // Hide standard length if shape_type is Rolls (frontend only)
        if ( ! is_admin() && $shape_type_is_rolls && $raw_key === 'length' ) {
            unset( $formatted_meta[ $meta_id ] );
            continue;
        }



    }


    return $formatted_meta;
}