<?php
function my_acf_save_prod( $post_id ) {

    // Only run for WooCommerce products.
    if ( get_post_type( $post_id ) !== 'product' ) {
        return;
    }

    $p_buyc = get_field( 'buy_cost', $post_id );
    $p_cf   = get_field( 'cost_factor', $post_id );

    $p_sc = (float) $p_buyc * (float) $p_cf;

    update_field( 'cost_per_cm', $p_sc, $post_id );
}

add_action( 'acf/save_post', 'my_acf_save_prod', 20 );