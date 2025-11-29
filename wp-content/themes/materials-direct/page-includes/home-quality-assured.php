<section class="quality-assured text-center">
    <div class="container quality-assured__container">
        <h2 class="quality-assured__heading"><?php the_field('quality_assured_heading'); ?></h2>
        <h4 class="quality-assured__subheading"><?php the_field('quality_assured_subheading'); ?></h4>
        <?php the_field('quality_assured_content'); ?>
        <?php $quality_assured_logo = get_field('quality_assured_logo'); ?>
        <img src="<?php echo $quality_assured_logo['url']; ?>" alt="<?php echo $quality_assured_logo['alt']; ?>" class="quality-assured__logo">

        <?php 
        $link = get_field('quality_assured_link'); 

        if ($link) : 
            $link_url = $link['url'];
            $link_title = $link['title'];
            $link_target = $link['target'] ? $link['target'] : '_self';
        ?>
            <a class="quality-assured__link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                <?php echo esc_html($link_title); ?>
            </a>
        <?php endif; ?>

    </div>    
</section>