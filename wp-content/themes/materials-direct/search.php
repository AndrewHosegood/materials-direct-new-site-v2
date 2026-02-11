<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Materials_Direct
 */

get_header();
?>

	<main id="primary" class="site-main search">
		<div class="container">
		<?php if ( have_posts() ) : ?>

			<header class="page-header search__header">
				<h1 class="page-title search__title">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search Results for: %s', 'materials-direct' ), '<span>' . get_search_query() . '</span>' );
					?>
				</h1>
			</header><!-- .page-header -->

			<div class="search__results">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
				echo '<div class="search__results-card">';
				$title = get_the_title();
				$content = get_the_excerpt();
				$link = get_the_permalink();
				echo '<h3 class="search__results-heading"><a href="'.$link.'">' .$title. '</a></h3>';
				echo '<span class="search__results-content">' .$content. '</span>';
				echo '</div>';
			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
		</div><!-- .search_results -->

		</div><!-- .container -->
	</main><!-- #main -->

<?php

get_footer();
