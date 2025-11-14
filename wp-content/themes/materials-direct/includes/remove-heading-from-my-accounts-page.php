<?php
add_filter( 'the_title', function( $title, $id ) {
    if ( is_page( 'my-account' ) && in_the_loop() ) {
        return ''; // remove title text
    }
    return $title;
}, 10, 2 );