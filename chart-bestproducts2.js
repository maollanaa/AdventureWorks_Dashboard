let productsChart = null;

function updateProductsChart() {
    // Get filter values
    const category = $('#categoryFilter').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const territory = $('#territoryFilter').val();

    // Build query parameters
    const params = new URLSearchParams({
        category: category,
        startDate: startDate,
        endDate: endDate,
        territory: territory
    });

    // Function to format numbers
    function formatNumber(value) {
        return value.toLocaleString('en-US');
    }

    console.log('Fetching products data with filters:', Object.fromEntries(params));

    fetch(`get_bestproducts2.php?${params}`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Raw Products data:', data);

            if (!data || data.length === 0) {
                console.error('No data received');
                return;
            }

            // Parse data correctly
            const productNames = data.map(item => item.ProductName);
            const productQuantities = data.map(item => {
                const quantity = parseInt(item.TotalQuantitySold);
                console.log(`Parsing ${item.TotalQuantitySold} to ${quantity}`);
                return quantity;
            });

            console.log('Product Names:', productNames);
            console.log('Product Quantities:', productQuantities);

            const ctx = document.getElementById('productsChart');
            
            if (!ctx) {
                console.error('Canvas element not found');
                return;
            }

            // Destroy existing chart if it exists
            if (productsChart) {
                productsChart.destroy();
            }

            productsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: productNames,
                    datasets: [{
                        label: 'Total Produk Terjual',
                        data: productQuantities,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Quantity Sold'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatNumber(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 5 Products by Quantity Sold'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return formatNumber(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching products data:', error);
            
            const chartContainer = document.getElementById('productsChart').closest('.card-body');
            if (chartContainer) {
                chartContainer.innerHTML = `<p style="color: red;">Error loading chart: ${error.message}</p>`;
            }
        });
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script chart-bestproducts.js loaded');
    updateProductsChart();
});

// Update chart when filter form is submitted
$('#filterForm').on('submit', function(e) {
    e.preventDefault();
    updateProductsChart();
});