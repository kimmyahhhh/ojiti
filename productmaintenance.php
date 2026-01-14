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
                    --primary-color: #2563eb;
                    --primary-hover: #1d4ed8;
                    --secondary-color: #64748b;
                    --success-color: #10b981;
                    --danger-color: #ef4444;
                    --warning-color: #f59e0b;
                    --dark-color: #1f2937;
                    --light-bg: #f8fafc;
                    --border-color: #e2e8f0;
                    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
                    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
                }

                body {
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    min-height: 100vh;
                }

                label {
                    color: var(--dark-color);
                    font-weight: 500;
                }

                .form-control, .form-select {
                    color: var(--dark-color) !important;
                    border: 2px solid var(--border-color) !important;
                    border-radius: 8px !important;
                    padding: 0.75rem 1rem !important;
                    font-size: 0.95rem !important;
                    transition: all 0.3s ease !important;
                    background-color: white !important;
                }

                .form-control:focus, .form-select:focus {
                    border-color: var(--primary-color) !important;
                    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
                    outline: none !important;
                }

                .form-control:disabled, .form-select:disabled {
                    background-color: #f1f5f9 !important;
                    opacity: 0.7;
                }

                .form-label {
                    color: var(--dark-color) !important;
                    font-weight: 500 !important;
                    margin-bottom: 0.5rem !important;
                }

                /* Selection box */
                .select2-container--default .select2-selection--single {
                    border: 2px solid var(--border-color) !important;
                    border-radius: 8px !important;
                    height: 46px !important;
                    padding: 0.5rem !important;
                }

                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: var(--dark-color) !important;
                    line-height: 28px !important;
                }

                /* Dropdown */
                .select2-dropdown {
                    border: 2px solid var(--border-color) !important;
                    border-radius: 8px !important;
                    box-shadow: var(--shadow-lg) !important;
                }

                .select2-results__option {
                    color: var(--dark-color) !important;
                    padding: 0.75rem 1rem !important;
                }

                /* Optional: Highlighted option */
                .select2-results__option--highlighted {
                    background-color: var(--primary-color) !important;
                    color: white !important;
                }

                .card-container {
                    background: white;
                    border-radius: 16px;
                    box-shadow: var(--shadow-md);
                    padding: 2rem;
                    margin-bottom: 1.5rem;
                    border: 1px solid var(--border-color);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .card-container:hover {
                    transform: translateY(-2px);
                    box-shadow: var(--shadow-lg);
                }

                .section-title {
                    color: var(--dark-color);
                    font-size: 1.25rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                    padding-bottom: 0.75rem;
                    border-bottom: 2px solid var(--border-color);
                }

                .btn {
                    border-radius: 8px;
                    padding: 0.625rem 1.25rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    border: none;
                    font-size: 0.95rem;
                }

                .btn-primary {
                    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
                    box-shadow: var(--shadow-sm);
                }

                .btn-primary:hover {
                    background: linear-gradient(135deg, var(--primary-hover), #1e40af);
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-md);
                }

                .btn-success {
                    background: linear-gradient(135deg, var(--success-color), #059669);
                    box-shadow: var(--shadow-sm);
                }

                .btn-success:hover {
                    background: linear-gradient(135deg, #059669, #047857);
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-md);
                }

                .btn-danger {
                    background: linear-gradient(135deg, var(--danger-color), #dc2626);
                    box-shadow: var(--shadow-sm);
                }

                .btn-danger:hover {
                    background: linear-gradient(135deg, #dc2626, #b91c1c);
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-md);
                }

                .table {
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: var(--shadow-sm);
                }

                .table thead th {
                    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
                    color: white;
                    font-weight: 600;
                    padding: 1rem 0.75rem;
                    border: none;
                    font-size: 0.875rem;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .table tbody tr {
                    transition: all 0.3s ease;
                    cursor: pointer;
                }

                .table tbody tr:hover {
                    background-color: #f8fafc;
                    transform: scale(1.01);
                }

                .table tbody td {
                    padding: 0.875rem 0.75rem;
                    border-color: var(--border-color);
                    vertical-align: middle;
                }

                .table tbody tr.selected {
                    background-color: #e0f2fe;
                    box-shadow: inset 0 0 0 2px var(--primary-color);
                }

                hr {
                    border: none;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, var(--border-color), transparent);
                    margin: 1.5rem 0;
                }

                .header-section {
                    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
                    color: white;
                    padding: 2rem;
                    border-radius: 16px;
                    box-shadow: var(--shadow-lg);
                    margin-bottom: 2rem;
                }

                .header-section h5 {
                    margin: 0;
                    font-size: 1.5rem;
                    font-weight: 600;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .fade-in {
                    animation: fadeIn 0.6s ease-out;
                }

                .tools-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                    margin-bottom: 1rem;
                }

                .action-buttons {
                    display: flex;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }

                .product-table-container {
                    max-height: 500px;
                    overflow-y: auto;
                    border-radius: 8px;
                }
            </style>

            <div class="container-fluid mt-1">
                <div class="header-section fade-in">
                    <h5><i class="fa-solid fa-boxes-stacked me-2"></i>Product Maintenance</h5>
                </div>

                <div class="row">
                    <div class="col-md-6 mt-2">
                        <div class="card-container fade-in">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="section-title mb-0"><i class="fa-solid fa-list me-2"></i>Product List</h6>
                                <div class="action-buttons">
                                    <button class="btn btn-primary" type="button" id="editBtn" onclick="EditProduct()" disabled><i class="fas fa-edit me-1"></i> Edit</button>
                                    <button class="btn btn-success" type="button" id="addNew" onclick="AddNew()"><i class="fas fa-plus me-1"></i> New</button>
                                </div>
                            </div>
                            <div class="product-table-container">
                                <table id="productTbl" style="width:100%;" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Product Name</th>
                                            <th width="25%">Supplier Name</th>
                                            <th width="25%">Product Category</th>
                                            <th width="25%">Product Type</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mt-2">
                        <div class="card-container fade-in">
                            <form method="POST">
                                <input type="hidden" name="id" id="ID">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="section-title mb-0"><i class="fa-solid fa-tools me-2"></i>Product Tools</h6>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary" type="button" id="saveBtn" onclick="Save();" disabled><i class="fa-solid fa-check-circle me-1"></i> Save</button>
                                        <button class="btn btn-danger" type="button" id="cancelBtn" onclick="Cancel();" disabled><i class="fa-regular fa-circle-xmark me-1"></i> Cancel</button>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" id="refNo" name="refNo" disabled>
                                <div class="tools-grid">
                                    <div>
                                        <label for="prodType" class="form-label">Product Type</label>
                                        <select class="form-select" name="prodType" id="prodType" disabled>
                                            <option value="" disabled>Select Type</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="prodCateg" class="form-label">Product Category</label>
                                        <select class="form-select" name="prodCateg" id="prodCateg" disabled>
                                            <option value="" disabled>Select Category</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="supplier" class="form-label">Supplier</label>
                                        <select class="form-select" name="supplier" id="supplier" disabled>
                                            <option value="" disabled>Select Supplier</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="ProdName" class="form-label">Product Name</label>
                                        <select class="form-select" name="prodName" id="prodName" disabled>
                                            <option value="" disabled>Select Product</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/productmaintenance.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
