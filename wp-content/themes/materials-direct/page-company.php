<?php
/*
Template Name: Company
*/

get_header();
?>

<!-- Banner -->
<section class="banner company-banner owl-carousel owl-theme">
	<?php if (have_rows('banner')) : ?>
		<?php while (have_rows('banner')) : the_row(); ?>
		<?php $banner_image = get_sub_field('banner_image'); ?>
		<?php $banner_button = get_sub_field('banner_button_link'); ?>
			<div class="item company-banner__item" style="background-image:url('<?php echo $banner_image['url']; ?>');">
				<div class="banner__content company-banner__content">
					<?php 
					if(get_sub_field('banner_height')){
						$banner_height = get_sub_field('banner_height');
					} else {
						$banner_height = "440";
					}
					?>
					<h1 class="banner__heading company-banner__heading"><?php the_sub_field('banner_heading'); ?></h1>
                    <?php if(get_sub_field('banner_subheading')){ ?>
                        <h3 class="banner__subheading company-banner__subheading"><?php the_sub_field('banner_subheading'); ?></h3>
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

        

<section class="company-precision-cut">
<h2 class="company-precision-cut__heading"><?php the_field('precision_cut_materials_heading'); ?></h2>
<h3 class="company-precision-cut__subheading"><?php the_field('precision_cut_materials_subheading'); ?></h3>
<div class="company-precision-cut__container container">
        <span class="company-precision-cut__left"><?php the_field('precision_cut_materials_content_left'); ?></span>
        <div class="company-precision-cut__right">
                <?php the_field('precision_cut_materials_content_right_new'); ?>
                <?php
                    $link_1 = get_field('precision_cut_materials_link'); 
                    if($link_1){ 
                        $link_1_url = $link_1['url'];
                        $link_1_title = $link_1['title'];
                        $link_1_target = $link_1['target'] ? $link_1['target'] : '_self';
                        ?>
                        <a target="<?php echo esc_attr($link_1_target); ?>" class="btn company-precision-cut__btn" href="<?php echo esc_url($link_1_url); ?>"><?php echo esc_html($link_1_title); ?></a>
                    <?php } ?>
            
        </div>
</div>
</section>



<section class="company-history">
<h2 class="company-history__heading"><?php the_field('our_history_heading'); ?></h2>
<div class="company-history__container container">
        <div class="company-history__left"><?php the_field('our_history_conetnt_left'); ?></div>
        <div class="company-history__right">
                <?php the_field('our_history_conetnt_right'); ?>
        </div>
</div>
</section>





<section class="company-our-mission">
<h2 class="company-our-mission__heading"><?php the_field('our_mission_heading'); ?></h2>
<div class="company-our-mission__container container">

    <?php if (have_rows('our_mission_cards')) : ?>
            <?php while (have_rows('our_mission_cards')) : the_row(); ?>
                <div class="company-our-mission__card">
                    <?php $mission_icon = get_sub_field('our_mission_icon'); ?>
                    <?php if(!empty(get_sub_field('our_mission_icon'))){ ?>
                        <img src="<?php echo $mission_icon['url']; ?>" alt="<?php echo $mission_icon['alt']; ?>" class="company-our-mission__icon">
                    <?php } else { ?>
                        <img src="/wp-content/uploads/woocommerce-placeholder-100x100.webp" alt="<?php echo $mission_icon['alt']; ?>" class="company-our-mission__icon">
                    <?php } ?>
                    
                    <p class="company-our-mission__content"><?php the_sub_field('our_mission_text'); ?></p>
                </div>
            <?php endwhile; ?>
    <?php endif; ?> 
        
</div>
</section>


<section class="company-a-division-of">
<div class="company-a-division-of__container container">
        <div class="company-a-division-of__card">
            <?php $division_icon_1 = get_field('a_division_of_icon_1'); ?>
            <?php $division_icon_2 = get_field('a_division_of_icon_2'); ?>
            <?php $division_icon_3 = get_field('a_division_of_icon_3'); ?>
            <?php $division_icon_4 = get_field('a_division_of_icon_4'); ?>
            <img src="<?php echo $division_icon_1['url']; ?>" alt="<?php echo $division_icon_1['alt']; ?>" class="company-a-division-of__icon">
            <img src="<?php echo $division_icon_2['url']; ?>" alt="<?php echo $division_icon_2['alt']; ?>" class="company-a-division-of__icon">
            <img src="<?php echo $division_icon_3['url']; ?>" alt="<?php echo $division_icon_3['alt']; ?>" class="company-a-division-of__icon">
            <img src="<?php echo $division_icon_4['url']; ?>" alt="<?php echo $division_icon_4['alt']; ?>" class="company-a-division-of__icon">
        </div>
        <div class="company-a-division-of__card">
            <h3 class="company-a-division-of__heading"><?php the_field('a_division_of_title'); ?></h3>
            <span class="company-a-division-of__content">
                <?php the_field('a_division_of_content'); ?>
                <?php $link_2 = get_field('a_division_of_button'); ?>
                <?php
                if($link_2){ 
                    $link_2_url = $link_2['url'];
                    $link_2_title = $link_2['title'];
                    $link_2_target = $link_2['target'] ? $link_2['target'] : '_self';
                ?>        
                <a target="<?php echo esc_attr($link_2_target); ?>" class="btn company-a-division-of__btn" href="<?php echo esc_url($link_2_url); ?>"><?php echo esc_html($link_2_title); ?></a>
                <?php } ?>
        </div>
</div>
</section>

<section class="company-delivering">
    <div class="container company-delivering__container text-center">
            <h3 class="company-delivering__heading"><?php the_field('custom_parts_heading'); ?></h3>
            <span class="company-delivering__content">
                    <?php the_field('custom_parts_content'); ?>
            </span>
    </div>
</section>




<?php $we_care_background_image = get_field('we_care_background_image'); ?>
<section class="company-we-care" style="
<?php 
if(!empty($we_care_background_image)){ 
    echo "background-image: url( ".$we_care_background_image['url']."; )";
    } ?>
">
    <div class="container company-we-care__container text-center">
            <h3 class="company-we-care__heading"><?php the_field('we_care_heading'); ?></h3>
            <span class="company-we-care__content">
                <?php the_field('we_care_content'); ?>
            </span>
    </div>
</section>

<?php $company_supporting_image = get_field('company_supporting_img'); ?>
<section class="company-supporting">
    <div class="container company-supporting__container">
            <h3 class="company-supporting__heading"><?php the_field('company_supporting_heading'); ?></h3>
            <h3 class="company-supporting__heading-bold"><?php the_field('company_supporting_heading_bold'); ?></h3>
            <span class="company-supporting__content">
                    <?php the_field('company_supporting_heading_content'); ?>
            </span>
            <div class="company-supporting__columns">
                <div class="company-supporting__left"></div>
                <div class="company-supporting__right">
                    <h4 class="company-supporting__below-heading"><?php the_field('customer_supporting_below_heading'); ?></h4>
                    <span class="company-supporting__below-content">
                            <?php the_field('customer_supporting_below_content'); ?>
                    </span>
                </div>
            </div>
    </div>
</section>


<section class="company-manufacturing">
    <div class="container company-manufacturing__container text-center">
            <h3 class="company-manufacturing__heading"><?php the_field('our_manufacturing_heading'); ?></h3>
            <p class="company-manufacturing__content"><?php the_field('our_manufacturing_content'); ?></p>
            <span class="company-manufacturing__content-icons">

            <?php if (have_rows('our_manufacturing_cards')) : ?>
            <?php while (have_rows('our_manufacturing_cards')) : the_row(); ?>

                <div class="company-manufacturing__content-card">
                    <?php $our_manufacturing_icon = get_sub_field('our_manufacturing_icon'); ?>
                    <img src="<?php echo $our_manufacturing_icon['url']; ?>" alt="<?php echo $our_manufacturing_icon['url']; ?>" class="company-manufacturing__content-logo">
                </div>
            <?php endwhile; ?>
            <?php endif; ?> 
<!-- 
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div>
                <div class="company-manufacturing__content-card">
                    <img src="/wp-content/uploads/2026/01/logos_0016_Layer-1.jpg" alt="logo" class="company-manufacturing__content-logo">
                </div> -->

            </span>
    </div>
</section>



<!-- Sectors -->
<?php require_once('page-includes/home-sectors.php'); ?>
<!-- Sectors -->


<!-- Quality Assured -->
<?php require_once('page-includes/home-testimonials.php'); ?>
<!-- Quality Assured -->


<!-- Any Questions -->
<?php require_once('page-includes/home-any-questions.php'); ?> 
<!-- Any Questions -->


<?php

get_footer();
