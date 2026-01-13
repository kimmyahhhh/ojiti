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
                <div class="shadow p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Purchases Sales</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mt-1">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <form action="POST">
                                <div class="mb-2 row">
                                    <label class="col-sm-2 col-form-label" for="purchaseSelect">Select:</label>
                                    <div class="col-sm-9">
                                        <select id="purchaseSelect" name="purchaseSelect" class="form-select">
                                            <option value="PJ">Purchase Journal</option>
                                            <option value="SJ">Sales Journal</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-start mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input me-1" type="radio" value="Yes" name="option" id="asof" onclick="AsOf('FromTo');">
                                        <label class="form-check-label" for="asof">As Of</label>
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <label class="col-sm-2 col-form-label" for="fromAsOf">From:</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="fromAsOf" name="fromAsOf" class="form-control" disabled>
                                    </div>
                                </div>
                                
                                <div class="mb-2 row">
                                    <label class="col-sm-2 col-form-label" for="toAsOf">To:</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="toAsOf" name="toAsOf" class="form-control" disabled>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-start mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input me-1" type="radio" value="Yes" name="option" id="asofMonthly" onclick="AsOf('Monthly');">
                                        <label class="form-check-label" for="asofMonthly">As Of Monthly</label>
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label" for="month">Month:</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="month" name="month" class="form-control" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-11 d-flex justify-content-end">
                                    <button class="btn btn-primary" type="button" id="searchBtn" name="searchBtn" onclick="Search();">
                                        <i class="fa-solid fa-magnifying-glass"></i> Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/purchaseandsales.js?<?= time() ?>"></script>

        <script>
            // Initialize date pickers and prevent future dates
            $(document).ready(function() {
                // From / To dates (As Of)
                $('#fromAsOf, #toAsOf').datetimepicker({
                    format: 'Y-m-d',
                    timepicker: false,
                    maxDate: 0,       // 0 = today, disallow future dates
                    scrollInput: false
                });

                // Monthly date (month-year only)
                $('#month').datetimepicker({
                    format: 'Y-m',
                    timepicker: false,
                    maxDate: 0,       // disallow future months
                    scrollInput: false
                });
            });
        </script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>