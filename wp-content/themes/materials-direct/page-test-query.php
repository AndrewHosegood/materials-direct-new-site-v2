<?php
/*
Template Name: Test Query
*/
get_header();

global $wpdb;

// Search term
$raw_search = 'softtherm';

// Prepare LIKE safely
$search = '%' . $wpdb->esc_like( $raw_search ) . '%';

// helper function
function truncate_plain_text( $text, $limit = 200 ) {
    $text = wp_strip_all_tags( $text );
    $text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
    $text = trim( preg_replace( '/\s+/', ' ', $text ) );

    if ( mb_strlen( $text ) <= $limit ) {
        return $text;
    }

    $truncated = mb_substr( $text, 0, $limit );

    // Avoid cutting off mid-word
    $truncated = preg_replace( '/\s+\S*$/', '', $truncated );

    return $truncated . '…';
}


// Build SQL
$sql = $wpdb->prepare("
    SELECT p.ID, p.post_title, p.post_excerpt, pm.meta_value AS search_priority
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm
        ON pm.post_id = p.ID
        AND pm.meta_key = 'search_priority'
    WHERE p.post_type = 'product'
      AND p.post_status = 'publish'
      AND p.post_title LIKE %s
    ORDER BY
        CAST(pm.meta_value AS UNSIGNED) ASC,
        p.post_title ASC
    LIMIT 40
", $search);

// Run query
$results = $wpdb->get_results( $sql );

?>

<div class="container">
    <h3 style="margin-top: 1rem;">Test Product Search Results 't-pad'</h3>

    <?php if ( ! empty( $results ) ) : ?>
       
        <ul style="max-width: 800px; border: 5px solid #ccc; padding: 0.2rem 1rem 1rem 1rem; margin-bottom: 3rem;">
            <?php foreach ( $results as $row ) : ?>
                <?php //echo "<pre>"; ?>
                <?php //print_r($row); ?>
                <?php //echo "</pre>"; ?>
                <li style="border-bottom: 1px solid #efefef; padding-bottom: 0.5rem;">
                    <a style="color: black;" href="<?php echo esc_url( get_permalink( $row->ID ) ); ?>"><strong style="margin-top:0.4rem; display:inline-block;"><?php echo esc_html( $row->post_title ); ?></strong></a><br>
                    <?php if ( ! empty( $row->post_excerpt ) ) : ?>
                        <div class="product-description">
                            <?php echo esc_html( truncate_plain_text( $row->post_excerpt, 200 ) ); ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No results found.</p>
    <?php endif; ?>
</div>

<?php
get_footer();








/*
get_header();

echo "<h1>TEST</h1>";

$search_term = 't-pad';


$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    's'              => $search_term,

    'tax_query' => array(
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => array( 'exclude-from-search' ),
            'operator' => 'NOT IN',
        ),
    ),

    'suppress_filters' => false,
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) {
    echo '<ul>';
    while ( $query->have_posts() ) {
        $query->the_post();
        echo '<li>' . get_the_title() . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No products found.</p>';
}

wp_reset_postdata();

get_footer();
*/




/*
$value = 'tpad 1500';

add_filter('posts_clauses', function ($clauses, $query) {
    global $wpdb;

    if (!is_admin() && $query->get('thickness_ordering')) {

        $clauses['join'] .= "
            LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        ";

        $clauses['where']  .= " AND tt.taxonomy = 'thickness'";
        $clauses['orderby'] = "t.name ASC";
        $clauses['groupby'] = "{$wpdb->posts}.ID";
    }

    return $clauses;
}, 10, 2);


$args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    's'              => $value,    
    'thickness_ordering' => true,       
];

$query = new WP_Query($args);
?>

<div class="test-query-results">
    <h2>Test Query Results</h2>

    <?php if ($query->have_posts()) : ?>
        <ul>
            <?php while ($query->have_posts()) : $query->the_post(); ?>

                <?php
                $thickness_terms = get_the_terms(get_the_ID(), 'thickness');
                $thickness_name  = $thickness_terms ? $thickness_terms[0]->name : '—';
                ?>

                <li>
                    <strong><?php the_title(); ?></strong>
                    <br>
                    Thickness: <?php echo esc_html($thickness_name); ?>
                </li>

            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>No products found.</p>
    <?php endif; ?>

</div>

<?php
wp_reset_postdata();
get_footer();
*/
