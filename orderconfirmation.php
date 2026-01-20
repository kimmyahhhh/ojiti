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

        <?php
            // // Fetch categories with VAT
            // $sql = "SELECT DISTINCT Category FROM tbl_invlist WHERE Type = 'With VAT' ORDER BY Category ASC";
            // $result = mysqli_query($connection, $sql);
            // $categoriesWithVAT = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $categoriesWithVAT[] = $row['Category'];
            //     }
            // }

            // // Fetch categories without VAT
            // $sql = "SELECT DISTINCT Category FROM tbl_invlist WHERE Type = 'Non-VAT' ORDER BY Category ASC";
            // $result = mysqli_query($connection, $sql);
            // $categoriesNonVAT = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $categoriesNonVAT[] = $row['Category'];
            //     }
            // }

            // //Fetch type based on Branch
            // $sql = "SELECT DISTINCT Type FROM tbl_invlist WHERE Branch = 'HEAD OFFICE'";
            // $result = mysqli_query($connection, $sql);
            // $typeHeadOffice = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $typeHeadOffice[] = $row['Type'];
            //     }
            // }

            // $sql = "SELECT DISTINCT Type FROM tbl_invlist WHERE Branch = 'ISYN-SANTIAGO'";
            // $result = mysqli_query($connection, $sql);
            // $typeIsynSantiago = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $typeIsynSantiago[] = $row['Type'];
            //     }
            // }


            // //fetching oder_no
            // $sql = "SELECT max(TransactionNo) FROM tbl_order_confirmation";
            // $result = mysqli_query($connection, $sql);
            // if (mysqli_num_rows($result) > 0) {
            //     $row = mysqli_fetch_assoc($result);
            //     $order_no = $row['max(TransactionNo)'];
            //     $order_no++;
            // }

        ?>

        <style>
            td {
                font-weight: 400;
            }

            form {
                padding: 20px;
                background-color: white;
                border-radius: 10px;
            }

            label,
            thead {
                color: #090909;
            }

            main {
                background-color: #EAEAF6;
            }

            th {
                font-weight: bold;
                color: #090909;
                position: sticky;
                top: 0;
            }
            .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
        </style>

            <div class="container mt-4">
                <!--Header-->
                <div class="shadow mt-4 p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Order Confirmation</p>
                </div>

                <!-- Row 1 Search -->
                <div class="row mt-2">
                    <div class="col-md-12">
                        <form action="" method="post" id="myForm" class="col-md-12 mt-3 shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <input type="text" name="order_no" class="form-control" id="order_no" placeholder="Order Confirmation No." disabled value="">
                                </div>
                                <div class="col-md-4 justify-content-end">
                                    <button class="btn btn-info w-100 " data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa-solid fa-magnifying-glass"></i> Search transmital</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="row mb-3">
                                        <label for="colFormLabel" class="col-sm-2 col-form-label">TO:</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="recipient" id="recipient" placeholder="RECIPIENT NAME" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row mb-3">
                                        <label for="colFormLabel" class="col-sm-2 col-form-label">FROM:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control ms-2" name="sender" id="sender" value="ISYNERGIESINC" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                
                <!-- Row 2 -->
                <div class="row mt-3">
                    <!-- Particulars Column -->
                    <div class="col-md-6 mb-3">
                        <div class="shadow rounded-3" style="background-color:white">
                            <form id="orderform" method="POST" class="needs-validation" novalidate onsubmit="return validateForm()">
                                <div class="head">
                                    <h5>Product Information</h5>
                                    <hr style="height:1px">
                                </div>
                                
                                <div class="row mt-2 mb-3" id="isynBranchDiv">
                                    <div class="col-md-3">
                                        <label for="">Isyn Branch:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select class="form-select" name="isynBranch" id="isynBranch">
                                            <option value="" selected disabled>Select</option>
                                            <?php
                                            // $query = "SELECT DISTINCT Branch FROM tbl_invlist ORDER BY Branch";
                                            // $query_run = mysqli_query($connection, $query);
                                            // if (mysqli_num_rows($query_run) > 0) {
                                            //     while ($row = mysqli_fetch_assoc($query_run)) {
                                            ?>
                                                    <!-- <option value=" -->
                                                    <?php 
                                                    // echo $row['Branch'] 
                                                    ?>
                                                    <!-- "> -->
                                                    <?php 
                                                    // echo $row['Branch'] 
                                                    ?>
                                                    <!-- </option> -->
                                            <?php
                                            //     }
                                            // }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="type">Type:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select class="form-select" name="type" id="type" onchange="LoadCategory(this.value);">
                                                <option value="" selected>Select</option>
                                                <option value="With VAT">With VAT</option>
                                                <option value="Non-VAT">Non-VAT</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="">Category:</label>
                                    </div>
                                    <div class="col-md-9">
                                            <select class="form-select" aria-label="Category" name="category" id="category" onchange="LoadSerialProduct(this.value);">
                                                <option value="" selected>Select</option>
                                                <option value="Battery">Battery</option>
                                                <option value="Cable">Cable</option>
                                                <option value="Cartridge">Cartridge</option>
                                                <option value="Connector">Connector</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-md-3">
                                        <label for="">Select by:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-check-label" for="inlineRadio1">Product name</label>
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="product">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-check-label" for="inlineRadio2">Serial No.</label>
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="serial">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3" id="serialLabel">
                                        <label id="selectLabel" class="form-label mt-2">Select:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select id="itemSelect" name="select" class="form-select" aria-label="Default select example" required>
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="">SI no:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select id="SIno" name="SIno" class="form-control">
                                            <option value="" selected disabled>Select SI No</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Product Summary Column -->
                    <div class="col-md-6">
                        <div class="shadow rounded-3" style="background-color:white">
                            <form id="summary" method="POST" class="needs-validation" novalidate>
                                <div class="head">
                                    <h5>Product Summary</h5>
                                    <hr style="height:1px">
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Supplier SI:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="supplierSIdisplay" id="supplierSIdisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Serial No:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="serialNodisplay" id="serialNodisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Product:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="productDisplay" id="productDisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Supplier:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="supplierDisplay" id="supplierDisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">SRP:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="srpDisplay" id="srpDisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Quantity:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="quantityDisplay" id="quantityDisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Dealer's Price:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="delearsPriceDisplay" id="delearsPriceDisplay">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="">Total Price:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" readonly name="totalPriceDisplay" id="totalPriceDisplay">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--Row 3-->
                <div class="row mt-3">
                    <div class="col-12">
                        <form id="compute" method="POST" class="needs-validation shadow" novalidate onsubmit="return validateForm()">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Quantity:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="quantity" id="quantityInput" placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Edit SRP</label>
                                            <input class="form-check-input" type="checkbox" name="editSRPtoggle" id="editSRPtoggle" onclick="editSRPToggle()">
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="editSRP" disabled id="editSRP">
                                        </div>
                                    </div>
                                    <div class="row mt-2" hidden>
                                        <div class="col-md-12">
                                            <input type="text" id="warranty" class="form-control" readonly disabled required>
                                            <input type="text" id="vat" class="form-control" readonly disabled required>
                                            <input type="text" id="vatsales" class="form-control" readonly disabled required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Table -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="p-3 rounded-2 shadow" style="background-color: white;">
                            <div class="col-md-12">
                                <div class="align-items-center justify-content-between mb-4">
                                    <button class="btn btn-danger px-3 py-2 mx-1 float-end" id="cancel-btn" onclick="cancelProduct()" disabled><i class="fa-solid fa-circle-xmark" ></i> Cancel Product</button>
                                    <button class="btn btn-success px-3 py-2 mx-1 float-end" id="submit-btn" type="submit" name="addToList" disabled><i class="fa-solid fa-floppy-disk"></i> Submit</button>
                                    <button class="btn btn-primary px-3 py-2 mx-1 float-end" id="addButton"><i class="fa-solid fa-square-plus" ></i> Add</button>
                                    <p class="fw-medium fs-5" style="color: #090909;">Details</p>
                                </div>
                                <hr style="height: 1px">
                                <div class="row">
                                    <div class="overflow-auto" style="max-height: 400px;">
                                        <table class="table table-hover table-borderless table-responsive" id="table1" style="background-color: white;">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>SRP</th>
                                                    <th>SI No.</th>
                                                    <th>Vat</th>
                                                    <th>Vat Sales</th>
                                                    <th>Warranty</th>
                                                    <th>Date Prepared</th>
                                                    <th>Serial No.</th>
                                                    <th>Category</th>
                                                    <th>Type</th>
                                                    <th>Branch</th>
                                                    <th>Supplier</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end mt-3">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Transmittal</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 d-flex mb-3" role="search">
                                    <input type="search" class="form-control me-2" placeholder="Search" aria-level="Search" id="searchInput" placeholder="Search">
                                </div>
                            </div>
                            <div class="row">
            
            
            
                                <div class="col-md-6">
                                    <label for="Firstname" class="form-label mt-2">To</label>
                                    <input type="date" class="form-control" placeholder="" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="Firstname" class="form-label mt-2">From</label>
                                    <input type="date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <hr style="height:1px">
                        <div>
                            <table class="table table-hover table-striped mt-3">
                                <thead>
                                    <tr>
                                        <th>
                                            Transaction No.
                                        </th>
                                        <th>
                                            Client
                                        </th>
                                        <th>
                                            Date
                                        </th>
                                        <th>
                                            Out
                                        </th>
                                        <th>
                                            SI No.
                                        </th>
            
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info">Retrive</button>
                            <button type="button" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/outgoinginventory.js?<?= time() ?>"></script>
        <script src="./orderconfirmation.js?<?= time() ?>"></script>
        
    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
