<?php
header('Content-Type: application/json');

include 'db-aw_sales.php';

// Query untuk mengambil total penjualan per tahun
$query = "
    SELECT 
        YEAR(fs.OrderDate) AS year,
        SUM(fs.SalesAmount) AS total_sales
    FROM 
        fact_sales fs
    GROUP BY 
        YEAR(fs.OrderDate)
    ORDER BY 
        year
";

$result = mysqli_query($conn, $query);

$years = [];
$sales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $years[] = $row['year'];
    $sales[] = round($row['total_sales'], 2);
}

echo json_encode([
    'years' => $years,
    'sales' => $sales
]);

mysqli_close($conn);
?>