<?php
add_filter('woocommerce_cart_item_name', 'wrap_product_name_in_span', 10, 3);
function wrap_product_name_in_span($product_name, $cart_item, $cart_item_key) {
    // Only run this on the checkout page
    if (!is_checkout()) {
        return $product_name;
    }

    // Wrap the product name in a span
    $wrapped_name = '<span class="product-name-bold">' . $product_name . '</span>';

    return $wrapped_name;
}