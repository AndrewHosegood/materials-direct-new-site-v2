<section class="sectors">
    <div class="container sectors__container">
        <h2 class="sectors__title">Sectors</h2>
        <p class="sectors__subtitle">Each sector has its own unique requirements and specifications. Materials Direct works to ensure that each sector we supply to, receives the highest quality and best-performing products in relation to their industry and specific need.</p>
        <div class="sectors__content three-column-grid grid-gap-three">

        <?php
        // Query all 'sectors' posts
        $args = array(
            'post_type'      => 'sector',
            'posts_per_page' => -1, // get all
            'orderby'        => 'title',
            'order'          => 'ASC'
        );

        $sectors_query = new WP_Query($args);

        // Loop through the sectors
        if ($sectors_query->have_posts()) :
            while ($sectors_query->have_posts()) : $sectors_query->the_post(); ?>
                <?php $sector_featured_image = get_field('sector_featured_image'); ?>
                <div class="sectors__card">
                    <img src="<?php echo $sector_featured_image['url']; ?>" alt="Aerospace" class="sectors__card-img">
                    <div class="sectors__card-content">
                        <h4 class="sectors__heading"><?php the_title(); ?></h4>
                        <p class="sectors__content-text"><?php the_field('sector_card_content'); ?></p>
                        <a class="button sectors__btn" href="/sector/aerospace/" title="Learn More"><?php the_field('sector_card_button'); ?></a>
                    </div>
                </div>
            <?php endwhile;
        else :
            echo '<p>No sectors found.</p>';
        endif;

        // Restore global post data
        wp_reset_postdata();
        ?>

        </div>
    </div>
</section>