<?php
/*
Template Name: Credit Account Application
*/

get_header();
?>

<!-- Banner -->
<section class="banner credit-account-banner owl-carousel owl-theme">
	<?php if (have_rows('banner')) : ?>
		<?php while (have_rows('banner')) : the_row(); ?>
		<?php $banner_image = get_sub_field('banner_image'); ?>
		<?php $banner_button = get_sub_field('banner_button_link'); ?>
			<div class="item credit-account-banner__item" style="background-image:url('<?php echo $banner_image['url']; ?>');">
				<div class="banner__content credit-account-banner__content">
					<?php 
					if(get_sub_field('banner_height')){
						$banner_height = get_sub_field('banner_height');
					} else {
						$banner_height = "440";
					}
					?>
					<h1 class="banner__heading credit-account-banner__heading"><?php the_sub_field('banner_heading'); ?></h1>
                    <?php if(get_sub_field('banner_subheading')){ ?>
                        <h3 class="banner__subheading"><?php the_sub_field('banner_subheading'); ?></h3>
                    <?php } ?>
                    <?php if($banner_button){ ?>
                        <a class="button banner__btn" href="<?php echo esc_url($banner_button['url']); ?>"><?php echo esc_html($banner_button['title']); ?></a>
                    <?php } ?>
				</div>
			</div>
		<?php endwhile; ?>
    <?php endif; ?>  
</section>
<!-- Banner -->


<!-- Content -->
<section class="credit-account-application">

        <div class="credit-account-application__form">
            <div class="container credit-account-application__container">
                <?php $form_shortcode = get_field('credit_account_application_form'); ?>
                <?php $form_display = do_shortcode($form_shortcode) ?>
                <?php echo $form_display; ?>
            </div> 
        </div>



        <div class="credit-account-application__4-column-company-info">
            <div class="credit-account-application__container"> 

                <!-- 4 column banner -->
                <?php if (have_rows('selling_points', 'options')) : ?>
                    <div class="featured-icons">
                        <div class="container featured-icons__container">

                            <?php while (have_rows('selling_points', 'options')) : the_row(); ?>
                                <div class="featured-icons__column">
                                    <div class="featured-icons__icon-left">
                                        <?php $selling_point_icon = get_sub_field('selling_points_icon', 'options') ?>
                                        <img class="featured-icons__icon" alt="<?php echo $selling_point_icon['alt']; ?>" src="<?php echo $selling_point_icon['url']; ?>">
                                    </div>
                                    <div class="featured-icons__content-right">
                                        <h6 class="featured-icons__heading"><?php the_sub_field('selling_points_heading', 'options'); ?></h6>
                                        <p class="featured-icons__content"><?php the_sub_field('selling_points_content', 'options'); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>

                        </div>
                    </div>
                <?php endif; ?> 
                <!-- end 4 column banner -->


                <!-- About Materials Direct -->
                <div class="credit-account-application__welcome-text">
                    <div class="container credit-account-application__welcome-text-container">
                        <h2 class="credit-account-application__welcome-text-heading"><?php the_field('about_materials_direct_heading'); ?></h2>
                        <h4 class="credit-account-application__welcome-text-subheading"><?php the_field('about_materials_direct_subheading'); ?></h4>
                        <h4 class="credit-account-application__welcome-text-subheading-2"><?php the_field('about_materials_direct_subheading_2'); ?></h4>
                        <span class="credit-account-application__welcome-text-content"><?php the_field('about_materials_direct_content'); ?></span>
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

        <?php
$args = array(
    'post_type'      => 'manufacturing',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
);

$manufacturing_query = new WP_Query($args);

if ( $manufacturing_query->have_posts() ) : ?>
    
    <div class="credit-account-application__three-column text-center">
        <h2 class="credit-account-application__three-column-title">Manufacturing Services</h2>
        <p class="credit-account-application__three-column-content">With some of the most trusted and well known brands available, we can provide you materials in the following categories</p>

        <!-- <div class="manufacturing-services__carousel three-column-grid grid-gap-four-point-five container credit-account-appication-three-column__container"> -->
        <div class="manufacturing-services__carousel owl-carousel owl-theme container credit-account-application__three-column-container">
        <?php while ( $manufacturing_query->have_posts() ) : $manufacturing_query->the_post(); ?>
            
            
                <div class="item sectors-three-column__content">
                    <!-- Thumbnail wrapped in link -->
                    <?php $manufacturing_services_custom_featured_image = get_field('manufacturing_services_img'); ?>
                    <a href="<?php the_permalink(); ?>" class="credit-account-application__three-column_thumb">
                        <img src="<?php echo $manufacturing_services_custom_featured_image['url']; ?>" alt="<?php echo $manufacturing_services_custom_featured_image['alt']; ?>" class="credit-account-application__three-column-image">
                    </a>

                    <!-- Title -->
                    <h4 class="credit-account-application__three-column-heading">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h4>

                    <!-- Excerpt -->
                    <div class="credit-account-application__three-column-excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <!-- VIEW MORE link -->
                    <a class="button credit-account-application__three-column-btn" href="<?php the_permalink(); ?>">VIEW MORE</a>
                </div>
            
        
        <?php endwhile; ?>
        </div>

    </div>

<?php 
endif;

wp_reset_postdata();
?>










</div>
</section>
<!-- Content -->




<?php

get_footer();
