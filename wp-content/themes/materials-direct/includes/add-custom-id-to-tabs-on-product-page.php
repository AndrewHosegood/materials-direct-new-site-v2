<?php
add_action( 'woocommerce_after_single_product_summary', function() {
    ob_start();
}, 0 );

add_action( 'woocommerce_after_single_product_summary', function() {
    $html = ob_get_clean();

    // Inject ID into the wrapper div
    $html = str_replace(
        '<div class="woocommerce-tabs wc-tabs-wrapper"',
        '<div id="product_details" class="woocommerce-tabs wc-tabs-wrapper"',
        $html
    );

    // Now wrap the whole thing with your custom div
    //$wrapped = '<div class="product-details-wrapper">' . $html . '</div>';

    //echo $wrapped;

    echo $html;
}, 9999 );