<?php $link_3a = get_field('custom_cut_materials_button_link_1'); ?>
<?php $link_3b = get_field('custom_cut_materials_button_link_2'); ?>

<section class="manufacturing-single-column text-center">
    <div class="container manufacturing-single-column__container">
        <h2 class="manufacturing-single-column__heading"><?php the_field('custom_cut_materials_heading'); ?></h2>
        <div class="manufacturing-single-column__content">
            <?php the_field('custom_cut_materials_content'); ?>
        </div>

        <?php 
        if ($link_3a) {
            $url_3a = $link_3a['url'];
            $title_3a = $link_3a['title'];
        ?>
            <a class="manufacturing-single-column__button-2" href="<?php echo esc_url( $url_3a ); ?>"><?php echo esc_html( $title_3a ); ?></a>
        <?php } ?>

        <?php 
        if ($link_3b) {
            $url_3b = $link_3b['url'];
            $title_3b = $link_3b['title'];
        ?>
            <a class="manufacturing-single-column__button-1" href="<?php echo esc_url( $url_3b ); ?>"><?php echo esc_html( $title_3b ); ?></a>
        <?php } ?>
        

    </div>
</section>

<?php if(get_field('custom_cut_materials_heading')){ ?>
<section class="featured-icons">
    <div class="container featured-icons__container">
        <div class="featured-icons__column">
            <div class="featured-icons__icon-left">
                <img class="featured-icons__icon" alt="No Tooling Charge" src="/wp-content/uploads/2025/11/tooling-cost.svg">
            </div>
            <div class="featured-icons__content-right">
                <h6 class="featured-icons__heading">No Tooling Charge</h6>
                <p class="featured-icons__content">There is NEVER a tooling charge for manufacturing</p>
            </div>
        </div>
        <div class="featured-icons__column">
            <div class="featured-icons__icon-left">
                <img class="featured-icons__icon" alt="" src="/wp-content/uploads/2025/11/fast-manufacture.svg">
            </div>
            <div class="featured-icons__content-right">
                <h6 class="featured-icons__heading">Fast Manufacturing</h6>
                <p class="featured-icons__content">Parts made &amp; shipped worldwide in as little as 24 hours</p>
            </div>
        </div>
        <div class="featured-icons__column">
            <div class="featured-icons__icon-left">
                <img class="featured-icons__icon" alt="" src="/wp-content/uploads/2025/11/support.svg">
            </div>
            <div class="featured-icons__content-right">
                <h6 class="featured-icons__heading">Technical Support</h6>
                <p class="featured-icons__content">Technical expertise available from our specialists</p>
            </div>
        </div>
        <div class="featured-icons__column">
            <div class="featured-icons__icon-left">
                <img class="featured-icons__icon" alt="" src="/wp-content/uploads/2025/11/secure.svg">
            </div>
            <div class="featured-icons__content-right">
                <h6 class="featured-icons__heading">100% Payment Secure</h6>
                <p class="featured-icons__content">We ensure secure payment with a wide array of options</p>
            </div>
        </div>
    </div>
</section>
<?php } ?>

<section class="manufacturing-instant-quotes text-center">
    <h3 class="manufacturing-instant-quotes__heading"><?php the_field('custom_cut_materials_heading_2'); ?></h3>
    <div class="container">
        <div class="manufacturing-instant-quotes__content">
            <span class="manufacturing-instant-quotes__content-left"><?php the_field('custom_cut_materials_content_left'); ?></span>
            <span class="manufacturing-instant-quotes__content-right"><?php the_field('custom_cut_materials_content_right'); ?></span>
        </div>
    </div>




<?php if(get_field('custom_cut_materials_heading')){ ?>

    <div class="container manufacturing-instant-quotes__container four-column-grid grid-gap-zero-point-seven">

    <?php if (have_rows('custom_cut_cards')) : ?>
        <?php $count = 0; ?>
        <?php while (have_rows('custom_cut_cards')) : the_row(); ?>
            <?php $count ++ ?>
                <div class="manufacturing-instant-quotes__card">
                    <span class="manufacturing-instant-quotes__number"><?php echo $count; ?></span>
                    <div class="manufacturing-instant-quotes__img-wrap">
                        <?php $card_image = get_sub_field('custom_cut_cards_icon'); ?>
                        <img class="manufacturing-instant-quotes__img" alt="<?php echo $card_image['alt']; ?>" src="<?php echo $card_image['url']; ?>">
                    </div>
                    <div class="manufacturing-instant-quotes__card-text"><?php the_sub_field('custom_cut_cards_text'); ?></div>
                </div>
            
        <?php endwhile; ?>
    <?php endif; ?> 

    </div>

<?php } ?>

</section>


