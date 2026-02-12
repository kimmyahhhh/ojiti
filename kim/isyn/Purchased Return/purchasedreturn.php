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

            include_once("../../database/connection.php");
            $db = new Database();
            $conn = $db->conn;

            // Fetch next transaction number
            $sql = "SELECT MAX(CAST(SUBSTRING(TransactionNo, 4) AS UNSIGNED)) as max_num FROM tbl_purchasereturned WHERE TransactionNo LIKE 'PRN%'";
            $result = $conn->query($sql);
            $transactionNo = 'PRN000001';
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $nextNum = ($row['max_num'] ?? 0) + 1;
                $transactionNo = 'PRN' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            }
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

            .form-control, .form-select {
                border-radius: var(--radius-md);
                border: 1px solid #dee2e6;
                padding: 0.6rem 0.8rem;
                font-size: 0.95rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            .form-control:focus, .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            }
            
            .form-control:read-only {
                background-color: #f8f9fa;
                cursor: default;
            }

            /* Table Styling */
            .table-container {
                border-radius: var(--radius-md);
                overflow: hidden;
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

            .clickable-row.selected {
                background-color: #e3f2fd !important;
                position: relative;
            }
            
            .clickable-row.selected::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background-color: var(--primary-color);
            }

            /* Tabs */
            .nav-tabs {
                border-bottom: 2px solid var(--border-color);
                gap: 10px;
            }

            .nav-tabs .nav-link {
                border: none;
                color: var(--text-muted);
                font-weight: 500;
                padding: 10px 20px;
                border-radius: var(--radius-md) var(--radius-md) 0 0;
                transition: all 0.2s;
            }

            .nav-tabs .nav-link:hover {
                color: var(--primary-color);
                background-color: rgba(13, 110, 253, 0.05);
            }

            .nav-tabs .nav-link.active {
                color: var(--primary-color) !important;
                border-bottom: 3px solid var(--primary-color);
                background-color: transparent;
                font-weight: 600;
            }

            /* Buttons */
            .btn {
                border-radius: var(--radius-md);
                padding: 0.5rem 1.2rem;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }
            
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
            }
            
            .btn-primary:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(13, 110, 253, 0.4);
            }

            /* Custom Scrollbar */
            .overflow-auto::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .overflow-auto::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            .overflow-auto::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

            .overflow-auto::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
            
            /* Modal Enhancements */
            .modal-content {
                border-radius: var(--radius-lg);
                border: none;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            
            .modal-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid var(--border-color);
                border-radius: var(--radius-lg) var(--radius-lg) 0 0;
                padding: 1.5rem;
            }
            
            .modal-body {
                padding: 1.5rem;
            }
            
            .modal-title {
                font-weight: 600;
                color: var(--text-color);
            }

            /* Utilities */
            .text-small {
                font-size: 0.85rem;
            }
        </style>

            <div class="container-fluid mt-4">
                <div class="custom-card p-3 mb-4">
                    <div class="card-header-title">
                        <i class="fa-solid fa-undo fs-4"></i> <span class="fs-5">Purchased Return</span>
                    </div>
                </div>

            <!-- Product Details Modal -->
            <div class="modal fade" id="productDetailsModal" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productDetailsModalLabel">Product Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="productDetails" nonvalidate>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="SIno" class="form-label">SI No:</label>
                                            <input type="text" id="SInoDisplay" name="SIno" class="form-control" readonly>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="serialNo" class="form-label">Serial No.:</label>
                                                <input type="text" id="serialNoDisplay" name="serialNo" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="product" class="form-label">Product:</label>
                                                <input type="text" id="productDisplay" name="product" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="supplier" class="form-label">Supplier:</label>
                                                <input type="text" id="supplierDisplay" name="supplier" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="category" class="form-label">Category:</label>
                                                <input type="text" id="categoryDisplay" name="category" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="type" class="form-label">Type:</label>
                                                <input type="text" id="typeDisplay" name="type" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="isyn-branch-product" class="form-label">ISYN branch:</label>
                                                <input type="text" id="branchDisplay" name="isyn-branch-product" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="dealer-price" class="form-label">Dealer price:</label>
                                                <input type="number" id="dealers_priceDisplay" name="dealer-price" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="srp" class="form-label">SRP:</label>
                                                <input type="number" id="srpDisplay" name="srp" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="quantity" class="form-label">Quantity:</label>
                                                <input type="number" id="quantityDisplay" name="quantity" class="form-control" min="1">
                                                <input type="hidden" id="maxQuantityDisplay">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-primary w-100 py-2" onclick="returnSingleItem()">
                                                <i class="fa-solid fa-plus-circle"></i> Save & Add to List
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row">
                    <!-- Search Product -->
                    <div class="col-md-4">
                        <div class="custom-card p-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold text-dark mb-0">Search Product</h5>
                            </div>
                            <div class="section-divider mt-0"></div>
                            
                            <form method="post" nonvalidate id="searchProduct">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="return-type" class="form-label">Return type:</label>
                                            <select id="return-type" name="return-type" class="form-select">
                                                <option value="" selected disabled>Select Return Type</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="isynBranch" class="form-label">ISYN branch:</label>
                                            <select id="isynBranch" name="isynBranch" class="form-select">
                                                <option value="" selected disabled>Select Branch</option>
                                                <?php
                                                $query = "SELECT DISTINCT Branch FROM tbl_invlist ORDER BY Branch";
                                                $query_run = $conn->query($query);
                                                if ($query_run && $query_run->num_rows > 0) {
                                                    while ($row = $query_run->fetch_assoc()) {
                                                ?>
                                                        <option value="<?php echo $row['Branch']; ?>"><?php echo $row['Branch']; ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type:</label>
                                            <select class="form-select" aria-label="type" required name="productType[]" id="type">
                                                <option value="" selected disabled>Select Type</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category:</label>
                                            <select id="category" name="category" class="form-select">
                                                <option value="" selected disabled>Select Category</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="mt-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary w-100" id="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search Products</button>
                            </div>
                        </div>
                    </div>
                    <!-- List of Search Items -->
                    <div class="col-md-8">
                        <div class="custom-card p-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0">Product List</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="listActionBtn">
                                    <i class="fa-solid fa-eye"></i> View Details
                                </button>
                            </div>
                            <div class="section-divider mt-0"></div>
                            
                            <div class="table-container overflow-auto" style="height: 400px; max-height: 400px;">
                                <table class="table table-hover" id="searchtable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%">Product</th>
                                            <th style="width: 20%">SI No.</th>
                                            <th style="width: 25%">Serial No.</th>
                                            <th style="width: 15%">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableList">
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-2">
                                <small class="text-muted fst-italic">Select a row to view details</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Product List of Return -->
                    <div class="col-md-12">
                        <div class="custom-card p-4 mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold text-dark mb-0">Items to Return</h5>
                            </div>
                            
                            <ul class="nav nav-tabs mb-4" id="returnTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="to-return-tab" data-bs-toggle="tab" data-bs-target="#to-return-pane" type="button" role="tab" aria-controls="to-return-pane" aria-selected="true"><i class="fa-solid fa-clipboard-list me-2"></i>To Return</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned-pane" type="button" role="tab" aria-controls="returned-pane" aria-selected="false"><i class="fa-solid fa-box-archive me-2"></i>Returned History</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="returnTabsContent">
                                <div class="tab-pane fade show active" id="to-return-pane" role="tabpanel" aria-labelledby="to-return-tab">
                                    <div class="table-container overflow-auto" style="height: 500px; max-height: 500px;">
                                        <table class="table table-hover" id="returnTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 38%">Product</th>
                                                    <th style="width: 15%">SI No.</th>
                                                    <th style="width: 20%">Serial No.</th>
                                                    <th style="width: 12%">Quantity</th>
                                                    <th style="width: 15%">Return Type</th>
                                                </tr>
                                            </thead>
                                            <tbody id="returnList">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="returned-pane" role="tabpanel" aria-labelledby="returned-tab">
                                    <div class="table-container overflow-auto" style="height: 500px; max-height: 500px;">
                                        <table class="table table-hover" id="archivedTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 38%">Product</th>
                                                    <th style="width: 15%">SI No.</th>
                                                    <th style="width: 20%">Serial No.</th>
                                                    <th style="width: 12%">Quantity</th>
                                                    <th style="width: 15%">Return Type</th>
                                                </tr>
                                            </thead>
                                            <tbody id="archivedList">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="section-divider mt-4 mb-3"></div>
                            
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Transaction ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-hashtag text-muted"></i></span>
                                        <input class="form-control border-start-0 ps-0" id="returnReceiptID" value="<?php echo $transactionNo; ?>" disabled style="background-color: #f8f9fa; font-weight: 600; letter-spacing: 1px;">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Filter Category</label>
                                    <select id="printCategoryFilter" class="form-select">
                                        <option value="All">All Categories</option>
                                        <?php
                                        // Get all unique categories
                                        $sql = "SELECT DISTINCT Category FROM tbl_purchasereturned WHERE (Status IS NULL OR Status != 'Archived') ORDER BY Category ASC";
                                        $result = $conn->query($sql);
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row['Category']) . '">' . htmlspecialchars($row['Category']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-success text-white" id="archiveBtn" onclick="archiveVisibleItems()"><i class="fa-solid fa-box-archive"></i> Process Return</button>
                                        <button type="button" class="btn btn-dark text-white" id="printBtn" onclick="printData()" style="display:none;"><i class="fa-solid fa-print"></i> Print</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <!-- <script src="../../js/inventorymanagement/outgoinginventory.js?<?= time() ?>"></script> -->
        <script src="../../js/inventorymanagement/purchasedreturned_maintenance.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>

<script>
    $(document).ready(function() {
        // Add click event for View Details button
        $('#listActionBtn').click(function() {
            // Check if there's a selected row
            var selectedRow = $('.clickable-row.selected');
            if (selectedRow.length > 0) {
                var selectedSIno = selectedRow.find('.SInoSelect').text();
                var selectedSerialNo = selectedRow.find('.SerialnoSelected').text();
                var selectedProduct = selectedRow.find('.ProductSelected').text();
                
                // Fetch product summary and display in Product Details
                $.ajax({
                    type: 'POST',
                    url: './ajax-inventory/product-summary.php',
                    data: {
                        SIno: selectedSIno,
                        Serialno: selectedSerialNo,
                        Product: selectedProduct
                    },
                    dataType: 'json',
                    success: function(productSummary) {
                        console.log(productSummary);
                        if (productSummary.error) {
                            alert(productSummary.error);
                        } else {
                            updateProductSummary(productSummary);
                            var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                            modal.show();
                        }
                    },
                    error: function() {
                        alert('Error fetching product summary');
                    }
                });
            } else {
                alert('Please select a product from the list first');
            }
        });

        $('#search-btn').click(function() {
            var branch = $('#isynBranch').val();
            var type = $('#type').val();
            var category = $('#category').val();

            $.ajax({
                method: 'POST',
                url: './ajax-inventory/product-search.php',
                data: {
                    branch: branch,
                    type: type,
                    category: category
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    // Clear previous results
                    $('#tableList').empty();

                    // Check if response contains data
                    if (response.length > 0) {
                        // Iterate through each object in the array
                        $.each(response, function(index, item) {
                            var row = '<tr class="clickable-row" id="doubleclickrow">' +
                                '<td class="ProductSelected">' + item.Product + '</td>' +
                                '<td class="SInoSelect">' + item.SIno + '</td>' +
                                '<td class="SerialnoSelected">' + item.Serialno + '</td>' +
                                '<td>' + item.Quantity + '</td>' +
                                '</tr>';
                            $('#tableList').append(row);
                        });

                        // Register click event for dynamically created rows
                        $('.clickable-row').click(function() {
                            // Remove previous selection
                            $('.clickable-row').removeClass('selected');
                            
                            // Add selection to clicked row
                            $(this).addClass('selected');
                            
                            var selectedSIno = $(this).find('.SInoSelect').text(); // Get SIno from clicked row
                            var selectedSerialNo = $(this).find('.SerialnoSelected').text();
                            var selectedProduct = $(this).find('.ProductSelected').text();
                            console.log('Selected SIno:', selectedSIno);
                            console.log('Selected Serial No.:', selectedSerialNo);
                            console.log('Selected Product:', selectedProduct);

                            // Ajax call to fetch product summary
                            $.ajax({
                                type: 'POST',
                                url: './ajax-inventory/product-summary.php',
                                data: {
                                    SIno: selectedSIno,
                                    Serialno: selectedSerialNo,
                                    Product: selectedProduct
                                },
                                dataType: 'json',
                                success: function(productSummary) {
                                    console.log(productSummary);
                                    if (productSummary.error) {
                                        alert(productSummary.error);
                                    } else {
                                        updateProductSummary(productSummary);
                                        var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                                        modal.show();
                                    }
                                },
                                error: function() {
                                    alert('Error fetching product summary');
                                }
                            });
                        });
                    } else {
                        $('#tableList').html('<tr><td colspan="4">No products found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: 'error',
                        title: 'Product not found'
                    });
                }
            });
        });

        function updateProductSummary(productSummary) {
            $('#serialNoDisplay').val(productSummary.Serialno);
            $('#SInoDisplay').val(productSummary.SIno);
            $('#productDisplay').val(productSummary.product);
            $('#supplierDisplay').val(productSummary.Supplier);
            $('#categoryDisplay').val(productSummary.Category);
            $('#typeDisplay').val(productSummary.Type);
            $('#branchDisplay').val(productSummary.Branch);
            $('#dealers_priceDisplay').val(productSummary.DealerPrice);
            $('#srpDisplay').val(productSummary.SRP);
            $('#quantityDisplay').val(productSummary.Quantity);
            $('#maxQuantityDisplay').val(productSummary.Quantity);
            $('#quantityDisplay').attr('max', productSummary.Quantity);
        }
    });


    /*
    //for opening quantity modal
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('searchtable');
        const modal = document.getElementById('quantityModal');
        const closeBtn = document.querySelector('.close');
        const modalText = document.getElementById('modal-text');

        // Handle double-click on table rows
        table.addEventListener('dblclick', (event) => {
            const targetRow = event.target.closest('tr');
            if (targetRow) {
                const cells = targetRow.getElementsByTagName('td');
                let rowData = '';
                for (let cell of cells) {
                    rowData += cell.innerText + ' ';
                }
                //modalText.innerText = rowData.trim();
                //modal.style.display = 'block';
            }
        });

        // Close the modal
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Close the modal when clicking outside of the modal
        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    */


    /*
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('searchtable');
        const modal = new bootstrap.Modal(document.getElementById('quantityModal'));


        // Handle double-click on table rows
        table.addEventListener('dblclick', (event) => {
            const targetRow = event.target.closest('tr');
            if (targetRow) {
                const cells = targetRow.getElementsByTagName('td');
                let rowData = '';
                for (let cell of cells) {
                    rowData += cell.innerText + ' ';
                }
                modal.show();
            }
        });
    });
    */


    function clearField() {
        document.getElementById("quantityInput").value = "";
        $('quantityModal').modal('hide');
    }

    function saveDataToTable() {
        var quantity = document.getElementById('quantityInput').value;
        var product = document.getElementById("productDisplay").value;
        var sino = document.getElementById("SInoDisplay").value;
        var serialno = document.getElementById("serialNoDisplay").value;
        var returntype = document.getElementById("return-type").value;
        var dealerPrice = document.getElementById("dealers_priceDisplay").value;
        var srp = document.getElementById("srpDisplay").value;
        var branch = document.getElementById("branchDisplay").value;

        // Calculate totals
        var totalDealerPrice = (parseFloat(dealerPrice) || 0) * (parseInt(quantity) || 0);
        var totalSRP = (parseFloat(srp) || 0) * (parseInt(quantity) || 0);

        var newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td>${product}</td>
            <td>${sino}</td>
            <td>${serialno}</td>
            <td>${quantity}</td>
            <td>${returntype}</td>
            <td style="display:none;">${dealerPrice}</td>
            <td style="display:none;">${srp}</td>
            <td style="display:none;">${branch}</td>
            <td style="display:none;">${totalDealerPrice}</td>
            <td style="display:none;">${totalSRP}</td>
        `;

        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        tableBody.appendChild(newRow);
    }

    //return btn function
    function submitData() {
        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        var dataArray = [];

        tableBody.querySelectorAll("tr").forEach(function(row) {
            // Skip rows that are already saved
            if (row.getAttribute('data-saved') === 'true') {
                return;
            }

            var cells = row.querySelectorAll("td");
            var data = {
                Product: cells[0] ? cells[0].innerText || '' : '',
                SIno: cells[1] ? cells[1].innerText || '' : '',
                SerialNo: cells[2] ? cells[2].innerText || '' : '',
                Quantity: cells[3] ? cells[3].innerText || '' : '',
                TransactionType: cells[4] ? cells[4].innerText || '' : '',
                DealerPrice: cells[5] ? cells[5].innerText || '' : '',
                SRP: cells[6] ? cells[6].innerText || '' : '',
                Branch: cells[7] ? cells[7].innerText || '' : '',
                TotalPrice: cells[8] ? cells[8].innerText || '' : '',
                TotalSRP: cells[9] ? cells[9].innerText || '' : '',
                TransactionNo: document.getElementById("returnReceiptID").value,
                Type: document.getElementById("type").value,
                Category: document.getElementById("category").value,
                Supplier: document.getElementById("supplierDisplay").value,
            };
            dataArray.push(data);
            console.log(data);
        });

        if (dataArray.length === 0) {
            // If all items are already saved, just reload or show message
            // But we should probably check if there were any items at all
            if (tableBody.querySelectorAll("tr").length > 0) {
                // All items were already saved
                 Swal.fire({
                    icon: "info",
                    title: "Items already saved",
                    text: "All items in the list have already been returned."
                });
                setTimeout(() => { location.reload(); }, 2000);
            } else {
                 Swal.fire({
                    icon: "warning",
                    title: "No items",
                    text: "Please add items to return."
                });
            }
            return;
        }

        // Ask for Reason
        Swal.fire({
            title: 'Reason for Return',
            input: 'textarea',
            inputLabel: 'Please enter the reason for returning these items',
            inputPlaceholder: 'Type your reason here...',
            inputAttributes: {
                'aria-label': 'Type your reason here'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit Return',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('You need to write a reason!')
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var reason = result.value;
                
                // Add reason to each item
                dataArray.forEach(function(item) {
                    item.Reason = reason;
                });

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./ajax-inventory/submit-purchase-return.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            console.log(xhr.responseText);
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: "success",
                                title: "Successfully saved!",
                            });
                            
                            
                            // Reload data from database
                            loadReturnedItems();

                            // Clear search inputs/results but keep return table
                            var tableBody2 = document.getElementById("searchtable").querySelector("tbody");
                            tableBody2.innerHTML = "";
                            document.getElementById("searchProduct").reset();
                            document.getElementById("productDetails").reset();
                        } else {
                            // console.error('Error:', xhr.status, xhr.statusText);
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: 'error',
                                title: 'Error inserting data: ' + (xhr.responseText || xhr.statusText)
                            });
                        }
                    }
                };
                xhr.send(JSON.stringify(dataArray));
            }
        });
    }

    // Function to filter return table based on category
    function filterReturnTable() {
        var selectedCategory = document.getElementById('printCategoryFilter').value;
        // Select rows from both tables or just the active one?
        // Let's filter both so when user switches tab, it's already filtered.
        var returnRows = document.getElementById('returnTable').querySelectorAll('tbody tr');
        var archivedRows = document.getElementById('archivedTable').querySelectorAll('tbody tr');
        
        var applyFilter = function(rows) {
            rows.forEach(function(row) {
                var categoryCell = row.querySelector('.item-category');
                if (categoryCell) {
                    var category = categoryCell.textContent;
                    if (selectedCategory === 'All' || category === selectedCategory) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        };

        applyFilter(returnRows);
        applyFilter(archivedRows);
    }

    // Attach event listener to the category filter
    document.addEventListener('DOMContentLoaded', function() {
        var categoryFilter = document.getElementById('printCategoryFilter');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterReturnTable);
        }
        
        // Tab switching logic for print button visibility
        var printBtn = document.getElementById('printBtn');
        var archiveBtn = document.getElementById('archiveBtn');
        var toReturnTab = document.getElementById('to-return-tab');
        var returnedTab = document.getElementById('returned-tab');
        
        if (toReturnTab && returnedTab && printBtn && archiveBtn) {
            toReturnTab.addEventListener('shown.bs.tab', function (e) {
                printBtn.style.display = 'none';
                archiveBtn.style.display = 'block';
            });
            returnedTab.addEventListener('shown.bs.tab', function (e) {
                printBtn.style.display = 'block';
                archiveBtn.style.display = 'none';
            });
        }
    });

    // Function to archive visible items (from To Return list)
    function archiveVisibleItems() {
        var selectedCategory = document.getElementById('printCategoryFilter').value;
        var tableRows = document.getElementById('returnTable').querySelectorAll('tbody tr');
        var idsToArchive = [];

        // Collect IDs of visible rows
        tableRows.forEach(function(row) {
            // Check if row is visible (not hidden by filter)
            if (row.style.display !== 'none') {
                var id = row.getAttribute('data-id');
                if (id) {
                    idsToArchive.push(id);
                }
            }
        });

        if (idsToArchive.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No items',
                text: 'No visible items to return and archive.'
            });
            return;
        }

        Swal.fire({
            title: 'Return & Archive?',
            text: "This will mark the visible items as returned (archived). Continue?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Archive'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send to server to archive
                fetch('./ajax-inventory/process-print-archive.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: idsToArchive }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Reload the table to reflect archived status (items will disappear from To Return)
                        loadReturnedItems();
                        loadArchivedItems(); // Reload archived list to show new items
                        
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: "success",
                            title: "Items archived successfully",
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to process: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    // Function to print data
    function printData() {
        var selectedCategory = document.getElementById('printCategoryFilter').value;
        var tableRows = document.getElementById('archivedTable').querySelectorAll('tbody tr');
        var idsToPrint = [];

        // Collect IDs of visible rows
        tableRows.forEach(function(row) {
            // Check if row is visible (not hidden by filter)
            if (row.style.display !== 'none') {
                var id = row.getAttribute('data-id');
                if (id) {
                    idsToPrint.push(id);
                }
            }
        });

        if (idsToPrint.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No items',
                text: 'No visible archived items to print.'
            });
            return;
        }

        Swal.fire({
            title: 'Print Items?',
            text: "This will print the visible archived items. Continue?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Print'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send to server to prepare print session
                fetch('./ajax-inventory/set-print-session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: idsToPrint }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Open print window in session mode
                        var printUrl = 'print-returned-items.php?mode=session';
                        // Keep category for header info if needed, though session ids determine content
                        if (selectedCategory && selectedCategory !== 'All') {
                            printUrl += '&category=' + encodeURIComponent(selectedCategory);
                        }
                        window.open(printUrl, '_blank');
                        
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: "success",
                            title: "Ready to print",
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to process: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    // Function to archive a single item
    function archiveSingleItem(batchNo) {
        Swal.fire({
            title: 'Archive Item?',
            text: "Do you want to archive this specific item?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('./ajax-inventory/archive-single-return-item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ batchNo: batchNo }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                         Swal.fire({
                            icon: 'success',
                            title: 'Archived!',
                            text: 'Item has been archived.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadReturnedItems(); // Reload table
                        loadArchivedItems();
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to archive: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    //function for archiving data
    function archiveData() {
        var transactionNo = document.getElementById("returnReceiptID").value;
        if (!transactionNo) return;

        Swal.fire({
            title: 'Are you sure?',
            text: "This will archive all returned items for this transaction. They will be hidden from the list but kept in the database.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('./ajax-inventory/archive-return-items.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ transactionNo: transactionNo }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                         Swal.fire(
                            'Archived!',
                            'Your returned items have been archived.',
                            'success'
                        );
                        loadReturnedItems(); // Reload table (will exclude archived items)
                        loadArchivedItems();
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to archive: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    function loadReturnedItems() {
        // Fetch all data as requested
        fetch(`./ajax-inventory/fetch-return-items.php`)
            .then(response => response.json())
            .then(data => {
                var tableBody = document.getElementById("returnTable").querySelector("tbody");
                tableBody.innerHTML = ""; // Clear current view

                if (data.length > 0) {
                    data.forEach(item => {
                        var newRow = document.createElement("tr");
                        newRow.setAttribute("data-saved", "true");
                        // Add pointer cursor to indicate clickability - Removed as per request
                        // newRow.style.cursor = "pointer";
                        
                        // Robust way to find the Batch ID regardless of case or hidden characters
                        var batchId = item.Batchno || item.BatchNo || item.batchno || item.id || item.ID;
                        if (batchId === undefined || batchId === null) {
                            var key = Object.keys(item).find(k => k.toLowerCase() === 'batchno');
                            if (key) batchId = item[key];
                        }
                        
                        // Set data-id for easier access
                        newRow.setAttribute("data-id", batchId);
                        
                        // Click to archive removed as per request
                        // newRow.onclick = function() { archiveSingleItem(batchId); };

                        newRow.innerHTML = `
                            <td>${item.Product}</td>
                            <td>${item.SIno}</td>
                            <td>${item.Serialno}</td>
                            <td>${item.Quantity}</td>
                            <td>${item.Type}</td>
                            <td style="display:none;">${item.DealerPrice}</td>
                            <td style="display:none;">${item.SRP}</td>
                            <td style="display:none;">${item.Branch}</td>
                            <td style="display:none;">${item.TotalPrice}</td>
                            <td style="display:none;">${item.TotalSRP}</td>
                            <td style="display:none;" class="item-category">${item.Category}</td>
                        `;
                        tableBody.appendChild(newRow);
                    });
                    
                    // Apply filter if one is selected
                    if (typeof filterReturnTable === 'function') {
                        filterReturnTable();
                    }
                }
            })
            .catch(error => console.error('Error loading items:', error));
    }

    function loadArchivedItems() {
        // Fetch archived data
        fetch(`./ajax-inventory/fetch-archived-items.php`)
            .then(response => response.json())
            .then(data => {
                var tableBody = document.getElementById("archivedTable").querySelector("tbody");
                tableBody.innerHTML = ""; // Clear current view

                if (data.length > 0) {
                    data.forEach(item => {
                        var newRow = document.createElement("tr");
                        
                        // Robust way to find the Batch ID regardless of case or hidden characters
                        var batchId = item.Batchno || item.BatchNo || item.batchno || item.id || item.ID;
                        if (batchId === undefined || batchId === null) {
                            var key = Object.keys(item).find(k => k.toLowerCase() === 'batchno');
                            if (key) batchId = item[key];
                        }
                        
                        // Set data-id for easier access
                        newRow.setAttribute("data-id", batchId);

                        newRow.innerHTML = `
                            <td>${item.Product}</td>
                            <td>${item.SIno}</td>
                            <td>${item.Serialno}</td>
                            <td>${item.Quantity}</td>
                            <td>${item.Type}</td>
                            <td style="display:none;">${item.DealerPrice}</td>
                            <td style="display:none;">${item.SRP}</td>
                            <td style="display:none;">${item.Branch}</td>
                            <td style="display:none;">${item.TotalPrice}</td>
                            <td style="display:none;">${item.TotalSRP}</td>
                            <td style="display:none;" class="item-category">${item.Category}</td>
                        `;
                        tableBody.appendChild(newRow);
                    });
                    
                    // Apply filter if one is selected
                    if (typeof filterReturnTable === 'function') {
                        filterReturnTable();
                    }
                }
            })
            .catch(error => console.error('Error loading archived items:', error));
    }
    
    // Load items on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadReturnedItems();
        loadArchivedItems();
        loadAllProducts(); // Load all products into the List container
    });

    function loadAllProducts() {
        // Load all products from database to display in the List container
        $.ajax({
            method: 'POST',
            url: './ajax-inventory/load-all-products.php',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                
                // Clear previous results
                $('#tableList').empty();

                // Check if response contains data
                if (response.length > 0) {
                    // Iterate through each object in the array
                    $.each(response, function(index, item) {
                        var row = '<tr class="clickable-row" id="doubleclickrow">' +
                            '<td class="ProductSelected">' + item.Product + '</td>' +
                            '<td class="SInoSelect">' + item.SIno + '</td>' +
                            '<td class="SerialnoSelected">' + item.Serialno + '</td>' +
                            '<td>' + item.Quantity + '</td>' +
                            '</tr>';
                        $('#tableList').append(row);
                    });

                    // Register click event for dynamically created rows
                    $('.clickable-row').click(function() {
                        var selectedSIno = $(this).find('.SInoSelect').text(); // Get SIno from clicked row
                        var selectedSerialNo = $(this).find('.SerialnoSelected').text();
                        var selectedProduct = $(this).find('.ProductSelected').text();
                        console.log('Selected SIno:', selectedSIno);
                        console.log('Selected Serial No.:', selectedSerialNo);
                        console.log('Selected Product:', selectedProduct);

                        // Ajax call to fetch product summary
                        $.ajax({
                            type: 'POST',
                            url: './ajax-inventory/product-summary.php',
                            data: {
                                SIno: selectedSIno,
                                Serialno: selectedSerialNo,
                                Product: selectedProduct
                            },
                            dataType: 'json',
                            success: function(productSummary) {
                                console.log(productSummary);
                                if (productSummary.error) {
                                    alert(productSummary.error);
                                } else {
                                    updateProductSummary(productSummary);
                                    // Show the Product Details modal
                                    var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                                    modal.show();
                                }
                            },
                            error: function() {
                                alert('Error fetching product summary');
                            }
                        });
                    });
                } else {
                    $('#tableList').html('<tr><td colspan="4">No products found</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                $('#tableList').html('<tr><td colspan="4">Error loading products</td></tr>');
            }
        });
    }

    function showProductDetails(SIno, Serialno, Product) {
        // Fetch product summary and display in the Product Details container
        $.ajax({
            type: 'POST',
            url: './ajax-inventory/product-summary.php',
            data: {
                SIno: SIno,
                Serialno: Serialno,
                Product: Product
            },
            dataType: 'json',
            success: function(productSummary) {
                console.log(productSummary);
                if (productSummary.error) {
                    alert(productSummary.error);
                } else {
                    updateProductSummary(productSummary);
                    // Show the Product Details modal
                    var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                    modal.show();
                }
            },
            error: function() {
                alert('Error fetching product summary');
            }
        });
    }

    function returnSingleItem() {
        var product = document.getElementById("productDisplay").value;
        var sino = document.getElementById("SInoDisplay").value;
        var serialno = document.getElementById("serialNoDisplay").value;
        var quantity = document.getElementById("quantityDisplay").value;
        var maxQuantity = document.getElementById("maxQuantityDisplay").value;
        var returntype = document.getElementById("return-type").value;
        var dealerPrice = document.getElementById("dealers_priceDisplay").value;
        var srp = document.getElementById("srpDisplay").value;
        var branch = document.getElementById("branchDisplay").value;
        var type = document.getElementById("type").value;
        var category = document.getElementById("category").value;
        var supplier = document.getElementById("supplierDisplay").value;
        var transactionNo = document.getElementById("returnReceiptID").value;

        if (!product || !sino) {
            Swal.fire({
                icon: "warning",
                title: "Missing Information",
                text: "Please select a product first."
            });
            return;
        }

        if (!returntype) {
            Swal.fire({
                icon: "warning",
                title: "Missing Information",
                text: "Please select a Return Type."
            });
            return;
        }

        if (parseInt(quantity) <= 0) {
             Swal.fire({
                icon: "warning",
                title: "Invalid Quantity",
                text: "Quantity must be greater than 0."
            });
            return;
        }

        if (parseInt(quantity) > parseInt(maxQuantity)) {
             Swal.fire({
                icon: "warning",
                title: "Invalid Quantity",
                text: "Quantity cannot exceed the available quantity (" + maxQuantity + ")."
            });
            return;
        }

        // Ask for Reason
        Swal.fire({
            title: 'Reason for Return',
            input: 'textarea',
            inputLabel: 'Please enter the reason for returning this item',
            inputPlaceholder: 'Type your reason here...',
            inputAttributes: {
                'aria-label': 'Type your reason here'
            },
            showCancelButton: true,
            confirmButtonText: 'Save & Return',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('You need to write a reason!')
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var reason = result.value;

                // Calculate totals
                var totalDealerPrice = (parseFloat(dealerPrice) || 0) * (parseInt(quantity) || 0);
                var totalSRP = (parseFloat(srp) || 0) * (parseInt(quantity) || 0);

                var data = [{
                    Product: product,
                    SIno: sino,
                    SerialNo: serialno,
                    Quantity: quantity,
                    TransactionType: returntype,
                    DealerPrice: dealerPrice,
                    SRP: srp,
                    Branch: branch,
                    TotalPrice: totalDealerPrice,
                    TotalSRP: totalSRP,
                    TransactionNo: transactionNo,
                    Type: type,
                    Category: category,
                    Supplier: supplier,
                    Reason: reason
                }];

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./ajax-inventory/submit-purchase-return.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            
                            // Reload data from database
                            loadReturnedItems();
                            
                            // Close the modal
                            var modalEl = document.getElementById('productDetailsModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }

                            Swal.fire({
                                icon: "success",
                                title: "Returned & Saved",
                                text: "Item has been successfully returned and saved to database.",
                                timer: 1500
                            });
                            
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to save: ' + (xhr.responseText || xhr.statusText)
                            });
                        }
                    }
                };
                xhr.send(JSON.stringify(data));
            }
        });
    }
</script>
