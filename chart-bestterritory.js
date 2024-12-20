document.addEventListener('DOMContentLoaded', function() {
    function formatCurrency(value) {
        const numValue = Number(value);
        return '$' + numValue.toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
            style: 'decimal'
        });
    }

    console.log('Script chart-bestterritory.js loaded');

    fetch('get_bestterritory.php')
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Raw Territory data:', data);

            if (!data || data.length === 0) {
                console.error('No data received');
                return;
            }

            const territoryNames = data.map(item => item.TerritoryName);
            const salesAmounts = data.map(item => {
                const amount = parseFloat(item.TotalSales);
                console.log(`Parsing ${item.TotalSales} to ${amount}`);
                return amount;
            });

            console.log('Territory Names:', territoryNames);
            console.log('Sales Amounts:', salesAmounts);

            const ctx = document.getElementById('territoryChart');
            
            if (!ctx) {
                console.error('Canvas element not found');
                return;
            }

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: territoryNames,
                    datasets: [{
                        label: 'Total Sales',
                        data: salesAmounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)', // Single color for all bars
                        borderColor: 'rgba(54, 162, 235, 1)',      // Single border color
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
                                text: 'Sales Amount'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 5 Sales Territories'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching territory data:', error);
            
            const chartContainer = document.getElementById('territoryChart').closest('.card-body');
            if (chartContainer) {
                chartContainer.innerHTML = `<p style="color: red;">Error loading chart: ${error.message}</p>`;
            }
        });
});
