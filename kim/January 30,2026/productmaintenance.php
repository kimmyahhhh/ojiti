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
                    --primary-color: #3b8ff3;
                    --text-color: #334155;
                    --border-color: #cbd5e1;
                    --bg-light: #f8fafc;
                    --focus-ring: rgba(59, 143, 243, 0.25);
                }

                body {
                    background-color: #f1f5f9;
                }

                label {
                    color: var(--text-color);
                    font-weight: 600;
                    font-size: 0.875rem;
                    margin-bottom: 0.5rem;
                }

                /* Modern Card Design */
                .card-box {
                    background-color: white;
                    border-radius: 1rem;
                    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
                    border: 1px solid #e2e8f0;
                    padding: 1.5rem;
                    height: 100%;
                    transition: box-shadow 0.2s ease;
                }
                
                .card-box:hover {
                    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
                }

                /* Inputs & Selects */
                .form-control, .form-select {
                    color: #1e293b !important;
                    border: 1px solid var(--border-color) !important;
                    border-radius: 0.5rem;
                    padding: 0.625rem 1rem;
                    font-size: 0.95rem;
                    transition: all 0.2s ease-in-out;
                    background-color: #fff;
                }

                .form-control:focus, .form-select:focus {
                    border-color: var(--primary-color) !important;
                    box-shadow: 0 0 0 4px var(--focus-ring) !important;
                    outline: none;
                }

                .form-control:disabled, .form-select:disabled {
                    background-color: #f1f5f9;
                    border-color: #e2e8f0 !important;
                    opacity: 1;
                }

                /* Buttons */
                .btn {
                    border-radius: 0.5rem;
                    font-weight: 500;
                    padding: 0.625rem 1.25rem;
                    transition: all 0.2s ease;
                }

                /* Select2 Customization */
                .select2-container--default .select2-selection--single {
                    border: 1px solid var(--border-color) !important;
                    border-radius: 0.5rem;
                    height: 45px;
                    padding: 8px;
                    transition: all 0.2s ease;
                }

                .select2-container--default.select2-container--focus .select2-selection--single {
                    border-color: var(--primary-color) !important;
                    box-shadow: 0 0 0 4px var(--focus-ring) !important;
                }

                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #1e293b !important;
                    line-height: 28px;
                    padding-left: 4px;
                }

                .select2-container--default .select2-selection--single .select2-selection__arrow {
                    height: 43px;
                    right: 8px;
                }

                .select2-dropdown {
                    border: 1px solid var(--border-color) !important;
                    border-radius: 0.5rem;
                    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
                    margin-top: 4px;
                }

                .select2-results__option {
                    padding: 10px 14px;
                    font-size: 0.95rem;
                }

                .select2-results__option--highlighted {
                    background-color: var(--primary-color) !important;
                }

                /* Form Layout */
                form {
                    width: 100%;
                }

                /* Table Styling */
                .table-custom th {
                    background-color: #f8fafc;
                    color: #64748b;
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 0.75rem;
                    letter-spacing: 0.05em;
                    padding: 1rem;
                    border-bottom: 2px solid #e2e8f0;
                    position: sticky;
                    top: 0;
                    z-index: 10;
                }
                
                .table-custom td {
                    padding: 1rem;
                    vertical-align: middle;
                    color: #334155;
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 0.9rem;
                }

                .table-custom tr:hover td {
                    background-color: #f8fafc;
                }

                /* Header Styling */
                .page-header {
                    background: linear-gradient(to right, #ffffff, #f8fafc);
                    border-left: 5px solid var(--primary-color);
                }

                .section-title {
                    color: #0f172a;
                    font-weight: 700;
                    letter-spacing: -0.025em;
                }

                /* Scrollbar */
                .custom-scroll {
                    overflow: auto;
                    scrollbar-width: thin;
                    scrollbar-color: #cbd5e1 transparent;
                }
                
                .custom-scroll::-webkit-scrollbar {
                    width: 6px;
                    height: 6px;
                }
                
                .custom-scroll::-webkit-scrollbar-track {
                    background: transparent; 
                }
                
                .custom-scroll::-webkit-scrollbar-thumb {
                    background-color: #cbd5e1; 
                    border-radius: 20px;
                }
            </style>

            <div class="container-fluid mt-1">
                <div class="card-box mb-3">
                    <p class="fs-5 my-2 fw-bold" style="color: var(--primary-color);">Product Maintenance</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mt-2">
                        <div class="card-box custom-scroll">
                            <p class="section-title fs-5">Product List</p>
                            <hr style="height: 1px">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="productTbl" style="width:100%;" class="table table-bordered table-custom">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th width="5%">Product Name</th>
                                                <th width="5%">Supplier Name</th>
                                                <th width="5%">Product Category</th>
                                                <th width="5%">Product Type</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productList">
    
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mt-2">
                        <div class="card-box">
                            <form method="POST">
                                <input type="hidden" name="id" id="ID">
                                <div class="align-items-center justify-content-between mb-4">
                                    <button class="btn btn-primary float-end mx-2" type="button" id="editBtn" onclick="EditProduct()" disabled><i class="fas fa-edit"></i> Edit</button>
                                    <button class="btn btn-success float-end mx-2" type="button" id="addNew" onclick="AddNew()"> <i class="fas fa-plus"></i> New</button>
    
                                    <p class="section-title fs-5">Tools</p>
                                </div>
                                <hr style="height: 1px">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <input type="hidden" class="form-control" id="refNo" name="refNo" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="prodType" class="form-label">Product Type</label>
                                        <select class="form-select" name="prodType" id="prodType" disabled>
                                            <option value="" disabled></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="prodCateg" class="form-label">Product Category</label>
                                        <select class="form-select" name="prodCateg" id="prodCateg" disabled>
                                            <option value="" disabled></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="supplier" class="form-label">Supplier</label>
                                        <select class="form-select" name="supplier" id="supplier" disabled>
                                            <option value="" disabled></option>
                                        </select>
                                        
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ProdName" class="form-label">Product Name</label>
                                        <select class="form-select" name="prodName" id="prodName" disabled>
                                            <option value="" disabled></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-6 mb-3">
                                        <button class="btn btn-primary float-end mx-2" type="button" id="saveBtn" onclick="Save();" disabled><i class="fa-solid fa-check-circle"></i> Save</button>
                                        <button class="btn btn-danger float-end mx-2" type="button" id="cancelBtn" onclick="Cancel();" disabled><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
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