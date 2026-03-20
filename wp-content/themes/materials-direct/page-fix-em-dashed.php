<?php
/*
Template Name: Fix Em Dash
*/

get_header();
?>



<!-- Banner -->
<?php require_once('page-includes/sector/sector-banner.php'); ?>
<!-- Banner -->

<div class="container">

<?php
/**
 * One-time: Convert all smart dashes in product titles to plain hyphen
 * Run by visiting: yoursite.com/fix-product-dashes.php
 */
require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('Admin only.');
}

$products = get_posts([
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

$dash_map = [
    '–' => '-',   // en dash
    '—' => '-',   // em dash
    '−' => '-',   // minus sign
    '‒' => '-',   // figure dash
    '‑' => '-',   // non-breaking hyphen
];

$fixed = 0;

foreach ($products as $id) {
    $raw_title = get_post_field('post_title', $id, 'raw'); // bypass filters

    $new_title = str_replace(array_keys($dash_map), array_values($dash_map), $raw_title);

    // Optional: also strip ™ and ® if you ever want them gone (most stores do)
    // $new_title = str_replace(['™', '®'], '', $new_title);

    if ($new_title !== $raw_title) {
        wp_update_post([
            'ID'         => $id,
            'post_title' => $new_title,
        ], true); // true = suppress filters/hooks if you want

        $fixed++;
        error_log("Fixed product #{$id}: {$new_title}");
    }
}

echo "<h2>Done!</h2>";
echo "<p>Fixed <strong>{$fixed}</strong> product titles.</p>";
echo "<p><a href='/shop/'>Go back to shop</a></p>";

?>

</div>



<?php

get_footer();
