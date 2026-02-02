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
                .container-fluid .shadow.p-5.rounded-3 form .mb-3 { margin-bottom: 1.25rem !important; }
                .container-fluid .shadow.p-5.rounded-3 form .form-label { margin-bottom: 0.75rem; }
                .container-fluid .shadow.p-5.rounded-3 form .form-control,
                .container-fluid .shadow.p-5.rounded-3 form .form-select { 
                    padding: 1rem 1.25rem; 
                    font-size: 1.1rem;
                    min-height: 50px;
                    border-radius: 8px;
                }
                .container-fluid .shadow.p-5.rounded-3 form .d-flex { margin-bottom: 1.25rem !important; }
                .container-fluid .shadow.p-5.rounded-3 form .row { margin-bottom: 1.25rem !important; }
                .container-fluid .shadow.p-5.rounded-3 form .col-form-label { 
                    margin-bottom: 0; 
                    line-height: 1.6;
                    font-size: 1.1rem;
                    padding-top: 1rem;
                    font-weight: 500;
                }
                .container-fluid .shadow.p-5.rounded-3 form .form-check-label { 
                    margin-bottom: 0; 
                    line-height: 1.6;
                    font-size: 1.05rem;
                    font-weight: 500;
                }
                .container-fluid .shadow.p-5.rounded-3 form .row .row { margin-bottom: 0.75rem !important; }
                .container-fluid .shadow.p-5.rounded-3 form label { margin-bottom: 0; line-height: 1.6; }
                .container-fluid .shadow.p-5.rounded-3 form .form-check { margin-bottom: 1.25rem !important; }
                .container-fluid .shadow.p-5.rounded-3 form .col-form-label { padding-top: 1rem; padding-bottom: 1rem; }
                .container-fluid .shadow.p-5.rounded-3 form .form-check-input { 
                    margin-top: 0.5rem; 
                    width: 1.4rem;
                    height: 1.4rem;
                }
                .container-fluid .shadow.p-5.rounded-3 form .btn {
                    padding: 0.75rem 2rem;
                    font-size: 1.1rem;
                    font-weight: 500;
                    border-radius: 8px;
                    min-height: 45px;
                }
                th {
                    font-weight: bold;
                    color: #090909;
                    position: sticky;
                    top: 0;
                }
            </style>

            <div class="container-fluid mt-1">
                <div class="shadow p-3 rounded-3 mb-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Purchases Sales</p>
                </div>

                <div class="row justify-content-center mt-3">
                    <div class="col-sm-12 col-md-11 col-lg-10">
                        <div class="shadow p-5 rounded-3" style="background-color: white; min-height: 580px;">
                            <form action="POST" class="h-100 d-flex flex-column">
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label" for="purchaseSelect">Journal:</label>
                                    <div class="col-sm-10">
                                        <select id="purchaseSelect" name="purchaseSelect" class="form-select">
                                            <option value="PJ">Purchase Journal</option>
                                            <option value="SJ">Sales Journal</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-check-inline d-flex align-items-center">
                                        <input class="form-check-input me-2" type="radio" value="Yes" name="option" id="asof" onclick="AsOf('FromTo');">
                                        <label class="form-check-label" for="asof">As Of</label>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label" for="fromAsOf">From:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="fromAsOf" name="fromAsOf" class="form-control" placeholder="mm/dd/yyyy" disabled>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label" for="toAsOf">To:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="toAsOf" name="toAsOf" class="form-control" placeholder="mm/dd/yyyy" disabled>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-check-inline d-flex align-items-center">
                                        <input class="form-check-input me-2" type="radio" value="Yes" name="option" id="asofMonthly" onclick="AsOf('Monthly');">
                                        <label class="form-check-label" for="asofMonthly">As Of Monthly</label>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label" for="month">Month:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="month" name="month" class="form-control" placeholder="mm/yyyy" disabled>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <div class="text-end">
                                        <button class="btn btn-primary" type="button" id="searchBtn" name="searchBtn" onclick="Search();">
                                            <i class="fa-solid fa-magnifying-glass"></i> Search
                                        </button>
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

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/purchaseandsales.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>