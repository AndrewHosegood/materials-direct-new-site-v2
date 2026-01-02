<?php
/*
Template Name: Faq
*/

get_header();
?>



<!-- Banner -->
<?php require_once('page-includes/sector/sector-banner.php'); ?>
<!-- Banner -->




<!-- FAQs -->
<div class="container faqs">
    <h1 class="faqs__heading text-center">Frequently Asked Questions</h1>
    <div class="faqs__accordion wp-faq-schema-wrap wp-faq-schema-accordion">
        <div class="faqs__ui-accordion wp-faq-schema-items ui-accordion ui-widget ui-helper-reset" role="tablist">

            <?php if (have_rows('faqs')) : ?>
                <?php while (have_rows('faqs')) : the_row(); 
                    $i = get_row_index();
                    $is_first = ($i === 1);
                ?>

                <h3 class="faqs__ui-accordion-header <?php echo $is_first ? 'ui-state-active ui-accordion-header-active' : 'ui-accordion-header-collapsed'; ?>"
                    role="tab"
                    id="faq-header-<?php echo $i; ?>"
                    aria-controls="faq-panel-<?php echo $i; ?>"
                    aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
                    aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
                    tabindex="0"
                >
                    <span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"><i class="fa-solid fa-caret-right faqs__icon"></i></span>
                    <?php the_sub_field('question'); ?>
                </h3>

                <div class="faqs__ui-accordion-content"
                    id="faq-panel-<?php echo $i; ?>"
                    aria-labelledby="faq-header-<?php echo $i; ?>"
                    role="tabpanel"
                    aria-hidden="<?php echo $is_first ? 'false' : 'true'; ?>"
                    style="display: <?php echo $is_first ? 'block' : 'none'; ?>;"
                >
                    <p><?php the_sub_field('answer'); ?></p>
                </div>

                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- FAQs -->



<?php

get_footer();
