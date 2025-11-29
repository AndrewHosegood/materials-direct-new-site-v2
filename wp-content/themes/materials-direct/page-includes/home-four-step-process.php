<section class="four-step-process text-center">
    <!-- <h3 class="four-step-process__heading">Simple <em class="four-step-process__heading-italic"><strong>4 step</strong></em> process<br>to getting your parts.</h3> -->
    <h3 class="four-step-process__heading"><?php the_field('four_step_process_heading'); ?></h3>

    <div class="container four-step-process__container four-column-grid grid-gap-zero-point-seven">
        <?php $four_step_process_img_1 = get_field('four_step_process_image_1'); ?>
        <?php $four_step_process_img_2 = get_field('four_step_process_image_2'); ?>
        <?php $four_step_process_img_3 = get_field('four_step_process_image_3'); ?>
        <?php $four_step_process_img_4 = get_field('four_step_process_image_4'); ?>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_1['url']; ?>" alt="<?php echo $four_step_process_img_1['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_1'); ?></p>
        </div>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_2['url']; ?>" alt="<?php echo $four_step_process_img_2['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_2'); ?></p>
        </div>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_3['url']; ?>" alt="<?php echo $four_step_process_img_3['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_3'); ?></p>
        </div>
        <div class="four-step-process__card">
            <div class="four-step-process__icon-container"><img src="<?php echo $four_step_process_img_4['url']; ?>" alt="<?php echo $four_step_process_img_4['alt']; ?>" class="four-step-process__icon"></div>
            <p class="four-step-process__card-heading"><?php the_field('four_step_process_title_4'); ?></p>
        </div>
    </div>
</section>