<?php
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => 4,
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN',
        ),
    ),
);

$featured_products = new WP_Query( $args );

if ( $featured_products->have_posts() ) { ?>
    <section class="featured-products text-center">
        <div class="container">
            <h2 class="featured-products__heading">Our Featured Products</h2>
            <ul class="products featured-products__content four-column-grid grid-gap-zero-point-seven">
            <?php
            while ( $featured_products->have_posts() ) {
                $featured_products->the_post();
                wc_get_template_part( 'content', 'product' );
            }
            ?>
            </ul>
        </div>
    </section>    
<?php }

wp_reset_postdata();
?>