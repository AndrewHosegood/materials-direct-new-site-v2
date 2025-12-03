<?php if(get_field('manufacturing_latest_products_heading')){?>
<?php $colour_1 = get_field('manufacturing_latest_products_background', get_the_ID()); ?>
<section class="manufacturing-latest-products text-center" style="background: <?php echo esc_attr( $colour_1 ); ?>;">
<h2 class="manufacturing-latest-products__heading"><?php the_field('manufacturing_latest_products_heading'); ?></h2>
<?php
// Query the first 3 products under the "Electrical Insulators" category
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => 3,               // Limit output
    'orderby'        => 'date',          // Order by publish date
    'order'          => 'DESC',          // Newest first
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'name',        // or 'slug' if you prefer
            'terms'    => 'Electrical Insulators',
        ),
    ),
);

$electrical_insulators = new WP_Query($args);
?>
<div class="manufacturing-latest-products__container container">
<div class="manufacturing-latest-products__content-outer">

    <?php if ($electrical_insulators->have_posts()) : ?>
    
        <ul class="products manufacturing-latest-products__content">

            <?php while ($electrical_insulators->have_posts()) : $electrical_insulators->the_post(); ?>
                
                <li class="manufacturing-latest-products__card" <?php wc_product_class(); ?>>

                        <div class="manufacturing-latest-products__mask">
                            <div class="manufacturing-latest-products__image-links double">
                                <a class="manufacturing-latest-products__image-links-link" rel="nofollow" href="http://localhost:8888/product/kapton-200hn-a0-0-0508mm-dupont/">
                                Order<br>Custom Parts</a>
                                <a class="manufacturing-latest-products__image-links-link" target="_blank" href="http://localhost:8888/wp-content/uploads/2025/12/Kapton-HN_NEW.pdf">
                                    Product<br>Data Sheet
                                </a>
                            </div>

                            <a class="manufacturing-latest-products__link" href="<?php the_permalink(); ?>">
                                <div class="woocommerce-shop__soft-border"></div>
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail( 'woocommerce_thumbnail', array( 'class' => 'manufacturing-latest-products__image' ) ); ?>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="manufacturing-latest-products__info-card">
                        <h4 class="manufacturing-latest-products__title woocommerce-loop-product__title"><?php the_title(); ?></h4>

                        <?php 
                        $product = wc_get_product(get_the_ID());
                        if ( $product && $product->get_sku() ) : ?>
                            <span class="manufacturing-latest-products__sku">SKU: <?php echo $product->get_sku(); ?></span>
                        <?php endif; ?>

                        <?php
                        $terms = get_the_terms( get_the_ID(), 'product_cat' );

                        if ( $terms && ! is_wp_error( $terms ) ) :
                            echo '<div class="manufacturing-latest-products__i-cat">';
                            $cat_links = [];

                            foreach ( $terms as $term ) {
                                $cat_links[] = sprintf(
                                    '<a class="manufacturing-latest-products__i-cat-link" href="%s" rel="tag">%s</a>',
                                    esc_url( get_term_link( $term ) ),
                                    esc_html( $term->name )
                                );
                            }

                            echo implode(', ', $cat_links);
                            echo '</div>';
                        endif;
                        ?>
                        </div>
                        <?php
                            $short_desc = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

                            if ( ! empty( $short_desc ) ) {
                                echo '<div class="manufacturing-latest-products__short-description">' . $short_desc . '</div>';
                            }
                        ?>

                        <a class="manufacturing-latest-products__btn button" href="<?php the_permalink(); ?>">Select options</a>
                    
                </li>

            <?php endwhile; ?>

        </ul>

    <?php else : ?>

        <p>No products found in this category.</p>

    <?php endif; ?>

</div>
</div>

<?php wp_reset_postdata(); ?>

</section>
<?php } ?>