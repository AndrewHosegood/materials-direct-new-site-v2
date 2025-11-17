<?php
/**
 * Save custom checkbox values to cart item data
 */
add_filter('woocommerce_add_cart_item_data', 'save_optional_fees_cart_item_data', 10, 3);
function save_optional_fees_cart_item_data($cart_item_data, $product_id, $variation_id) {

    $checkboxes = [
        'add_manufacturers_COFC',
        'add_fair',
        'add_materials_direct_COFC',
    ];

    foreach ($checkboxes as $field) {
        if (isset($_POST[$field]) && !empty($_POST[$field])) {
            $cart_item_data['optional_fees'][$field] = floatval($_POST[$field]);
        }
    }

    return $cart_item_data;
}



/**
 * Show selected optional fees under the product name in cart/checkout
 */
add_filter('woocommerce_get_item_data', 'display_optional_fees_cart', 10, 2);
function display_optional_fees_cart($item_data, $cart_item) {

    if (isset($cart_item['optional_fees'])) {

        $labels = [
            'add_manufacturers_COFC'     => 'Manufacturers COFC',
            'add_fair'                   => 'First Article Inspection Report',
            'add_materials_direct_COFC'  => 'Materials Direct COFC',
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
add_action('woocommerce_cart_calculate_fees', 'add_optional_fee_costs');
function add_optional_fee_costs() {
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


/*
add_action('woocommerce_cart_calculate_fees', 'add_optional_fee_costs');
function add_optional_fee_costs() {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    $labels = [
        'add_manufacturers_COFC' => 'Add Manufacturers COFC',
        'add_fair' => 'Add FAIR',
        'add_materials_direct_COFC' => 'Add Materials Direct COFC',
    ];

    // Initialize fee totals array
    $fee_totals = [];

    // Loop through all cart items and sum fees by type, multiplying by quantity
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if (!isset($cart_item['optional_fees'])) {
            continue;
        }
        foreach ($cart_item['optional_fees'] as $key => $amount) {
            if ($amount > 0) {
                $fee_totals[$key] = isset($fee_totals[$key]) ? $fee_totals[$key] + floatval($amount) : floatval($amount);
            }
        }
    }

    // Add a single consolidated fee for each fee type
    foreach ($fee_totals as $key => $total) {
        if ($total > 0) {
            WC()->cart->add_fee(
                $labels[$key] ?? $key,
                $total,
                true 
            );
        }
    }
}
*/


/*
add_action('woocommerce_cart_calculate_fees', 'add_optional_fee_costs');
function add_optional_fee_costs() {

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    $labels = [
        'add_manufacturers_COFC'     => 'Add Manufacturers COFC',
        'add_fair'                   => 'Add FAIR',
        'add_materials_direct_COFC'  => 'Add Materials Direct COFC',
    ];

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

        if (!isset($cart_item['optional_fees'])) {
            continue;
        }

        foreach ($cart_item['optional_fees'] as $key => $amount) {
            if ($amount > 0) {
                WC()->cart->add_fee(
                    $labels[$key] ?? $key,
                    floatval($amount),
                    true  // taxable (set false if not taxable)
                );
            }
        }
    }
}
    */