<?php
//Define the expiry time
define('CUSTOM_CART_EXPIRY', 10800); // 2 hours


//Start timer when first item added to cart
add_action('woocommerce_add_to_cart', 'custom_cart_start_timer', 10, 6);

function custom_cart_start_timer() {

    if ( ! WC()->session ) {
        return;
    }

    if ( ! WC()->session->has_session() ) {
        WC()->session->set_customer_session_cookie(true);
    }

    if ( ! WC()->session->get('cart_start_time') ) {
        WC()->session->set('cart_start_time', time());
    }

}


//Reset timer if cart becomes empty
add_action('woocommerce_before_calculate_totals', 'custom_cart_reset_timer');

function custom_cart_reset_timer() {

    if ( ! WC()->session || ! WC()->cart ) {
        return;
    }

    if ( WC()->cart->is_empty() ) {
        WC()->session->__unset('cart_start_time');
    }

}


//Calculate remaining time (single source of truth)
function get_cart_remaining_seconds() {

    if ( ! WC()->session ) {
        return CUSTOM_CART_EXPIRY;
    }

    $start = WC()->session->get('cart_start_time');

    if ( ! $start ) {
        return CUSTOM_CART_EXPIRY;
    }

    $remaining = ( $start + CUSTOM_CART_EXPIRY ) - time();

    return max(0, $remaining);

}

//Force cart expiration
add_action('template_redirect', 'custom_force_cart_expiration');

function custom_force_cart_expiration() {

    if ( ! WC()->session || ! WC()->cart ) {
        return;
    }

    $remaining = get_cart_remaining_seconds();

    if ( $remaining <= 0 && ! WC()->cart->is_empty() ) {

        WC()->cart->empty_cart();

        WC()->session->__unset('cart_start_time');

        if ( is_user_logged_in() ) {
            delete_user_meta( get_current_user_id(), '_woocommerce_persistent_cart' );
        }

    }

}


//Display the timer in the cart
add_action('woocommerce_before_cart', 'add_cart_expiry_banner');

function add_cart_expiry_banner() {

    if ( WC()->cart->is_empty() ) {
        return;
    }

    $remaining = get_cart_remaining_seconds();
?>
<div class="woocommerce-message">
Place your order within 
<span class="cart-expiry-contdown" data-remaining="<?php echo esc_attr($remaining); ?>"></span>
or stock may become unavailable.
</div>
<?php
}




// Reset timer when cart is empty
add_action('woocommerce_cart_item_removed', 'custom_reset_timer_when_cart_empty', 10, 2);

function custom_reset_timer_when_cart_empty($cart_item_key, $cart) {

    if ( ! WC()->session ) {
        return;
    }

    if ( $cart->get_cart_contents_count() === 0 ) {
        WC()->session->__unset('cart_start_time');
    }

}



// Enqueue the countdown script on the cart page
add_action( 'wp_footer', 'cart_expiry_countdown_js', 20);

function cart_expiry_countdown_js() {
    if ( ! is_cart() || WC()->cart->is_empty() ) {
        return;
    }
    ?>
    <script type="text/javascript">

            jQuery(function($){

            var $countdown = $('.cart-expiry-contdown');
            if(!$countdown.length) return;

            var remaining = parseInt($countdown.data('remaining'),10);

            function formatTime(seconds){

                var h = Math.floor(seconds/3600);
                var m = Math.floor((seconds%3600)/60);
                var s = seconds%60;

                var str = '';
                if(h>0) str += h+'hr ';
                str += m+' mins '+s+' Seconds';

                return str;
            }

            function tick(){

                if(remaining <= 0){
                    $countdown.text('0 mins 0 Seconds');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                    return;
                }

                $countdown.text(formatTime(remaining));
                remaining--;

            }

            tick();
            setInterval(tick,1000);

        });


    </script>
    <?php
}
// Enqueue the countdown script on the cart page