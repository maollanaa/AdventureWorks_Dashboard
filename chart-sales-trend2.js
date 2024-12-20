// Konfigurasi Chart.js untuk sales trend
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

// Fungsi untuk mendapatkan data sales trend dari server
function fetchSalesTrendData(period = 'year') {
    // Get filter values
    const category = $('#categoryFilter').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const territory = $('#territoryFilter').val();

    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'get_sales_trend2.php',
            method: 'GET',
            data: {
                period: period,
                category: category,
                startDate: startDate,
                endDate: endDate,
                territory: territory
            },
            dataType: 'json',
            success: function (response) {
                console.log("Fetched data:", response);
                resolve(response);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
                reject(error);
            }
        });
    });
}

// Deklarasi global
var myLineChart = null;

// Fungsi untuk menginisialisasi atau memperbarui chart
async function updateSalesTrendChart(period) {
    try {
        const salesTrendData = await fetchSalesTrendData(period);
        console.log("Sales Trend Data:", salesTrendData); // Debug log

        // Destroy existing chart if it exists
        if (myLineChart) {
            console.log("Destroying existing chart...");
            myLineChart.destroy();
        }

        var ctx = document.getElementById("myAreaChart").getContext("2d");
        console.log("Initializing chart...");

        myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesTrendData.labels,
                datasets: [{
                    label: "Total Penjualan",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: salesTrendData.sales,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: period
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function (value) {
                                return '$' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: {
                    display: true
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function (tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error("Error updating sales trend chart:", error);
    }
}


// Event handler untuk perubahan periode
$(document).ready(function () {
    // Default chart initialization
    updateSalesTrendChart('year');

    // Event listener for period filter
    $('#periodFilter').on('change', function () {
        updateSalesTrendChart($(this).val());
    });

    // Event listener for filter form submission
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        const period = $('#periodFilter').val();
        updateSalesTrendChart(period);
    });
});