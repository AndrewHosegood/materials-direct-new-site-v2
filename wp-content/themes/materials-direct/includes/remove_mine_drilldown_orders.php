<?php
add_filter( 'views_edit-shop_order', function( $views ) {
    unset( $views['mine'] );
    return $views;
} );