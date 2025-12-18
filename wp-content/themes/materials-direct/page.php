<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Materials_Direct
 */
?>
<!-- TEMPLATE DEBUG: <?php echo basename(__FILE__); ?> -->
<?php
get_header();

        if (is_shop() || is_product_category()) {
			
			echo '<div id="advanced-filter" class="filter-heading-background">';
			echo '<div class="filter-content-wrapper">';
			echo '<h4 class="filter-heading">Product Filter</h4>';
			echo '<a class="filter-btn" href="/shop/">Reset</a>';
			echo '<a class="filter-btn-hide" href="">Hide</a>';
			echo '</div>';
			echo '</div>';

            echo '<div class="filter-wrapper">';
			echo '<div class="filter-wrapper-inner">';
			echo do_shortcode('[woof]');
            
			echo '</div>';
			echo '</div>';
        }

?>


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
get_sidebar();
get_footer();
