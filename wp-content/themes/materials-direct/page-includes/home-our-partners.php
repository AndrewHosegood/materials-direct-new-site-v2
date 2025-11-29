<?php
    // Query all posts from the custom post type "partner_brands"
    $args = array(
        'post_type'      => 'partner_brands',
        'posts_per_page' => -1,  // show all
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $brands = new WP_Query($args);

    if ($brands->have_posts()) : ?>
        <section class="our-partners text-center">
            <h2 class="our-partners__heading">Our Partners</h2>
                <div class="container">
                    <div class="our-partners__carousel owl-carousel owl-theme">
                        <?php while ($brands->have_posts()) : $brands->the_post(); ?>
                            <div class="item our-partners__item" style="text-align: center;">

                                <a href="<?php the_permalink(); ?>" style="display: block;">
                                    <?php if (has_post_thumbnail()){ ?>
                                        <?php the_post_thumbnail('medium', ['style' => 'max-width: 100%; height: auto;']); ?>
                                    <?php } ?>
                                </a>

                            </div>
                        <?php endwhile; ?>
                </div>        
            </div>    
            <a class="button" href="/partner_brands/">Shop By Brand</a>       
        </section>
    <?php else : ?>
        <p>No partner brands found.</p>
    <?php endif;
    wp_reset_postdata();
    ?>