<?php

add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'custom_order_item_meta_logic', 10, 2 );
function custom_order_item_meta_logic( $formatted_meta, $item ) {

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
         * ---- FRONTEND HIDING ----
         */
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





/*

add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'filter_admin_order_item_meta', 10, 2 );
function filter_admin_order_item_meta( $formatted_meta, $item ) {

    foreach ( $formatted_meta as $meta_id => $meta ) {

        if ( ! isset( $meta->display_key ) ) {
            continue;
        }

        $value = isset( $meta->value ) && is_numeric( $meta->value )
            ? (float) $meta->value
            : null;
 
        if ( $meta->display_key === 'custom_radius' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Radius (MM)';
        }     

        
        if ( $meta->display_key === 'custom_radius_inches' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Radius (INCHES)';
        }    

        if ( $meta->display_key === 'length_inches' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Length (INCHES)';
        }

        if ( $meta->display_key === 'width_inches' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Width (INCHES)';
        }

        if ( $meta->display_key === 'cost_per_part' ) {

            $formatted_meta[ $meta_id ]->display_key = 'Cost Per Part';
        }

        if ( $meta->display_key === 'Customer Shipping Weight(s)' ) {
            if ( is_numeric( $meta->value ) ) {
                $weight = round( (float) $meta->value, 3 );
                $formatted_meta[ $meta_id ]->display_value = $weight . 'kg';
            }
        } 


        if ( in_array( $meta->display_key, array( 'price', 'shipments', 'conversion_factor' ), true ) ) {
            unset( $formatted_meta[ $meta_id ] );
        }
    }

    return $formatted_meta;
}





add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'hide_specific_order_item_meta_keys', 10, 2 );
function hide_specific_order_item_meta_keys( $formatted_meta, $item ) {

    if ( is_admin() ) {
        return $formatted_meta;
    }

    $keys_to_hide = array(
        'despatch_string',
        'Customer Shipping Weight(s)',
        'cost_per_part',
        'price',
        'is_scheduled',
        'stock_quantity',
		'scheduled_shipments',
    );

    foreach ( $formatted_meta as $key => $meta ) {
        if ( in_array( $meta->key, $keys_to_hide ) ) {
            unset( $formatted_meta[ $key ] );
        }
    }

    return $formatted_meta;
}
    */