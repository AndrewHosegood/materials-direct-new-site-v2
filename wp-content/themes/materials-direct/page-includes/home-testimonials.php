<section class="testimonials">
    <div class="container testimonials__container">
        <h2 class="testimonials__heading">Testimonials</h2>
        <h3 class="testimonials__subheading">A few of our very satisfied customers</h3>
        <div class="testimonials__carousel owl-carousel owl-theme">
            <?php if (have_rows('testimonials')) : ?>
                
                    <?php while (have_rows('testimonials')) : the_row(); ?>
                        <div class="item">
                            <p class="testimonials__quote"><?php the_sub_field('testimonial_quote'); ?></p>
                            <h4 class="testimonials__title"><?php the_sub_field('testimonial_title'); ?></h4>
                        </div>
                    <?php endwhile; ?>
                
            <?php endif; ?> 
        </div>
    </div>
</section>