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
                td {
                    font-weight: 400;
                }

                form {
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                    display: flex;
                    flex-direction: column;
                    min-height: 300px;
                    /* Adjust as needed */
                }

                label,
                thead {
                    color: #090909;
                }

                main {
                    background-color: #EAEAF6;
                    height: 100vh;
                }

                th {
                    font-weight: bold;
                    color: #090909;
                    position: sticky;
                    top: 0;
                }

                .form-footer {
                    margin-top: auto;
                    /* Pushes the footer to the bottom */
                    text-align: right;
                }
                 .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
            </style>

            <div class="container mt-4">
                <div class="shadow rounded-3 p-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2 ">Back Up</p>
                </div>
                <div class="col-md-12 mb-3 mt-4">
                    <form class="p-3 needs-validation shadow" id="backupForm" method="POST" enctype="multipart/form-data" novalidate>
                        <label for="branch" class="fw-medium fs-5">Full Data Backup</label>
                        <hr style="height: 1px">
                        <label for="backupFolder" class="form-label">Select Backup Folder</label>
                        <div class="row mb-3 mt-2">
                            <div class="col-md-2 text-end">
                                <button class="btn btn-primary float-end mx-1" id="select-folder" type="button"> <i class="fa-solid fa-pen-to-square"></i> Select Folder</button>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="folder-info" placeholder="Please choose a directory" readonly>
                            </div>

                            <div class="invalid-feedback">Please select a backup folder.</div>
                        </div>
                        <div class="text-center mb-3">
                            <p><strong>Note:</strong> <em>The system saves the backup in a pre-defined directory. Choose a backup folder to save files to that location.</em></p>
                        </div>

                        <!-- Progress Bar Container (Hidden by default) -->
                        <div id="progress-container" class="mb-3" style="display: none;">
                            <label class="form-label">Backup Progress</label>
                            <div class="progress" style="height: 25px;">
                                <div id="backup-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p id="progress-text" class="text-center mt-1 text-muted small">Waiting to start...</p>
                        </div>

                        <div class="form-footer">
                            <button class="btn btn-success" type="submit" id="start-btn"><i class="fa-solid fa-plus"></i> Start</button>
                        </div>
                    </form>
                </div>
            </div>

                    
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/backup.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
