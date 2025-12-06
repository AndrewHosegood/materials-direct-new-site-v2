<?php
add_action( 'woocommerce_before_single_product', 'hide_rolls_tab_based_on_acf', 20 );
function hide_rolls_tab_based_on_acf() {
    if ( ! is_product() ) {
        return;
    }

    global $post;

    // Get ACF values
    $sold_as_roll_length = get_field('sold_as_roll_length', $post->ID); // true/false
    $roll_length         = get_field('roll_length', $post->ID);         // number or null



    if ( empty( $sold_as_roll_length ) || empty( $roll_length ) ) : ?>
		<style>
            /* Hide the <li> that contains the input with id="rolls" */
            li.product-page__tabs-list:has(input#rolls) {
                display: none !important;
            }
        </style>
    <?php
    endif;
}