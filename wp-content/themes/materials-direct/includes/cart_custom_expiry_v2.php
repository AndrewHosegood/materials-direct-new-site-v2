<?php
//Define the expiry time
//define('CUSTOM_CART_EXPIRY', 10800); // 2 hours
function get_custom_cart_expiry() {

    $expiry = get_field('cart_expiry_in_seconds', 'option');

    if ( empty($expiry) || ! is_numeric($expiry) ) {
        $expiry = 7200; // fallback = 2 hours
    }

    return max(60, (int)$expiry); // safety minimum
}

//Start timer when first item added to cart
add_action('woocommerce_add_to_cart', 'custom_cart_start_timer', 10, 6);

function custom_cart_start_timer() {

    if ( ! WC()->session || ! WC()->cart ) {
        return;
    }

    if ( ! WC()->session->has_session() ) {
        WC()->session->set_customer_session_cookie(true);
    }

    if ( WC()->cart->get_cart_contents_count() === 1 ) {
        WC()->session->set('cart_start_time', time());
    }

}




//Calculate remaining time (single source of truth)
function get_cart_remaining_seconds(){

    if ( ! WC()->session ) {
        return 0;
    }

    $start = WC()->session->get('cart_start_time');

    if ( ! $start ) {
        return 0;
    }

    $expiry = get_custom_cart_expiry();

    $remaining = ($start + $expiry) - time();

    return max(0, $remaining);
}
/*
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
    */

//Force cart expiration
add_action('template_redirect', 'custom_force_cart_expiration');

function custom_force_cart_expiration() {

    if ( ! WC()->session || ! WC()->cart ) {
        return;
    }

    $remaining = get_cart_remaining_seconds();

    if ( $remaining <= 0 ) {

        if ( ! WC()->cart->is_empty() ) {
            WC()->cart->empty_cart();
        }


        WC()->session->__unset('cart_start_time');

        if ( is_user_logged_in() ) {
            delete_user_meta( get_current_user_id(), '_woocommerce_persistent_cart' );
        }

    }

}

// Display the timer in the cart
add_action('woocommerce_before_cart', 'add_cart_expiry_banner');

function add_cart_expiry_banner() {

    if ( WC()->cart->is_empty() ) {
        // Clear JS expiry if cart empty
        echo "<script>localStorage.removeItem('wc_cart_expiry');</script>";
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




// Lets reliably check the cart contents and reset the sesssion if the cart contents are 0 - THIS ONE WORKS!!!!
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
            if(isNaN(remaining)) return;

            // Prevent duplicate timers
            if(window.wcCartTimer){
                clearInterval(window.wcCartTimer);
            }

            /*
            -----------------------------------------
            Sync expiry with localStorage (multi-tab)
            Server timer always wins
            -----------------------------------------
            */

            var expiry = localStorage.getItem('wc_cart_expiry');
            var serverExpiry = Date.now() + (remaining * 1000);

            if(!expiry || serverExpiry > expiry){
                expiry = serverExpiry;
                localStorage.setItem('wc_cart_expiry', expiry);
            }

            remaining = Math.floor((expiry - Date.now()) / 1000);


            function formatTime(seconds){

                if(seconds < 0){
                    seconds = 0;
                }

                var h = Math.floor(seconds/3600);
                var m = Math.floor((seconds%3600)/60);
                var s = seconds%60;

                var str = '';

                if(h > 0){
                    str += h + 'hr ';
                }

                str += m + ' mins ' + s + ' Seconds';

                return str;
            }


            function tick(){

                if(remaining <= 0){

                    clearInterval(window.wcCartTimer);

                    $countdown.text('Cart expired');

                    // Clean storage so next cart starts fresh
                    localStorage.removeItem('wc_cart_expiry');

                    if(!sessionStorage.getItem('wc_cart_expired_reload')){

                        sessionStorage.setItem('wc_cart_expired_reload','1');

                        setTimeout(function(){
                            location.reload();
                        },2000);

                    }

                    return;
                }

                $countdown.text(formatTime(remaining));

                remaining--;

            }


            tick();

            window.wcCartTimer = setInterval(tick,1000);


            /*
            -----------------------------------------
            Sync expiration across tabs
            -----------------------------------------
            */

            window.addEventListener('storage', function(e){

                if(e.key === 'wc_cart_expiry' && e.oldValue !== e.newValue){
                    location.reload();
                }

            });

        });

        /*
        jQuery(function($){

            var $countdown = $('.cart-expiry-contdown');
            if(!$countdown.length) return;

            var remaining = parseInt($countdown.data('remaining'),10);
            if(isNaN(remaining)) return;

            // Prevent multiple timers
            if(window.wcCartTimer){
                clearInterval(window.wcCartTimer);
            }

            // Store expiry timestamp for cross-tab sync
            var expiry = Date.now() + (remaining * 1000);
            localStorage.setItem('wc_cart_expiry', expiry);

            function formatTime(seconds){

                var h = Math.floor(seconds/3600);
                var m = Math.floor((seconds%3600)/60);
                var s = seconds%60;

                var str = '';

                if(h > 0){
                    str += h + 'hr ';
                }

                str += m + ' mins ' + s + ' Seconds';

                return str;
            }

            function tick(){

            if(remaining <= 0){

                clearInterval(window.wcCartTimer);

                $countdown.text('Cart expired');

                if(!sessionStorage.getItem('wc_cart_expired_reload')){

                    sessionStorage.setItem('wc_cart_expired_reload', '1');

                    setTimeout(function(){
                        location.reload();
                    },2000);

                }

                return;
            }

                $countdown.text(formatTime(remaining));

                remaining--;

            }

            tick();

            window.wcCartTimer = setInterval(tick,1000);

            // Sync cart expiration across tabs
            window.addEventListener('storage', function(e){

                if(e.key === 'wc_cart_expiry'){
                    location.reload();
                }

            });

        });
        */
        </script>
    <?php
}

// Enqueue the countdown script on the cart page