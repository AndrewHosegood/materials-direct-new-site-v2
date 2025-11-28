<?php
// Add custom voucher field to the checkout page
add_action('woocommerce_review_order_before_order_total', 'add_voucher_field_to_checkout', 1);
function add_voucher_field_to_checkout() {
    ?>

    <?php if (is_user_logged_in()) { ?>
    <?php $voucher_display = WC()->session->get('voucher_discount'); ?>  

    <?php if(empty($voucher_display)){ ?>
        <tr class="voucher-discount-toggle">
            <th>Have a discount code?</th>
            <td style="width: 235px;"><a id="voucherClick" style="font-weight: 300;text-decoration: underline; cursor:pointer;">Click Here</a></td>
        </tr>
    <?php } ?>  
    
    <tr class="voucher-discount" style="display:none;">
        <th>Discount</th>
        <td>
            <input class="voucher-discount__input" style="display:inline; margin: 0; width:135px; padding:0.25rem; font-size:0.73rem; border: 1px solid #dedede; font-weight: 400; color: #999;" type="text" id="voucher_code" name="voucher_code" placeholder="Enter discount code" />
            <button style="padding:0.29rem 0.7rem; font-size:0.8rem; background:#2d3e4f; margin:0; color: white; font-weight: 400;" type="button" id="apply_voucher">Add</button>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function($) {
            $('#apply_voucher').on('click', function() {
                var voucher = $('#voucher_code').val();
                $.ajax({
                    url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
                    type: 'POST',
                    data: {
                        action: 'apply_voucher',
                        security: '<?php echo wp_create_nonce("apply_voucher_nonce"); ?>',
                        voucher_code: voucher
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Discount code applied!');
                            $('#voucher_code').prop('disabled', true);
                            $('#apply_voucher').prop('disabled', true);
                            location.reload(); // Reload to reflect the discount
                        } else {
                            alert(response.data.message || 'Invalid discount code!');
                        }
                    }
                });
            });
        });
    </script>

    <?php } ?>

    <?php
}
// Add custom voucher field to the checkout page




// Validate voucher code and return discount value
function validate_voucher_code($voucher_code) {
    if (have_rows('vouchers', 'option')) {
        while (have_rows('vouchers', 'option')) {
            the_row();
            $stored_code = get_sub_field('voucher_code');
            $discount = get_sub_field('discount_amount');

            if ($stored_code === $voucher_code) {
                return $discount / 100; // Convert percentage to decimal
            }
        }
    }
    return false; // Return false if no matching code is found
}
// Validate voucher code and return discount value



// Apply voucher discount via AJAX
add_action('wp_ajax_apply_voucher', 'apply_voucher_discount');
add_action('wp_ajax_nopriv_apply_voucher', 'apply_voucher_discount');
function apply_voucher_discount() {
    check_ajax_referer('apply_voucher_nonce', 'security');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to use this discount code.']);
        return;
    }

    $customer_id = get_current_user_id();
    $customer_email = wp_get_current_user()->user_email;
    $voucher_code = sanitize_text_field($_POST['voucher_code']);
    $used_vouchers = get_user_meta($customer_id, 'used_voucher_codes', true);

    if (!$used_vouchers) {
        $used_vouchers = [];
    }

    /* TAKE NOTE */
    /* Re-occuring vouchers can be used multiple times by $allowed_users */


    /* define list of allowed users who get access to multiple discounts */
    $allowed_users = [];

    if (have_rows('allowed_users_for_discount', 'option')) { 
        while (have_rows('allowed_users_for_discount', 'option')) {
            the_row();

            $user_id = get_sub_field('allowed_user_id');
            $user_email = get_sub_field('allowed_user_email');

            if ($user_id && $user_email) { 
                $allowed_users[] = [
                    'id' => intval($user_id), 
                    'email' => sanitize_email($user_email) 
                ];
            }
        }
    }
    /* define list of allowed users who get access to multiple discounts */


    /* Get the list of allowed users */
    $is_allowed_user = false;
    foreach ($allowed_users as $user) {
        if ($customer_id === $user['id'] && $customer_email === $user['email']) {
            $is_allowed_user = true;
            break;
        }
    }



       // Handle special vouchers for allowed users
       $special_vouchers = ['xmdwqr_multiple_15', 'omhyze_multiple_10']; // Add comma seperated values in here as an array


       if ($is_allowed_user && in_array($voucher_code, $special_vouchers)) {
           // Special users can use these vouchers multiple times, no checks required
           $discount = validate_voucher_code($voucher_code);
       } else {
           // For other users, ensure the voucher hasn't been used already
           if (in_array($voucher_code, $used_vouchers)) {
               wp_send_json_error(['message' => 'You have already used this discount code.']);
               return;
           }
           $discount = validate_voucher_code($voucher_code);
       }



    if ($discount) {
        WC()->session->set('voucher_discount', $discount);

        // Add this voucher code to the user's list of used vouchers
        $used_vouchers[] = $voucher_code;
        update_user_meta($customer_id, 'used_voucher_codes', $used_vouchers);

        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => 'Incorrect discount code!']);
    }
}
// Apply voucher discount via AJAX




// Modify cart total to apply voucher discount
add_action('woocommerce_cart_calculate_fees', 'apply_voucher_discount_to_cart');
function apply_voucher_discount_to_cart() {
    $voucher_discount = WC()->session->get('voucher_discount');

    if ($voucher_discount) {
        $cart = WC()->cart;
        $discount_amount = $cart->get_subtotal() * $voucher_discount;
        $cart->add_fee('Discount Code', -$discount_amount, true);
    }
}
// Modify cart total to apply voucher discount




// Save voucher discount to order meta
add_action('woocommerce_checkout_update_order_meta', 'save_voucher_discount_to_order');
function save_voucher_discount_to_order($order_id) {
    $discount = WC()->session->get('voucher_discount');

    if ($discount) {
        update_post_meta($order_id, '_voucher_discount', $discount);
    }
}
// Save voucher discount to order meta





// Add voucher discount to emails
add_filter('woocommerce_email_order_meta_fields', 'add_voucher_discount_to_email_meta', 10, 3);
function add_voucher_discount_to_email_meta($fields, $sent_to_admin, $order) {
    $discount = get_post_meta($order->get_id(), '_voucher_discount', true);
    if ($discount) {
        $fields['voucher_discount'] = array(
            'label' => 'Discount Code',
            'value' => $discount * 100 . '%',
        );
    }
    return $fields;
}
// Add voucher discount to emails




// Add voucher discount to admin order view
add_action('woocommerce_admin_order_data_after_order_details', 'add_voucher_discount_to_admin_order_meta');
function add_voucher_discount_to_admin_order_meta($order) {
    $discount = get_post_meta($order->get_id(), '_voucher_discount', true);
    if ($discount) {
        echo '<p><strong>Discount Code:</strong> ' . ($discount * 100) . '%</p>';
    }
}
// Add voucher discount to admin order view



// Reset voucher session after order is placed
add_action('woocommerce_thankyou', 'reset_voucher_discount_session');
function reset_voucher_discount_session() {
    WC()->session->set('voucher_discount', false); // Reset the session variable
}
// Reset voucher session after order is placed




// check if the session exists for debugging
/*
add_action('wp_footer', 'debug_woocommerce_session');
function debug_woocommerce_session() {
    if (is_checkout() || is_cart()) { // Only show on cart/checkout pages
        $voucher_discount = WC()->session->get('voucher_discount');
        echo '<script>console.log("Discount Code Session: ' . esc_js($voucher_discount) . '");</script>';
    }
}
    */
// check if the session exists for debugging





// clear the current session for debugging
// add_action('wp_ajax_reset_voucher_session', 'reset_voucher_session');
// add_action('wp_ajax_nopriv_reset_voucher_session', 'reset_voucher_session');
// function reset_voucher_session() {
//     WC()->session->set('voucher_discount', false);
//     wp_send_json_success('Voucher session cleared');
// }
// clear the current session for debugging







// Display used voucher codes on the cart page for debugging
/*
add_action('woocommerce_before_cart', 'display_used_voucher_codes_debug');
function display_used_voucher_codes_debug() {
    if (!is_user_logged_in()) {
        echo '<p><strong>Debug:</strong> You are not logged in. No discount codes to display.</p>';
        return;
    }

    $customer_id = get_current_user_id();
    $used_vouchers = get_user_meta($customer_id, 'used_voucher_codes', true);

    if (!empty($used_vouchers) && is_array($used_vouchers)) {
        echo '<p><strong>Debug - Used Discount Codes:</strong></p>';
        echo '<ul>';
        foreach ($used_vouchers as $voucher) {
            echo '<li>' . esc_html($voucher) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p><strong>Debug - Used Discount Codes:</strong> No discount codes have been used yet.</p>';
    }
}
    */
// Display used voucher codes on the cart page for debugging







// reset the usage restriction for testing and debugging
// http://localhost:8888/?reset_voucher_usage=true
add_action('init', 'reset_voucher_usage_for_testing');
function reset_voucher_usage_for_testing() {
    // Check if the reset query parameter is present and user is logged in
    if (isset($_GET['reset_voucher_usage']) && $_GET['reset_voucher_usage'] === 'true') {
        $customer_id = get_current_user_id();

        // Ensure the user is logged in
        if ($customer_id) {
            // Reset the voucher usage meta
            update_user_meta($customer_id, 'used_voucher_codes', false);

            // Provide feedback for testing
            echo '<p style="color: green; font-weight: bold;">Voucher usage has been reset successfully!</p>';
        } else {
            echo '<p style="color: red; font-weight: bold;">You must be logged in to reset voucher usage.</p>';
        }

        // Stop further processing to avoid affecting other site functionality
        exit;
    }
}
// reset the usage restriction for testing and debugging



