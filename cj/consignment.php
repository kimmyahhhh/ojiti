<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
        
        // Include database connection
        include('../../database/connection.php');
        $db = new Database();
        $conn = $db->conn;
        
        // Fetch categories with VAT
        $categoriesWithVAT = [];
        try {
            $sql = "SELECT DISTINCT Category FROM tbl_invlist WHERE UPPER(Type) = 'WITH VAT' ORDER BY Category ASC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $categoriesWithVAT[] = $row['Category'];
                }
            }
        } catch (Exception $e) { $categoriesWithVAT = []; }

        // Fetch categories without VAT
        $categoriesNonVAT = [];
        try {
            $sql = "SELECT DISTINCT Category FROM tbl_invlist WHERE UPPER(Type) = 'NON-VAT' ORDER BY Category ASC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $categoriesNonVAT[] = $row['Category'];
                }
            }
        } catch (Exception $e) { $categoriesNonVAT = []; }

        //Fetch all available types from inventory
        $allTypes = [];
        try {
            $sql = "SELECT DISTINCT Type FROM tbl_invlist ORDER BY Type ASC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $allTypes[] = $row['Type'];
                }
            }
        } catch (Exception $e) { $allTypes = []; }
        
        // Fetch branches
        $branches = [];
        try {
            $sql = "SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='BRANCH' ORDER BY ItemName ASC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $branches[] = $row['ItemName'];
                }
            }
        } catch (Exception $e) { $branches = ['HEAD OFFICE', 'ISYN-SANTIAGO']; }
        
        // Fetch ISYN branches from inventory
        $isynBranches = [];
        try {
            $sql = "SELECT DISTINCT Branch FROM tbl_invlist ORDER BY Branch ASC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $isynBranches[] = $row['Branch'];
                }
            }
        } catch (Exception $e) { $isynBranches = ['HEAD OFFICE', 'ISYN-SANTIAGO']; }
?>

<!doctype html>
<html lang="en" dir="ltr">
    <?php include('../../includes/pages.header.php'); ?>
    <link rel="stylesheet" href="../../assets/datetimepicker/jquery.datetimepicker.css">
    <link rel="stylesheet" href="../../assets/select2/css/select2.min.css">

    <body class="  ">
        <div id="loading">
            <div class="loader simple-loader">
                <div class="loader-body"></div>
            </div>
        </div>

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

        <style>
            td { font-weight: 400; }
            form { padding: 20px; background-color: white; border-radius: 10px; }
            label, thead { color: #090909; }
            main { background-color: #EAEAF6; height: 100% ; }
            th { font-weight: bold; color: #090909; position: sticky; top: 0; }
        </style>

        <div class="container-fluid mt-4">
            <div class="customer-profile shadow p-3 rounded-2 mb-3" style="background-color: white;">
                <p style="color: blue; font-weight: bold;" class="fs-5 my-2"><i class="fa-solid fa-file-contract me-2"></i>Consignment</p>
            </div>
            
            <div class="row mt-2 align-items-stretch">
                <div class="col-md-6 d-flex flex-column">
                    <form class="p-3 rounded-2 needs-validation shadow" id="branchForm" novalidate>
                        <div class="col-md-12 mb-3">
                            <label for="branch" class="fw-medium fs-5">Branch</label>
                            <hr style="height: 1px">
                            <select class="form-select" id="branch" aria-label="Default select example">
                                <option value="" selected disabled>Select</option>
                                <?php foreach ($branches as $b) echo "<option value='".htmlspecialchars($b)."'>".htmlspecialchars($b)."</option>"; ?>
                            </select>
                        </div>
                    </form>

                    <form class="mt-3 p-3 rounded-2 shadow flex-grow-1" id="particulars">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fw-medium fs-5" style="color: #090909;">Particulars</p>
                        </div>
                        <hr style="height: 1px">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="isynBranch" class="form-label">Isyn Branch</label>
                                <select class="form-select" required name="isynBranch" id="isynBranch">
                                    <option value="" selected disabled>Select</option>
                                    <?php foreach ($isynBranches as $b) echo "<option value='".htmlspecialchars($b)."'>".htmlspecialchars($b)."</option>"; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">Select by:</label>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="product" checked>
                                    <label class="form-check-label" for="inlineRadio1">Product Name</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="serial">
                                    <label class="form-check-label" for="inlineRadio2">Serial No.</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type">
                                    <option value="" selected disabled>Select</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Select Item</label>
                                <select id="itemSelect" name="select[]" class="form-select" required>
                                    <option value="" selected disabled>Select</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category">
                                    <option value="" selected disabled>Select</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SI No</label>
                                <select id="SInoSelect" name="SIno[]" class="form-control">
                                    <option value="" selected disabled>Select</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="quantityInput" class="form-label">Consign Quantity</label>
                                <input type="number" class="form-control" id="quantityInput" required>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-6 d-flex">
                    <form class="p-3 rounded-2 shadow flex-grow-1" style="background-color: white;" id="summary">
                        <p class="fw-medium fs-5" style="color: #090909;">Product Summary</p>
                        <hr style="height: 1px">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="product" class="form-label">Product</label>
                                <input type="text" id="product" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="supplier_si" class="form-label">Supplier SI</label>
                                <input type="text" id="supplier_si" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="SIno" class="form-label">Serial No.</label>
                                <input type="text" id="SIno" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier</label>
                                <input type="text" id="supplier" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Available Qty</label>
                                <input type="text" id="quantity" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="srp" class="form-label">SRP</label>
                                <input type="text" id="srp" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="dealers_price" class="form-label">Dealer Price</label>
                                <input type="text" id="dealers_price" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="total_price" class="form-label">Total Cost</label>
                                <input type="text" id="total_price" class="form-control" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 table-responsive p-3 rounded-2 shadow" style="background-color: white;">
                <div class="align-items-center justify-content-between mb-4">
                    <button class="btn btn-danger px-3 py-2 mx-1 float-end" type="button" id="cancel-btn" onclick="cancelProduct()" disabled>
                        <i class="fa-regular fa-circle-xmark"></i> Cancel Product
                    </button>
                    <button class="btn btn-success px-3 py-2 mx-1 float-end" type="button" id="submit-btn" onclick="saveDataFromTable()" disabled>
                        <i class="fa-solid fa-check-circle"></i> Submit
                    </button>
                    <button class="btn btn-primary px-3 py-2 mx-1 float-end" type="button" id="add-btn" onclick="addToTable()">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                    <p class="fw-medium fs-5" style="color: #090909;">Pricing</p>
                </div>
                <hr style="height: 1px">
                <div class="overflow-auto mb-3" style="max-height: 300px;">
                    <table class="table table-hover table-borderless" id="myTable">
                        <thead>
                            <tr>
                                <th>Quantity</th><th>Product</th><th>Dealer Price</th><th>Total Price</th><th>SRP</th><th>Total SRP</th><th>Mark Up</th><th>Vat Sale</th><th>VAT</th><th>Amount Due</th><th>Stock</th><th>Branch</th><th>Type</th><th>Category</th><th>Supplier SI</th><th>SI No.</th><th>Supplier</th><th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include('../../includes/pages.footer.php'); ?>
        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            // Global variables for consignment.js
            var allTypes = <?php echo json_encode($allTypes ?? []); ?>;
            var categoriesWithVAT = <?php echo json_encode($categoriesWithVAT ?? []); ?>;
            var categoriesNonVAT = <?php echo json_encode($categoriesNonVAT ?? []); ?>;
            var ajaxBasePath = './ajax-inventory/';
        </script>
        <script src="../../js/inventorymanagement/consignment.js?<?= time() ?>"></script>
    </body>
</html>
<?php
    } else {
        echo '<script> window.location.href = "../../login.php"; </script>';
    }
?>

