<?php
add_filter('woocommerce_available_payment_gateways', 'ah_payment_gateway_disable_items');


function ah_payment_gateway_disable_items($available_gateways) {

    // Skip in admin unless it's an AJAX request
    if (is_admin() && !defined('DOING_AJAX')) {
        return $available_gateways;
    }

    // Ensure WooCommerce and cart are available
    if (!function_exists('WC') || !WC()->cart || !method_exists(WC()->cart, 'is_empty') || !WC()->session) {
        error_log('WooCommerce or session not available in ah_payment_gateway_disable_items');
        return $available_gateways;
    }

    // Get cart total with fallback
    $cart_total = (float) (WC()->cart->total ?? 0);

    // Get user's remaining credit
    $credit_limit_remaining = function_exists('get_field') ? get_field('credit_options_credit_limit_remaining', 'user_' . get_current_user_id()) : 0;

    //echo "Cart Total: " . $cart_total . "<br>";
    //echo "Credit LImit Remaining: " . $credit_limit_remaining . "<br>";

    if (!is_numeric($credit_limit_remaining)) {
        $credit_limit_remaining = 0;
        error_log('Invalid credit_limit_remaining for user ' . get_current_user_id());
        return $available_gateways;
    }

    $remaining = $credit_limit_remaining - $cart_total;

    $cart_items = WC()->cart->get_cart();

    // $current_user = wp_get_current_user();

    // echo $current_user;

    foreach ($cart_items as $cart_item) {
        // If the order is a scheduled delivery order
        if ( isset( $cart_item['custom_inputs']['is_scheduled'] ) ) {

            if ($remaining >= 0) {
                // If the customer has credit hide bacs and stripe
                if (!empty($available_gateways) && is_array($available_gateways)) {
                    if (isset($available_gateways['bacs'])) unset($available_gateways['bacs']);
                    if (isset($available_gateways['stripe'])) unset($available_gateways['stripe']);
                    if (isset($available_gateways['cod'])) unset($available_gateways['cod']); // temporary remove on staging and live
                }
            } else {
                //If the customers credit account is empty hide everything
                if (!empty($available_gateways) && is_array($available_gateways)) {
                    if (isset($available_gateways['crediting_gateway'])) unset($available_gateways['crediting_gateway']);
                    if (isset($available_gateways['stripe'])) unset($available_gateways['stripe']);
                    if (isset($available_gateways['bacs'])) unset($available_gateways['bacs']);
                    if (isset($available_gateways['cod'])) unset($available_gateways['cod']); // temporary remove on staging and live
                }

                add_action('wp_footer', function () {
                    if (is_checkout() && !is_admin()) {
                        ?>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            function disablePlaceOrderBtn() {
                                var btn = document.getElementById('place_order');
                                if (btn) {
                                    btn.disabled = true;
                                    btn.style.opacity = '0.5';
                                    btn.style.cursor = 'not-allowed';
                                    btn.value = 'Insufficient Credit';
                                    btn.setAttribute('data-value', 'Insufficient Credit');
                                }
                            }
                            disablePlaceOrderBtn();
                            // Re-apply if checkout updates via AJAX
                            jQuery(document.body).on('updated_checkout', disablePlaceOrderBtn);
                        });
                        </script>
                        <?php
                    }
                });
            }
            break; // Exit after first match
        } else {
            if($remaining <= 0){
                if (!empty($available_gateways) && is_array($available_gateways)) {
                    if (isset($available_gateways['crediting_gateway'])) unset($available_gateways['crediting_gateway']);
                }
            }
        }
    }

    return $available_gateways;

}