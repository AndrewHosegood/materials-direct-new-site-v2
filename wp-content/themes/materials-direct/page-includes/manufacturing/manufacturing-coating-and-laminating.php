<?php $colour_2 = get_field('coating_and_laminating_background', get_the_ID()); ?>
<?php 
$link_2 = get_field('coating_and_laminating_button_link');
if ($link_2) {
    $url_2 = $link_2['url'];
    $title_2 = $link_2['title'];
?>
<section class="manufacturing-single-column text-center" style="background: <?php echo esc_attr( $colour_2 ); ?>;">
    <div class="container manufacturing-single-column__container">
        <h2 class="manufacturing-single-column__heading"><?php the_field('coating_and_laminating_heading'); ?></h2>
        <div class="manufacturing-single-column__content">
            <?php the_field('coating_and_laminating_content'); ?>
        </div>
        <a class="manufacturing-single-column__button-2" href="<?php echo esc_url( $url_2 ); ?>"><?php echo esc_html( $title_2 ); ?></a>
    </div>
</section>
<?php } ?>