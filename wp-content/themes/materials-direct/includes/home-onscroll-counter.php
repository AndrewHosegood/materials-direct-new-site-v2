<?php
// product count onscroll event

function custom_jquery_script() {
    $total_products = wp_count_posts('product')->publish;
    // get product count by this year
    $current_year = date('Y');

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'date_query'     => array(
            array(
                'year' => $current_year,
            ),
        ),
    );

    $query = new WP_Query($args);

    $total_products_this_year = $query->post_count;

    //echo 'Total number of products published this year: ' . $total_products_this_year;

    wp_reset_postdata();
    // get product count by this year
    ?>
    <script>
        jQuery(document).ready(function($) {
            var countersTriggered = {
                'no_of_products': false,
                'counter__products-added': false,
                'counter__satisfied': false,
				'counter__cut-parts': false
            };

            $(window).scroll(function() {
                var scrollPosition = $(this).scrollTop();

                // Check for each counter element and trigger countUp function
                checkAndTriggerCounter('counter__no-of-products', 500, <?php echo $total_products; ?>, scrollPosition);
                checkAndTriggerCounter('counter__products-added', 500, <?php echo $total_products_this_year +200; ?>, scrollPosition);
                checkAndTriggerCounter('counter__satisfied', 500, 10320, scrollPosition);
				checkAndTriggerCounter('counter__cut-parts', 500, 5.87, scrollPosition);
            });

            function checkAndTriggerCounter(counterClass, triggerPosition, initialValue, scrollPosition) {
                if (scrollPosition >= triggerPosition && !countersTriggered[counterClass]) {
                    countersTriggered[counterClass] = true;
                    
                    // Calculate the target value dynamically
                    var targetValue = calculateTargetValue(initialValue);
                    
                    countUp(counterClass, targetValue);
                }
            }

            function countUp(counterClass, targetValue) {
                var initialValue = 0;
                var duration = 3000; // Adjust the duration as needed

				$('.' + counterClass).next('.counter__small').hide();

                $({ countNum: initialValue }).animate({
                    countNum: targetValue
                }, {
                    duration: duration,
                    easing: 'linear',
                    step: function () {
                        $('.' + counterClass).text(Math.floor(this.countNum));
                    },
                    complete: function () {
                        $('.' + counterClass).text(targetValue);
						$('.' + counterClass).next('.counter__small').show();
                    }
                });
            }

            function calculateTargetValue(initialValue) {
                var date = new Date();
                var currentMonth = date.getMonth() + 1; // JavaScript's getMonth() returns zero-based months
                var currentYear = date.getFullYear();

                // Assuming the increment happens on the 1st of every month
                if (currentMonth === 1) { // If it's January, increment the value
                    initialValue += 15;
                }
                
                // Adjust for multiple increments per month if necessary

                return initialValue;
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_jquery_script');

// product count onscroll event