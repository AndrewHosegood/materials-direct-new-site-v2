<?php
/*  Override default cart session expiration to 2 hours */
add_filter( 'wc_session_expiring', 'custom_wc_cart_expiration' );
add_filter( 'wc_session_expiration', 'custom_wc_cart_expiration' );

function custom_wc_cart_expiration( $seconds ) {
    return 60 * 60 * 2; // 7200 seconds = 2 hours
}
/* End override default cart session expiration to 2 hours */


/* Add the expiry banner/countdown on the Cart page */
add_action( 'woocommerce_before_cart', 'add_cart_expiry_banner' );

function add_cart_expiry_banner() {
    if ( WC()->cart->is_empty() ) {
        return;
    }

    // Get accurate remaining seconds from the actual session record
    $remaining = get_cart_remaining_seconds();

    ?>
    <div class="woocommerce-message" role="alert" tabindex="-1">
        <span style="display: inline;">Place your order within. <span class="cart-expiry-contdown" data-remaining="<?php //echo esc_attr( $remaining ); ?>"></span> Stock may become unavailable before the checkout time has expired!</span>
    </div>

    <?php
}

// Helper: get real remaining seconds until cart expires
function get_cart_remaining_seconds() {
    if ( ! WC()->session ) {
        return 7200; // fallback
    }

    $session_key = WC()->session->get_customer_id();

    global $wpdb;
    $expiration = $wpdb->get_var( $wpdb->prepare(
        "SELECT session_expiration FROM {$wpdb->prefix}woocommerce_sessions WHERE session_key = %s LIMIT 1",
        $session_key
    ) );

    if ( ! $expiration ) {
        return 7200; // fallback if session not yet in DB
    }

    return max( 0, (int) $expiration - time() );
}
/* Add the expiry banner/countdown on the Cart page */


// Enqueue the countdown script on the cart page
add_action( 'wp_footer', 'cart_expiry_countdown_js' );

function cart_expiry_countdown_js() {
    if ( ! is_cart() || WC()->cart->is_empty() ) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var $countdown = $('.cart-expiry-contdown');
        var remaining = parseInt($countdown.data('remaining'), 10) || 7200;

        function formatTime(seconds) {
            var h = Math.floor(seconds / 3600);
            var m = Math.floor((seconds % 3600) / 60);
            var s = seconds % 60;

            var str = '';
            if (h > 0) {
                str += h + 'hr ';
            }
            str += m + ' mins ' + s + ' Seconds';
            return str;
        }

        function tick() {
            if (remaining <= 0) {
                $countdown.text('0 mins 0 Seconds');
                return;
            }
            $countdown.text(formatTime(remaining));
            remaining--;
        }

        tick();                    // show immediately
        var timer = setInterval(tick, 1000);
    });
    </script>
    <?php
}

// Enqueue the countdown script on the cart page