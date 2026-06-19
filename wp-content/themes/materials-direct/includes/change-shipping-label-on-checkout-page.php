<?php
add_filter( 'woocommerce_shipping_package_name', 'custom_shipping_package_label', 10, 3 );

function custom_shipping_package_label( $package_name, $i, $package ) {
    return 'Shipping';
}