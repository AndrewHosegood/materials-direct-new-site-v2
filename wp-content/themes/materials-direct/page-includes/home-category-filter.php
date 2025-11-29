<div class="home-category-menu">
    <div class="home-category-menu__top">
        <h1 class="home-category-menu__title">Instant Online Pricing For Custom Parts <span class="home-category-menu__break">Manufactured Within 24 Hours!</span></h1>
    </div>
    <div class="home-category-menu__content">
        <?php if (have_rows('home_category_menu_cards')) : ?>
            <?php while (have_rows('home_category_menu_cards')) : the_row(); ?>
                <span class="home-category-menu__card">
                    <h4 class="home-category-menu__card-heading"><?php the_sub_field('home_category_menu_heading'); ?></h4>
                    <?php $home_category_menu_image = get_sub_field('home_category_menu_image') ?>
                    <a class="home-category-menu__link" href="<?php the_sub_field('home_category_menu_link') ?>"><img class="home-category-menu__card-img" src="<?php echo $home_category_menu_image['url']; ?>" alt="<?php echo $home_category_menu_image['alt']; ?>"></a>
                    <a class="home-category-menu__btn" href="<?php the_sub_field('home_category_menu_link') ?>">View Product Range</a>
                </span>
            <?php endwhile; ?>
        <?php endif; ?>    

    </div>
    <a class="home-category-menu__lower" href="/shop/">View All Products</a>
</div>