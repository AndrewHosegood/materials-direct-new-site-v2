<section class="technical-bulletin">
    <div id="bulletinOpen" class="technical-bulletin__container">
        <div class="technical-bulletin__left-col">
            <h3 class="technical-bulletin__subheading"><?php the_field('technical_bulletin_title'); ?> - <?php the_field('technical_bulletin_subheading'); ?></h3>
            <h2 class="technical-bulletin__heading"><?php the_field('technical_bulletin_heading'); ?></h2>

            <p class="technical-bulletin__content"><?php the_field('technical_bulletin_content'); ?></p> 
            <a href="#" class="technical-bulletin__btn"><?php the_field('technical_bulletin_button'); ?></a>
        </div>
        <div class="technical-bulletin__right-col">
            <?php 
            $home_technical_bulletin_img = get_field('technical_bulletin_image');

            if( !empty($home_technical_bulletin_img) && is_array($home_technical_bulletin_img) ){
                $img_url = $home_technical_bulletin_img['url'];
                $img_alt = $home_technical_bulletin_img['alt'];
            } else {
                $img_url = "/wp-content/uploads/2025/06/technical-bulletin-img.png";
                $img_alt = "Technical Bulletin";
            }
            ?>
            <img alt="<?php echo esc_attr($img_alt); ?>" class="technical-bulletin__bulletin" src="<?php echo esc_url($img_url); ?>">
        </div>    
    </div>
</section>