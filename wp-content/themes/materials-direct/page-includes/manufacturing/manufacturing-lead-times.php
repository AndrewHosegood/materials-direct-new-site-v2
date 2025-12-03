<?php $colour_1 = get_field('manufacturing_lead_times_guaranteed_background', get_the_ID()); ?>
<?php 
$link_1 = get_field('manufacturing_one_column_button_link');
if ($link_1) {
    $url_1 = $link_1['url'];
    $title_1 = $link_1['title'];
?>
<section class="manufacturing-single-column text-center" style="background: <?php echo esc_attr( $colour_1 ); ?>;">
    <div class="container manufacturing-single-column__container">
        <h2 class="manufacturing-single-column__heading"><?php the_field('manufacturing_one_column_heading'); ?></h2>
        <div class="manufacturing-single-column__content">
            <?php the_field('manufacturing_one_column_content'); ?>
        </div>
        <a class="manufacturing-single-column__button-1" href="<?php echo esc_url( $url_1 ); ?>"><?php echo esc_html( $title_1 ); ?></a>
    </div>
</section>
<?php } ?>