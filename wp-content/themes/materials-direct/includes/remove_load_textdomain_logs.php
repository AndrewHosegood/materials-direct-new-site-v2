<?php
// my debug_log is being flooded by these warning this temporarily switched this off
add_filter( 'doing_it_wrong_trigger_error', function( $trigger, $function ) {
    if ( $function === '_load_textdomain_just_in_time' ) {
        return false;
    }
    return $trigger;
}, 10, 2 );