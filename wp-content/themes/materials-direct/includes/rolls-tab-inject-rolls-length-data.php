<?php
add_action( 'wp_head', 'get_the_roll_length_value_form_insert' );

function get_the_roll_length_value_form_insert() {

    if ( ! is_product() ) {
        return;
    }

    $ah_roll_length = (float) get_field('roll_length');
?>

<script type="text/javascript">
jQuery(document).ready(function($) {

    let rollLength = <?php echo json_encode($ah_roll_length); ?>;
    let rollLengthCalc = rollLength / 1000;

    let originalText = null;

    function getOriginalStock() {
        if (!originalText) {
            originalText = $('.live-stock-wrapper .in-stock').text();
        }
        return originalText;
    }

    $('input[name="tabs_input"]').on('change', function() {

        var $stockEl = $('.live-stock-wrapper .in-stock');
        var selectedVal = $(this).val();

        let baseText = getOriginalStock();
        let number = parseFloat(baseText);

        // 👉 ROLLS SELECTED
        if (selectedVal === 'rolls') {

            if (!isNaN(number) && rollLengthCalc > 0) {

                var newNumber = number / rollLengthCalc;

                if (!Number.isInteger(newNumber)) {
                    newNumber = Math.floor(newNumber);
                }

                var label = newNumber == 1 ? 'Roll' : 'Rolls In Stock';
                $stockEl.text(newNumber + ' ' + label);
            }

            // Add the roll length display (only once)
            if ($('.rollsLengthInput').length === 0) { 
                $('#cont_length_mm .product-page__rolls-label-text-1').after(
                    `<div class="rollsLengthInput">
                        ${rollLengthCalc}
                    </div>`
                );
            }

        } else {
            // 👉 ANY OTHER TAB → RESET BACK
            $stockEl.text(baseText);
        }

    });

});
</script>

<?php
}