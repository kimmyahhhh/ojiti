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
        
        <style>
            main {
                background-color: #EAEAF6;
                height: 100% ;
            }
            .container { max-width: 100%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
        </style>

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <div class="container mt-4">
                <!-- <?php include('../../includes/message-prompt.php'); ?> -->
                <div class="container mt-4">
                    <div class="shadow rounded-3 p-3" style="background-color: white;">
                        <p style="color: blue; font-weight: bold;" class="fs-5 my-2 ">Pending Releases</p>
                    </div>
                    <div class="row mt-4 mb-4 ">
                        <div class="col-md-12 ">
                            <div class="shadow p-3 rounded-3 bg-white ">
                                <div class="align-items-center justify-content-between mb-3">
                                    <!-- <button id="editButton" name="renewal_button" disabled class="btn btn-warning text-white float-end" type="button" onclick="toggleEdit()"><i class="fa-solid fa-rotate"></i> Save Renewal</button> -->
                                    <!-- <button class="btn btn-success float-end mx-2" type="reset" name="new" onclick="clearForm()"> <i class="fa-solid fa-plus"></i> Add New</button> -->
                                    <button class="btn btn-success float-end mx-2" id="addNew" type="button" name="new" data-bs-toggle="modal" data-bs-target="#exampleModal" disabled> <i class="fa-solid fa-plus"></i> New</button>
                                    <p class="fw-medium fs-5" style="color: #090909;">Application</p>
                                </div>
                                <hr style="height: 1px">
                                <div class="table-responsive">
                                    <div class="overflow-auto" style="height: 200px;">
                                        <table class="table  table-hover  table-borderless " style="background-color: white;">
                                            <thead>
                                                <tr>
                                                    <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Client Name</th>
                                                    <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Client No.</th>
                                                    <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Program</th>
                                                    <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Product</th>
                                                    <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Loan Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="checkVouchersTableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="/isyn-app/src/config/config_accounts_monitoring/config_pending_release.php" method="POST">
                        <div class="row mb-4 ">
                            <div class="col-md-4 col-sm-12 ">
                                <div class="shadow rounded-3 p-3 bg-white">
                                    <p class="fw-medium fs-5" style="color: #090909;">Primary Details</p>
                                    <hr>

                                    <div class="row">
                                        <label for="programPenReleases" class="col-sm-4 col-form-label fw-bold">Program</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="programPenReleases" id="programPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="productPenReleases" class="col-sm-4 col-form-label fw-bold">Product</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="productPenReleases" id="productPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="staffPenReleases" class="col-sm-4 col-form-label fw-bold">Staff</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="staffPenReleases" id="staffPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="modePenReleases" class="col-sm-4 col-form-label fw-bold">Mode</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="modePenReleases" id="modePenReleases">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label for="termPenReleases" class="col-sm-4 col-form-label fw-bold">Term</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="termPenReleases" id="termPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="ratePenReleases" class="col-sm-4 col-form-label fw-bold">Rate</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="ratePenReleases" id="ratePenReleases">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label for="computationPenReleases" class="col-sm-4 col-form-label fw-bold">Int Computation</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="computationPenReleases" id="computationPenReleases">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label for="tagPenReleases" class="col-sm-4 col-form-label fw-bold">Tag</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="tagPenReleases" id="tagPenReleases">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label for="chargesPenReleases" class="col-sm-4 col-form-label fw-bold">Pre Charges</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="chargesPenReleases" id="chargesPenReleases">
                                        </div>
                                    </div>

                                    <hr>

                                    <p class="fw-medium fs-5" style="color: #090909;">Total</p>
                                    <hr>

                                    <div class="row">
                                        <label for="loanAmountPenReleases" class="col-sm-4 col-form-label fw-bold">Loan Amount</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="loanAmountPenReleases" id="loanAmountPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="interestPenReleases" class="col-sm-4 col-form-label fw-bold">Interest</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="interestPenReleases" id="interestPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="mbaPenReleases" class="col-sm-4 col-form-label fw-bold">MBA</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="mbaPenReleases" id="mbaPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="cbuPenReleases" class="col-sm-4 col-form-label fw-bold">CBU</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="cbuPenReleases" id="cbuPenReleases">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label for="efPenReleases" class="col-sm-4 col-form-label fw-bold">EF</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="efPenReleases" id="efPenReleases">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="netAmountPenReleases" class="col-sm-4 col-form-label fw-bold">Net Amount</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="netAmountPenReleases" id="netAmountPenReleases">
                                        </div>
                                    </div>

                                    <hr>
                                    <p class="fw-medium fs-5" style="color: #090909;">Amortization</p>
                                    <hr>

                                    <div class="row">
                                        <label for="principalAmort" class="col-sm-4 col-form-label fw-bold">Principal</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="principalAmort" id="principalAmort">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="interestAmort" class="col-sm-4 col-form-label fw-bold">Interest</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="interestAmort" id="interestAmort">
                                        </div>
                                    </div>



                                    <div class="row">
                                        <label for="mbaAmort" class="col-sm-4 col-form-label fw-bold">MBA</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="mbaAmort" id="mbaAmort">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="cbuAmort" class="col-sm-4 col-form-label fw-bold">CBU</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="cbuAmort" id="cbuAmort">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <label for="efAmort" class="col-sm-4 col-form-label fw-bold">EF</label>
                                        <div class="col-sm-8">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="efAmort" id="efAmort">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="totalAmort" class="col-sm-5 col-form-label fw-bold">Total Amount</label>
                                        <div class="col-sm-7">
                                            <input type="text" readonly class="form-control-plaintext text-end" name="totalAmort" id="totalAmort">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-8 col-sm-12 ">
                                        <div class="shadow rounded-3 p-3 bg-white">


                                            <p class="fw-medium fs-5" style="color: #090909;">Funding Details</p>
                                            <hr>

                                            <input type="hidden" class="form-control" id="ClientId" name="ClientId" value="<?php echo $ClientId ?>">
                                            <input type="hidden" class="form-control" id="IDNum" name="IDNum" value="">


                                            <div class="mb-2 row">
                                                <label class="col-sm-5 col-form-label">Voucher Details</label>
                                                <div class="col-sm-7">
                                                    <select id="inputReleaseType" name="inputReleaseType" class="form-select border-secondary border-opacity-50" aria-label="Default select example">
                                                        <option selected disabled>SELECT RELEASE TYPE</option>
                                                        <?php
                                                        // $query_type = "SELECT DISTINCT Type FROM tbl_banksetup";
                                                        // $query_type_run = mysqli_query($connection, $query_type);

                                                        // if (mysqli_num_rows($query_type_run) > 0) {
                                                        //     while ($row_type = mysqli_fetch_assoc($query_type_run)) {
                                                                // echo "<option value='" . $row_type['Type'] . "'>" . $row_type['Type'] . "</option>";
                                                        ?>
                                                                <!-- <option value="<?php 
                                                                // echo $row_type['Type'] 
                                                                ?>"><?php 
                                                                // echo $row_type['Type'] 
                                                                ?></option> -->
                                                        <?php
                                                        //     }
                                                        // }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-sm-5 col-form-label">Bank Account</label>
                                                <div class="col-sm-7">
                                                    <select id="inputBankAccount" name="inputBankAccount" class="form-select border-secondary border-opacity-50" aria-label="Default select example">
                                                        <option selected disabled>SELECT BANK ACCOUNT</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label class="col-sm-5 col-form-label">Fund/Tag</label>
                                                <div class="col-sm-7">
                                                    <select id="inputFundTag" name="inputFundTag" class="form-select border-secondary border-opacity-50" aria-label="Default select example">
                                                        <option selected disabled>SELECT FUND/TAG</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <label for="voucher" class="col-sm-5 col-form-label">Voucher/Check No.</label>
                                                <div class="col-sm-7">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <input id="inputVoucher" name="inputVoucher" type="text" class="form-control border-secondary border-opacity-50" readonly>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input id="inputCheckNo" name="inputCheckNo" type="text" class="form-control border-secondary border-opacity-50" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12 mb-4">
                                        <div class="shadow rounded-3 p-3 bg-white">

                                            <p class="fw-medium fs-5" style="color: #090909;">Release Document</p>
                                            <hr>


                                            <div class="row">
                                                <div class="col-md-12 mb-2">
                                                    <button disabled type="submit" id="saveDetailsBtn" name="saveDetailsBtn" class="btn btn-success text-white w-100"><i class="fa-solid fa-floppy-disk"></i> Save Details</button>
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    <button disabled type="button" id="voucherBtn" name="voucherBtn" class="btn btn-primary text-white w-100"><i class="fa-solid fa-print"></i> Voucher </button>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <button disabled type="submit" id="checkBtn" name="checkBtn" class="btn btn-primary text-white w-100"><i class="fa-solid fa-print"></i> Check/Confirm</button>
                                                
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    <button disabled type="button" id="lrsBtn" name="lrsBtn" class="btn btn-primary text-white w-100" onclick="printVoucher()"><i class="fa-solid fa-print"></i> LRS / Disclosure </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <div class="shadow p-3 rounded-3 bg-white ">
                                            <p class="fw-medium fs-5">Voucher Details</p>
                                            <hr>
                                            <div class="alert text-center fw-bold" role="alert">
                                                <input type="text" readonly class="form-control-plaintext text-center fw-bold" name="particulars" id="particulars">
                                            </div>

                                            <div class="table-responsive">
                                                <div class="overflow-auto" style="height: 150px;">
                                                    <table class="table  table-hover  table-borderless " style="background-color: white;">
                                                        <thead>
                                                            <tr>
                                                                <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Account</th>
                                                                <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Account No.</th>
                                                                <th class="fw-bold fs-6 text-uppercase" style="color:#090909">SL</th>
                                                                <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Debit</th>
                                                                <th class="fw-bold fs-6 text-uppercase" style="color:#090909">Credit</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="loanTableBody"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <!-- <script src="../../js/generalledger/posting.js?<?= time() ?>"></script> -->

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
        
        <script>
            $(document).ready(function() {
                // Use jQuery to send AJAX request
                $.ajax({
                    url: '../isyn-app-v2/pages/accounts-monitoring/ajax/ajax_pending_releases.php',
                    type: 'GET',
                    success: function(response) {
                        // Update the table body with the response data
                        $('#checkVouchersTableBody').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status, error);
                    }
                });

                // Handle row click to redirect to print-lrs.php with the client ID
                $('#checkVouchersTableBody').on('click', 'tr', function() {
                    var userId = $(this).data('user-id');

                    $('#lrsBtn').click(function() {
                        // Check if a user ID is selected
                        if (userId) {
                            // Redirect to the print-lrs.php page with the selected user ID as a parameter
                            window.location.href = './print/print-lrs.php?userId=' + userId;
                        } else {
                            alert('Please select a client to print');
                        }
                    });

                    $('#voucherBtn').click(function() {
                        // Check if a user ID is selected
                        if (userId) {
                            // Redirect to the print-lrs.php page with the selected user ID as a parameter
                            window.location.href = './print/print-voucher.php?userId=' + userId;
                        } else {
                            alert('Please select a client to print');
                        }
                    });
                });
            }); // Moved the closing parenthesis here
        </script>

        <script>
            function populateFormFields(row) {
                var userId = row.dataset.userId;
                document.getElementById('IDNum').value = userId;

                console.log(userId);

                if (userId) {
                    // Make an AJAX request to fetch user details
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                var userData = JSON.parse(xhr.responseText);

                                // Populate form fields with user data
                                document.getElementById('inputReleaseType').disabled = false;
                                document.getElementById('programPenReleases').value = userData.Program;
                                document.getElementById('productPenReleases').value = userData.Product;
                                document.getElementById('staffPenReleases').value = userData.PO;
                                document.getElementById('modePenReleases').value = userData.Mode;
                                document.getElementById('termPenReleases').value = userData.Term;
                                document.getElementById('ratePenReleases').value = userData.InterestRate;
                                document.getElementById('computationPenReleases').value = userData.IntComputation;
                                document.getElementById('tagPenReleases').value = userData.Tag;
                                document.getElementById('chargesPenReleases').value = userData.IsPrededucted;

                                document.getElementById('loanAmountPenReleases').value = userData.LoanAmount;
                                document.getElementById('interestPenReleases').value = userData.Interest;
                                document.getElementById('mbaPenReleases').value = userData.MBA;
                                document.getElementById('cbuPenReleases').value = userData.CBU;
                                document.getElementById('efPenReleases').value = userData.EF;
                                document.getElementById('netAmountPenReleases').value = userData.NetAmount;

                                document.getElementById('principalAmort').value = userData.PrincipalAmo;
                                document.getElementById('interestAmort').value = userData.InterestAmo;
                                document.getElementById('mbaAmort').value = userData.MBAAmo;
                                document.getElementById('cbuAmort').value = userData.CBUAmo;
                                document.getElementById('efAmort').value = userData.EFAmo;
                                document.getElementById('totalAmort').value = userData.TotalAmo;

                                document.getElementById('particulars').value = userData.Particulars;

                                document.getElementById('ClientId').value = userData.ClientId;
                            } else {
                                console.error('Failed to fetch user details');
                            }
                        }
                    };
                    xhr.open('GET', '/isyn-app/src/includes/accounts-monitoring/ajax/ajax_check_vouchers.php?userId=' + userId, true);
                    xhr.send();
                } else {
                    clearFormFields();
                    document.getElementById('addNew').disabled = false;
                }
            }
        </script>

        <script>
            $(document).ready(function() {
                $('#inputReleaseType').on('change', function() {
                    var releaseType = $(this).val();
                    if (releaseType) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/ajax_fetch_bankAccounts.php',
                            data: {
                                inputReleaseType: releaseType
                            },
                            success: function(response) {
                                var data = JSON.parse(response);
                                $('#inputBankAccount').html(data.bank);
                            },
                            error: function(xhr, status, error) {
                                console.error(error); // Log AJAX error
                            }
                        });
                    }
                });

                $('#inputBankAccount').on('change', function() {
                    var bankAccount = $(this).val();
                    if (bankAccount) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/ajax_fetch_bankAccounts.php',
                            data: {
                                inputBankAccount: bankAccount
                            },
                            success: function(response) {
                                var data = JSON.parse(response);
                                $('#inputFundTag').html(data.fund);
                            },
                            error: function(xhr, status, error) {
                                console.error(error); // Log AJAX error
                            }
                        });
                    }
                });
            });


            $(document).ready(function() {
                $('#inputBankAccount').on('change', function() {
                    var releaseType = $('#inputReleaseType').val();
                    var bankAccount = $(this).val();

                    if (releaseType && bankAccount) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/ajax_fetch_CheckVoucher.php',
                            data: {
                                inputReleaseType: releaseType,
                                inputBankAccount: bankAccount
                            },
                            success: function(response) {
                                var data = JSON.parse(response);

                                document.getElementById('saveDetailsBtn').disabled = false;
                                document.getElementById('voucherBtn').disabled = false;
                                document.getElementById('lrsBtn').disabled = false;


                                if (data.voucher && data.checkNo) {
                                    $('#inputVoucher').val(data.voucher);
                                    $('#inputCheckNo').val(data.checkNo);
                                } else {
                                    alert('Error: Invalid response');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(error); // Log AJAX error
                                alert('Error occurred while fetching data');
                            }
                        });
                    }
                });
            });
        </script>
