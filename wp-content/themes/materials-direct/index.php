<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Materials_Direct
 */

get_header();
?>


<main id="primary" class="site-main container">

    <?php if ( have_posts() ) : ?>
		<section class="news">
        <div class="news__grid">

            <?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('news__card'); ?>>

                    <a href="<?php the_permalink(); ?>" class="news__image">
                        <?php 
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail('medium_large');
                        } else {
                            // fallback image or blank div
                            echo '<div class="no-image"></div>';
                        }
                        ?>
                    </a>

                    <div class="news__content">

					    <p class="news__date"><i class="fa-regular fa-clock"></i> <?php echo get_the_date('d/m/Y'); ?></p>

                        <h3 class="news__title">
                            <a class="news__title-link" href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>



                        <!-- <div class="news__links"><span class="news__favourites"><a href="#"><i class="fa-regular fa-heart news__link-icon"></i><span class="news__link-icon-number">0</span></a></span> <a class="news__readmore" href="<?php //the_permalink(); ?>"><i class="fa-regular fa-file-lines news__link-icon"></i> Read more</a></div> -->
						<?php 
						$post_id = get_the_ID();
						$count   = md_get_post_favourites($post_id);
						$active  = is_user_logged_in() && md_user_has_favourited(get_current_user_id(), $post_id);
						?>
						 <div class="news__links">
							<span class="news__favourites">
								<a href="#" 
								class="md-fav-toggle" 
								data-post="<?php echo $post_id; ?>">

								<i class="<?php echo $active ? 'fa-solid' : 'fa-regular'; ?> fa-heart news__link-icon"></i>
								<span class="news__link-icon-number"><?php echo $count; ?></span>
								</a>
							</span>

							<a class="news__readmore" href="<?php the_permalink(); ?>">
								<i class="fa-regular fa-file-lines news__link-icon"></i> Read more
							</a>
						</div>

                    </div>

                </article>

            <?php endwhile; ?>

        </div><!-- .blog-card-grid -->

        <?php the_posts_navigation(); ?>

    <?php else : ?>

        <?php get_template_part( 'template-parts/content', 'none' ); ?>

    <?php endif; ?>
	</section>
</main>

<?php

get_footer();
