<?php
/*
Template Name: Credit Account Application
*/

get_header();
?>

<!-- Banner -->
<?php require_once('page-includes/sector/sector-banner.php'); ?>
<!-- Banner -->

<!-- Content -->
<section class="credit-account-application">

        <div class="credit-account-application__form">
            <div class="container credit-account-application__container">
                <?php echo do_shortcode('[contact-form-7 id="18b47b5" title="Credit Account Application"]'); ?>
            </div> 
        </div>

        <div class="credit-account-application__4-column-company-info">
            <div class="container credit-account-application__container"> 
                <!-- 4 column banner -->
                <div class="featured-icons">
                    <div class="container featured-icons__container">
                        <div class="featured-icons__column">
                            <div class="featured-icons__icon-left">
                                <img class="featured-icons__icon" alt="No Tooling Charge" src="/wp-content/uploads/2025/11/tooling-cost.svg">
                            </div>
                            <div class="featured-icons__content-right">
                                <h6 class="featured-icons__heading">No Tooling Charge</h6>
                                <p class="featured-icons__content">There is NEVER a tooling charge for manufacturing</p>
                            </div>
                        </div>
                        <div class="featured-icons__column">
                            <div class="featured-icons__icon-left">
                                <img class="featured-icons__icon" alt="" src="/wp-content/uploads/2025/11/fast-manufacture.svg">
                            </div>
                            <div class="featured-icons__content-right">
                                <h6 class="featured-icons__heading">Fast Manufacturing</h6>
                                <p class="featured-icons__content">Parts made &amp; shipped worldwide in as little as 24 hours</p>
                            </div>
                        </div>
                        <div class="featured-icons__column">
                            <div class="featured-icons__icon-left">
                                <img class="featured-icons__icon" alt="" src="/wp-content/uploads/2025/11/support.svg">
                            </div>
                            <div class="featured-icons__content-right">
                                <h6 class="featured-icons__heading">Technical Support</h6>
                                <p class="featured-icons__content">Technical expertise available from our specialists</p>
                            </div>
                        </div>
                        <div class="featured-icons__column">
                            <div class="featured-icons__icon-left">
                                <img class="featured-icons__icon" alt="" src="/wp-content/uploads/2025/11/secure.svg">
                            </div>
                            <div class="featured-icons__content-right">
                                <h6 class="featured-icons__heading">100% Payment Secure</h6>
                                <p class="featured-icons__content">We ensure secure payment with a wide array of options</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end 4 column banner -->

                <!-- About Materials Direct -->
                <div class="welcome-text">
                    <div class="container welcome-text__container">
                        <h2 class="welcome-text__heading">Materials Direct</h2>
                        <h4>About the Company</h4>
                        <h4>Precision cut technical materials fast</h4>
                        <p>Materials Direct has an extensive offering of technical materials from trusted manufacturers to service the electronics and LED lighting industries.</p>
                        <p>We are constantly increasing our manufacturers and product portfolio and we would love to hear from you about a product we don’t currently stock, <a href="/contact-us/">click here to get in touch</a></p>
                    </div>
                </div>
                <!-- About Materials Direct -->
            </div>
        </div>

        <!-- Testimonials -->
        <?php require_once('page-includes/home-testimonials.php'); ?>
        <!-- Testimonials -->

        <div class="credit-account-application__partner-logos">
            <div class="container credit-account-application__partner-logos-container">
                    <!-- Our Partners -->
                    <?php require_once('page-includes/home-our-partners.php'); ?> 
                    <!-- Our Partners -->
            </div>
        </div>

        <!-- <section class="credit-account-appication-three-column">
            <h2 class="credit-account-appication-three-column__title">Manufacturing Services</h2>
            <p class="credit-account-appication-three-column__content">With some of the most trusted and well known brands available, we can provide you materials in the following categories</p>
			<div class="three-column-grid grid-gap-three-point-five container">

				
				<div class="sectors-three-column__content">
                    <img src="" alt="" class="sectors-three-column__img">
					<h3 class="sectors-two-column__heading credit-account-appication-three-column__heading">Coating & Laminating</h2>
					<p class="credit-account-appication-three-column__subheading">You may just need to apply an adhesive coating your substrate or you may wish to bond a high voltage barrier layer to your thermal pad.</p>
                    <a href="#" class="button sectors-two-column__btn">VIEW NOW</a>
				</div>
                <div class="sectors-three-column__content">
                    <img src="" alt="" class="sectors-three-column__img">
					<h3 class="sectors-two-column__heading credit-account-appication-three-column__heading">Coating & Laminating</h2>
					<p class="credit-account-appication-three-column__subheading">You may just need to apply an adhesive coating your substrate or you may wish to bond a high voltage barrier layer to your thermal pad.</p>
                    <a href="#" class="button sectors-two-column__btn">VIEW NOW</a>
				</div>
                <div class="sectors-three-column__content">
                    <img src="" alt="" class="sectors-three-column__img">
					<h3 class="sectors-two-column__heading credit-account-appication-three-column__heading">Coating & Laminating</h2>
					<p class="credit-account-appication-three-column__subheading">You may just need to apply an adhesive coating your substrate or you may wish to bond a high voltage barrier layer to your thermal pad.</p>
                    <a href="#" class="button sectors-two-column__btn">VIEW NOW</a>
				</div>




			</div>
		</section> -->


<div class="container credit-account-application__container">
        <?php
// Query Manufacturing Services CPT
$manufacturing_query = new WP_Query( array(
    'post_type'      => 'manufacturing',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
) );

if ( $manufacturing_query->have_posts() ) : ?>
    
    <div class="credit-account-appication-three-column text-center">
        <h2 class="credit-account-appication-three-column__title">Manufacturing Services</h2>
        <p class="credit-account-appication-three-column__content">With some of the most trusted and well known brands available, we can provide you materials in the following categories</p>

        <div class="three-column-grid grid-gap-three-point-five container credit-account-appication-three-column__container">
        <?php while ( $manufacturing_query->have_posts() ) : $manufacturing_query->the_post(); ?>
            
            
                <div class="sectors-three-column__content">
                    <!-- Thumbnail wrapped in link -->
                    <a href="<?php the_permalink(); ?>" class="manufacturing-thumb">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'medium', array( 'class' => 'sectors-three-column__img' ) );
                        }
                        ?>
                    </a>

                    <!-- Title -->
                    <h2 class="sectors-three-column__title">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>

                    <!-- Excerpt -->
                    <div class="sectors-three-column__excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <!-- VIEW MORE link -->
                    <a class="manufacturing-view-more" href="<?php the_permalink(); ?>">
                        VIEW MORE
                    </a>
                </div>
            
        
        <?php endwhile; ?>
        </div>

    </div>

<?php 
endif;

// Reset post data
wp_reset_postdata();
?>




</div>
</section>
<!-- Content -->




<?php

get_footer();
