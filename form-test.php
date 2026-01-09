<?php
// Capture POST values safely
$use_inches   = isset($_POST['inches']);
$width_value  = $_POST['custom_width']  ?? '';
$length_value = $_POST['custom_length'] ?? '';
$qty_value    = $_POST['custom_qty']    ?? 1;

// Debug output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Test</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        .product-page__input-wrap {
            display: block;
            margin-bottom: 10px;
        }
        .product-page__input {
            width: 200px;
            padding: 5px;
        }
    </style>
</head>
<body>

<form method="post" id="dimension_form">

    <label class="product-page__input-wrap">
        <input
            type="checkbox"
            id="use_inches"
            name="inches"
            value="1"
            <?php echo $use_inches ? 'checked' : ''; ?>
        >
        Choose Inches
    </label>

    <label class="product-page__input-wrap">
        <span id="width_label">Width (MM)</span>:
        <input
            class="product-page__input"
            type="number"
            id="input_width"
            name="custom_width"
            min="0.01"
            step="0.01"
            required
            value="<?php echo htmlspecialchars($width_value); ?>"
        >
    </label>

    <label class="product-page__input-wrap">
        <span id="length_label">Length (MM)</span>:
        <input
            class="product-page__input"
            type="number"
            id="input_length"
            name="custom_length"
            min="0.01"
            step="0.01"
            required
            value="<?php echo htmlspecialchars($length_value); ?>"
        >
    </label>

    <label class="product-page__input-wrap">
        Total number of parts:
        <input
            class="product-page__input"
            type="number"
            id="input_qty"
            name="custom_qty"
            min="1"
            step="1"
            required
            value="<?php echo htmlspecialchars($qty_value); ?>"
        >
    </label>

    <button type="button" id="generate_price">
        Submit Dimensions
    </button>

</form>

<script>
jQuery(function ($) {

    const INCH_TO_MM = 25.4;

    function updateUI() {

        if ($('#use_inches').is(':checked')) {

            $('#width_label').text('Width (INCHES)');
            $('#length_label').text('Length (INCHES)');

            $('#input_width').attr('placeholder', 'Width Inches');
            $('#input_length').attr('placeholder', 'Length Inches');

        } else {

            $('#width_label').text('Width (MM)');
            $('#length_label').text('Length (MM)');

            $('#input_width').removeAttr('placeholder');
            $('#input_length').removeAttr('placeholder');

        }
    }

    // Run on page load (important after POST)
    updateUI();

    // Handle checkbox toggle
    $('#use_inches').on('change', function () {

        if ($(this).is(':checked')) {
            // Clear values when switching to inches
            $('#input_width').val('');
            $('#input_length').val('');
        }

        updateUI();
    });

    // Convert inches → mm before submit
    $('#generate_price').on('click', function () {

        if ($('#use_inches').is(':checked')) {

            let width  = parseFloat($('#input_width').val());
            let length = parseFloat($('#input_length').val());

            if (!isNaN(width)) {
                $('#input_width').val((width * INCH_TO_MM).toFixed(2));
            }

            if (!isNaN(length)) {
                $('#input_length').val((length * INCH_TO_MM).toFixed(2));
            }
        }

        $('#dimension_form').submit();
    });

});
</script>

</body>
</html>
