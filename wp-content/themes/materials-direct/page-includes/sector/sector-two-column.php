<?php if (have_rows('two_column_layout')) : ?>
	<?php while (have_rows('two_column_layout')) : the_row(); ?>
		<section class="sectors-two-column">
			<div class="two-column-grid grid-gap-three container <?php if(get_sub_field('layout_alignment') === "Right"){ echo "reverse"; } ?>">
				<?php $two_column_layout_image = get_sub_field('two_column_layout_image'); ?>
				<img src="<?php echo $two_column_layout_image['url']; ?>" alt="<?php echo $two_column_layout_image['alt']; ?>" class="sectors-two-column__img">
				<div class="sectors-two-column__content">
					<h2 class="sectors-two-column__heading"><?php the_sub_field('two_column_layout_heading'); ?></h2>
					<p><?php the_sub_field('two_column_layout_content'); ?></p>

					<?php 
                        $link_1 = get_sub_field('two_column_layout_button_link');
                        if ($link_1) {
                            $url_1 = $link_1['url'];
                            $title_1 = $link_1['title'];
                    ?>
					<a href="<?php echo esc_url( $url_1 ); ?>" class="button sectors-two-column__btn"><?php echo esc_html( $title_1 ); ?></a>
					<?php } ?>
				</div>
			</div>
		</section>
 		<?php endwhile; ?>
<?php endif; ?>  