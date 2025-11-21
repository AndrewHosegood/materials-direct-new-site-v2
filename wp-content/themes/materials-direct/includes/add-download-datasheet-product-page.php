<?php
add_action( 'woocommerce_single_product_summary', 'add_download_datasheet_product_page', 26 );
function add_download_datasheet_product_page() {

            if( have_rows('download_items') ) {
                while ( have_rows('download_items') ) {
                    the_row();
                    $image = get_sub_field('download_image');
                    ?>

                    <a target="_blank" class="product-page__download-datasheet" href="<?php the_sub_field('download_file'); ?>">Download Datasheet</a>

                    <?php
                }
            }            

}