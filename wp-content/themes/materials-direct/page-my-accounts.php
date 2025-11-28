<?php
/*
Template Name: My Accounts
*/

get_header();

?>

<?php if( is_account_page() ) { ?>
    <?php $banner_image = get_field('banner_image'); ?>
	<section class="my-accounts-banner" style="background-image:url('<?php echo $banner_image['url']; ?>');"><h1 class="my-accounts-banner__heading"><?php the_field('banner_heading'); ?></h1></section>
<?php } ?>

	<main id="primary" class="site-main container www">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

	<?php 

	echo do_shortcode('[account_banner]'); 
	?>

<?php

get_footer();
