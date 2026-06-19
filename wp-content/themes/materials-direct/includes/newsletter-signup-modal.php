<?php
function display_newsletter_signup() {
    // Start output buffering
    ob_start();
    ?>
    <div class="newsletter-signup">
        <div class="newsletter-signup__modal">
            <img class="newsletter-signup__icon" src="/wp-content/uploads/2024/08/times-solid-2.svg">
            <p class="newsletter-signup__content"><?php the_field('technical_bulletin_modal_heading'); ?></p>
            <div class="newsletter-signup__form" id="gravity-form-wrapper">
            <?php echo do_shortcode('[contact-form-7 id="9ab3eac" title="Technical Bulletin Form"]'); ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Register the shortcode
// you can find the shorcode statement in the header.php file
add_shortcode('newsletter-signup', 'display_newsletter_signup');


function enqueue_newsletter_signup_script() {
    wp_enqueue_script(
        'newsletter-signup', 
        get_stylesheet_directory_uri() . '/js/newsletter-signup.js', 
        array('jquery'), 
        filemtime(get_stylesheet_directory() . '/js/newsletter-signup.js'), 
        true 
    );
}
add_action('wp_enqueue_scripts', 'enqueue_newsletter_signup_script');