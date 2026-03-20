<?php

// DO I NEED THIS LINE OF CODE TO RESET PRICES ON THE CART PAGE
//WC()->cart->calculate_totals();

add_action('init', function () {
	if (!function_exists('WC')) {
        return;
    }

    if (isset($_GET['set_currency'])) {
        WC()->session->set(
            'currency',
            sanitize_text_field($_GET['set_currency'])
        );
    }

	// Unset currency
    if (isset($_GET['unset_currency'])) {
        WC()->session->__unset('currency');
    }
});


function get_current_currency() {
    if (function_exists('WC') && WC()->session) {
        return WC()->session->get('currency', 'GBP');
    }
    return 'GBP';
}


function get_currency_rate() {
    $currency = get_current_currency();

    if ( $currency === 'GBP' ) {
        return 1.0;
    }

    $transient_key = 'currency_rate_' . strtolower( $currency );
    $rate = get_transient( $transient_key );

    if ( false === $rate ) {
        $loop = new WP_Query( [
            'post_type'      => 'currency',
            'posts_per_page' => 1,
            'name'           => $currency, 
            'post_status'    => 'publish',
        ] );

        $rate = 1.0; // fallback if no post found

        if ( $loop->have_posts() ) {
            $loop->the_post();
            $db_rate = (float) get_field( 'currency_rate_to_gbp' );
            if ( $db_rate > 0 ) {                    // safety check
                $rate = $db_rate + ( $db_rate * 0.002 ); // original uplift
            }
            wp_reset_postdata();
        }

        // Cache for 6 hours (21600 seconds) — adjust as needed
        set_transient( $transient_key, $rate, HOUR_IN_SECONDS * 6 );
    }

    return (float) $rate;
}


function get_currency_symbol() {
    $currency = get_current_currency();

    if ( $currency === 'GBP' ) {
        return '£';
    }

    $transient_key = 'currency_symbol_' . strtolower( $currency );
    $symbol = get_transient( $transient_key );

    if ( false === $symbol ) {
        $loop = new WP_Query( [
            'post_type'      => 'currency',
            'posts_per_page' => 1,
            'name'           => $currency,
            'post_status'    => 'publish',
        ] );

        $symbol = '€'; // better fallback than £

        if ( $loop->have_posts() ) {
            $loop->the_post();
            $db_symbol = get_field( 'currency_symbol' );
            if ( ! empty( $db_symbol ) ) {
                $symbol = $db_symbol;
            }
            wp_reset_postdata();
        }

        set_transient( $transient_key, $symbol, HOUR_IN_SECONDS * 6 );
    }

    return $symbol;
}
