<?php
add_action('admin_menu', 'add_google_marketplace_dashboard_item');

function add_google_marketplace_dashboard_item() {
    // Add top-level menu page
    add_menu_page(
        'Google Marketplace Price',               // Page title
        'Google Marketplace Price',               // Menu title
        'manage_options',                     // Capability
        'google-marketplace-price',               // Menu slug
        'google_marketplace_price_callback',      // Callback function
        'dashicons-admin-generic',                // Icon (optional)
        56                                       // Position (optional, just below WooCommerce)
    );
}

// Callback function to render the page
function google_marketplace_price_callback() {
    ?>
    <div class="wrap">
        <h1>Google Marketplace Price</h1>
        <p style="font-size: 0.9em; color: #a00; max-width: 600px;">
            <strong>Use this feature with caution.</strong><br>
            This action will update Google Marketplace prices on <strong>ALL</strong> the products in the database.<br>
            I recommend a database backup is actioned before proceeding.
        </p>
        <a href="/google-prices/?key=400284538621" class="button button-primary" style="margin-top: 20px;">
            Update Prices
        </a>
    </div>
    <?php
}