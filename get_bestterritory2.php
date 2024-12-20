<?php
header('Content-Type: application/json');

include 'db-aw_sales.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
$territory = isset($_GET['territory']) ? $_GET['territory'] : '';

// Base query
$baseQuery = "
SELECT 
    t.Name AS TerritoryName, 
    ROUND(SUM(f.SalesAmount), 2) AS TotalSales 
FROM 
    fact_sales f
JOIN 
    dim_territory t ON f.TerritoryID = t.TerritoryID
JOIN 
    dim_product dp ON f.ProductID = dp.ProductID
";

// Build WHERE conditions
$whereConditions = [];

if (!empty($category)) {
    $whereConditions[] = "dp.CategoryName = '" . mysqli_real_escape_string($conn, $category) . "'";
}

if (!empty($startDate)) {
    $whereConditions[] = "DATE_FORMAT(f.OrderDate, '%Y-%m') >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
}

if (!empty($endDate)) {
    $whereConditions[] = "DATE_FORMAT(f.OrderDate, '%Y-%m') <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
}

if (!empty($territory)) {
    $whereConditions[] = "t.Group = '" . mysqli_real_escape_string($conn, $territory) . "'";
}

// Add WHERE clause if there are conditions
if (!empty($whereConditions)) {
    $baseQuery .= " WHERE " . implode(" AND ", $whereConditions);
}

// Complete the query
$query = $baseQuery . "
GROUP BY 
    t.Name
ORDER BY 
    TotalSales DESC
LIMIT 5";

$result = mysqli_query($conn, $query);

$territories = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Ensure TotalSales is a number
    $row['TotalSales'] = floatval($row['TotalSales']);
    $territories[] = $row;
}

echo json_encode($territories);

mysqli_close($conn);
?>