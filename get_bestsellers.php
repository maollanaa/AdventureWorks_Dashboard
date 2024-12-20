<?php
include 'db-aw_sales.php';

// Tambahkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Query untuk mendapatkan kategori terlaris
$query = "SELECT 
            dim_product.CategoryName, 
            ROUND(SUM(fact_sales.SalesAmount), 2) AS TotalSales,
            ROUND(SUM(fact_sales.SalesAmount) / (SELECT SUM(SalesAmount) FROM fact_sales) * 100, 2) AS SalesPercentage
          FROM fact_sales 
          JOIN dim_product ON fact_sales.ProductID = dim_product.ProductID
          GROUP BY dim_product.CategoryName
          ORDER BY TotalSales DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$categories = [];
$sales = [];
$percentages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['CategoryName'];
    $sales[] = $row['TotalSales'];
    $percentages[] = $row['SalesPercentage'];
}

// Siapkan data untuk response JSON
$response = [
    'categories' => $categories,
    'sales' => $sales,
    'percentages' => $percentages
];

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>