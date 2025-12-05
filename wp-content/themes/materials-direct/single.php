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

	<main id="primary" class="site-main container news-detail">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'materials-direct' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'materials-direct' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>


		<?php
		// --- Related Posts Section ---
		$categories = wp_get_post_categories( get_the_ID() );

		if ( $categories ) {

			$related_args = array(
				'category__in'   => $categories,
				'post__not_in'   => array( get_the_ID() ),
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC'
			);

			$related_query = new WP_Query( $related_args );

			if ( $related_query->have_posts() ) : ?>
				
				<section class="news-detail__related container">
					<p class="news-detail__related-title">Related Posts</p>

					<div class="news-detail__related-grid">
						<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
							<article class="news-detail__related-card">
								
								<a href="<?php the_permalink(); ?>" class="news-detail__related-thumb">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'medium_large' ); ?>
									<?php endif; ?>
								</a>

								<div class="related-content">
									<span class="news-detail__related-date">
										<?php echo get_the_date(); ?>
									</span>

									<h3 class="news-detail__related-title-small">
										<a href="<?php the_permalink(); ?>">
											<?php the_title(); ?>
										</a>
									</h3>

									<a href="<?php the_permalink(); ?>" class="news-detail__read-more">
										Read more →
									</a>
								</div>

							</article>
						<?php endwhile; ?>
					</div>
				</section>

				<?php wp_reset_postdata(); ?>

			<?php endif;
		}
		// --- Related Posts Section ---
		?>






	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
