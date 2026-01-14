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
            include('../../includes.pages.navbar.php');
        ?>

        <?php
        // function transactionselect($connection, $column, $table, $selectedValue = null)
        // {
        //     $query = "SELECT DISTINCT $column FROM $table";
        //     $result = mysqli_query($connection, $query);
        //
        //     if ($result) {
        //         while ($row = mysqli_fetch_assoc($result)) {
        //             $value = $row[$column];
        //             $selected = ($value == $selectedValue) ? 'selected' : '';
        //
        //             echo "<option value='$value' $selected>$value</option>";
        //         }
        //     } else {
        //         echo "<option value=''>Error retrieving data</option>";
        //     }
        // }
        ?>

            <style>
                /* .form-bg {
                    width: 100%;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                }

                main {
                    background-color: #EAEAF6;
                    height: 100%;
                }


                label,
                th {
                    color: #090909;
                }

                .disabled-section {
                    opacity: 0.5;
                    pointer-events: none;
                    cursor: not-allowed;
                } */

                /* =================== */

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
                <div class="shadow rounded-3 p-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2 ">Loans Payment</p>
                </div>

                <div class="row">
                    <div class="col-md-8 mt-2">
                        <div class="shadow p-3 rounded-2" style="background-color: white;">
                            <p class="fw-medium fs-5" style="color: #090909;">Similar List</p>
                            <hr style="height: 1px;">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 row">
                                        <div class="col-lg-2 col-md-3">
                                            <label for="TransactionType" class="form-label">Transaction Type</label>
                                        </div>
                                        <div class="col-lg-8 col-md-8">
                                            <select id="TransactionType" name="TransactionType" class="form-select" onchange="LoadTransactClientName(this.value)">
                                                <option value="" selected disabled>Select</option>
                                                <option value="INDIVIDUAL">INDIVIDUAL</option>
                                                <option value="CENTER">CENTER</option>
                                                <option value="GROUP">GROUP</option>
                                                <option value="WRITEOFF">WRITEOFF</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mt-3">
                                            <table id="transactClientNameTbl" style="width:100%;" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                    </tr>
                                                </thead>
                                                <tbody id="transactClientNameList">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mt-2">
                        <form class="shadow p-3 rounded-2" style="background-color: white;" novalidate>
                            <div class="mb-3" style="background-color: white;">
                                <p class="fw-medium fs-5" style="color: #090909">Primary Details</p>
                                <hr style="height: 1px">
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="productNamePD" class="form-label">Product</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="productNamePD" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="poPD" class="form-label">PO</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="poPD" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="fundPD" class="form-label">Fund: </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="fundPD" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="modePD" class="form-label">Mode: </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="modePD" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <p class="fw-medium fs-5" style="color: #090909">Account Details</p>
                                <hr style="height: 1px">
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="loanAD" class="form-label">Loan: </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="loanAD" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="balanceAD" class="form-label">Balance: </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="balanceAD" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-3 d-flex justify-content-end">
                                            <label for="arrearsAD" class="form-label">Arrears: </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="arrearsAD" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-12 mt-2">
                        <div class=" shadow p-3 rounded-3" style="background-color: white;overflow:auto">
                            <!-- <hr style="height: 1px"> -->
                            <div class="">
                                <table id="paymentTbl" style="width:100%;" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="28%">Name</th>
                                            <th width="10%">Principal</th>
                                            <th width="10%">Interest</th>
                                            <th width="10%">Penalty</th>
                                            <th width="10%">CBU</th>
                                            <th width="10%">MBA</th>
                                            <th width="10%">Total</th>
                                            <th>ClientNo</th>
                                            <th>LoanID</th>
                                            <th>Fund</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentList">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>
                                                <button type="button" id="editAmountBtn" name="editAmountBtn" class="btn btn-secondary me-1" onclick="toggleEditAmount();" disabled>Edit</button>
                                                <button type="button" id="clearEditAmountBtn" name="clearEditAmountBtn" class="btn btn-secondary me-1" onclick="toggleClearEditAmount();" disabled>Clear</button>
                                                <button type="button" id="clearAllEditAmountBtn" name="clearAllEditAmountBtn" class="btn btn-secondary" onclick="toggleClearAllEditAmount();" disabled>Clear All</button>
                                            </th>
                                            <th>Principal</th>
                                            <th>Interest</th>
                                            <th>Penalty</th>
                                            <th>CBU</th>
                                            <th>MBA</th>
                                            <th>Total</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mt-2">
                        <form class="p-3 shadow rounded-3" style="background-color: white;">
                            <div class="align-items-center justify-content-between">
                                <div class="align-items-center justify-content-between">
                                    <p class="fw-medium fs-5" style="color: #090909;"> Edit Amounts</p>
                                </div>
                            </div>
                            <hr style="height:1px;">
    
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-2 row">
                                        <label for="edit-payment" class="form-label col-md-3">Payment</label>
                                        <div class=" col-md-9">
                                            <input type="text" class="form-control" id="edit-payment" placeholder="0.00" onchange="formatInput(this); DistributeAmounts();" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="edit-principal" class="form-label col-md-3">Principal</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="edit-principal" placeholder="0.00" onchange="formatInput(this); RecomputeAmountTotals()" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="edit-interest" class="form-label col-md-3">Interest</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="edit-interest" placeholder="0.00" onchange="formatInput(this); RecomputeAmountTotals()" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="edit-penalty" class="form-label col-md-3">Penalty</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="edit-penalty" placeholder="0.00" onchange="formatInput(this); RecomputeAmountTotals()" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="edit-cbu" class="form-label col-md-3">CBU</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="edit-cbu" placeholder="0.00" onchange="formatInput(this); RecomputeAmountTotals()" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="edit-mba" class="form-label col-md-3">MBA</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="edit-mba" placeholder="0.00" onchange="formatInput(this); RecomputeAmountTotals()" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="edit-total" class="form-label col-md-3">Total</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="edit-total" placeholder="0.00" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="btn-group-vertical" style="width:100%">
                                        <button type="button" id="btnFullPayment" class="btn btn-success mb-2" disabled><i class="fa-solid fa-circle-check"></i> Full Payment</button>
                                        <button type="button" id="btnWaivePenalty" class="btn btn-primary mb-2" disabled><i class="fa-solid fa-pen"></i> Waive Penalty</button>
                                        <button type="button" id="btnEditPayment" class="btn btn-primary mb-2" disabled><i class="fa-solid fa-pen"></i> Edit Payment</button>
                                        <button type="button" id="btnReset" class="text-white btn btn-warning mb-2" disabled><i class="fa-solid fa-rotate-right"></i> Reset</button>
                                    </div>
    
                                    <div class="btn-group-vertical mt-4" style="width:100%">
                                        <button type="button" id="btnDone" class="btn btn-success mb-2" disabled><i class="fa-solid fa-circle-check"></i> Done</button>
                                        <button type="button" id="btnCancel" class="btn btn-danger mb-2" disabled><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4 mt-2">
                        <div class="shadow p-3 rounded-2" style="background-color: white;">
                            <!-- Payment Type -->
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Payment type </p>
                            </div>
                            <hr style="height:1px;">
                                
                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="paymentType" class="form-label">Payment Type</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-select" id="paymentType" name="paymentType" onchange="SetPaymentType(this.value);">
                                        <option value="" disabled selected>Select</option>
                                        <option value="CASH">Cash</option>
                                        <option value="CHECK">Check</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Check Details -->
                            <hr style="height:1px;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Check Details</p>
                            </div>
                            <hr style="height:1px;">

                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="checkdate" class="form-label">Check Date</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="date" class="form-control" id="checkdate" name="checkdate" disabled>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="checkNo" class="form-label">Check No.</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="checkNo" name="checkNo" disabled>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="bankname" class="form-label">Bank Name</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="bankname" name="bankname" disabled>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="bankbranch" class="form-label">Bank Branch</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="bankbranch" name="bankbranch" disabled>
                                </div>
                            </div>

                        </div>
                    </div>
    
                    <div class="col-md-4 mt-2">
                        <div class="shadow p-3 rounded-2" style="background-color: white;">
                            <!-- Set OR Number -->
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Set OR Number</p>
                            </div>
                            <hr style="height:1px">

                            <div class="mb-3 row">
                                <div class="col-md-3">
                                    <label for="orFrom" class="form-label">OR From</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="orFrom" id="orFrom" class="form-control form-select" onchange="LoadORSeries(this.value);">
                                        <option selected>Select OR</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <div class="col-md-3">
                                    <label for="ORNo" class="form-label">OR No.</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="ORNo" class="form-control mt-1" name="ORNo" disabled>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-3">
                                    <label for="ORLeft" class="form-label">ORs Left</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="ORLeft" class="form-control mt-1" name="ORLeft" disabled>
                                </div>
                            </div>

                            <!-- Depository Bank -->
                            <hr style="height:1px;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Depository Bank</p>
                            </div>
                            <hr style="height:1px;">

                            <div class="mb-3 row">
                                <div class="col-md-4">
                                    <label for="depositoryBank" class="form-label">Depository Bank</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="depositoryBank" id="depositoryBank" class="form-control form-select">
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary w-100 p-3" onclick="SetOR();"><i class="fa-solid fa-floppy-disk"></i> Save Transaction</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="otherDetailsMDL" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="otherDetails" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Other Details</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-2 row">
                                        <label for="clientType" class="form-label col-md-3">Client Type</label>
                                        <div class="col-md-9">
                                            <select name="clientType" id="clientType" class="form-select" onchange="GetClientName(this.value);">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="clientName" class="form-label col-md-3">Name</label>
                                        <div id="clientNameSelDiv" class="col-md-9">
                                            <select name="clientName" id="clientName" class="form-select" onchange="GetClientInfo(this.value);">

                                            </select>
                                        </div>
                                        <div id="clientNameTxtDiv" class="col-md-9" style="display:None;">
                                            <input type="text" name="clientNameTxt" id="clientNameTxt" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="clientAddress" class="form-label col-md-3">Address</label>
                                        <div class="col-md-9">
                                            <input type="text" name="clientAddress" id="clientAddress" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="clientTIN" class="form-label col-md-3">TIN</label>
                                        <div class="col-md-9">
                                            <input type="text" name="clientTIN" id="clientTIN" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="particulars" class="form-label col-md-3">Particulars</label>
                                        <div class="col-md-9">
                                            <!-- <input type="text" id="particulars" name="particulars" class="form-control"> -->
                                            <textarea name="particulars" id="particulars" class="form-control" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary px-3 py-2 mx-1" type="button" id="proceedSaveTransact" name="proceedSaveTransact" onclick="SaveTransaction();"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/cashier/loanspayment.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
