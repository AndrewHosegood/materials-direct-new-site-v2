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

/*
add_action('init', function () {

    // Never touch currency during AJAX
    if (wp_doing_ajax()) {
        return;
    }

    if (!function_exists('WC') || !WC()->session) {
        return;
    }

    // Explicit currency override
    if (isset($_GET['set_currency'])) {

        $currency = sanitize_text_field($_GET['set_currency']);
        $allowed  = ['GBP', 'USD', 'EUR'];

        if (in_array($currency, $allowed, true)) {
            WC()->session->set('currency', $currency);
        }

        return;
    }

    // Plain PRODUCT page = force GBP
    if (is_product()) {
        WC()->session->set('currency', 'GBP');
    }

}, 20);
*/


function get_current_currency() {
    if (function_exists('WC') && WC()->session) {
        return WC()->session->get('currency', 'GBP');
    }
    return 'GBP';
}


function get_currency_rate() {
    $currency = get_current_currency();

    if ($currency !== 'GBP') {
        $loop = new WP_Query([
            'post_type'      => 'currency',
            'posts_per_page' => 1,
            'name'           => $currency,
        ]);

        $rate = 1.0;

        if ($loop->have_posts()) {
            $loop->the_post();
            $rate = (float) get_field('currency_rate_to_gbp');
            $rate += ($rate * 0.002); // your uplift
        }

        wp_reset_postdata();
        return $rate;
    }

    return 1.0;
}

function get_currency_symbol() {
    $currency = get_current_currency();

    if ($currency !== 'GBP') {
        $loop = new WP_Query([
            'post_type'      => 'currency',
            'posts_per_page' => 1,
            'name'           => $currency,
        ]);

        $symbol = '£';

        if ($loop->have_posts()) {
            $loop->the_post();
            $symbol = get_field('currency_symbol');
        }

        wp_reset_postdata();
        return $symbol;
    }

    return '£';
}


/*
function get_currency_symbol() {
	if (isset($_SESSION['currency']) && $_SESSION['currency'] != 'GBP') {
		$loop = new WP_Query(
			array(
				'post_type' => 'currency',
				'posts_per_page' => 1,
				's' => $_SESSION['currency']
			)
		);
		while ( $loop->have_posts() ) : $loop->the_post();
			return get_field('currency_symbol');
		endwhile;
		wp_reset_postdata();
	} else {
		return '£';
	}
}
*/
/*
function get_currency_rate() {
	if (isset($_SESSION['currency']) && $_SESSION['currency'] != 'GBP') {
		$loop = new WP_Query(
			array(
				'post_type' => 'currency',
				'posts_per_page' => 1,
				's' => $_SESSION['currency']
			)
		);
		while ( $loop->have_posts() ) : $loop->the_post();
			$return_rate = get_field('currency_rate_to_gbp');
			if (get_the_title() !== 'GBP') {
				$return_rate_addon = ($return_rate/100) * 0.20;
				$return_rate = get_field('currency_rate_to_gbp') + $return_rate_addon;
			}
			return $return_rate;
		endwhile;
		wp_reset_postdata();
	} else {
		return '1';
	}
}
*/