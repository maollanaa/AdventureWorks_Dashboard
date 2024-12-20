<?php
include 'db-aw_sales.php';

// Query untuk mendapatkan top 5 produk terjual berdasarkan total kuantitas
$query = "
    SELECT 
        p.Name AS ProductName, 
        SUM(fs.OrderQty) AS TotalQuantitySold
    FROM 
        fact_sales fs
    JOIN 
        dim_product p ON fs.ProductID = p.ProductID
    GROUP BY 
        p.ProductID, p.Name
    ORDER BY 
        TotalQuantitySold DESC
    LIMIT 5
";

$result = mysqli_query($conn, $query);

// Periksa apakah query berhasil
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Ambil data ke dalam array
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Keluarkan sebagai JSON
header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($conn);
?>