<?php
/*
Template Name: Home
*/

get_header();
?>

<!-- Category Filter -->
<?php require_once('page-includes/category-filter.php'); ?>
<!-- Category Filter -->



<!-- Featured Icons -->
<?php require_once('page-includes/featured-icons.php'); ?>
<!-- Featured Icons -->



<!-- Technical Bulletin -->
<?php require_once('page-includes/technical-bulletin.php'); ?>
<!-- Technical Bulletin -->


<!-- Counter -->
<?php require_once('page-includes/counter.php'); ?>
<!-- Counter -->


<!-- Welcome Text -->
<section class="welcome-text">
    <div class="container welcome-text__container">
        <h2 class="welcome-text__heading"><?php the_title(); ?></h2>
        <?php the_content(); ?>
    </div>
</section>
<!-- Welcome Text -->


<!-- Testimonials -->
<section class="testimonials">
    <div class="container testimonials__container">
        <h2 class="testimonials__heading">Testimonials</h2>
        <h3 class="testimonials__subheading">A few of our very satisfied customers</h3>
        <div class="testimonials__carousel owl-carousel owl-theme">
            <?php if (have_rows('testimonials')) : ?>
                
                    <?php while (have_rows('testimonials')) : the_row(); ?>
                        <div class="item">
                            <p class="testimonials__quote"><?php the_sub_field('testimonial_quote'); ?></p>
                            <h4 class="testimonials__title"><?php the_sub_field('testimonial_title'); ?></h4>
                        </div>
                    <?php endwhile; ?>
                
            <?php endif; ?> 
        </div>
    </div>
</section>
<!-- Testimonials -->


<!-- Sectors -->
<section class="sectors">
    <div class="container sectors__container">
        <h2 class="sectors__title">Sectors</h2>
        <p class="sectors__subtitle">Each sector has its own unique requirements and specifications. Materials Direct works to ensure that each sector we supply to, receives the highest quality and best-performing products in relation to their industry and specific need.</p>
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
<!-- Sectors -->


<!-- Quality Assured -->
<section class="quality-assured text-center">
    <div class="container quality-assured__container">
        <h2 class="quality-assured__heading"><?php the_field('quality_assured_heading'); ?></h2>
        <h4 class="quality-assured__subheading"><?php the_field('quality_assured_subheading'); ?></h4>
        <?php the_field('quality_assured_content'); ?>
        <?php $quality_assured_logo = get_field('quality_assured_logo'); ?>
        <img src="<?php echo $quality_assured_logo['url']; ?>" alt="<?php echo $quality_assured_logo['alt']; ?>" class="quality-assured__logo">

        <?php 
        $link = get_field('quality_assured_link'); 

        if ($link) : 
            $link_url = $link['url'];
            $link_title = $link['title'];
            $link_target = $link['target'] ? $link['target'] : '_self';
        ?>
            <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                <?php echo esc_html($link_title); ?>
            </a>
        <?php endif; ?>

    </div>    
</section>
<!-- Quality Assured -->

<!-- Wide range of services -->
<section class="wide-range-of-services text-center">
    <h3 class="wide-range-of-services__heading">Materials Direct Offers A Wide Range of Services</h3>
    <div class="container wide-range-of-services__container three-column-grid grid-gap-three">

        <div class="wide-range-of-services__card">
            <div class="wide-range-of-services__card-icon">
                <img src="/wp-content/uploads/2025/11/005-machine.svg" alt="Rotary Die Cutting" class="wide-range-of-services__card-icon-img">
            </div>
            <div class="wide-range-of-services__card-content">
                <h4 class="wide-range-of-services__card-content-heading">Rotary Die Cutting</h4>
                <p class="wide-range-of-services__card-content-text">The ideal cutting method for large quantity orders; delivering fast and accurate conversion of materials into custom parts and shapes.</p>
            </div>
        </div>

        <div class="wide-range-of-services__card">
            <div class="wide-range-of-services__card-icon">
                <img src="/wp-content/uploads/2025/11/005-machine.svg" alt="Rotary Die Cutting" class="wide-range-of-services__card-icon-img">
            </div>
            <div class="wide-range-of-services__card-content">
                <h4 class="wide-range-of-services__card-content-heading">Rotary Die Cutting</h4>
                <p class="wide-range-of-services__card-content-text">The ideal cutting method for large quantity orders; delivering fast and accurate conversion of materials into custom parts and shapes.</p>
            </div>
        </div>

        <div class="wide-range-of-services__card">
            <div class="wide-range-of-services__card-icon">
                <img src="/wp-content/uploads/2025/11/005-machine.svg" alt="Rotary Die Cutting" class="wide-range-of-services__card-icon-img">
            </div>
            <div class="wide-range-of-services__card-content">
                <h4 class="wide-range-of-services__card-content-heading">Rotary Die Cutting</h4>
                <p class="wide-range-of-services__card-content-text">The ideal cutting method for large quantity orders; delivering fast and accurate conversion of materials into custom parts and shapes.</p>
            </div>
        </div>

        <div class="wide-range-of-services__card">
            <div class="wide-range-of-services__card-icon">
                <img src="/wp-content/uploads/2025/11/005-machine.svg" alt="Rotary Die Cutting" class="wide-range-of-services__card-icon-img">
            </div>
            <div class="wide-range-of-services__card-content">
                <h4 class="wide-range-of-services__card-content-heading">Rotary Die Cutting</h4>
                <p class="wide-range-of-services__card-content-text">The ideal cutting method for large quantity orders; delivering fast and accurate conversion of materials into custom parts and shapes.</p>
            </div>
        </div>

        <div class="wide-range-of-services__card">
            <div class="wide-range-of-services__card-icon">
                <img src="/wp-content/uploads/2025/11/005-machine.svg" alt="Rotary Die Cutting" class="wide-range-of-services__card-icon-img">
            </div>
            <div class="wide-range-of-services__card-content">
                <h4 class="wide-range-of-services__card-content-heading">Rotary Die Cutting</h4>
                <p class="wide-range-of-services__card-content-text">The ideal cutting method for large quantity orders; delivering fast and accurate conversion of materials into custom parts and shapes.</p>
            </div>
        </div>

        <div class="wide-range-of-services__card">
            <div class="wide-range-of-services__card-icon">
                <img src="/wp-content/uploads/2025/11/005-machine.svg" alt="Rotary Die Cutting" class="wide-range-of-services__card-icon-img">
            </div>
            <div class="wide-range-of-services__card-content">
                <h4 class="wide-range-of-services__card-content-heading">Rotary Die Cutting</h4>
                <p class="wide-range-of-services__card-content-text">The ideal cutting method for large quantity orders; delivering fast and accurate conversion of materials into custom parts and shapes.</p>
            </div>
        </div>

    </div>
</section>    
<!-- Wide range of services -->



<!-- Our Featured Products -->
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
<!-- Our Featured Products -->

<!-- Request A Material -->
<section class="request-material">
    <div class="container request-material__container">
        <h2 class="request-material__heading">Request a Material</h2>
       <p class="request-material__content">If you appreciate our ordering process but don’t see the material you need in our current inventory, please let us know the specific material you’re looking for and we will add it to our system for you to order.</p>
       <a class="button" href="/request-materials/">Request a Material</a>
    </div>
</section>
<!-- Request A Material -->

<!-- 4 Step Process -->
<section class="four-step-process text-center">
    <!-- <h3 class="four-step-process__heading">Simple <em class="four-step-process__heading-italic"><strong>4 step</strong></em> process<br>to getting your parts.</h3> -->
    <h3 class="four-step-process__heading"><?php the_field('four_step_process_heading'); ?></h3>

    <div class="container four-step-process__container four-column-grid grid-gap-zero-point-seven">
        <?php $four_step_process_img_1 = get_field('four_step_process_image_1'); ?>
        <?php $four_step_process_img_2 = get_field('four_step_process_image_2'); ?>
        <?php $four_step_process_img_3 = get_field('four_step_process_image_3'); ?>
        <?php $four_step_process_img_4 = get_field('four_step_process_image_4'); ?>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_1['url']; ?>" alt="<?php echo $four_step_process_img_1['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_1'); ?></p>
        </div>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_2['url']; ?>" alt="<?php echo $four_step_process_img_2['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_2'); ?></p>
        </div>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_3['url']; ?>" alt="<?php echo $four_step_process_img_3['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_3'); ?></p>
        </div>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_4['url']; ?>" alt="<?php echo $four_step_process_img_4['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_4'); ?></p>
        </div>
    </div>
</section>
<!-- 4 Step Process -->

<!-- Materials Direct Promo Video -->
 <section class="promo-video">
    <p class="promo-video__text">View our video for an easy breakdown of what we can offer you.</p>
    <div class="promo-video__container">
        <span class="promo-video__blue-panel"></span>
        <video width="755" height="428" class="promo-video__video" controls poster="http://localhost:8888/wp-content/uploads/2025/11/video-thumb.jpg">
        <source src="http://localhost:8888/wp-content/uploads/2025/11/Materials-Direct-Promo.m4v" type="video/mp4">
        Your browser does not support the video tag.
        </video>
    </div>
</section>
<!-- Materials Direct Promo Video -->

<!-- Our Partners -->
    <?php
    // Query all posts from the custom post type "partner_brands"
    $args = array(
        'post_type'      => 'partner_brands',
        'posts_per_page' => -1,  // show all
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $brands = new WP_Query($args);

    if ($brands->have_posts()) : ?>
        <section class="our-partners text-center">
            <h2 class="our-partners__heading">Our Partners</h2>
                <div class="container">
                    <div class="our-partners__carousel owl-carousel owl-theme">
                        <?php while ($brands->have_posts()) : $brands->the_post(); ?>
                            <div class="item our-partners__item" style="text-align: center;">

                                <a href="<?php the_permalink(); ?>" style="display: block;">
                                    <?php if (has_post_thumbnail()){ ?>
                                        <?php the_post_thumbnail('medium', ['style' => 'max-width: 100%; height: auto;']); ?>
                                    <?php } ?>
                                </a>

                            </div>
                        <?php endwhile; ?>
                </div>        
            </div>    
            <a class="button" href="/partner_brands/">Shop By Brand</a>       
        </section>
    <?php else : ?>
        <p>No partner brands found.</p>
    <?php endif;
    wp_reset_postdata();
    ?>
<!-- Our Partners -->

<!-- Any Questions -->
<section class="any-questions text-center">
    <div class="container">
        <h2 class="any-questions__heading">Any Questions?</h2>
        <p class="any-questions__contact-details">
            <a class="any-questions__link" href="tel:+441908222211">+44 (0)1908 222 211</a> | <a class="any-questions__link" href="mailto:info@materials-direct.com">info@materials-direct.com</a>
            <?php echo do_shortcode('[contact-form-7 id="f3692bd" title="Contact Form Home Page"]'); ?>
        </p>
    </div>
</section>

<!-- Any Questions -->




<?php
get_sidebar();
get_footer();
