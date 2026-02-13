<?php
/*
Template Name: Custom Product Search Results
*/

get_header();

global $wpdb;

// Truncate helper (keep if not already in functions.php)
if (!function_exists('truncate_plain_text')) {
    function truncate_plain_text($text, $limit = 200) {
        $text = wp_strip_all_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $limit);
        $truncated = preg_replace('/\s+\S*$/', '', $truncated);
        return $truncated . '…';
    }
}

$original_term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

if (empty($original_term)) {
    echo '<div class="container"><p>No search term provided.</p></div>';
    get_footer();
    exit;
}

// Normalize only for the search query (display shows what user actually typed)
$term = normalize_search_term($original_term);

$search = '%' . $wpdb->esc_like($term) . '%';
$per_page = 9;
$paged    = max(1, absint($_GET['paged'] ?? 1));
$offset   = ($paged - 1) * $per_page;

// Count query
$count_sql = $wpdb->prepare("
    SELECT COUNT(DISTINCT p.ID)
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm_priority
        ON pm_priority.post_id = p.ID AND pm_priority.meta_key = 'search_priority'
    LEFT JOIN {$wpdb->postmeta} pm_sku
        ON pm_sku.post_id = p.ID AND pm_sku.meta_key = '_sku'
    WHERE p.post_type = 'product'
      AND p.post_status = 'publish'
      AND (
          p.post_title LIKE %s
          OR p.post_excerpt LIKE %s
          OR p.post_content LIKE %s
          OR pm_sku.meta_value LIKE %s
      )
", $search, $search, $search, $search);

$total       = (int) $wpdb->get_var($count_sql);
$total_pages = ceil($total / $per_page);

?>
<div class="container" style="margin-top:2rem;">

    <h2>Search results for "<?= esc_html($original_term) ?>"
        <?php if ($total > 0) : ?>
            <span style="font-weight:normal; font-size:0.7em;">(<?= $total ?> found)</span>
        <?php endif; ?>
    </h2>

    <?php if ($total === 0) : ?>
        <p>No products found.</p>
    <?php else : ?>

        <?php
        // Main results query
        $sql = $wpdb->prepare("
            SELECT DISTINCT
                p.ID,
                p.post_title,
                p.post_excerpt,
                p.post_content,
                pm_priority.meta_value AS search_priority,
                pm_sku.meta_value AS sku
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm_priority
                ON pm_priority.post_id = p.ID AND pm_priority.meta_key = 'search_priority'
            LEFT JOIN {$wpdb->postmeta} pm_sku
                ON pm_sku.post_id = p.ID AND pm_sku.meta_key = '_sku'
            WHERE p.post_type = 'product'
              AND p.post_status = 'publish'
              AND (
                  p.post_title LIKE %s
                  OR p.post_excerpt LIKE %s
                  OR p.post_content LIKE %s
                  OR pm_sku.meta_value LIKE %s
              )
            ORDER BY
                (pm_priority.meta_value IS NULL) ASC,
                pm_priority.meta_value ASC,
                p.post_title ASC
            LIMIT %d OFFSET %d
        ", $search, $search, $search, $search, $per_page, $offset);

        $results = $wpdb->get_results($sql);
        ?>

        <ul class="search__results custom-search-results-full" style="list-style:none; padding:0;">
            <?php foreach ($results as $row) :
                $permalink = get_permalink($row->ID);
                $title     = esc_html($row->post_title);
                $sku       = !empty($row->sku) ? ' (SKU: ' . esc_html($row->sku) . ')' : '';
                $excerpt   = !empty($row->post_excerpt)
                    ? truncate_plain_text($row->post_excerpt, 200)
                    : truncate_plain_text($row->post_content, 200);
                $thumbnail = get_the_post_thumbnail($row->ID, 'medium', ['style' => 'width: 100%; max-width:100%; height:auto; float:left; margin:0;']);
            ?>
                <li class="search__results-card" style="clear:both; overflow:hidden; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid #eee;">
                    
                    <a href="<?= esc_url($permalink) ?>" class="search__results-thumb">
                        <div class="woocommerce-shop__soft-border"></div>
                        <?php if ($thumbnail) echo $thumbnail; ?>
                    </a>

                    <a href="<?= esc_url($permalink) ?>" class="search__results-content">
                        <div class="search__results-title-content">
                            <a href="<?= esc_url($permalink) ?>" style="text-decoration:none; color:#000;">
                                <p class="search__results-title"><?= $title ?></p>
                            </a>
                            <p class="search__results-sku">SKU: <?= esc_html($row->sku) ?></p>
                        </div>

                        <div class="woocommerce-shop__short-description"><?= $row->post_excerpt ?></div>
                        <a class="button search__results-btn" href="<?= esc_url($permalink) ?>">Select Options</a>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($total_pages > 1) :
            $current_url   = add_query_arg(null, null);
            $base_url      = remove_query_arg('paged', $current_url);
            $paginate_base = add_query_arg('paged', '%#%', $base_url);
        ?>
            <div class="pagination">
                <div class="pagination__container">
                <?= paginate_links([
                    'base'      => $paginate_base,
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $total_pages,
                    'prev_text' => '←',
                    'next_text' => '→',
                    'type'      => 'plain',
                ]) ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php get_footer(); ?>


<?php












/*


get_header();

global $wpdb;

// Truncate helper
if (!function_exists('truncate_plain_text')) {
    function truncate_plain_text($text, $limit = 200) {
        $text = wp_strip_all_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $limit);
        $truncated = preg_replace('/\s+\S*$/', '', $truncated);
        return $truncated . '…';
    }
}

$term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

if (empty($term)) {
    echo '<div class="container"><p>No search term provided.</p></div>';
    get_footer();
    exit;
}

$search = '%' . $wpdb->esc_like($term) . '%';
$per_page = 9;

// Changed from 'page' to 'paged' to avoid conflict with WordPress's built-in 'page' query var
$paged    = max(1, absint($_GET['paged'] ?? 1));
$offset   = ($paged - 1) * $per_page;

// 1. Count total matching products (unchanged)
$count_sql = $wpdb->prepare("
    SELECT COUNT(DISTINCT p.ID)
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm_priority
        ON pm_priority.post_id = p.ID AND pm_priority.meta_key = 'search_priority'
    LEFT JOIN {$wpdb->postmeta} pm_sku
        ON pm_sku.post_id = p.ID AND pm_sku.meta_key = '_sku'
    WHERE p.post_type = 'product'
      AND p.post_status = 'publish'
      AND (
          p.post_title LIKE %s
          OR p.post_excerpt LIKE %s
          OR p.post_content LIKE %s
          OR pm_sku.meta_value LIKE %s
      )
", $search, $search, $search, $search);

$total        = (int) $wpdb->get_var($count_sql);
$total_pages  = ceil($total / $per_page);

?>
<div class="container" style="margin-top:2rem;">

    <h2>Search results for "<?= esc_html($term) ?>"
        <?php if ($total > 0) : ?>
            <span style="font-weight:normal; font-size:0.7em;">(<?= $total ?> found)</span>
        <?php endif; ?>
    </h2>

    <?php if ($total === 0) : ?>
        <p>No products found.</p>
    <?php else : ?>

        <?php
        // 2. Main results query (unchanged except LIMIT/OFFSET)
        $sql = $wpdb->prepare("
            SELECT DISTINCT
                p.ID,
                p.post_title,
                p.post_excerpt,
                p.post_content,
                pm_priority.meta_value AS search_priority,
                pm_sku.meta_value AS sku
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm_priority
                ON pm_priority.post_id = p.ID AND pm_priority.meta_key = 'search_priority'
            LEFT JOIN {$wpdb->postmeta} pm_sku
                ON pm_sku.post_id = p.ID AND pm_sku.meta_key = '_sku'
            WHERE p.post_type = 'product'
              AND p.post_status = 'publish'
              AND (
                  p.post_title LIKE %s
                  OR p.post_excerpt LIKE %s
                  OR p.post_content LIKE %s
                  OR pm_sku.meta_value LIKE %s
              )
            ORDER BY
                (pm_priority.meta_value IS NULL) ASC,
                pm_priority.meta_value ASC,
                p.post_title ASC
            LIMIT %d OFFSET %d
        ", $search, $search, $search, $search, $per_page, $offset);

        $results = $wpdb->get_results($sql);
        ?>

        <ul class="search__results custom-search-results-full" style="list-style:none; padding:0;">
            <?php foreach ($results as $row) :
                $permalink = get_permalink($row->ID);
                $title     = esc_html($row->post_title);
                $sku       = !empty($row->sku) ? ' (SKU: ' . esc_html($row->sku) . ')' : '';
                $excerpt   = !empty($row->post_excerpt)
                    ? truncate_plain_text($row->post_excerpt, 200)
                    : truncate_plain_text($row->post_content, 200);
                $thumbnail = get_the_post_thumbnail($row->ID, 'medium', ['style' => 'width: 100%; max-width:100%; height:auto; float:left; margin:0;']);
            ?>
                <li class="search__results-card" style="clear:both; overflow:hidden; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid #eee;">

                    <div class="search__results-thumb">
                        <?php if ($thumbnail) echo $thumbnail; ?>
                    </div>

                    <div class="search__results-content">

                        <div class="search__results-title-content">
                            <a href="<?= esc_url($permalink) ?>" style="text-decoration:none; color:#000;">
                                <p class="search__results-title"><?= $title ?></p>
                            </a>
                            <p class="search__results-sku">SKU: <?= esc_html($row->sku) ?></p>
                        </div>

                        <div class="woocommerce-shop__short-description"><?= $row->post_excerpt ?></div>
                        <a class="button search__results-btn" href="<?= esc_url($permalink) ?>">Select Options</a>
                    </div>
                   
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($total_pages > 1) :
            // Updated pagination to use 'paged' instead of 'page'
            $current_url = add_query_arg(null, null); // full current URL
            $base_url    = remove_query_arg('paged', $current_url);
            $paginate_base = add_query_arg('paged', '%#%', $base_url);
        ?>
            <div class="pagination" style="text-align:center; margin:3rem 0;">
                <?= paginate_links([
                    'base'      => $paginate_base,
                    'format'    => '', // Keeps it as ?paged=2 (query string style)
                    'current'   => $paged,
                    'total'     => $total_pages,
                    'prev_text' => '&laquo; Previous',
                    'next_text' => 'Next &raquo;',
                    'type'      => 'plain',
                ]) ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php get_footer(); ?>
*/