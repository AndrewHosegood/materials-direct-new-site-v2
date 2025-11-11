 $(function() {
    // Get today's date
    const today = new Date();

    // Calculate the minimum acceptable date (30 days from now)
    const minDate = new Date();
    minDate.setDate(today.getDate() + 30);

    // Helper function to format date in "8th December 2025" format
    function formatDateWithSuffix(date) {
        const day = date.getDate();
        const suffix = (day % 10 === 1 && day !== 11) ? "st" :
                    (day % 10 === 2 && day !== 12) ? "nd" :
                    (day % 10 === 3 && day !== 13) ? "rd" : "th";
        const month = date.toLocaleString('default', { month: 'long' });
        const year = date.getFullYear();
        return `${day}${suffix} ${month} ${year}`;
    }

    const minDateFormatted = formatDateWithSuffix(minDate);

    // Initialize datepicker
    $("#delivery_date").datepicker({
        dateFormat: "dd-mm-yy",
        minDate: 1, // Prevent selecting past dates
        maxDate: "+1Y",
        appendTo: '.delivery-options-modal',
        onSelect: function(selectedDate) {
            const selected = new Date(selectedDate.split('-').reverse().join('-')); // convert dd-mm-yy → yyyy-mm-dd

            if (selected < minDate) {
                alert(`This product is on backorder. Please choose a date after ${minDateFormatted}.`);
                $(this).val(''); // clear the input
            }
        }
    });
});
