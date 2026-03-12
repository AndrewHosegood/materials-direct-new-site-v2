<?php
add_filter( 'loop_shop_per_page', 'custom_products_per_page', 20 );
function custom_products_per_page( $cols ) {
    return 15;
}