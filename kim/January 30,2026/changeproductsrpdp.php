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
                td {
                    font-weight: 400;
                }

                form {
                    width: 100%;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                }

                label,
                th {
                    color: #090909;
                }

                main {
                    background-color: #EAEAF6;
                    height: auto;
                }

                th {
                    font-weight: bold;
                    color: #090909;
                    position: sticky;
                    top: 0;
                    background-color: #f8f9fa;
                }

                .custom-input {
                    border: none;
                    border-bottom: .1px solid gray;
                    outline: none;
                    width: 85px;
                    text-align: center;
                    margin-top: 20px;
                }

                .custom-input:focus {
                    border-bottom: 2px solid #0D6EFD;
                }

                .hidden_data {
                    display: none;
                }

                td,
                
                th {
                    color: #090909;
                    word-wrap: break-word;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    white-space: normal;
                }

                .custom-input {
                    border: none;
                    border-bottom: .1px solid gray;
                    outline: none;
                    width: 85px;
                    text-align: center;
                    margin-top: 20px;
                }

                .custom-input:focus {
                    border-bottom: 2px solid #0D6EFD;
                }

                .hidden_data {
                    display: none;
                }

                .table {
                    border-spacing: 0px;
                    table-layout: auto;
                    table-layout: fixed;
                    width: 100%;
                    margin-left: auto;
                    margin-right: auto;
                }

                .table th,
                .table td {
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    white-space: normal;
                }

                .table td {
                    padding: 8px;
                }
                
                .selectedtr {
                    background-color: #0D6EFD !important; 
                    color: white;
                }
            </style>

            <div class="container-fluid mt-1">
                <div class="shadow rounded-3 p-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Change of Product SRP/DP</p>
                </div>

                <!-- Combined Search Product and Current Inventory -->
                <div class="row">
                    <div class="col-md-12 mt-1">
                        <div class="shadow p-3 rounded-3 mb-3" style="background-color: white;">
                            <div class="align-items-center justify-content-between">
                                <p class="fw-medium fs-5" style="color: #090909;">Search Product & Current Inventory</p>
                                <hr style="height: 1px">
                            </div>
                            
                            <!-- Search Filters -->
                            <div class="row mt-1">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="updateType">Update Type:</label>
                                        <select id="updateType" name="updateType" class="form-select" onchange="GenTableHeader(this.value);">
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="isynBranch">Isyn Branch:</label>
                                        <select id="isynBranch" name="isynBranch" class="form-select">
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="type">Type:</label>
                                        <select class="form-select" aria-label="type" required name="type" id="type" onchange="LoadCategory(this.value);">
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="category">Category:</label>
                                        <select id="category" name="category" class="form-select">
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 text-end mb-3">
                                    <button type="button" name="searchInventory" id="searchInventory" class="btn btn-primary" onclick="SearchInventory();"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                                </div>
                            </div>

                            <hr style="height: 1px">

                            <!-- Inventory Table -->
                            <div style="height: 253px;">
                                <table id="inventoryTbl" style="width:100%;" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 36%">Product</th>
                                            <th style="width: 13%">SI No.</th>
                                            <th style="width: 15%">Serial No.</th>
                                            <th style="width: 12%">Quantity</th>
                                            <th style="width: 12%">Dealer's Price</th>
                                            <th style="width: 12%">SRP</th>
                                            <th>Supplier</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Branch</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventoryList">

                                    </tbody>
                                </table>
                            </div>

                            <hr style="height: 1px">

                            <!-- Actions -->
                            <div class="row mt-3 align-items-end">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label" for="newPrice">New Price:</label>
                                        <input type="text" id="newPrice" name="newPrice" class="form-control" placeholder="0.00" onchange="formatInput(this)" disabled>
                                    </div>
                                </div>
                                <div class="col-md-9 text-end">
                                    <div class="mb-2">
                                        <button type="button" id="cancelBtn" name="cancelBtn" class="btn btn-danger" onclick="CancelData();" disabled><i class="fa-solid fa-ban"></i> Cancel</button>
                                        <button type="button" id="addProduct" name="addProduct" class="btn btn-success" onclick="AddProductData();" disabled><i class="fa-solid fa-plus"></i> Add Product</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row: Product Details and Product List -->
                <div class="row">
                    <div class="col-md-4 mt-1">
                        <!-- Product Details -->
                        <div class="shadow p-3 rounded-3 mb-3" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Product Details</p>
                                <hr style="height: 1px">
                            </div>
                            <div class="container mt-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-2">
                                            <label class="form-label" for="sinoDisplay">SI No:</label>
                                            <input type="text" id="sinoDisplay" name="sinoDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="serialNoDisplay">Serial No:</label>
                                            <input type="text" id="serialNoDisplay" name="serialNoDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="productDisplay">Product:</label>
                                            <input type="text" id="productDisplay" name="productDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="supplierDisplay">Supplier:</label>
                                            <input type="text" id="supplierDisplay" name="supplierDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="categoryDisplay">Category:</label>
                                            <input type="text" id="categoryDisplay" name="categoryDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="typeDisplay">Type:</label>
                                            <input type="text" id="typeDisplay" name="typeDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="branchDisplay">Isyn Branch:</label>
                                            <input type="text" id="branchDisplay" name="branchDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="dealerspriceDisplay">Dealer Price:</label>
                                            <input type="text" id="dealerspriceDisplay" name="dealerspriceDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="srpDisplay">SRP:</label>
                                            <input type="text" id="srpDisplay" name="srpDisplay" class="form-control" readonly>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="quantityDisplay">Quantity:</label>
                                            <input type="number" id="quantityDisplay" name="quantityDisplay" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product List -->
                    <div class="col-md-8 mt-1">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Product List</p>
                            </div>
                            <hr style="height: 1px">
                            <div style="height: 342px">
                                <table id="productTbl" style="width:100%;" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 36%">Product</th>
                                            <th style="width: 13%">SI No.</th>
                                            <th style="width: 15%">Serial No.</th>
                                            <th style="width: 12%">Quantity</th>
                                            <th style="width: 12%">Old SRP</th>
                                            <th style="width: 12%">Updated SRP</th>
                                            <th>Supplier</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Branch</th>
                                            <th>Other Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productList">

                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-danger me-2 shadow" id="removeButton" onclick="RemoveProduct();" disabled><i class="fa-regular fa-circle-xmark"></i> Remove</button>
                                        <button type="button" class="btn btn-primary shadow" onclick="UpdateProduct();"><i class="fa-solid fa-arrows-rotate"></i> Update
                                        Product</button>
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

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/changeproductsrpdp.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>