<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
?>

<!doctype html>
<html lang="en" dir="ltr">
    <?php
        include('../../includes/pages.header.php');
    ?>
      <link rel="stylesheet" href="../../assets/datetimepicker/jquery.datetimepicker.css">
      <link rel="stylesheet" href="../../assets/select2/css/select2.min.css">

    <body class="  ">
        <!-- loader Start -->
        <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
        </div>
        <!-- loader END -->

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <style>
                :root {
                    --primary-color: #0d6efd;
                    --bg-color: #f4f6f9;
                    --card-bg: #ffffff;
                    --text-color: #333;
                    --text-muted: #6c757d;
                    --border-color: #e9ecef;
                    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
                    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
                    --radius-md: 10px;
                    --radius-lg: 15px;
                }

                body {
                    background-color: var(--bg-color);
                    color: var(--text-color);
                    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
                }

                /* Card Styling */
                .custom-card {
                    background-color: var(--card-bg);
                    border-radius: var(--radius-lg);
                    box-shadow: var(--shadow-sm);
                    border: 1px solid rgba(0,0,0,0.02);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                }

                .custom-card:hover {
                    box-shadow: var(--shadow-md);
                }

                .card-header-title {
                    color: var(--primary-color);
                    font-weight: 600;
                    font-size: 1.1rem;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 0;
                }

                .section-divider {
                    margin: 1rem 0;
                    border-top: 1px solid var(--border-color);
                    opacity: 0.5;
                }

                /* Form Elements */
                .form-label {
                    font-weight: 500;
                    font-size: 0.9rem;
                    color: var(--text-muted);
                    margin-bottom: 0.4rem;
                }

                .form-control {
                    border-radius: var(--radius-md);
                    border: 1px solid #dee2e6;
                    padding: 0.6rem 0.8rem;
                    font-size: 0.95rem;
                    color: var(--text-color) !important;
                    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                }

                .form-control:focus {
                    border-color: var(--primary-color);
                    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
                }

                .form-control:read-only {
                    background-color: #f8f9fa;
                    cursor: default;
                    border-color: #e9ecef;
                }

                /* Table Styling */
                .table-container {
                    border-radius: var(--radius-md);
                    overflow: hidden;
                    border: 1px solid var(--border-color);
                }

                .table {
                    margin-bottom: 0;
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0;
                }

                .table th {
                    background-color: #f8f9fa;
                    color: #495057;
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 0.8rem;
                    letter-spacing: 0.5px;
                    padding: 12px 16px;
                    border-bottom: 2px solid var(--border-color);
                    position: sticky;
                    top: 0;
                    z-index: 10;
                }

                .table td {
                    padding: 12px 16px;
                    vertical-align: middle;
                    border-bottom: 1px solid var(--border-color);
                    font-size: 0.95rem;
                    color: var(--text-color);
                }

                .table-hover tbody tr:hover {
                    background-color: #f1f8ff;
                    cursor: pointer;
                }

                /* Pagination */
                .custom-pagination {
                    display: flex;
                    justify-content: flex-end;
                    align-items: center;
                    gap: 10px;
                    margin-top: 20px;
                }

                .custom-pagination button {
                    background: white;
                    color: var(--primary-color);
                    border: 1px solid var(--primary-color);
                    padding: 6px 16px;
                    border-radius: var(--radius-md);
                    cursor: pointer;
                    font-size: 14px;
                    transition: all 0.2s;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }

                .custom-pagination button:hover:not(:disabled) {
                    background: var(--primary-color);
                    color: white;
                }

                .custom-pagination button:disabled {
                    background: #f8f9fa;
                    color: #ccc;
                    border-color: #e9ecef;
                    cursor: not-allowed;
                }

                .page-info {
                    font-weight: 600;
                    color: var(--text-muted);
                    font-size: 0.9rem;
                    margin: 0 10px;
                }
                
                /* Hide DataTables elements we don't want */
                .dataTables_length, .dataTables_info, .dataTables_paginate, .dataTables_filter {
                    display: none !important;
                }
            </style>

            <div class="container-fluid mt-4">
                <div class="custom-card p-3 mb-4">
                    <div class="card-header-title">
                        <i class="fa-solid fa-magnifying-glass fs-4"></i> <span class="fs-5">Search Products</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="custom-card p-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0">Product List</h5>
                                <div class="input-group" style="width: 300px;">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa-solid fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="customSearch" placeholder="Search product..." style="border-left: none;">
                                </div>
                            </div>
                            <div class="section-divider mt-0"></div>
                            
                            <div class="table-container">
                                <table id="productTbl" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="10%">Product</th>
                                            <th width="10%">Category</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">SRP</th>
                                            <th width="10%">Total SRP</th>
                                            <th width="10%">Dealer Price</th>
                                            <th width="10%">Total Price</th>
                                            <th width="10%">Warranty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productList">
    
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="custom-pagination">
                                <button id="prevBtn" onclick="previousPage()">
                                    <i class="fa-solid fa-chevron-left"></i> Previous
                                </button>
                                <span class="page-info" id="pageInfo">Page 1</span>
                                <button id="nextBtn" onclick="nextPage()">
                                    Next <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="custom-card p-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold text-dark mb-0">Product Details</h5>
                            </div>
                            <div class="section-divider mt-0"></div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="productName" class="form-label">Product:</label>
                                    <input type="text" id="productName" name="productName" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category:</label>
                                    <input type="text" id="category" name="category" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="warranty" class="form-label">Warranty:</label>
                                    <input type="text" id="warranty" name="warranty" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label">Quantity:</label>
                                    <input type="number" id="quantity" name="quantity" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="SRP" class="form-label">SRP:</label>
                                    <input type="number" id="SRP" name="SRP" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dealerPrice" class="form-label">Dealer Price:</label>
                                    <input type="number" id="dealerPrice" name="dealerPrice" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="totalDP" class="form-label">Total DP:</label>
                                    <input type="number" id="totalDP" name="totalDP" class="form-control" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="totalSRP" class="form-label">Total SRP:</label>
                                    <input type="number" id="totalSRP" name="totalSRP" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/searchproducts.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
