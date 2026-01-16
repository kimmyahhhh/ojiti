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

            <div class="container mt-4">
                <div class="customer-profile  p-3 shadow rounded-2" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Financial Statement</p>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="p-3 shadow rounded-2" style="background-color: white;">
                            <p class="fw-medium fs-5" style="color: #090909;">Date Filter</p>
                            <hr style="height: 2px">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1"> As Of</label>
                                    </div>
                                    <input type="date" class="form-control" id="asOfDate" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2"> Data Range</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" id="startDate" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" id="endDate" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 shadow rounded-2" style="background-color: white;">
                            <p class="fw-medium fs-5" style="color: #090909;">Funding</p>
                            <hr style="height: 2px">
                            <div class="col-md-12">
                                <label for="companyname" class="form-label">Fund/Tag</label>
                                <select class="form-select" aria-label="Default select example">
                                    <option value="">One</option>
                                    <option value="">Two</option>
                                    <option value="">Three</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 mb-4">
                        <div class="  p-3 shadow rounded-2 " style="background-color: white;">
                            <p class="fw-medium fs-5" style="color: #090909;">Report Filter</p>
                            <hr style="height: 2px">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">Income Statement</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">Financial Statement</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">Cash Flow Statement</label>
                            </div>
                            <div class="my-4">
                                <div class="row justify-content-end">
                                    <div class="col-md-6 text-md-end">
                                        <button class="btn btn-primary px-3 py-2 mx-1" type="button"><i class="fa-solid fa-repeat"></i> Retrieve</button>
                                        <button class="text-white btn btn-warning px-2 py-2 mx-1" type="button"><i class="fa-solid fa-arrows-rotate"></i> Clear Filter</button>
                                    </div>
                                </div>
                            </div>
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

<script>
    // calendar function
    document.addEventListener('DOMContentLoaded', function() {
        const dateInputs = document.querySelectorAll('.form-control[type="date"]');

        dateInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                let value = input.value;
                let parts = value.split('-');
                if (parts.length === 3) {
                    let year = parts[0].substring(0, 4); // Limiting to 4 digits
                    let month = parts[1];
                    let day = parts[2];
                    input.value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                }
            });

            input.addEventListener('blur', function() {
                let value = input.value;
                let parts = value.split('-');
                if (parts.length === 3) {
                    let year = parts[0].substring(0, 4); // Limiting to 4 digits
                    let month = parts[1].padStart(2, '0');
                    let day = parts[2].padStart(2, '0');
                    input.value = `${year}-${month}-${day}`;
                }
            });
        });
    });
</script>
