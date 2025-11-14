<?php
function my_remove_woocommerce_sorting_dropdown() {
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
}
add_action( 'wp', 'my_remove_woocommerce_sorting_dropdown' );