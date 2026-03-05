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
add_action('woocommerce_cart_calculate_fees', 'add_optional_fee_costs', 10);
function add_optional_fee_costs() {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Detect if this is a restored cart
    $is_restored_cart = false;
    foreach (WC()->cart->get_cart() as $item) {
        if (!empty($item['restored_from_capture'])) {
            $is_restored_cart = true;
            break;
        }
    }

    // If restored → check if the fee already exists; if yes, skip to preserve it
    if ($is_restored_cart) {
        $existing_fees = WC()->cart->get_fees();
        foreach ($existing_fees as $fee) {
            if ($fee->name === 'All COFC\'s & FAIR\'s') {
                error_log('Skipping add_optional_fee_costs — restored fee already exists');
                return; // Preserve the fee added during restore
            }
        }
        // If no fee exists yet (rare edge case), let it proceed or log
        error_log('No existing restored fee found — allowing normal fee calculation');
    }

    // Normal logic for non-restored carts (or restored but missing fee)
    $grand_total = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (isset($cart_item['optional_fees']) && is_array($cart_item['optional_fees'])) {
            foreach ($cart_item['optional_fees'] as $amount) {
                $grand_total += floatval($amount);
            }
        }
        if (isset($cart_item['custom_inputs']['total_optional_fees'])) {
            $grand_total += floatval($cart_item['custom_inputs']['total_optional_fees']);
        }
    }

    if ($grand_total > 0) {
        WC()->cart->add_fee('All COFC\'s & FAIR\'s', $grand_total, true);
        error_log("Added normal optional fees total: £{$grand_total}");
    } else if ($is_restored_cart) {
        error_log('Restored cart but no fees to add — no fee will be present');
    }
}