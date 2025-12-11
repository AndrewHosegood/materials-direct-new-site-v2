<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Materials_Direct
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found text-center">
			<header class="page-header">
				<h1 class="page-title error-404__title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'materials-direct' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content error-404__page-content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'materials-direct' ); ?></p>

					<a class="error-404__btn button" href="/shop/?#advanced-filter">Product Search</a>

					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'page-not-found',
							'menu_id'        => 'page-not-found',
							'container'      => false,
							'menu_class'     => 'error-404__not-found-menu',
						)
					);
					?>





			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
