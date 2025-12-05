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