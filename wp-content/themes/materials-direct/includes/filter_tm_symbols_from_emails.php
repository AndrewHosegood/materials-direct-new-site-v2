<?php
function custom_remove_tm_from_email_product_name( $name, $item, $is_visible ) {

    $search = array(
        '™',
        '&trade;',
        '&#8482;',
        '&#x2122;',
    );

    $name = str_replace( $search, '', $name );

    return $name;
}

add_filter( 'woocommerce_order_item_name', 'custom_remove_tm_from_email_product_name', 10, 3 );