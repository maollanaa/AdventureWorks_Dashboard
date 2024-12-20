<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tabel Produk AdventureWorks</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>

<body id="page-top">

    <div id="wrapper">
        <?php include '0-sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Tabel Produk</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Data Produk</h6>
                            <div class="d-flex align-items-center">
                                <label for="filterCategory" class="mr-2 mb-0">Kategori:</label>
                                <select id="filterCategory" class="form-control">
                                    <option value="">Semua</option>
                                    <?php
                                    include 'db-aw_sales.php';
                                    $query = "SELECT DISTINCT CategoryName FROM dim_product";
                                    $result = mysqli_query($conn, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='{$row['CategoryName']}'>{$row['CategoryName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th>Sub Kategori</th>
                                            <th>Harga Jual</th>
                                            <th>Warna</th>
                                            <th>Ukuran</th>
                                            <th>Jumlah Terjual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT dp.ProductID, dp.Name, dp.CategoryName, dp.SubCategoryName, dp.ListPrice, dp.Color, dp.Size, 
                                                  COALESCE(SUM(fs.OrderQty), 0) AS TotalTerjual
                                                  FROM dim_product dp
                                                  LEFT JOIN fact_sales fs ON dp.ProductID = fs.ProductID
                                                  GROUP BY dp.ProductID, dp.Name, dp.CategoryName, dp.SubCategoryName, dp.ListPrice, dp.Color, dp.Size";
                                        $result = mysqli_query($conn, $query);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr data-category='{$row['CategoryName']}'>";
                                                echo "<td>{$row['ProductID']}</td>";
                                                echo "<td>{$row['Name']}</td>";
                                                echo "<td>{$row['CategoryName']}</td>";
                                                echo "<td>{$row['SubCategoryName']}</td>";
                                                echo "<td>\${$row['ListPrice']}</td>";
                                                echo "<td>{$row['Color']}</td>";
                                                echo "<td>{$row['Size']}</td>";
                                                echo "<td>{$row['TotalTerjual']}</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8'>Tidak ada data tersedia</td></tr>";
                                        }
                                        mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            const dataTable = $('#dataTable').DataTable();

            $('#filterCategory').on('change', function() {
                const category = $(this).val();
                dataTable.column(2).search(category).draw();
            });
        });
    </script>
</body>

<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Detail Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="productDetails">Loading...</div>
            </div>
        </div>
    </div>
</div>

</html>