<?php
/*
Template Name: Partner Brands
*/

get_header();
?>

<!-- Banner -->
<section class="banner owl-carousel owl-theme" style="height:<?php the_field('banner_height'); ?>px;">
	<?php if (have_rows('banner')) : ?>
		<?php while (have_rows('banner')) : the_row(); ?>
			<div class="item" style="height:<?php the_field('banner_height'); ?>px;">
				<div class="banner__content" style="padding: 5rem 0;">
					<?php $banner_image = get_sub_field('banner_image'); ?>
                    <?php $banner_height = get_sub_field('banner_height'); ?>
					<h1 class="banner__heading"><?php the_sub_field('banner_heading'); ?></h1>
					<p class="banner__text"><?php the_sub_field('banner_subheading'); ?></p>
                    <?php if(get_sub_field('banner_button')){ ?>
                        <a class="button banner__btn" href="/shop/"><?php the_sub_field('banner_button'); ?></a>
                    <?php } ?>
				</div>
				<img src="<?php echo $banner_image['url']; ?>" alt="<?php echo $banner_image['alt']; ?>" class="banner__img" style="height:<?php the_field('banner_height'); ?>px;">
			</div>
		<?php endwhile; ?>
    <?php endif; ?>  
	
</section>
<!-- Banner -->

<div class="partner-brand__page container" style="padding: 40px 0; text-align: center;">


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
        <div class="partner-brand__grid four-column-grid grid-gap-zero-point-six">
            <?php while ($brands->have_posts()) : $brands->the_post(); ?>
                <div class="partner-brand__item" style="text-align: center;">
                    <a href="<?php the_permalink(); ?>" style="display: block;">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', ['style' => 'max-width: 100%; height: auto;']); ?>
                        <?php else : ?>
                            <div style="background: #f0f0f0; width: 150px; height: 150px; display: inline-flex; align-items: center; justify-content: center;">
                                <span>No Image</span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p>No partner brands found.</p>
    <?php endif;
    wp_reset_postdata();
    ?>
</div>

<?php

get_footer();
