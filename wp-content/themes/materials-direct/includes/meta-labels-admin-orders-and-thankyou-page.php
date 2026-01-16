<?php
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'filter_admin_order_item_meta', 10, 2 );
function filter_admin_order_item_meta( $formatted_meta, $item ) {
    /*
     * modify / remove labels
     */
    foreach ( $formatted_meta as $meta_id => $meta ) {

        if ( ! isset( $meta->display_key ) ) {
            continue;
        }

        // Get the meta value as $value
        $value = isset( $meta->value ) && is_numeric( $meta->value )
            ? (float) $meta->value
            : null;
        // End get the meta value as $value

        // Display the Radius(MM) if greater than 0    
        if ( $meta->display_key === 'custom_radius' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Radius (MM)';
        }     
        // End display the Radius(MM) if greater than 0

        // Display the Radius(INCHES) if greater than 0
        if ( $meta->display_key === 'custom_radius_inches' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Radius (INCHES)';
        }    
        // End display the Radius(INCHES) if greater than 0

        // Display the Length(INCHES) if greater than 0
        if ( $meta->display_key === 'length_inches' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Length (INCHES)';
        }
        // End display the Length(INCHES) if greater than 0

        // Display the Width(INCHES) if greater than 0
        if ( $meta->display_key === 'width_inches' ) {

            if ( $value === 0.0 ) {
                unset( $formatted_meta[ $meta_id ] );
                continue;
            }

            $formatted_meta[ $meta_id ]->display_key = 'Width (INCHES)';
        }
        // End display the Width(INCHES) if greater than 0

        // Display the Cost Per Part
        if ( $meta->display_key === 'cost_per_part' ) {

            // if ( is_email() && $meta->display_key === 'Cost Per Part' ) {
            //     unset( $formatted_meta[ $meta_id ] );
            //     continue;
            // }

            $formatted_meta[ $meta_id ]->display_key = 'Cost Per Part';
        }
        // End display the Cost Per Part

        // Display customer shipping weights
        if ( $meta->display_key === 'Customer Shipping Weight(s)' ) {
            if ( is_numeric( $meta->value ) ) {
                $weight = round( (float) $meta->value, 3 );
                $formatted_meta[ $meta_id ]->display_value = $weight . 'kg';
            }
        } ///
        // End display customer shipping weights


        // ----- REMOVE UNWANTED META -----

        if ( in_array( $meta->display_key, array( 'price', 'shipments', 'conversion_factor' ), true ) ) {
            unset( $formatted_meta[ $meta_id ] );
        }
    }

    return $formatted_meta;
}





add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'hide_specific_order_item_meta_keys', 10, 2 );
function hide_specific_order_item_meta_keys( $formatted_meta, $item ) {
    // Only hide on frontend (thank you page, My Account, emails, etc.)
    // Keep visible in admin for your reference
    if ( is_admin() ) {
        return $formatted_meta;
    }

    $keys_to_hide = array(
        'despatch_string',
        'Customer Shipping Weight(s)',
        'cost_per_part',
        'price',
        'is_scheduled',
        'roll_length',
		'scheduled_shipments',
        // Add any other internal keys here if needed
    );

    foreach ( $formatted_meta as $key => $meta ) {
        if ( in_array( $meta->key, $keys_to_hide ) ) {
            unset( $formatted_meta[ $key ] );
        }
    }

    return $formatted_meta;
}