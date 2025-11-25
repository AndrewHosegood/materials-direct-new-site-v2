<?php

/**
 * Save custom checkbox values to cart item data
 */
add_filter('woocommerce_add_cart_item_data', 'ss_save_optional_fees_cart_item_data', 10, 3);
function ss_save_optional_fees_cart_item_data($cart_item_data, $product_id, $variation_id) {

    $checkboxes_scheduled = [
        'add_manufacturers_COFC_ss',
        'add_fair_ss',
        'add_materials_direct_COFC_ss',
    ];

    foreach ($checkboxes_scheduled as $field) {
        if (isset($_POST[$field]) && !empty($_POST[$field])) {
            $cart_item_data['optional_fees'][$field] = floatval($_POST[$field]);
        }
    }

    return $cart_item_data;
}



/**
 * Show selected optional fees under the product name in cart/checkout
 */
add_filter('woocommerce_get_item_data', 'ss_display_optional_fees_cart', 10, 2);
function ss_display_optional_fees_cart($item_data, $cart_item) {

    if (isset($cart_item['optional_fees'])) {

        $labels = [
            'add_manufacturers_COFC_ss'     => 'Manufacturers COFC',
            'add_fair_ss'                   => 'First Article Inspection Report',
            'add_materials_direct_COFC_ss'  => 'Materials Direct COFC',
        ];

        foreach ($cart_item['optional_fees'] as $key => $amount) {
            $item_data[] = [
                'name'  => $labels[$key] ?? $key,
                'value' => '£' . number_format($amount, 2)
            ];
        }
    }

    return $item_data;
}




/**
 * Add optional fees to the cart totals based on checkboxes selected
 */
add_action('woocommerce_cart_calculate_fees', 'ss_add_optional_fee_costs');
function ss_add_optional_fee_costs() {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Initialize grand total for all optional fees
    $grand_total = 0;

    // Loop through all cart items and sum ALL fees (per line item, no qty multiplier)
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if (!isset($cart_item['optional_fees'])) {
            continue;
        }
        foreach ($cart_item['optional_fees'] as $key => $amount) {
            if ($amount > 0) {
                $grand_total += floatval($amount);
            }
        }
    }

    // Add a single consolidated fee for ALL optional fees
    if ($grand_total > 0) {
        WC()->cart->add_fee(
            'All COFC\'s & FAIR\'s', // Custom label as requested
            $grand_total,
            true // taxable (set false if not taxable)
        );
    }
}