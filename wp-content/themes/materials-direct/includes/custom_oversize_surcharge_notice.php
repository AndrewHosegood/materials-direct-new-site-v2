<?php
// Cart page hook (existing)
add_action('woocommerce_proceed_to_checkout', 'custom_oversize_surcharge_notice', 10);

// Checkout page hook (new)
add_action('woocommerce_review_order_before_payment', 'custom_oversize_surcharge_notice', 0);

function custom_oversize_surcharge_notice() {

    $cart = WC()->cart;
    if (!$cart) {
        return;
    }

    $show_oversize_note = false;
    $oversize_surcharge = 0;

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = isset($cart_item['product_id']) ? (int) $cart_item['product_id'] : 0;

        // Skip surcharge if disabled at product level
        if ($product_id && function_exists('get_field')) {
            $disable_oversize_surcharge = get_field('disable_oversize_surcharge', $product_id);
            if ($disable_oversize_surcharge) {
                continue;
            }
        }

        // 1) Pull width/length from regular (custom_inputs) when present
        $width  = isset($cart_item['custom_inputs']['width'])  ? (float) $cart_item['custom_inputs']['width']  : null;
        $length = isset($cart_item['custom_inputs']['length']) ? (float) $cart_item['custom_inputs']['length'] : null;

        // 2) Fallback to restored cart metadata (Width / Width (MM), Length / Length (MM))
        if ($width === null || $length === null) {
            $cm = isset($cart_item['cart_metadata']) && is_array($cart_item['cart_metadata'])
                ? $cart_item['cart_metadata']
                : [];

            // Some captures may store plain 'Width'/'Length' or '(MM)'
            if ($width === null) {
                if (isset($cm['Width']) && $cm['Width'] !== 'N/A') {
                    $width = (float) $cm['Width'];
                } elseif (isset($cm['Width (MM)']) && $cm['Width (MM)'] !== 'N/A') {
                    $width = (float) $cm['Width (MM)'];
                } else {
                    $width = 0;
                }
            }

            if ($length === null) {
                if (isset($cm['Length']) && $cm['Length'] !== 'N/A') {
                    $length = (float) $cm['Length'];
                } elseif (isset($cm['Length (MM)']) && $cm['Length (MM)'] !== 'N/A') {
                    $length = (float) $cm['Length (MM)'];
                } else {
                    $length = 0;
                }
            }
        }

        // 3) Determine number of shipments
        // Primary (regular flow): custom_inputs shipments_count
        $final_shipment_count = !empty($cart_item['custom_inputs']['shipments_count'])
            ? (int) $cart_item['custom_inputs']['shipments_count']
            : 0;

        // Fallback (restored flow): count restored_shipments if present
        if ($final_shipment_count <= 0 && !empty($cart_item['restored_shipments']) && is_array($cart_item['restored_shipments'])) {
            $final_shipment_count = max(1, count($cart_item['restored_shipments']));
        }

        // Safe default
        if ($final_shipment_count <= 0) {
            $final_shipment_count = 1;
        }

        // 4) Oversize rule (thresholds in mm)
        if ($width >= 1000 || $length >= 1000) {
            $oversize_surcharge = 30 * $final_shipment_count;
            $show_oversize_note = true;
            break; // one note is enough
        }
    }

    if ($show_oversize_note && $oversize_surcharge > 0) {
        echo "<small style='font-size: 10px; margin: -0.5rem 0 0.9rem 0; display: block; font-style: normal; font-weight: 700; text-align:right; color:#ef9003'>Shipping includes oversize surcharge of £30 per shipment.</small>";
    }
}