<?php
/**
 * Automatic 5% Voucher Discount (Virtual Coupon) - Improved for reliable percentage
 * Only applies 5% to qualifying products (is_single + onbackorder/outofstock)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ==================== HELPER ==================== */
function qualifies_for_single_discount( $product ) {
    if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
        return false;
    }

    $stock_status = $product->get_stock_status();
    $stock_qty    = $product->get_stock_quantity();

    $is_effectively_out = ( $stock_status === 'outofstock' ) ||
                          ( $stock_status === 'onbackorder' && $stock_qty <= 0 );

    if ( ! $is_effectively_out ) {
        return false;
    }

    $is_single = get_field( 'is_product_single', $product->get_id() );

    if ( empty( $is_single ) && $product->is_type( 'variation' ) ) {
        $is_single = get_field( 'is_product_single', $product->get_parent_id() );
    }

    return ( $is_single === true || $is_single == 1 || strtolower( $is_single ) === 'true' || $is_single === '1' );
}

/* ==================== 1. CREATE VIRTUAL COUPON (Stronger percent enforcement) ==================== */
add_filter( 'woocommerce_get_shop_coupon_data', 'create_virtual_single5_coupon', 10, 2 );
function create_virtual_single5_coupon( $coupon_data, $coupon_code ) {
    if ( strtolower( $coupon_code ) !== 'single5' ) {
        return $coupon_data;
    }

    return array(
        'id'                       => 0,
        'type'                     => 'percent',          // Must be 'percent'
        'amount'                   => 5,
        'individual_use'           => false,
        'product_ids'              => array(),
        'excluded_product_ids'     => array(),
        'usage_limit'              => 0,
        'usage_limit_per_user'     => 0,
        'limit_usage_to_x_items'   => null,
        'free_shipping'            => false,
        'discount_tax'             => true,   // Change to false if you want discount before tax
        'date_expires'             => null,
        'email_restrictions'       => array(),
        'virtual'                  => true,
        'discount_type'            => 'percent',   // Extra key for some WC versions
    );
}

/* ==================== 2. FORCE PERCENTAGE CALCULATION + RESTRICT TO QUALIFYING PRODUCTS ==================== */
add_filter( 'woocommerce_coupon_is_valid_for_product', 'restrict_single5_to_qualifying', 999, 4 );
function restrict_single5_to_qualifying( $valid, $product, $coupon, $values ) {
    if ( strtolower( $coupon->get_code() ) === 'single5' ) {
        return qualifies_for_single_discount( $product );
    }
    return $valid;
}

/* Optional: Force the discount amount calculation for this coupon */
add_filter( 'woocommerce_coupon_get_discount_amount', 'force_single5_percent_calculation', 999, 5 );
function force_single5_percent_calculation( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
    if ( strtolower( $coupon->get_code() ) === 'single5' && $cart_item ) {
        $product = $cart_item['data'];
        if ( qualifies_for_single_discount( $product ) ) {
            // Force 5% on this line's discounting amount
            return round( (float) $discounting_amount * 0.05, 2 );
        }
    }
    return $discount;
}

/* ==================== 3. AUTO APPLY / REMOVE COUPON ==================== */
add_action( 'woocommerce_cart_loaded_from_session', 'auto_apply_single5_coupon', 10 );
function auto_apply_single5_coupon( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    $coupon_code = 'single5';
    $has_qualifying = false;

    foreach ( $cart->get_cart() as $cart_item ) {
        if ( qualifies_for_single_discount( $cart_item['data'] ) ) {
            $has_qualifying = true;
            break;
        }
    }

    if ( $has_qualifying ) {
        if ( ! $cart->has_discount( $coupon_code ) ) {
            $cart->apply_coupon( $coupon_code );
        }
    } else {
        if ( $cart->has_discount( $coupon_code ) ) {
            $cart->remove_coupon( $coupon_code );
        }
    }
}

/* ==================== 4. FRIENDLY LABEL ==================== */
add_filter( 'woocommerce_cart_totals_coupon_label', 'rename_single5_label', 10, 2 );
function rename_single5_label( $label, $coupon ) {
    if ( strtolower( $coupon->get_code() ) === 'single5' ) {
        return __( '5% Voucher Discount', 'your-textdomain' );
    }
    return $label;
}