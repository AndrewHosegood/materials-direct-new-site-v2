<?php
add_filter( 'woocommerce_product_cross_sells_products_heading', function( $heading ) {
    return 'You may also be interested in';
});