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
                label {
                    color: #090909;
                }
                .form-control {
                    color: #090909 !important;
                    border:1px solid #000000 !important;
                }
                .form-select {
                    color: #090909 !important;
                    border:1px solid #000000 !important;
                }
                /* Selection box */
                .select2-container--default .select2-selection--single {
                    border: 1px solid #000000 !important;
                }

                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #090909 !important;
                }

                /* Dropdown */
                .select2-dropdown {
                    border: 1px solid #000000 !important;
                }

                .select2-results__option {
                    color: #090909 !important;
                }

                /* Optional: Highlighted option */
                .select2-results__option--highlighted {
                    background-color: #e0e0e0 !important;
                    color: #090909 !important;
                }
                /* Ensure selected option text stays dark */
                .select2-results__option--selected {
                    color: #090909 !important;
                    background-color: #e0e0e0 !important;
                }
                .select2-container--default .select2-results__option--selected {
                    color: #090909 !important;
                }
                .select2-container--default .select2-results__option--highlighted[aria-selected] {
                    color: #090909 !important;
                    background-color: #e0e0e0 !important;
                }
                .select2-container--default .select2-results__option--selectable:hover {
                    color: #090909 !important;
                    background-color: #e0e0e0 !important;
                }
                /* Make jQuery datetimepicker Clear button text white */
                .xdsoft_datetimepicker .xdsoft_clear_button {
                    color: #ffffff !important;
                }
                form {
                    width: 100%;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                }
                th {
                    font-weight: bold;
                    color: #090909;
                    position: sticky;
                    top: 0;
                }
            </style>

            <div class="container-fluid mt-1">
                <!-- Header -->
                <div class="shadow p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Transmittal Receipt</p>
                </div>

                <!-- Row 1 Search -->
                <div class="row">
                    <div class="col-md-12 mt-1">
                        <form id="myForm" method="POST">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <input type="text" name="transmittalNo" class="form-control" id="transmittalNo" placeholder="Transmittal No." disabled>
                                </div>
                                <div class="col-md-4 justify-content-end">
                                    <button type="button" class="btn btn-block btn-primary w-100" id="searchTransmittalBtn" name="searchTransmittalBtn" onclick="SearchTransmittal();"><i class="fa-solid fa-magnifying-glass"></i> Search Transmittal</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="row mb-3">
                                        <label for="colFormLabel" class="col-sm-1 col-form-label">TO:</label>

                                        <div class="col-sm-10">
                                             <input type="text" class="form-control" name="fromRep" id="fromRep" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row mb-3">
                                        <label for="colFormLabel" class="col-sm-1  col-form-label">FROM:</label>
                                        <div class="col-sm-9 ms-2">
                                            <input type="text" class="form-control" name="fromRep" id="fromRep" placeholder="ISYNERGIESINC" disabled>
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
                    <div class="col-md-6">
                        <div class="p-3 shadow rounded-3" style="background-color:white">
                            <form id="transmittalform" method="POST">
                                <div class="head">
                                    <h5>Particulars</h5>
                                    <hr style="height:1px">
                                </div>
                                <div>
                                    <p>Product Information</p>
                                </div>

                                <div class="row mb-4 mt-2">
                                    <div class="col-md-3">
                                        <input class="form-check-input" type="checkbox" name="isConsignment" id="isConsignment" value="Yes" onclick="isConsignmentBox()">
                                        <label for="isConsignment" class="form-check-label">Consignment</label>
                                    </div>
                                </div>

                                <div class="row mt-2 mb-3">
                                    <div class="col-md-3">
                                        <label for="">Isyn Branch:</label>
                                    </div>
                                    <div class="col-md-9">
                                       <select class="form-select" name="isynBranch" id="isynBranch" onchange="LoadBranch(this.value)">
                                                <option value="" selected>Select</option>
                                                <option value="HEAD OFFICE">HEAD OFFICE</option>
                                            </select>
                                    </div>
                                </div>

                                <div class="mb-2 row" id="consignmentBranchContainer" style="display: None;">
                                    <label class="col-sm-3 col-form-label" for="isynBranch">Branch:</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" name="consignmentBranch" id="consignmentBranch" onchange="forBranchClear();">
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <label class="col-sm-3 col-form-label" for="type">Type:</label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="type" id="type" onchange="LoadCategory(this.value);">
                                                <option value="" selected>Select</option>
                                                <option value="With VAT">With VAT</option>
                                                <option value="Non-VAT">Non-VAT</option>
                                            </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <label class="col-sm-3 col-form-label" for="category">Category:</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" aria-label="Category" name="category" id="category" onchange="LoadSerialProduct(this.value);">
                                                <option value="" selected>Select</option>
                                                <option value="Battery">Battery</option>
                                                <option value="Cable">Cable</option>
                                                <option value="Cartridge">Cartridge</option>
                                                <option value="Connector">Connector</option>
                                            </select>
                                    </div>
                                </div>

                                <div class="row mt-2 mb-2">
                                    <label class="col-sm-3 col-form-label">Select by:</label>
                                    <div class="col-sm-9">
                                        <div class="row mt-2">
                                            <div class="col-lg-5 col-sm-5">
                                                <div class="form-check">
                                                    <input class="form-check-input me-1" type="radio" value="productName" name="selectBy" id="productName">
                                                    <label class="form-check-label" for="productName">Product Name</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-4">
                                                <div class="form-check">
                                                    <input class="form-check-input me-1" type="radio" value="serial" name="selectBy" id="serialNo">
                                                    <label class="form-check-label" for="serialNo">Serial No.</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <label class="col-sm-3 col-form-label" for="SerialProduct" id="SerialProdlbl">Serial:</label>
                                    <div class="col-sm-9">
                                        <select id="SerialProduct" name="SerialProduct" class="form-select" onchange="LoadProductSINo(this.value);">
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <label class="col-sm-3 col-form-label" for="SINo">Supplier SI:</label>
                                    <div class="col-sm-9">
                                        <select id="SINo" name="SINo" class="form-select" onchange="LoadProductSummary();">
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Product Summary Column -->
                    <div class="col-md-6">
                        <div class="p-3 shadow rounded-3" style="background-color:white">
                            <form id="summary" method="POST">
                                <div class="head">
                                    <h5>Product Summary</h5>
                                    <hr style="height:1px">
                                </div>
                                <div class="row mt-2">
                                    <label for="psSupplierSI" class="col-md-3 form-label">Supplier SI:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psSupplierSI" name="psSupplierSI" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psSerialNo" class="col-md-3 form-label">Serial No.:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psSerialNo" name="psSerialNo" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psProduct" class="col-md-3 form-label">Product:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psProduct" name="psProduct" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psSupplier" class="col-md-3 form-label">Supplier:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psSupplier" name="psSupplier" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psSRP" class="col-md-3 form-label">SRP:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psSRP" name="psSRP" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psQuantity" class="col-md-3 form-label">Quantity:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psQuantity" name="psQuantity" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psDealerPrice" class="col-md-3 form-label">Dealer's Price:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psDealerPrice" name="psDealerPrice" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <label for="psTotalPrice" class="col-md-3 form-label">Total Price:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="psTotalPrice" name="psTotalPrice" class="form-control" disabled>
                                    </div>                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--Row 3-->
                <div class="row mt-3">
                    <div class="col-12">
                        <form id="transmittalform2" method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Quantity:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" class="form-control" name="saleQty" id="saleQty" placeholder="0" min="1" onchange="ComputeAvailQty(this.value);">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>
                                                <input class="form-check-input" type="checkbox" name="isEditSRP" id="isEditSRP" onclick="isEditSRPBTN()"> Edit SRP
                                            </label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="finalSRP" id="finalSRP" onchange="formatInput(this);" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Row 4 -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="shadow p-3 rounded-3" style="height: 300px; background-color:white; overflow:auto;">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <button type="button" class="btn btn-success px-3 py-2 mx-1" id="addbtn" name="addbtn" onclick="addItem();"><i class="fa-solid fa-plus"></i> Add</button>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-danger" id="cancelProduct" name="cancelProduct" onclick="cancelProduct()" disabled><i class="fa-solid fa-circle-xmark"></i> Cancel Product</button>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <table id="itemsTbl" style="width:100%;" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>SRP</th>
                                        <th>SI No</th>
                                        <th>Serial No</th>
                                        <th>Product</th>
                                        <th>Supplier</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Branch</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Row 5 -->

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="shadow p-3 rounded-3 mb-4" style="background-color: white;">
                            <form id="myForm2" method="POST">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Remarks:</label>
                                        <input type="text" id="remarks" name="remarks" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-4 mt-2">
                                    <div class="col-md-3">
                                        <input class="form-check-input" type="checkbox" name="isOtherDetails" id="isOtherDetails" value="Yes" onclick="isOtherDetailsBox()">
                                        <label for="isOtherDetails" class="form-check-label">Other Details</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="row mt-2">
                                            <label for="carrier" class="col-md-2 form-label">Carrier:</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="carrier" id="carrier" disabled>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <label for="dateCarrier" class="col-md-2 form-label">Date:</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="dateCarrier" id="dateCarrier" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row mt-2">
                                            <label for="receivedBy" class="col-md-3 form-label">Received by:</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="receivedBy" id="receivedBy" disabled>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <label for="dateReceivedBy" class="col-md-3 form-label">Date:</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="dateReceivedBy" id="dateReceivedBy" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="buttons d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-primary mx-2" id="submitBtn" name="submitBtn" onclick="SubmitBtn();"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="SearchTransmittalMDL" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="SearchTransmittal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Search Transmittal</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label for="transmittalClient" class="bold-label">Client</label>
                                    <input type="text" name="transmittalClient" id="transmittalClient" class="form-control" placeholder="Client name">
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" id="transmittalSearchBtn" name="transmittalSearchBtn" onclick="TransmittalSearch();"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                                        <button class="btn btn-secondary" id="transmittalClearBtn" name="transmittalClearBtn" onclick="TransmittalClear();"><i class="fa-regular fa-circle-xmark"></i> Clear</button>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <div class="">
                                <table id="listTbl" class="table table-bordered table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="20%">TRANS NO.</th>
                                            <th width="20%">CLIENT</th>
                                            <th width="20%">DATE</th>
                                            <th width="20%">OUT</th>
                                            <th width="20%">SI No.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listList">
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button class="btn btn-success col-12" type="button" id="rePrint" name="rePrint" onclick="RePrint();" disabled><i class="fa-solid fa-print"></i> REPRINT</button>
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
        <script src="../../js/inventorymanagement/transmittalreceipt.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
