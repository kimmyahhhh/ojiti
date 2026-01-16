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
            td {
                font-weight: 400;
            }
        
            form {
                width: 100%;
                max-width: 600px;
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

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <div class="container mt-4 mb-5">
                <div class=" p-3 shadow rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="my-2 fs-5">ACash Module</p>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-5">
                        <div class=" shadow p-3 rounded-3  " style="background-color: white;">
                            <div class=" d-flex align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Acash Information</p>
                                <label for="file-upload" class="btn btn-info mb-2"><i class="fa-solid fa-upload"></i> Upload</label>
                                <input id="file-upload" type="file" style="display: none;">
                            </div>
                            <hr style="height:1px;">
                            <table class="table">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class=" shadow p-3 rounded-3  " style="background-color: white;">
                            <div class=" d-flex align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">ECpay Transaction</p>
                                <label for="file-upload" class="btn btn-info mb-2"><i class="fa-solid fa-upload"></i> Upload</label>
                                <input id="file-upload" type="file" style="display: none;">

                            </div>
                            <hr style="height:1px;">
                            <table class="table">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php
            include('../../includes/pages.footer.php');
        ?>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
