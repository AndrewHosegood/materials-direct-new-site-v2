<?php
function custom_remove_product_titles_from_shop_and_categories() {
    if ( is_shop() || is_product_category() || is_product() ) {
        remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
    }
}
add_action( 'woocommerce_before_shop_loop', 'custom_remove_product_titles_from_shop_and_categories', 1 );