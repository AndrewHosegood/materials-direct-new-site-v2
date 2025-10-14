<?php
add_action( 'woocommerce_before_shop_loop_item', 'custom_add_container_div_to_thumbnail', 5 );

function custom_add_container_div_to_thumbnail() {
	echo '<div class="woocommerce-shop__mask">';
}