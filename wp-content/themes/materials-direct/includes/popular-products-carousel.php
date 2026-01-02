<?php
function popular_products_carousel_shortcode() {
    // WP_Query arguments
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'meta_key'       => 'total_sales',  // Sorting by sales count (popularity)
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        echo '<h2 class="popular-products-carousel__heading">Popular Products</h2>';
        echo '<div class="popular-products-carousel">';

        $product_count = 0;

        while ($query->have_posts()) {
            $query->the_post();
            global $product; 

            $product_count++;

            $lighter_class = ($product_count == 2 || $product_count == 4) ? ' lighter' : '';

            ?>
       
            <div class="popular-products-carousel__item">
            <div class="popular-products-carousel__thumb">
            <div class="popular-products-carousel__mask"></div>
            <?php 
            $download_items = get_field('download_items'); 
            ?>
            <div style="display: flex;" class="image_links double">
            <a rel="nofollow" href="<?php echo get_the_permalink(); ?>" class="product_type_simple">Order<br>Custom Parts</a>
            <?php 
            if( $download_items ) {
                $first_iteration = true;
                $count = 0;
                foreach( $download_items as $item ) {
                    $count++;
                        $download_file = $item['download_file']; ?>
                        <?php if($count == 1){ ?>
                            <a target="_blank" class="link" href="<?php echo $download_file; ?>">Product<br>Data Sheet</a>
                        <?php } ?>
                    <?php 
                }
            }
            ?>
            </div>

            <a href="<?php echo esc_url(get_the_permalink()); ?>">
                <img class="cat-hover-image" src="/wp-content/uploads/2024/03/category_hover_with_text.jpg"></a>
                <?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail'); ?>
            </div>
            <div class="popular-products-carousel__details <?php echo esc_attr($lighter_class); ?>">
            <h3 class="popular-products-carousel__title"><?php  echo esc_html(get_the_title()) ?></h3>
            <p class="popular-products-carousel__sku">SKU: <?php echo esc_html($product->get_sku()); ?></p>
            </div>
            <a class="button popular-products-carousel__btn" href="<?php echo esc_url(get_the_permalink()); ?>">Select options</a>
            </div>
        <?php
        }

        echo '</div>'; 


    } else {
        echo '<p>No popular products found</p>';
    }


    wp_reset_postdata();


    return ob_get_clean();
}


add_shortcode('popular_products_carousel', 'popular_products_carousel_shortcode');
