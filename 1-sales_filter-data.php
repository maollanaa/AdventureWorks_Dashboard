<?php
include 'db-aw_sales.php';

// Get filter parameters
$category = isset($_POST['category']) ? $_POST['category'] : '';
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$territory = isset($_POST['territory']) ? $_POST['territory'] : '';

// Base query parts
$baseSelect = "
    SELECT 
        SUM(fs.SalesAmount) as total_revenue,
        COUNT(DISTINCT fs.SalesOrderID) as total_orders,
        COUNT(DISTINCT fs.CustomerID) as total_customers,
        SUM(fs.SalesAmount) / COUNT(DISTINCT fs.SalesOrderID) as avg_order_value
    FROM fact_sales fs
    JOIN dim_product dp ON fs.ProductID = dp.ProductID
    JOIN dim_territory dt ON fs.TerritoryID = dt.TerritoryID
";

$whereConditions = [];

// Add filter conditions
if (!empty($category)) {
    $whereConditions[] = "dp.CategoryName = '" . mysqli_real_escape_string($conn, $category) . "'";
}

if (!empty($startDate)) {
    $whereConditions[] = "DATE_FORMAT(fs.OrderDate, '%Y-%m') >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
}

if (!empty($endDate)) {
    $whereConditions[] = "DATE_FORMAT(fs.OrderDate, '%Y-%m') <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
}

if (!empty($territory)) {
    $whereConditions[] = "dt.Group = '" . mysqli_real_escape_string($conn, $territory) . "'";
}

// Combine where conditions
$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = " WHERE " . implode(" AND ", $whereConditions);
}

$finalQuery = $baseSelect . $whereClause;

// Execute query
$result = mysqli_query($conn, $finalQuery);
$data = mysqli_fetch_assoc($result);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($data);
?>