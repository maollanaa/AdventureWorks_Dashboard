// Set default font
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Fungsi untuk membuat Pie Chart
function createCategoryPieChart(categories, sales, percentages) {
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories.map((category, index) =>
                `${category}, ${percentages[index]}%`
            ),
            datasets: [{
                data: sales,
                backgroundColor: [
                    '#4e73df', // Biru
                    '#1cc88a', // Hijau
                    '#36b9cc', // Tosca
                    '#f6c23e', // Kuning
                    '#e74a3b', // Merah
                    '#858796'  // Abu-abu
                ],
                hoverBackgroundColor: [
                    '#2e59d9',
                    '#17a673',
                    '#2c9faf',
                    '#dda20a',
                    '#be2617',
                    '#666666'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 20
                }
            },
            cutoutPercentage: 70,
        },
    });
}

// Ambil data dari server
fetch('get_bestsellers.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data); // Tambahkan ini untuk cek data
        createCategoryPieChart(
            data.categories,
            data.sales,
            data.percentages
        );
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        // Tambahkan pesan error di halaman
        document.getElementById('myPieChart').innerHTML = 'Error loading chart: ' + error.message;
    });

    