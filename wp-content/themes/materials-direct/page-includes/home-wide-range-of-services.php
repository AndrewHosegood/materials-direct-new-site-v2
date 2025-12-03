<section class="wide-range-of-services text-center">
<h3 class="wide-range-of-services__heading">Materials Direct Offers A Wide Range of Services</h3>
<div class="container wide-range-of-services__container three-column-grid grid-gap-three">
<?php
// Arguments for custom post type query
$args = array(
    'post_type'      => 'manufacturing',
    'posts_per_page' => -1, // show all
    'post_status'    => 'publish',
    'order'          => 'ASC'
);

// Start custom query
$manufacturing_query = new WP_Query($args);

if ( $manufacturing_query->have_posts() ) : ?>

    <!-- <div class="container wide-range-of-services__container three-column-grid grid-gap-three"> -->

        <?php while ( $manufacturing_query->have_posts() ) : $manufacturing_query->the_post(); ?>

            <?php if ( has_post_thumbnail() ) : ?>
            <a class="wide-range-of-services__link" href="<?php the_permalink(); ?>">
            <div class="wide-range-of-services__card">

                <!-- Featured Image -->
                
                    <div class="wide-range-of-services__card-icon">
                        
                            <?php 
                            the_post_thumbnail(
                                'medium',
                                array(
                                    'class' => 'wide-range-of-services__card-icon-img'
                                )
                            );
                            ?>
                        
                    </div>
                

                <!-- Content / Excerpt -->
                <div class="wide-range-of-services__card-content">
                    <h4 class="wide-range-of-services__card-content-heading">
                        <?php 
                        if(get_the_title() === "Punch Press Die Cutting"){
                            echo "Die Cutting";
                        } else {
                            the_title(); 
                        }
                        ?>
                    </h4>
                    <?php the_excerpt(); ?>
                </div>

 

            </div>
            </a>
            <?php endif; ?>

        <?php endwhile; ?>

    <!-- </div> -->

<?php
endif;

// Reset post data
wp_reset_postdata();
?>



</div>
</section>  


