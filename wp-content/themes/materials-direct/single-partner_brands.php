<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Materials_Direct
 */

get_header();
?>


<!-- Banner -->
<section class="brands-banner owl-carousel owl-theme">
	<?php if (have_rows('brands_banner')) : ?>
		<?php while (have_rows('brands_banner')) : the_row(); ?>
			<div class="item brands-banner__item">

				<?php $banner_image = get_sub_field('brands_banner_background_image'); ?>
				<?php
          if(get_field('brands_banner_height')){
              $banner_height = get_field('brands_banner_height'); 
          } else {
              $banner_height = '355'; 
          }
        ?>
				<?php $banner_logo = get_sub_field('brands_banner_logo'); ?>

				<div class="brands-banner__container" style="background-image: url(<?php echo $banner_image['url']; ?>); height: <?php echo $banner_height; ?>px;">
					
					<div class="brands-banner__content" style="height: <?php echo $banner_height; ?>px;">
						<img src="<?php echo $banner_logo['url']; ?>" alt="<?php echo $banner_logo['alt']; ?>" class="brands-banner__logo">
						<span class="brands-banner__content-wrap"><?php the_sub_field('brands_banner_content'); ?></span>
					</div>
					

				</div>
			</div>
		<?php endwhile; ?>
    <?php endif; ?>  
	
</section>
<!-- Banner -->


<div class="container">
  <div class="partner-brands-content">

    <?php the_post(); ?>

    <?php
    // Get repeater field
    $partner_brands_categories = get_field('partner_brands_categories') ?: [];

    $total_items = count($partner_brands_categories);
    $items_per_page = 40;
    $paged = max(1, get_query_var('paged', 1));
    $offset = ($paged - 1) * $items_per_page;

    // Slice repeater items for pagination
    $partner_brands_categories_paged = array_slice($partner_brands_categories, $offset, $items_per_page);
    ?>

    <?php if ($total_items > 0) { ?>
      <div class="partner-brands-content__count">
        <p><?php echo esc_html($total_items); ?> Results Found</p>
      </div>
    <?php } else {
		echo 'No Results Found';
	} ?>

    <?php if (!empty($partner_brands_categories_paged)) : ?>


      <?php 
          foreach ($partner_brands_categories_paged as $category) : 
              $thumb = $category['brands_category_thumbnail'] ?? null;
              
              // Determine image source and alt text
              if (!empty($thumb) && is_array($thumb)) {
                  $image_src = $thumb['url'];
                  $image_alt = $thumb['alt'] ?? '';
              } else {
                  $image_src = wc_placeholder_img_src();           // WooCommerce default placeholder
                  $image_alt = __('Placeholder', 'materials-direct');
              }
      ?>
        <div class="partner-brands-content__wrapper">


        <div class="partner-brands-content__thumb">
            <img class="partner-brands-content__img"
                 src="<?php echo esc_url($image_src); ?>"
                 alt="<?php echo esc_attr($image_alt); ?>">
          </div>



          <div class="partner-brands-content__right">
            <h4 class="partner-brands-content__title">
              <?php echo esc_html($category['brands_category_title']); ?>
            </h4>
            <p class="partner-brands-content__description">
              <?php echo wp_kses_post($category['brands_category_description']); ?>
            </p>
          </div>

          <a class="partner-brands-content__link vc_btn3-style-flat"
             href="<?php echo esc_url($category['brands_category_link']); ?>">
             View Product
          </a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <div class="pager">
      <div class="pages">
        <?php
        $total_pages = ceil($total_items / $items_per_page);
        if ($total_pages > 1) {
          echo paginate_links([
            'base'      => add_query_arg('paged', '%#%'),
            'format'    => '',
            'current'   => $paged,
            'total'     => $total_pages,
            'prev_text' => __('« Prev Page'),
            'next_text' => __('Next Page »'),
          ]);
        }
        ?>
      </div>
    </div>

  </div>
</div>

<div class="container">
  <?php $short = '[popular_products_carousel]'; ?>
  <?php echo do_shortcode($short); ?>
</div>














<?php
//get_sidebar();
get_footer();
