<?php

add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'custom_order_item_meta_logic', 10, 2 );

function custom_order_item_meta_logic( $formatted_meta, $item ) {

    error_log( '### CUSTOM ORDER ITEM META LOGIC FUNCTION FIRED ###' );

    /*
     * ============================================================
     * KEYS TO HIDE ON FRONTEND
     * ============================================================
     */

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


    /*
     * ============================================================
     * KEYS TO HIDE FOR RESTORED CAPTURE CARTS
     * ============================================================
     */

    $captured_cart_hidden_keys = array(
        'Sheets Required',
        'shipping_total_raw',
        'on_backorder',
        'raw_date',
        'discount_raw_new',
        'cost_per_part_raw',
        '_Shipping Total',
    );


    /*
     * ============================================================
     * COFC / FAIR FIELDS
     *
     * These are deliberately handled separately so that:
     *
     * 1. They are displayed as currency.
     * 2. They are always shown at the END of the metadata.
     * ============================================================
     */

    $fee_keys = array(
        'Manufacturers COFC',
        'First Article Inspection Report',
        'Materials Direct COFC',
    );


    /*
     * ============================================================
     * DETERMINE WHETHER PRODUCT IS A ROLL
     * ============================================================
     */


    /*
    $shape_type_is_rolls = false;
    $shape_type_is_circle_radius = false;

    foreach ( $formatted_meta as $meta ) {

        if ( $meta->key === 'shape_type' ) {

            $shape_type = strtolower( trim( $meta->value ) );

            if ( $shape_type === 'rolls' ) {
                $shape_type_is_rolls = true;
            }

            if ($shape_type === 'circle-radius' || $shape_type === 'circle radius') {
                $shape_type_is_circle_radius = true;
            }
        }
    }
    */

    $shape_type_is_rolls = false;
    $shape_type_is_circle_radius = false;

    foreach ( $formatted_meta as $meta ) {

        if ($meta->key === 'shape_type' || strtolower( trim( $meta->key ) ) === 'part shape') {

            $shape_type = strtolower( trim( $meta->value ) );

            if ( $shape_type === 'rolls' ) {
                $shape_type_is_rolls = true;
            }

            if ($shape_type === 'circle-radius' || $shape_type === 'circle radius') {
                $shape_type_is_circle_radius = true;
            }
        }
    }

    /*
     * ============================================================
     * STORAGE FOR THE THREE FEES
     *
     * We remove them from their normal position and add them
     * back at the end of the metadata array.
     * ============================================================
     */

    $fee_meta = array();


    /*
     * ============================================================
     * PROCESS NORMAL ORDER ITEM META
     * ============================================================
     */

    foreach ( $formatted_meta as $meta_id => $meta ) {

            error_log(
                'META DEBUG - key: ' . $meta->key .
                ' | display_key: ' . $meta->display_key .
                ' | value: ' . $meta->value
            );

        $raw_key    = $meta->key;
        $display_key = $meta->display_key;

        /*
        * --------------------------------------------------------
        * HIDE WIDTH AND LENGTH FOR CIRCLE RADIUS
        * --------------------------------------------------------
        *
        * Width and Length are still stored in the order item.
        * We are only preventing them from being displayed.
        */

        if ($shape_type_is_circle_radius && in_array($raw_key, array( 'width', 'length', 'Width (MM)', 'Length (MM)' ),true)) {
            unset( $formatted_meta[ $meta_id ] );
            continue;
        }

        /*
         * --------------------------------------------------------
         * CAPTURE COFC / FAIR FIELDS FOR REPOSITIONING
         * --------------------------------------------------------
         *
         * Do this before the normal processing so that these
         * three fields can be moved to the very end.
         */

        if ( in_array( $raw_key, $fee_keys, true ) ) {

            $fee_meta[ $raw_key ] = $meta;

            unset( $formatted_meta[ $meta_id ] );

            continue;
        }


        /*
         * --------------------------------------------------------
         * DETERMINE NUMERIC VALUE
         * --------------------------------------------------------
         */

        $value = isset( $meta->value ) && is_numeric( $meta->value )
            ? (float) $meta->value
            : null;


        /*
         * --------------------------------------------------------
         * HIDE CAPTURE CART META
         * --------------------------------------------------------
         */

        if ( in_array( $raw_key, $captured_cart_hidden_keys, true ) ) {

            unset( $formatted_meta[ $meta_id ] );

            continue;
        }


        /*
         * --------------------------------------------------------
         * HIDE REGULAR FRONTEND META
         * --------------------------------------------------------
         */

        if ( $raw_key === 'cost_per_part' ) {

            unset( $formatted_meta[ $meta_id ] );

            continue;
        }


        if (
            ! is_admin() &&
            in_array( $raw_key, $keys_to_hide_frontend, true )
        ) {

            unset( $formatted_meta[ $meta_id ] );

            continue;
        }


        /*
         * --------------------------------------------------------
         * REMOVE ZERO-VALUE META
         * --------------------------------------------------------
         */

        if (
            in_array(
                $raw_key,
                array(
                    'custom_radius',
                    'custom_radius_inches',
                    'length_inches',
                    'width_inches'
                ),
                true
            )
        ) {

            if ( $value === 0.0 ) {

                unset( $formatted_meta[ $meta_id ] );

                continue;
            }
        }


        /*
         * ========================================================
         * LABEL RENAMES
         * ========================================================
         */

        switch ( $raw_key ) {

            case 'custom_radius':

                //$formatted_meta[ $meta_id ]->display_key = 'Radius (MM)!!';
                $radius = (float) $meta->value;
                $diameter_value = $radius;

                $formatted_meta[ $meta_id ]->display_key = 'Diameter (MM)';
                $formatted_meta[ $meta_id ]->display_value = $diameter_value;

                break;


            case 'custom_radius_inches':

                $formatted_meta[ $meta_id ]->display_key = 'Diameter (INCHES)';

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
         * ========================================================
         * FORMAT SHIPPING WEIGHT
         * ========================================================
         */

        if (
            $raw_key === 'Customer Shipping Weight(s)' &&
            is_numeric( $meta->value )
        ) {

            $weight = round( (float) $meta->value, 3 );

            $formatted_meta[ $meta_id ]->display_value = $weight . 'kg';
        }


        /*
         * ========================================================
         * HIDE STANDARD LENGTH FOR ROLLS ON FRONTEND
         * ========================================================
         */

        if (
            ! is_admin() &&
            $shape_type_is_rolls &&
            $raw_key === 'length'
        ) {

            unset( $formatted_meta[ $meta_id ] );

            continue;
        }
    }


    /*
     * ============================================================
     * FORMAT THE THREE COFC / FAIR FIELDS
     * ============================================================
     *
     * The fields have already been removed from their original
     * positions.
     *
     * We now:
     *
     * - give them the correct display label;
     * - format their value as WooCommerce currency;
     * - add them back at the END.
     *
     * This means they will always be the final three metadata
     * entries underneath the product name.
     * ============================================================
     */

    foreach ( $fee_keys as $fee_key ) {

        if ( ! isset( $fee_meta[ $fee_key ] ) ) {
            continue;
        }

        $meta = $fee_meta[ $fee_key ];

        /*
         * Convert the stored value to a number.
         */

        $amount = is_numeric( $meta->value )
            ? (float) $meta->value
            : 0;


        /*
         * Do not display zero-value fees.
         *
         * This matches the behaviour you already have on the
         * Cart page.
         */

        if ( $amount <= 0 ) {
            continue;
        }


        /*
         * Set the display label.
         */

        switch ( $fee_key ) {

            case 'Manufacturers COFC':

                $meta->display_key = 'Manufacturers COFC';

                break;


            case 'First Article Inspection Report':

                $meta->display_key = 'First Article Inspection Report';

                break;


            case 'Materials Direct COFC':

                $meta->display_key = 'Materials Direct COFC';

                break;
        }


        /*
         * Format as WooCommerce currency.
         *
         * This produces, for example:
         *
         * £10.00
         * £12.50
         */

        $meta->display_value = wc_price( $amount );


        /*
         * Add the fee back to the end of the metadata array.
         */

        $formatted_meta[] = $meta;
    }


    /*
     * ============================================================
     * RETURN FINAL FORMATTED META
     * ============================================================
     */

    return $formatted_meta;
}