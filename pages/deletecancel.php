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
      <title>iSyn | Modify Transactions</title>

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
                label,
                th {
                    color: #090909;
                }

                form {
                    width: 100%;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                }

                main {
                    background-color: #EAEAF6;
                    height: 100vh;
                }
                 .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
            </style>

            <div class="container mt-4 mb-4">
                <div class=" shadow p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Modify Transactions</p>
                </div>
                <div class="row mt-4 mb-5">
                    <div class="col-md-8">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="fw-medium fs-5 text-dark">Today's Transaction</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="deleteTransaction" class="btn btn-danger me-2" onclick="DeleteTransaction()" disabled>
                                            <i class="fa-solid fa-trash-can"></i> Delete Transaction
                                        </button>
                                        <button type="button" id="cancelTransaction" class="btn btn-warning text-white me-2" onclick="CancelTransaction()" disabled>
                                            <i class="fa-regular fa-circle-xmark"></i> Cancel OR
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <div class="col-md-12 mb-2">
                                <div class="form-group row mb-2">
                                    <label for="orTypes" class="col-sm-auto col-form-label">OR Type</label>
                                    <div class="col-sm-4">
                                        <select name="orTypes" id="orTypes" class="form-select" onchange="LoadTransactions(this.value)">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="transactionTbl" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <th>OR No.</th>
                                        <th>Name</th>
                                        <th>ClientNo</th>
                                        <th>LoanID</th>
                                        <th>Nature</th>
                                        <th>Fund</th>
                                        <th>CDate</th>
                                    </thead>
                                    <tbody id="transactionList">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="fw-medium fs-5 text-dark">Payment Details</p>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <div class="col-md-12">
                                <form method="POST">
                                    <div class="form-group row mb-2">
                                        <label for="orno" class="col-sm-3 col-form-label">OR No: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="orno" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="fund" class="col-sm-3 col-form-label">Fund: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="fund" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="po" class="col-sm-3 col-form-label">PO: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="po" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label for="nature" class="col-sm-3 col-form-label">Nature: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="nature" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="principal" class="col-sm-3 col-form-label">Principal: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="principal" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="interest" class="col-sm-3 col-form-label">Interest: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="interest" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="cbu" class="col-sm-3 col-form-label">CBU: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="cbu" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="penalty" class="col-sm-3 col-form-label">Penalty: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="penalty" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="mba" class="col-sm-3 col-form-label">MBA: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="mba" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <label for="total" class="col-sm-3 col-form-label">Total: </label>
                                        <div class="col-sm-6">
                                            <input type="text" id="total" class="form-control" disabled>
                                        </div>
                                    </div>
                                </form>
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
        <script src="../../js/cashier/modifytransaction.js?<?= time() ?>"></script>


    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
