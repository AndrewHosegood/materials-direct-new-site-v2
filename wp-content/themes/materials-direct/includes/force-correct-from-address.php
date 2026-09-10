<?php
// Add From to all custom email scripts
add_filter('wp_mail_from', function ($from) {
    return 'info@materials-direct.com';
});

add_filter('wp_mail_from_name', function ($name) {
    return 'Materials Direct';
});
// Add From to all custom email scripts