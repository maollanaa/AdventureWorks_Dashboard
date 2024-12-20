<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard AdventureWorks</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include '0-sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Detail Penjualan</h1>
                        <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->

                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <form id="filterForm" class="row g-3">
                                        <!-- Product Category Filter -->
                                        <div class="col-md-3">
                                            <label class="form-label">Kategori Produk</label>
                                            <select class="form-control" id="categoryFilter" name="category">
                                                <option value="">Semua Kategori</option>
                                                <?php
                                                include 'db-aw_sales.php';
                                                $query = "SELECT DISTINCT CategoryName FROM dim_product ORDER BY CategoryName";
                                                $result = mysqli_query($conn, $query);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<option value='" . htmlspecialchars($row['CategoryName']) . "'>" . htmlspecialchars($row['CategoryName']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Date Range Filters -->
                                        <div class="col-md-3">
                                            <label class="form-label">Dari Bulan</label>
                                            <input type="month"
                                                class="form-control"
                                                id="startDate"
                                                name="startDate"
                                                min="2011-05"
                                                max="2014-06"
                                                value="2011-05">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Sampai Bulan</label>
                                            <input type="month"
                                                class="form-control"
                                                id="endDate"
                                                name="endDate"
                                                min="2011-05"
                                                max="2014-06"
                                                value="2014-06">
                                        </div>

                                        <!-- Territory Filter -->
                                        <div class="col-md-2">
                                            <label class="form-label">Territory Group</label>
                                            <select class="form-control" id="territoryFilter" name="territory">
                                                <option value="">Semua Territory</option>
                                                <?php
                                                include 'db-aw_sales.php';
                                                $query = "SELECT DISTINCT `Group` FROM dim_territory ORDER BY `Group`";
                                                $result = mysqli_query($conn, $query);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<option value='" . htmlspecialchars($row['Group']) . "'>" . htmlspecialchars($row['Group']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Apply Filter Button -->
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary form-control">Apply</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Include database connection
                    include 'db-aw_sales.php';

                    $query_total_revenue = "SELECT SUM(SalesAmount) AS total_revenue FROM fact_sales";
                    $result_total_revenue = mysqli_query($conn, $query_total_revenue);
                    $total_revenue = mysqli_fetch_assoc($result_total_revenue)['total_revenue'];

                    $query_total_orders = "SELECT COUNT(SalesOrderID) AS total_orders FROM fact_sales";
                    $result_total_orders = mysqli_query($conn, $query_total_orders);
                    $total_orders = mysqli_fetch_assoc($result_total_orders)['total_orders'];

                    $query_total_customers = "SELECT COUNT(DISTINCT CustomerID) AS total_customers FROM fact_sales";
                    $result_total_customers = mysqli_query($conn, $query_total_customers);
                    $total_customers = mysqli_fetch_assoc($result_total_customers)['total_customers'];

                    $query_aov = "SELECT SUM(SalesAmount) / COUNT(SalesOrderID) AS avg_order_value FROM fact_sales";
                    $result_aov = mysqli_query($conn, $query_aov);
                    $avg_order_value = mysqli_fetch_assoc($result_aov)['avg_order_value']

                    ?>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Total Revenue Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Penjualan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                $<?php echo number_format($total_revenue, 2); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Orders Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Pesanan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($total_orders); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Customers Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Pelanggan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($total_customers); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Average Order Value Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Rata-rata Penjualan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                $<?php echo number_format($avg_order_value, 2); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Line Chart -->
                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Tren Penjualan</h6>
                                    <div class="dropdown no-arrow">
                                        <select id="periodFilter" class="form-control">
                                            <option value="year">Per Tahun</option>
                                            <option value="quarter">Per Kuartal</option>
                                            <option value="month">Per Bulan</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Sales Territories</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Pastikan ada canvas dengan ID yang benar -->
                                    <canvas id="territoryChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Produk Terjual</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="productsChart"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>FP DWO &copy; AdventureWorks2019</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="chart-sales-trend2.js"></script>
    <script src="chart-bestterritory2.js"></script>
    <script src="chart-bestproducts2.js"></script>
    <script src="1-sales_filter.js"></script>

</body>

</html>