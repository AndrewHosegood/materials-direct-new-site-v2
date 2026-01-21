<?php
function display_exchange_rates_shortcode() {
    //http://localhost:8888/exchange-rates/?key=200365271086
    //https://exchangerate.host/dashboard?logged_in=1
    // API URL
    if ($_GET['key'] !== '200365271086') {
        http_response_code(403); // Forbidden
        return('Access denied');
    }
    $domain = $_SERVER['HTTP_HOST'];
    $api_url = "https://api.exchangerate.host/live?access_key=71efc9d846d3b3458094c3e4810c51f2&source=GBP&currencies=USD,EUR,GBP";
    
    // Fetch exchange rates
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return '<p>Error: Unable to connect to exchange rate service.</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['success']) && $data['success'] === true) {

        $gbp_usd = isset($data['quotes']['GBPUSD']) ? number_format($data['quotes']['GBPUSD'], 2) : 'N/A';
        $gbp_eur = isset($data['quotes']['GBPEUR']) ? number_format($data['quotes']['GBPEUR'], 2) : 'N/A';

        $gbp_usd_display = $gbp_usd * 1.1;
        $gbp_eur_display = $gbp_eur * 1.1;
        // $gbp_usd_display = $gbp_usd * 10;
        // $gbp_eur_display = $gbp_eur * 10;
        if($domain === 'newbuild.staging-materials-direct.co.uk'){
            update_field('currency_rate_to_gbp', $gbp_usd_display, 1350); //add 10% [multiply base value by 1.1]
            update_field('currency_rate_to_gbp', $gbp_eur_display, 1348); //add 10% [multiply base value by 1.1]
        } else {
            update_field('currency_rate_to_gbp', $gbp_usd_display, 1384); //add 10% [multiply base value by 1.1]
            update_field('currency_rate_to_gbp', $gbp_eur_display, 1386); //add 10% [multiply base value by 1.1]
        }
        

        // send email confirmation
        $to = 'andrewh@materials-direct.com';
        $subject = 'Exchange Rate Update Completed';
        $message = "The exchange rate update task has successfully run.\n\n"
                 . "GBP to USD: $gbp_usd (Updated: $gbp_usd_display)\n"
                 . "GBP to EUR: $gbp_eur (Updated: $gbp_eur_display)\n\n"
                 . "Timestamp: " . date('Y-m-d H:i:s');
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
    
        wp_mail($to, $subject, $message, $headers);

        return "
            <div style='background:#efefef; padding: 0 1rem; margin-bottom: 2rem;'>
            <h4>Raw Exchange Rates (Updated)</h4>
            <p><strong>GBP to USD:</strong> {$gbp_usd}<br>
            <strong>GBP to EUR:</strong> {$gbp_eur}</p>

            <h4>Refactored Exchange Rates (Updated)</h4>
            <p><strong>GBP to USD:</strong> {$gbp_usd_display}<br>
            <strong>GBP to EUR:</strong> {$gbp_eur_display}</p>
            </div>
        ";

    } else {
        return '<p>Error: Unable to retrieve exchange rates.</p>';
    }
}

//Register the shortcode
add_shortcode('exchange_rates', 'display_exchange_rates_shortcode');


// Stop google from indexing this page
function add_noindex_to_specific_page() {
    if (isset($_GET['key']) && $_GET['key'] === '200365271086' && is_page('exchange-rates')) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}

add_action('wp_head', 'add_noindex_to_specific_page');

