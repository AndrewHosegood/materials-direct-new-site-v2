<?php
/*
Template Name: Contact
*/

get_header();
?>

<!-- Map -->
<iframe class="contact__map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2453.6685829537246!2d-0.825466223033532!3d52.049348771941624!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487701a8416cfd9b%3A0xbfb0366b9e54e2e!2sUniversal%20Science!5e0!3m2!1sen!2suk!4v1764755960594!5m2!1sen!2suk" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
<!-- Map -->

<!-- Content -->
<div class="container contact__container">

    <div class="contact__row">

        <h3 class="contact__heading">Contact Materials Direct</h3>

        <div class="contact__wrapper">
            <?php the_content(); ?>
		</div>

    </div>
        <div class="contact__row">
        <h3 class="contact__heading">General Enquiry</h3>
         <?php echo do_shortcode('[contact-form-7 id="f3692bd" title="Contact Form Home Page"]'); ?>
    </div>
</div>
<!-- Content -->




<?php

get_footer();
