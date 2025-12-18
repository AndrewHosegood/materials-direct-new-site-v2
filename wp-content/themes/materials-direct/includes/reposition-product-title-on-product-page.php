<?php
// Remove the default product title
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

// Add the product title back at a custom position (you can change the priority as needed)
add_action( 'woocommerce_single_product_summary', 'custom_add_product_title', 5 );

function custom_add_product_title() {
    echo '<h1 class="product-page__title">' . get_the_title() . '</h1>';
}