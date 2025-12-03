<?php
/**
 * The template for displaying all manufacturing custom post type posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Materials_Direct
 */

get_header();
?>








<?php
/**
 * Template Part: Manufacturing Pages Flexible Content
 */
?>






<?php if ( have_rows('manufacturing_pages') ) { ?>

    <?php while ( have_rows('manufacturing_pages') ) { the_row(); ?>

        <?php if ( get_row_layout() == 'banner_fc' ) { ?>
            
            <section class="banner owl-carousel owl-theme">

                <?php if ( have_rows('banner_card') ) { ?>

                    <?php while ( have_rows('banner_card') ) { the_row(); ?>

                        <?php 
                        // Get fields from inside the repeater
                        $banner_image      = get_sub_field('banner_fc_image');
                        $banner_heading    = get_sub_field('banner_fc_heading');
                        $banner_subheading = get_sub_field('banner_fc_subheading');
                        $banner_button     = get_sub_field('banner_fc_button_link');
                        ?>

                        <div class="item" 
                            <?php if ($banner_image) { ?>
                                style="background-image:url('<?php echo esc_url($banner_image['url']); ?>');"
                            <?php } ?>
                        >
                            <div class="banner__content">

                                <?php if ($banner_heading) { ?>
                                    <h1 class="banner__heading"><?php echo esc_html($banner_heading); ?></h1>
                                <?php } ?>

                                <?php if ($banner_subheading) { ?>
                                    <h3 class="banner__subheading"><?php echo esc_html($banner_subheading); ?></h3>
                                <?php } ?>

                                <?php if ($banner_button) { ?>
                                    <a class="button banner__btn" 
                                       href="<?php echo esc_url($banner_button['url']); ?>">
                                        <?php echo esc_html($banner_button['title']); ?>
                                    </a>
                                <?php } ?>

                            </div>
                        </div>

                    <?php } ?>

                <?php } ?>  

            </section>

        <?php } elseif(get_row_layout() == 'two_column_layout'){ ?>

            <section class="sectors-two-column">
                <div class="two-column-grid grid-gap-three-point-five container <?php if(get_sub_field('layout_alignment_fc') === "Right"){ echo "reverse"; } ?>">
                    <?php $two_column_layout_image = get_sub_field('two_column_layout_fc_image'); ?>
                    <img src="<?php echo $two_column_layout_image['url']; ?>" alt="<?php echo $two_column_layout_image['alt']; ?>" class="sectors-two-column__img manufacturing-two-column__img">
                    <div class="sectors-two-column__content">
                        <h2 class="sectors-two-column__heading manufacturing-two-column__heading"><?php the_sub_field('two_column_layout_fc_heading'); ?></h2>
                        <?php the_sub_field('two_column_layout_fc_content'); ?>


                        <?php 
                        $link_1 = get_sub_field('two_column_layout_fc_button_link');
                        if ($link_1) {
                            $url_1 = $link_1['url'];
                            $title_1 = $link_1['title'];
                        ?>
                        <a href="<?php echo esc_url( $url_1 ); ?>" class="button sectors-two-column__btn"><?php echo esc_html( $title_1 ); ?></a>

                        <?php } ?>


                    </div>
                </div>
            </section>

        <?php } elseif(get_row_layout() == 'one_column_layout') {
            $colour_1 = get_sub_field('one_column_layout_fc_background_colour', get_the_ID());
            ?>    
            <section class="manufacturing-single-column text-center" style="background: <?php echo esc_attr( $colour_1 ); ?>;">
                <div class="container manufacturing-single-column__container">
                    <h2 class="manufacturing-single-column__heading"><?php the_sub_field('one_column_layout_fc_heading'); ?></h2>
                    <div class="manufacturing-single-column__content">
                        <?php the_sub_field('one_column_layout_fc_content'); ?>
                    </div>
                    <?php $link_2 = get_sub_field('one_column_layout_fc_button_link'); ?>
                    <?php
                    if ($link_2) {
                        $url_2 = $link_2['url'];
                        $title_2 = $link_2['title'];
                    } 
                    ?>
                    <?php if(get_sub_field('one_column_layout_fc_button_style')=== 'solid button'){ ?>
                        <a class="manufacturing-single-column__button-2" href="<?php echo esc_url( $url_2 ); ?>"><?php echo esc_html( $title_2 ); ?></a>
                    <?php } else { ?>
                        <a class="manufacturing-single-column__button-1" href="<?php echo esc_url( $url_2 ); ?>"><?php echo esc_html( $title_2 ); ?></a>
                    <?php } ?>
                    
                </div>
                </section>

            <?php
            } elseif(get_row_layout() == 'our_latest_products') { ?>


                <?php if(get_sub_field('our_latest_products_fc_heading')){?>
                <?php $colour_1 = get_sub_field('our_latest_products_fc_background_colour', get_the_ID()); ?>
                <section class="manufacturing-latest-products text-center" style="background: <?php echo esc_attr( $colour_1 ); ?>;">
                <h2 class="manufacturing-latest-products__heading"><?php the_sub_field('our_latest_products_fc_heading'); ?></h2>
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

                <?php

            } elseif(get_row_layout() == 'custom_cut_materials'){
                ?>

                <?php $link_3a = get_sub_field('custom_cut_materials_fc_button_1'); ?>
                <?php $link_3b = get_sub_field('custom_cut_materials_fc_button_2'); ?>

                <section class="manufacturing-single-column text-center">
                    <div class="container manufacturing-single-column__container">
                        <h2 class="manufacturing-single-column__heading"><?php the_sub_field('custom_cut_materials_fc_heading'); ?></h2>
                        <div class="manufacturing-single-column__content">
                            <?php the_sub_field('custom_cut_materials_fc_content'); ?>
                        </div>

                        <?php 
                        if ($link_3a) {
                            $url_3a = $link_3a['url'];
                            $title_3a = $link_3a['title'];
                        ?>
                            <a class="manufacturing-single-column__button-2" href="<?php echo esc_url( $url_3a ); ?>"><?php echo esc_html( $title_3a ); ?></a>
                        <?php } ?>

                        <?php 
                        if ($link_3b) {
                            $url_3b = $link_3b['url'];
                            $title_3b = $link_3b['title'];
                        ?>
                            <a class="manufacturing-single-column__button-1" href="<?php echo esc_url( $url_3b ); ?>"><?php echo esc_html( $title_3b ); ?></a>
                        <?php } ?>
                        

                    </div>
                </section>

                <?php if(get_field('custom_cut_materials_heading')){ ?>
                    <section class="featured-icons">
                        <div class="container featured-icons__container">
                            <div class="featured-icons__column">
                                <div class="featured-icons__icon-left">
                                    <img class="featured-icons__icon" alt="No Tooling Charge" src="http://localhost:8888/wp-content/uploads/2025/11/tooling-cost.svg">
                                </div>
                                <div class="featured-icons__content-right">
                                    <h6 class="featured-icons__heading">No Tooling Charge</h6>
                                    <p class="featured-icons__content">There is NEVER a tooling charge for manufacturing</p>
                                </div>
                            </div>
                            <div class="featured-icons__column">
                                <div class="featured-icons__icon-left">
                                    <img class="featured-icons__icon" alt="" src="http://localhost:8888/wp-content/uploads/2025/11/fast-manufacture.svg">
                                </div>
                                <div class="featured-icons__content-right">
                                    <h6 class="featured-icons__heading">Fast Manufacturing</h6>
                                    <p class="featured-icons__content">Parts made &amp; shipped worldwide in as little as 24 hours</p>
                                </div>
                            </div>
                            <div class="featured-icons__column">
                                <div class="featured-icons__icon-left">
                                    <img class="featured-icons__icon" alt="" src="http://localhost:8888/wp-content/uploads/2025/11/support.svg">
                                </div>
                                <div class="featured-icons__content-right">
                                    <h6 class="featured-icons__heading">Technical Support</h6>
                                    <p class="featured-icons__content">Technical expertise available from our specialists</p>
                                </div>
                            </div>
                            <div class="featured-icons__column">
                                <div class="featured-icons__icon-left">
                                    <img class="featured-icons__icon" alt="" src="http://localhost:8888/wp-content/uploads/2025/11/secure.svg">
                                </div>
                                <div class="featured-icons__content-right">
                                    <h6 class="featured-icons__heading">100% Payment Secure</h6>
                                    <p class="featured-icons__content">We ensure secure payment with a wide array of options</p>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php } ?>

                <section class="manufacturing-instant-quotes text-center">
                    <h3 class="manufacturing-instant-quotes__heading"><?php the_sub_field('custom_cut_materials_fc_heading_2'); ?></h3>
                    <div class="container">
                        <div class="manufacturing-instant-quotes__content">
                            <span class="manufacturing-instant-quotes__content-left"><?php the_sub_field('custom_cut_materials_fc_content_left'); ?></span>
                            <span class="manufacturing-instant-quotes__content-right"><?php the_sub_field('custom_cut_materials_fc_content_right'); ?></span>
                        </div>
                    </div>




                <?php if(get_field('custom_cut_materials_heading')){ ?>

                    <div class="container manufacturing-instant-quotes__container four-column-grid grid-gap-zero-point-seven">

                    <?php if (have_rows('custom_cut_fc_cards')) : ?>
                        <?php $count = 0; ?>
                        <?php while (have_rows('custom_cut_fc_cards')) : the_row(); ?>
                            <?php $count ++ ?>
                                <div class="manufacturing-instant-quotes__card">
                                    <span class="manufacturing-instant-quotes__number"><?php echo $count; ?></span>
                                    <div class="manufacturing-instant-quotes__img-wrap">
                                        <?php $card_image = get_sub_field('custom_cut_cards_fc_icon'); ?>
                                        <img class="manufacturing-instant-quotes__img" alt="<?php echo $card_image['alt']; ?>" src="<?php echo $card_image['url']; ?>">
                                    </div>
                                    <div class="manufacturing-instant-quotes__card-text"><?php the_sub_field('custom_cut_cards_fc_text'); ?></div>
                                </div>
                            
                        <?php endwhile; ?>
                    <?php endif; ?> 

                    </div>

                <?php } ?>

                </section>


                <?php
            } elseif(get_row_layout() == 'dark_three_column_panel'){ ?>



                <section class="wide-range-of-services text-center">
                <h3 class="wide-range-of-services__heading-large">Custom Cutting To Meet Your Specification</h3>    
                <h3 class="wide-range-of-services__heading">Materials Direct Offers A Wide Range of Services</h3>
                <div class="container wide-range-of-services__container three-column-grid grid-gap-three">
                <?php
                // Arguments for custom post type query
                $args = array(
                    'post_type'      => 'manufacturing',
                    'posts_per_page' => -1, // show all
                    'post_status'    => 'publish',
                    'order'          => 'ASC'
                );

                // Start custom query
                $manufacturing_query = new WP_Query($args);

                if ( $manufacturing_query->have_posts() ) : ?>

                    <!-- <div class="container wide-range-of-services__container three-column-grid grid-gap-three"> -->

                        <?php while ( $manufacturing_query->have_posts() ) : $manufacturing_query->the_post(); ?>

                            <?php if ( has_post_thumbnail() ) : ?>
                            <a class="wide-range-of-services__link" href="<?php the_permalink(); ?>">
                            <div class="wide-range-of-services__card">

                                <!-- Featured Image -->
                                
                                    <div class="wide-range-of-services__card-icon">
                                        
                                            <?php 
                                            the_post_thumbnail(
                                                'medium',
                                                array(
                                                    'class' => 'wide-range-of-services__card-icon-img'
                                                )
                                            );
                                            ?>
                                        
                                    </div>
                                

                                <!-- Content / Excerpt -->
                                <div class="wide-range-of-services__card-content">
                                    <h4 class="wide-range-of-services__card-content-heading">
                                        <?php 
                                        if(get_the_title() === "Punch Press Die Cutting"){
                                            echo "Die Cutting";
                                        } else {
                                            the_title(); 
                                        }
                                        ?>
                                    </h4>
                                    <?php the_excerpt(); ?>
                                </div>

                

                            </div>
                            </a>
                            <?php endif; ?>

                        <?php endwhile; ?>

                    <!-- </div> -->

                <?php
                endif;

                // Reset post data
                wp_reset_postdata();
                ?>



                </div>
                </section>  

                
            <?php } elseif(get_row_layout() == 'quality_assured'){ ?>

                <?php $link_4 = get_sub_field('quality_assured_fc_link'); ?>


                <section class="testimonials">
                    <div class="container testimonials__container">
                        <h2 class="testimonials__heading"><?php the_sub_field('quality_assured_fc_heading'); ?></h2>
                        <div class="manufacturing-testimonials__content">
                            <?php the_sub_field('quality_assured_fc_content'); ?>
                        </div>
                        <div class="testimonials__carousel owl-carousel owl-theme">
                            <?php if (have_rows('testimonials')) : ?>
                                
                                    <?php while (have_rows('testimonials')) : the_row(); ?>
                                        <div class="item manufacturing-testimonials__border">
                                            <p class="testimonials__quote"><?php the_sub_field('testimonial_quote'); ?></p>
                                            <h4 class="testimonials__title"><?php the_sub_field('testimonial_title'); ?></h4>
                                        </div>
                                    <?php endwhile; ?>
                                
                            <?php endif; ?> 
                        </div>
                    </div>
                    <?php 
                        if ($link_4) {
                            $url_4 = $link_4['url'];
                            $title_4 = $link_4['title'];
                        ?>
                            <a class="button manufacturing-testimonials__btn" href="<?php echo esc_url( $url_4 ); ?>"><?php echo esc_html( $title_4 ); ?></a>
                    <?php } ?>

                </section>

            <?php } elseif(get_row_layout() == 'our_partners'){ ?>

                <?php require_once('page-includes/home-our-partners.php'); ?> 
                    
            <?php } elseif(get_row_layout() == 'any_questions'){ ?>

                    <?php require_once('page-includes/home-any-questions.php'); ?> 

            <?php } elseif(get_row_layout() == 'sectors'){ ?>

                    <section class="sectors">
                        <div class="container sectors__container">
                            <h2 class="sectors__title"><?php the_sub_field('sectors_fc_heading'); ?></h2>
                            <p class="sectors__subtitle"><?php the_sub_field('sectors_fc_subheading'); ?></p>
                            <div class="sectors__content three-column-grid grid-gap-three">

                            <?php
                            // Query all 'sectors' posts
                            $args = array(
                                'post_type'      => 'sector',
                                'posts_per_page' => -1, // get all
                                'orderby'        => 'title',
                                'order'          => 'ASC'
                            );

                            $sectors_query = new WP_Query($args);

                            // Loop through the sectors
                            if ($sectors_query->have_posts()) :
                                while ($sectors_query->have_posts()) : $sectors_query->the_post(); ?>
                                    <?php $sector_featured_image = get_field('sector_featured_image'); ?>
                                    <div class="sectors__card">
                                        <img src="<?php echo $sector_featured_image['url']; ?>" alt="Aerospace" class="sectors__card-img">
                                        <div class="sectors__card-content">
                                            <h4 class="sectors__heading"><?php the_title(); ?></h4>
                                            <p class="sectors__content-text"><?php the_field('sector_card_content'); ?></p>
                                            <a class="button sectors__btn" href="/sector/aerospace/" title="Learn More"><?php the_field('sector_card_button'); ?></a>
                                        </div>
                                    </div>
                                <?php endwhile;
                            else :
                                echo '<p>No sectors found.</p>';
                            endif;

                            // Restore global post data
                            wp_reset_postdata();
                            ?>

                            </div>
                        </div>
                    </section>

            <?php }// end eleseif
            ?>

    <?php } // end while ?>

<?php } // end if ?>











<?php

get_footer();
