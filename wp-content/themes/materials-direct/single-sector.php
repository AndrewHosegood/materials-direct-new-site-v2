<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Materials_Direct
 */

get_header();
?>

<!-- Banner -->
<section class="banner owl-carousel owl-theme">
	<?php if (have_rows('banner')) : ?>
		<?php while (have_rows('banner')) : the_row(); ?>
			<div class="item">
				<div class="banner__content">
					<?php $banner_image = get_sub_field('banner_image'); ?>
					<?php 
					if(get_sub_field('banner_height')){
						$banner_height = get_sub_field('banner_height');
					} else {
						$banner_height = "440";
					}
					?>
					<h1 class="banner__heading"><?php the_sub_field('banner_heading'); ?></h1>
					<h3 class="banner__subheading"><?php the_sub_field('banner_subheading'); ?></h3>
					<a class="button banner__btn" href="/shop/"><?php the_sub_field('banner_button'); ?></a>
				</div>
				<img src="<?php echo $banner_image['url']; ?>" alt="<?php echo $banner_image['alt']; ?>" class="banner__img" style="height:<?php the_field('banner_height'); ?>px;">
			</div>
		<?php endwhile; ?>
    <?php endif; ?>  
	
</section>
<!-- Banner -->



<!-- 2 column content -->

<?php if (have_rows('two_column_layout')) : ?>
	<?php while (have_rows('two_column_layout')) : the_row(); ?>
		<section class="sectors-two-column">
			<div class="two-column-grid grid-gap-three container <?php if(get_sub_field('layout_alignment') === "Right"){ echo "reverse"; } ?>">
				<?php $two_column_layout_image = get_sub_field('two_column_layout_image'); ?>
				<img src="<?php echo $two_column_layout_image['url']; ?>" alt="<?php echo $two_column_layout_image['alt']; ?>" class="sectors-two-column__img">
				<div class="sectors-two-column__content">
					<h2 class="sectors-two-column__heading"><?php the_sub_field('two_column_layout_heading'); ?></h2>
					<p><?php the_sub_field('two_column_layout_content'); ?></p>
					<a class="button sectors-two-column__btn"><?php the_sub_field('two_column_layout_button'); ?></a>
				</div>
			</div>
		</section>
 		<?php endwhile; ?>
<?php endif; ?>  

<!-- 2 column content -->

<!-- Explore our product range -->
<section class="explore-product-range text-center">
<h2><?php the_field('explore_our_product_range_title'); ?></h2>
<div class="container three-column-grid grid-gap-one-point-two">
<?php if (have_rows('explore_our_product_range')) : ?>
	
		<?php while (have_rows('explore_our_product_range')) : the_row(); ?>
			<div class="explore-product-range__card">
				<h3 class="explore-product-range__card-heading"><?php the_sub_field('explore_our_product_range_heading'); ?></h3>
				<p class="explore-product-range__card-content"><?php the_sub_field('explore_our_product_range_content'); ?></p>
				<a class="button explore-product-range__btn" href="explore-product-range__card-btn"><?php the_sub_field('explore_our_product_range_button'); ?></a>
			</div>
		<?php endwhile; ?>
	
<?php endif; ?>  
</div>
</section>

<!-- Explore our product range -->

	<!--
	<main id="primary" class="site-main container this-page">

		<?php
		/*
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'materials-direct' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'materials-direct' ) . '</span> <span class="nav-title">%title</span>',
				)
			);


			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; 
		*/
		?>

	</main>
	-->
<?php

get_footer();
