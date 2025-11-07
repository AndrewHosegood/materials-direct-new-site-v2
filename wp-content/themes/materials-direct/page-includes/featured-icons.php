<section class="featured-icons">
    <div class="container featured-icons__container">
        
        <?php if (have_rows('featured_icons')) : ?>
            <?php while (have_rows('featured_icons')) : the_row(); ?>
                <?php $featured_icons_image = get_sub_field('featured_icons_image') ?>
                
                
                <div class="featured-icons__column">
                    <div class="featured-icons__icon-left">
                        <img class="featured-icons__icon" alt="<?php echo $featured_icons_image['alt']; ?>" src="<?php echo $featured_icons_image['url']; ?>">
                    </div>
                    <div class="featured-icons__content-right">
                        <h6 class="featured-icons__heading"><?php the_sub_field('featured_icons_heading'); ?></h6>
                        <p class="featured-icons__content"><?php the_sub_field('featured_icons_content'); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?> 
    </div>
        
</section>