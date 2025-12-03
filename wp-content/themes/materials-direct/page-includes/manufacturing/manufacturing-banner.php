<section class="banner owl-carousel owl-theme">
	<?php if (have_rows('banner')) : ?>
		<?php while (have_rows('banner')) : the_row(); ?>
		<?php $banner_image = get_sub_field('banner_image'); ?>
			<div class="item" style="background-image:url('<?php echo $banner_image['url']; ?>');">
				<div class="banner__content">
					<?php 
					if(get_sub_field('banner_height')){
						$banner_height = get_sub_field('banner_height');
					} else {
						$banner_height = "440";
					}
					?>
					<h1 class="banner__heading"><?php the_sub_field('banner_heading'); ?></h1>
					<h3 class="banner__subheading"><?php the_sub_field('banner_subheading'); ?></h3>

                    <?php
                    $link_1 = get_sub_field('banner_button_link');
                    if ($link_1) {
                        $url_1 = $link_1['url'];
                        $title_1 = $link_1['title'];
                    ?>

					<a class="button banner__btn" href="<?php echo esc_url( $url_1 ); ?>"><?php echo esc_html( $title_1 ); ?></a>
                    <?php } ?>

				</div>
			</div>
		<?php endwhile; ?>
    <?php endif; ?>  
</section>