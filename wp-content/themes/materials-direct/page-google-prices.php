<?php
/*
Template Name: Google Prices
*/

// http://localhost:8888/google-prices/?key=400284538621
$key = $_GET['key'] ?? '';

if ( $key !== '400284538621' ) {
    wp_die( 'Unauthorized access.' );
}

// Disable all output
ob_start();

// Update prices in batches
$total_products = wp_count_posts( 'product' )->publish;
$batch_size     = 100;
$total_batches  = ceil( $total_products / $batch_size );

error_log( "[Price Update] Starting price update for {$total_products} products in {$total_batches} batches" ); 

for ( $i = 0; $i < $total_batches; $i++ ) {
    $offset = $i * $batch_size;

    error_log( "[Price Update] Processing batch {$i} (offset {$offset})" );

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $batch_size,
        'offset'         => $offset,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    );

    $product_ids = get_posts( $args );

    if ( empty( $product_ids ) ) {
        error_log( "[Price Update] No products found in batch {$i}" );
        continue;
    }

    foreach ( $product_ids as $product_id ) {
        update_100x100_price_for_product( $product_id );
    }
}

error_log( "[Price Update] All batches complete." );

ob_end_clean();
echo 'Price update completed.';
exit;


// --------------------
// Price Update Logic
// --------------------

function update_100x100_price_for_product( $product_id ) {
    $product = wc_get_product( $product_id );
    $product_title = $product ? $product->get_name() : '';
    $product_title_appended = "100mm x 100mm - " . $product_title;
    $borderSizeAcf      = floatval( get_field( 'border_around', $product_id ) );
    $cost_per_cm2       = floatval( get_field( 'cost_per_cm', $product_id ) );
    $globalPriceAdjust  = floatval( get_field( 'global_adjust_square_rectangle', 'options' ) );
    $qtySelected        = 1;

    if ( $cost_per_cm2 === 0 || $globalPriceAdjust === 0 ) {
        return;
    }

    $borderSize     = $borderSizeAcf * 2;
    $setWidth       = 10;
    $setLength      = 10;

    $maxSetWidth    = $setWidth + $borderSize;
    $maxSetLength   = $setLength + $borderSize;
    $ppp            = $maxSetLength * $maxSetWidth * $cost_per_cm2;

    $myPPPValueTotalAcf = number_format( $ppp * $globalPriceAdjust * $qtySelected, 2 );
    $myPPPValueTotalAcf = number_format($myPPPValueTotalAcf * 1.2, 2); // add 20%


    update_field( '100_x_100mm_price', $myPPPValueTotalAcf, $product_id );
    update_field( 'google_product_title', $product_title_appended, $product_id );
}
