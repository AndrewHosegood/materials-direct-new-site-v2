<?php
add_action( 'woocommerce_single_product_summary', 'add_view_product_help_links', 25 );

function add_view_product_help_links() {
	echo '<div class="product-page__view-product-details"><a class="product-page__view-product-details-link" href="#product_details">View Product Details</a> | <a id="need_help_with_ordering" class="product-page__view-product-details-link" href="#">Need Help With Ordering?</a></div>';
}