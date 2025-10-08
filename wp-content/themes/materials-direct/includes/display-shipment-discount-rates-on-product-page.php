<?php
function add_delivery_options_modal() {
    include get_template_directory() . '/includes/scheduled-shipments-info-modal.php';
}
add_action('wp_footer', 'add_delivery_options_modal');