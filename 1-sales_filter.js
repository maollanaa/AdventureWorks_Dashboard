$(document).ready(function() {

    // Set initial values
    $('#startDate').val('2011-05');
    $('#endDate').val('2014-06');

    // Validate start date
    $('#startDate').on('change', function() {
        var startDate = $(this).val();
        var endDate = $('#endDate').val();
        
        // Ensure start date isn't after end date
        if (startDate > endDate) {
            $(this).val(endDate);
        }
        
        // Ensure start date isn't before May 2011
        if (startDate < '2011-05') {
            $(this).val('2011-05');
        }
    });

    // Validate end date
    $('#endDate').on('change', function() {
        var startDate = $('#startDate').val();
        var endDate = $(this).val();
        
        // Ensure end date isn't before start date
        if (endDate < startDate) {
            $(this).val(startDate);
        }
        
        // Ensure end date isn't after June 2014
        if (endDate > '2014-06') {
            $(this).val('2014-06');
        }
    });

    // Handle form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        var formData = $(this).serialize();
        
        // Send AJAX request
        $.ajax({
            url: '1-sales_filter-data.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Update dashboard cards
                updateDashboardCards(response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    
    // Function to update dashboard cards
    function updateDashboardCards(data) {
        // Update Total Revenue
        $('.text-primary.text-uppercase').next('.h5').text('$' + numberFormat(data.total_revenue));
        
        // Update Total Orders
        $('.text-success.text-uppercase').next('.h5').text(numberFormat(data.total_orders));
        
        // Update Total Customers
        $('.text-info.text-uppercase').next('.h5').text(numberFormat(data.total_customers));
        
        // Update Average Order Value
        $('.text-warning.text-uppercase').next('.h5').text('$' + numberFormat(data.avg_order_value));
    }
    
    // Helper function to format numbers
    function numberFormat(number) {
        return new Intl.NumberFormat('en-US', { 
            minimumFractionDigits: 2,
            maximumFractionDigits: 2 
        }).format(number);
    }
});
