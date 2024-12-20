<?php
include 'db-aw_sales.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
$territory = isset($_GET['territory']) ? $_GET['territory'] : '';

// Base query
$baseQuery = "
    SELECT 
        p.Name AS ProductName, 
        SUM(fs.OrderQty) AS TotalQuantitySold
    FROM 
        fact_sales fs
    JOIN 
        dim_product p ON fs.ProductID = p.ProductID
    JOIN 
        dim_territory dt ON fs.TerritoryID = dt.TerritoryID
";

// Build WHERE conditions
$whereConditions = [];

if (!empty($category)) {
    $whereConditions[] = "p.CategoryName = '" . mysqli_real_escape_string($conn, $category) . "'";
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

// Add WHERE clause if there are conditions
if (!empty($whereConditions)) {
    $baseQuery .= " WHERE " . implode(" AND ", $whereConditions);
}

// Complete the query
$query = $baseQuery . "
    GROUP BY 
        p.ProductID, p.Name
    ORDER BY 
        TotalQuantitySold DESC
    LIMIT 5
";

$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Get data into array
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Output as JSON
header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($conn);
?>