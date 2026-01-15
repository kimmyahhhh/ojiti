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
                    --primary-color: #ffffffff;
                    --primary-hover: #ffffffff;
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
                    color: #000000 !important;
                    font-weight: 500;
                    margin-bottom: 0.5rem;
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
                    color: #ffffff !important;
                }

                .btn-primary {
                    background: #82ccf8ff !important;
                    color: #ffffff !important;
                    border: none;
                    padding: 0.625rem 1.25rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    border-radius: 8px;
                    font-size: 0.95rem;
                }

                .btn-primary:hover {
                    background: #6bb6ff !important;
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-md);
                }

                .btn-success {
                    background: linear-gradient(135deg, var(--success-color), #059669);
                    color: #ffffff !important;
                    box-shadow: var(--shadow-sm);
                }

                .btn-success:hover {
                    background: linear-gradient(135deg, #059669, #047857);
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-md);
                }

                .btn-danger {
                    background: linear-gradient(135deg, var(--danger-color), #dc2626);
                    color: #ffffff !important;
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
                    background: #f8f9fa !important;
                    color: #000000 !important;
                    font-weight: 600;
                    padding: 1rem 0.75rem;
                    border: 1px solid #e2e8f0 !important;
                    font-size: 0.875rem;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .table tbody tr {
                    background: #ffffff !important;
                    transition: all 0.3s ease;
                }

                .table tbody tr:hover {
                    background: #062a4e !important;
                    transform: scale(1.01);
                }

                .table tbody td {
                    padding: 0.875rem 0.75rem;
                    border-color: #e2e8f0 !important;
                    vertical-align: middle;
                    color: #000000 !important;
                }

                .form-check-input:checked {
                    background-color: var(--primary-color);
                    border-color: var(--primary-color);
                }

                .form-check-input:focus {
                    border-color: var(--primary-color);
                    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
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

                .modal-content {
                    border-radius: 16px;
                    border: none;
                    box-shadow: var(--shadow-lg);
                }

                .modal-header {
                    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
                    color: white;
                    border-radius: 16px 16px 0 0;
                    border: none;
                }

                .modal-header .btn-close {
                    filter: brightness(0) invert(1);
                }

                .input-group-text {
                    background-color: var(--light-bg);
                    border: 2px solid var(--border-color);
                    color: var(--dark-color);
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .fade-in {
                    animation: fadeIn 0.6s ease-out;
                }
            </style>

            <div class="container-fluid mt-1">
                <!-- Header -->
                <div class="header-section fade-in">
                    <h5 style="color: blue; font-weight: bold;"><i class="fa-solid fa-file-contract me-2"></i>Transmittal Receipt</h5>
                </div>

                <!-- Row 1 Search -->
                <div class="row">
                    <div class="col-md-12 mt-1">
                        <div class="card-container fade-in">
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
                                            <input type="text" class="form-control" name="toRep" id="toRep" placeholder="RECIPIENT NAME">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row mb-3">
                                        <label for="colFormLabel" class="col-sm-1  col-form-label">FROM:</label>
                                        <div class="col-sm-9 ms-2">
                                            <input type="text" class="form-control" name="fromRep" id="fromRep" placeholder="ISYNERGIESINC"disabled>
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
                        <div class="card-container fade-in">
                            <form id="transmittalform" method="POST">
                                <div class="head">
                                    <h5 class="section-title"><i class="fa-solid fa-box me-2"></i>Particulars</h5>
                                </div>
                                <div class="alert alert-info mb-4" role="alert">
                                    <i class="fa-solid fa-info-circle me-2"></i>Product Information
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
                        <div class="card-container fade-in">
                            <form id="summary" method="POST">
                                <div class="head">
                                    <h5 class="section-title"><i class="fa-solid fa-chart-line me-2"></i>Product Summary</h5>
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
                        <div class="card-container fade-in">
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
                        <div class="card-container fade-in">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><i class="fa-solid fa-list me-2"></i>Items List</h6>
                                    <div>
                                        <button type="button" class="btn btn-success px-3 py-2 me-2" id="addbtn" name="addbtn" onclick="addItem();"><i class="fa-solid fa-plus me-1"></i> Add</button>
                                        <button type="button" class="btn btn-danger" id="cancelProduct" name="cancelProduct" onclick="cancelProduct()" disabled><i class="fa-solid fa-circle-xmark me-1"></i> Cancel Product</button>
                                    </div>
                                </div>
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
                        <div class="card-container fade-in">
                            <form id="myForm2" method="POST">
                                <h6 class="section-title"><i class="fa-solid fa-comment-dots me-2"></i>Additional Details</h6>
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label for="remarks" class="form-label fw-bold">Remarks:</label>
                                        <textarea id="remarks" name="remarks" class="form-control" rows="3" placeholder="Enter any additional remarks..."></textarea>
                                    </div>
                                </div>

                                <div class="row mb-4 mt-2">
                                    <div class="col-md-3">
                                        <input class="form-check-input" type="checkbox" name="isOtherDetails" id="isOtherDetails" value="Yes" onclick="isOtherDetailsBox()">
                                        <label for="isOtherDetails" class="form-check-label">Other Details</label>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-6">
                                        <div class="card-container p-3">
                                            <h6 class="section-title mb-3"><i class="fa-solid fa-truck me-2"></i>Carrier Information</h6>
                                            <div class="row mt-2">
                                                <label for="carrier" class="col-md-3 form-label">Carrier:</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="carrier" id="carrier" disabled>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <label for="dateCarrier" class="col-md-3 form-label">Date:</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="dateCarrier" id="dateCarrier" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-container p-3">
                                            <h6 class="section-title mb-3"><i class="fa-solid fa-user-check me-2"></i>Receipt Information</h6>
                                            <div class="row mt-2">
                                                <label for="receivedBy" class="col-md-3 form-label">Received by:</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="receivedBy" id="receivedBy" disabled>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <label for="dateReceivedBy" class="col-md-3 form-label">Date:</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="dateReceivedBy" id="dateReceivedBy" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="buttons d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary btn-lg px-4" id="submitBtn" name="submitBtn" onclick="SubmitBtn();"><i class="fa-solid fa-floppy-disk me-2"></i> Submit Transmittal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="SearchTransmittalMDL" data-bs-backdrop="true" data-bs-keyboard="false" tabindex="-1" aria-labelledby="SearchTransmittal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Search Transmittal</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card-container mb-3">
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <label for="searchNameFrom" class="form-label fw-bold">Name From</label>
                                        <input type="text" name="searchNameFrom" id="searchNameFrom" class="form-control" placeholder="Enter sender name...">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary w-100" id="transmittalSearchBtn" name="transmittalSearchBtn" onclick="TransmittalSearch();"><i class="fa-solid fa-magnifying-glass me-2"></i> Search</button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-secondary w-100" id="transmittalClearBtn" name="transmittalClearBtn" onclick="ClearSearch();"><i class="fa-solid fa-times me-2"></i> Clear</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="listTbl" class="table table-hover table-bordered" style="width:100%">
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
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button class="btn btn-success btn-lg w-100" type="button" id="rePrint" name="rePrint" onclick="RePrint();" disabled><i class="fa-solid fa-retweet me-2"></i> REPRINT</button>
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

        <script>
            $(document).ready(function() {
                // Validation: TO and FROM recipient fields - letters only
                $('#toRep, #fromRep').on('input', function() {
                    var value = $(this).val();
                    // Remove any characters that are not letters (including spaces and special characters)
                    var lettersOnly = value.replace(/[^a-zA-Z\s]/g, '');
                    if (value !== lettersOnly) {
                        $(this).val(lettersOnly);
                    }
                });

                // Validation: Edit SRP field - numbers only (no letters or special characters)
                $('#finalSRP').on('input', function() {
                    var value = $(this).val();
                    // Remove any characters that are not numbers or decimal point
                    var numbersOnly = value.replace(/[^0-9.]/g, '');
                    
                    // Ensure only one decimal point
                    var parts = numbersOnly.split('.');
                    if (parts.length > 2) {
                        numbersOnly = parts[0] + '.' + parts.slice(1).join('');
                    }
                    
                    // Limit decimal places to 2
                    if (parts.length === 2 && parts[1].length > 2) {
                        numbersOnly = parts[0] + '.' + parts[1].substring(0, 2);
                    }
                    
                    if (value !== numbersOnly) {
                        $(this).val(numbersOnly);
                    }
                });

                // Date picker configuration: Carrier and Received By dates - no future dates
                setTimeout(function() {
                    // Initialize carrier date picker
                    $('#dateCarrier').datetimepicker({
                        format: 'Y-m-d',
                        timepicker: false,
                        maxDate: 0, // 0 means today, prevents future dates
                        scrollInput: false,
                        onSelectDate: function(ct, $input) {
                            // When carrier date is selected, update received date min date
                            var carrierDate = $input.val();
                            updateReceivedDateMinDate(carrierDate);
                        }
                    });
                    
                    // Initialize received date picker
                    $('#dateReceivedBy').datetimepicker({
                        format: 'Y-m-d',
                        timepicker: false,
                        maxDate: 0, // 0 means today, prevents future dates
                        scrollInput: false
                    });
                    
                    // Function to update received date minimum date
                    function updateReceivedDateMinDate(carrierDate) {
                        var receivedDate = $('#dateReceivedBy').val();
                        
                        if (carrierDate) {
                            // Update received date picker to have minimum date of carrier date
                            try {
                                $('#dateReceivedBy').datetimepicker('destroy');
                            } catch(e) {
                                // Ignore if already destroyed
                            }
                            
                            $('#dateReceivedBy').datetimepicker({
                                format: 'Y-m-d',
                                timepicker: false,
                                maxDate: 0, // No future dates
                                minDate: carrierDate, // Must be after or equal to carrier date
                                scrollInput: false
                            });
                            
                            // If received date is already set and is before carrier date, clear it
                            if (receivedDate) {
                                var carrierDateObj = new Date(carrierDate);
                                var receivedDateObj = new Date(receivedDate);
                                
                                if (receivedDateObj < carrierDateObj) {
                                    alert('Received date must be after or equal to carrier date.');
                                    $('#dateReceivedBy').val('');
                                }
                            }
                        } else {
                            // If carrier date is cleared, remove min date restriction
                            try {
                                $('#dateReceivedBy').datetimepicker('destroy');
                            } catch(e) {
                                // Ignore if already destroyed
                            }
                            
                            $('#dateReceivedBy').datetimepicker({
                                format: 'Y-m-d',
                                timepicker: false,
                                maxDate: 0, // No future dates
                                scrollInput: false
                            });
                        }
                    }
                    
                    // When carrier date changes, update received date minimum date
                    $('#dateCarrier').on('change', function() {
                        var carrierDate = $(this).val();
                        updateReceivedDateMinDate(carrierDate);
                    });
                    
                    // Validate received date when it changes
                    $('#dateReceivedBy').on('change', function() {
                        var carrierDate = $('#dateCarrier').val();
                        var receivedDate = $(this).val();
                        
                        if (carrierDate && receivedDate) {
                            // Compare dates
                            var carrierDateObj = new Date(carrierDate);
                            var receivedDateObj = new Date(receivedDate);
                            
                            // Set time to 00:00:00 for accurate date comparison
                            carrierDateObj.setHours(0, 0, 0, 0);
                            receivedDateObj.setHours(0, 0, 0, 0);
                            
                            if (receivedDateObj < carrierDateObj) {
                                alert('Received date must be after or equal to carrier date.');
                                $(this).val('');
                                return false;
                            }
                        }
                    });
                }, 100);
            });
        </script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
