<?php
// Add From to all custom email scripts
add_filter('wp_mail_from', function ($from) {
    return 'info@staging-materials-direct.co.uk';
});

add_filter('wp_mail_from_name', function ($name) {
    return 'Materials Direct Staging';
});
// Add From to all custom email scripts