<?php
function send_credit_limit_reminder_email_once() {

	if (is_admin() || !is_user_logged_in()) {
        return;
    }

	$user_id = get_current_user_id();
    $credit_limit_remaining = (float) get_field('credit_options_credit_limit_remaining', 'user_' . $user_id);
    $allow_credit = (bool) get_field('credit_options_allow_user_credit_option', 'user_' . $user_id);
    $original_credit_allowence = (float) get_field('credit_options_original_credit_allowance', 'user_' . get_current_user_id());

	// Validate ACF fields
    if (!is_numeric($credit_limit_remaining) || !$allow_credit) {
        return;
    }

    $cart_total = (float) WC()->cart->get_total('edit');
    $remaining = $credit_limit_remaining - $cart_total;

	// Bail if remaining is positive
    if ($remaining >= 0) {
        return;
    }

	// Check if email was sent within the last 3 minutes
    $last_sent = get_user_meta($user_id, 'credit_limit_email_last_sent', true);
    $time_since_last_sent = $last_sent ? time() - $last_sent : null;

    if ($last_sent && (time() - $last_sent < 180)) {
        return;
    }

    // Check if cart contents have changed
    $cart = WC()->cart->get_cart();
    $cart_hash = md5(json_encode($cart));
    $last_cart_hash = get_user_meta($user_id, 'credit_limit_last_cart_hash', true);

    if ($last_cart_hash && $cart_hash === $last_cart_hash) {
        return;
    }

    $current_user = wp_get_current_user();
    $customer_name = trim($current_user->user_firstname . ' ' . $current_user->user_lastname) ?: 'Customer';
    $customer_email = $current_user->user_email;

    $billing_company = WC()->customer->get_billing_company() ?: 'N/A';
    $billing_phone = WC()->customer->get_billing_phone() ?: 'N/A';
    $billing_address = WC()->customer->get_billing_address_1() ?: 'N/A';
    $billing_city = WC()->customer->get_billing_city() ?: 'N/A';
    $billing_postcode = WC()->customer->get_billing_postcode() ?: 'N/A';
    $billing_country = WC()->customer->get_billing_country() ?: 'N/A';

    // Get latest order for customer by billing email
    
    $latest_order_number = 'N/A';

    $order_query = new WP_Query([
        'post_type'      => 'shop_order',
        'post_status'    => ['wc-completed', 'wc-processing', 'wc-on-hold'],
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => [
            [
                'key'   => '_billing_email',
                'value' => $customer_email,
            ],
        ],
    ]);

    if ($order_query->have_posts()) {
        $order_query->the_post();
        $order = wc_get_order(get_the_ID());

        if ($order) {
            $latest_order_number = $order->get_order_number();
        }
    }

    wp_reset_postdata();

    // Get latest order for customer by billing email

    $subject_customer = 'Information About Your Materials Direct Account';
    $message_customer = sprintf(
        '<p>Dear '.$customer_name.',</p>
        <p>Please ensure you keep your account in credit as credit arrears will will effect your shipments.</p>
        <h3>Your Account Summary</h3>
        <ul>
            <li><strong>Your Credit Limit Is:</strong> '.$remaining.'</li>
        </ul>
        <p>Please contact our support team to discuss credit issues if required:</p>
        <p>Email: <a href="mailto:accounts@materials-direct.com">accounts@materials-direct.com</a><br>Phone: 01908 221222</p>
        <p>Thank you,<br>The Materials Direct Team</p>'
    );

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $sent_customer = wp_mail($customer_email, $subject_customer, $message_customer, $headers);

    $subject_accounts = sprintf('Customer %s has run out of credit', $customer_name);
    $message_accounts = sprintf(
        '<p>The customer %s (%s) has run out of credit on materials-direct.com.</p>
         <p>The order the customer placed was #%s</p>
        <h3>Customer Details</h3>
        <ul>
            <li><strong>Name:</strong> %s</li>
            <li><strong>Email:</strong> %s</li>
            <li><strong>Company:</strong> %s</li>
            <li><strong>Telephone:</strong> %s</li>
            <li><strong>Address:</strong> %s, %s, %s, %s</li>
        </ul>
        <h3>Account Summary</h3>
        <ul>
            <li><strong>Credit Limit:</strong> %s</li>
            <li><strong>Cart Total:</strong> %s</li>
            <li><strong>Credit Deficit:</strong> %s</li>
        </ul>',
        esc_html($customer_name),
        esc_html($customer_email),
        esc_html($latest_order_number),
        esc_html($customer_name),
        esc_html($customer_email),
        esc_html($billing_company),
        esc_html($billing_phone),
        esc_html($billing_address),
        esc_html($billing_city),
        esc_html($billing_postcode),
        esc_html($billing_country),
        wc_price($credit_limit_remaining),
        wc_price($cart_total),
        wc_price(abs($remaining))
    );

   // Retrieve accounts emails from ACF global options repeater
    $accounts_emails = [];
    $repeater = get_field('credit_arrears_emails', 'option');

    if ($repeater && is_array($repeater)) {
        foreach ($repeater as $row) {
            $email = isset($row['credit_arrears_email']) ? trim($row['credit_arrears_email']) : '';
            if ($email && is_email($email)) {
                $accounts_emails[] = $email;
            }
        }
    }

    // Default to true if no emails are configured (so meta still updates if customer email sent)
    $sent_accounts = true;

    if (!empty($accounts_emails)) {
        $sent_accounts = wp_mail($accounts_emails, $subject_accounts, $message_accounts, $headers);
    }

    if ($sent_customer && $sent_accounts) {
        $meta_updated = update_user_meta($user_id, 'credit_limit_email_last_sent', time());
        $meta_updated = update_user_meta($user_id, 'credit_limit_last_cart_hash', $cart_hash);
       error_log('Emails sent fo r user ID: ' . $user_id);
    } else {
        error_log('Failed to send one or both emails for user ID: ' . $user_id);
    }

}

add_action('woocommerce_before_checkout_form', 'send_credit_limit_reminder_email_once');