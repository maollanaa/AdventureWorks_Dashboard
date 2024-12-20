<?php
header('Content-Type: application/json');

include 'db-aw_sales.php';

$query = "SELECT 
    t.Name AS TerritoryName, 
    ROUND(SUM(f.SalesAmount), 2) AS TotalSales 
FROM 
    fact_sales f
JOIN 
    dim_territory t ON f.TerritoryID = t.TerritoryID
GROUP BY 
    t.Name
ORDER BY 
    TotalSales DESC
LIMIT 5";

$result = mysqli_query($conn, $query);

$territories = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Pastikan TotalSales adalah angka
    $row['TotalSales'] = floatval($row['TotalSales']);
    $territories[] = $row;
}

echo json_encode($territories);

mysqli_close($conn);
?>