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
        .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
    </style>

            <div class="container mt-4 mb-4">
                <div class="customer-profile p-3 shadow rounded-2" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Update Table Sctructure</p>
                </div>

                <div class="container">
                    <div class="row mt-4 shadow p-3 rounded-3" style="background-color: white;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="fw-medium fs-5" style="color: #090909;">Funding</p>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <button class="text-white btn btn-primary me-2" type="button"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                        <button class="btn btn-primary me-2" type="button"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                        <button class="btn btn-danger" type="button"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px">
                        </div>
                        <div class="col-md-12">
                            <label for="" class="form-label">Fund Name</label>
                            <input type="text" class="form-control" id="" placeholder="" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-5">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <p class="fw-medium fs-5" style="color: #090909;">AMS Fund</p>
                            <hr style="height: 1px">
                            <table class="table table-hover table-borderless" style="background-color: white;">
                                <tbody>
                                    <tr>
                                        <td>70</td>
                                    </tr>
                                    <tr>
                                        <td>200</td>
                                    </tr>
                                    <tr>
                                        <td>200</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <div class="row" style="margin: 50%;">
                            <div class="col-md-12 align-items-center justify-content-center">
                                <button class="btn btn-primary mb-2" type="button"><i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                            <div class="col-md-12 align-items-center justify-content-center">
                                <button class="btn btn-danger" type="button"><i class="fa-solid fa-arrow-left"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <p class="fw-medium fs-5" style="color: #090909;">GL Fund</p>
                            <hr style="height: 1px">
                            <table class="table table-hover table-borderless" style="background-color: white;">
                                <tbody>
                                    <tr>
                                        <td>70</td>
                                    </tr>
                                    <tr>
                                        <td>100</td>
                                    </tr>
                                    <tr>
                                        <td>20</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/outgoinginventory.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
