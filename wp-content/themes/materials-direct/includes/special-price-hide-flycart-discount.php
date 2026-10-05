<?php
function md_is_special_price_order() {

    // Direct Special Price order
    if ( function_exists( 'WC' ) && WC()->session ) {

        $session_special_price = WC()->session->get( 'special_price', null );

        if (
            $session_special_price !== null &&
            floatval( $session_special_price ) > 0
        ) {
            return true;
        }
    }

    // Special Price order restored from a Capture Cart
    if ( function_exists( 'WC' ) && WC()->cart ) {

        foreach ( WC()->cart->get_cart() as $cart_item ) {

            if ( ! empty( $cart_item['is_special_price_order'] ) ) {
                return true;
            }
        }
    }

    return false;
}

add_filter(
    'advanced_woo_discount_rules_do_process_discounts_of_each_rule',
    'md_prevent_flycart_discount_on_special_price',
    10,
    6
);

function md_prevent_flycart_discount_on_special_price(
    $process,
    $is_cart,
    $rule,
    $product,
    $cart_item,
    $price_display_condition
) {

    // Only affect Flycart cart discount rules.
    if ( ! $is_cart ) {
        return $process;
    }

    // Normal orders remain completely unchanged.
    if ( ! md_is_special_price_order() ) {
        return $process;
    }

    // Stop Flycart's order-value/cart discount rules.
    if (
        is_object( $rule ) &&
        method_exists( $rule, 'hasCartDiscount' ) &&
        $rule->hasCartDiscount()
    ) {
        return false;
    }

    return $process;
}