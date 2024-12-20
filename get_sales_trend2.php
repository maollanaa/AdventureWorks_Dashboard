<?php
header('Content-Type: application/json');

include 'db-aw_sales.php';

$period = isset($_GET['period']) ? $_GET['period'] : 'year';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
$territory = isset($_GET['territory']) ? $_GET['territory'] : '';

// Build the base query with joins
$baseQuery = "
    FROM fact_sales fs
    JOIN dim_product dp ON fs.ProductID = dp.ProductID
    JOIN dim_territory dt ON fs.TerritoryID = dt.TerritoryID
";

// Build WHERE conditions
$whereConditions = [];

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

$whereClause = !empty($whereConditions) ? " WHERE " . implode(" AND ", $whereConditions) : "";

// Complete query based on period
switch($period) {
    case 'year':
        $query = "
            SELECT 
                YEAR(fs.OrderDate) AS label,
                SUM(fs.SalesAmount) AS total_sales
            " . $baseQuery . $whereClause . "
            GROUP BY 
                YEAR(fs.OrderDate)
            ORDER BY 
                label
        ";
        break;
        
    case 'quarter':
        $query = "
            SELECT 
                CONCAT(YEAR(fs.OrderDate), ' Q', QUARTER(fs.OrderDate)) AS label,
                YEAR(fs.OrderDate) AS year,
                QUARTER(fs.OrderDate) AS quarter,
                SUM(fs.SalesAmount) AS total_sales
            " . $baseQuery . $whereClause . "
            GROUP BY 
                YEAR(fs.OrderDate),
                QUARTER(fs.OrderDate)
            ORDER BY 
                year, quarter
        ";
        break;
        
    case 'month':
        $query = "
            SELECT 
                DATE_FORMAT(fs.OrderDate, '%Y-%m') AS label,
                YEAR(fs.OrderDate) AS year,
                MONTH(fs.OrderDate) AS month,
                SUM(fs.SalesAmount) AS total_sales
            " . $baseQuery . $whereClause . "
            GROUP BY 
                YEAR(fs.OrderDate),
                MONTH(fs.OrderDate)
            ORDER BY 
                year, month
        ";
        break;
}

$result = mysqli_query($conn, $query);

$labels = [];
$sales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['label'];
    $sales[] = round($row['total_sales'], 2);
}

echo json_encode([
    'labels' => $labels,
    'sales' => $sales
]);

mysqli_close($conn);
?>