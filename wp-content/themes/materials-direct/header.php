<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Materials_Direct
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'materials-direct' ); ?></a>

	<header id="masthead" class="header">

		<div class="header__top">
			<div class="container header__container">
				<ul class="header__contact-details">
						<li class="header__phone"><i class="fa-solid fa-phone-flip header__icon-phone"></i><a class="header__icon-link" href="tel:+44(0)1908222211">+44 (0)1908 222 211</a></li>
						<li class="header__mail"><i class="fa-regular fa-envelope header__icon-email"></i><a class="header__icon-link" href="mailto:info@materials-direct.com"> info@materials-direct.com</a></li>				
				</ul>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'action-bar-top-right',
						'menu_id'        => 'action-bar-top-right',
						'container'      => false,
						'menu_class'     => 'header__menu-right',
					)
				);
				?>
			</div>
		</div>

		<div class="container header__main-container">		
			<div class="site-branding header__left">
				<?php the_custom_logo(); ?>
			</div>
			<div class="header__right">
				<nav id="site-navigation" class="main-navigation">
					<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'materials-direct' ); ?></button>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'header__main-menu',
						)
					);
					?>
				</nav>

				<?php if ( function_exists( 'WC' ) ) : ?>
				<?php 
					$cart_count = 0;
					if ( WC()->cart && is_object( WC()->cart ) && method_exists( WC()->cart, 'get_cart' ) ) {
						$cart_count = count( WC()->cart->get_cart() );
					}
					?>
					<a href="/basket/" id="header-cart-wrapper" class="header__cart-wrapper">
						<img class="header__cart-icon" src="<?php echo esc_url( get_template_directory_uri() . '/images/cart-icon.png' ); ?>" alt="Cart">
						<span class="header__cart-count"><?php echo esc_html( $cart_count ); ?></span>
					</a>
				<?php endif; ?>

				


				<?php echo do_shortcode('[ivory-search id="112" title="Custom Search Form"]'); ?>
			</div>
		</div>

	</header><!-- #masthead -->


	<!-- Conditionally display shop page banner -->

	<?php if(is_shop() || is_product_category()){ ?>
		<?php include('includes/shop-page-banner.php'); ?>
	<?php } ?>
	
	<!-- Conditionally display shop page banner -->