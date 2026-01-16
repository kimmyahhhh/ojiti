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
                    height: 100%;
                }
                 .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
            </style>

            <div class="container mt-4">
                <div class=" shadow p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Paid/Unpaid Items Report</p>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 rounded-2 shadow" style="background-color: white;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="fw-medium fs-5" style="color: #090909;">Filter</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <button id="searchButton" class="btn btn-primary mx-1">
                                            <i class="fa-solid fa-magnifying-glass"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr style="height: 1px">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="fromDate" class="form-label mt-2">From</label>
                                    <input type="date" class="form-control" id="fromDate" placeholder="" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="toDate" class="form-label mt-2">To</label>
                                    <input type="date" class="form-control" id="toDate" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="" id="consignmentCheckbox">
                                <label class="form-check-label" for="consignmentCheckbox">
                                    Consignment
                                </label>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label for="typeSelect" class="form-label">Type</label>
                                <select class="form-select" id="typeSelect" aria-label="Default select example">
                                    <option value="1">Paid</option>
                                    <option value="2">Unpaid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="p-3 rounded-2 shadow" style="background-color: white;">
                            <div class="col-md-6">
                                <p class="fw-medium fs-5" style="color: #090909;">Total Summary</p>
                            </div>
                            <hr style="height: 1px">
                            <div class="col-md-6 mt-4">
                                <label for="" class="form-label ">Total Price:</label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="" class="form-label">Total SRP:</label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="" class="form-label">Total Markup:</label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="" class="form-label">Total Quantity:</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="table-responsive p-3 rounded-2 shadow" style="background-color: white;">
                            <div class="col-md-6">
                                <p class="fw-medium fs-5" style="color: #090909;">Client List</p>
                            </div>
                            <hr style="height: 1px">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckIndeterminate">
                                <label class="form-check-label" for="flexCheckIndeterminate">
                                    WITH SI
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="fromTo" class="form-label">From:</label>
                                    <input type="text" class="form-control" id="fromTo" placeholder="" required>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to" class="form-label">To:</label>
                                        <input type="text" class="form-control" id="to" placeholder="" required>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-hover table-borderless table-responsive table text-center" style="background-color: white;">
                                <thead>
                                    <th class="fw-bold fs-6 text-center" style="color: #090909;">
                                        Customer
                                    </th>
                                    <th class="fw-bold fs-6 text-center" style="color: #090909;">
                                        Total Amount Payables
                                    </th>
                                </thead>
                                <tbody>
                                    <!-- replace with acquiring data from database -->
                                    <tr>
                                        <td class="text-center">John Ray</td>
                                        <td class="text-center">20000</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">John Ray</td>
                                        <td class="text-center">20000</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="fromTo" class="form-label">Total Clients</label>
                                    <input type="text" class="form-control" id="fromTo" placeholder="" required>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to" class="form-label">Total</label>
                                        <input type="number" class="form-control" id="to" placeholder="0.0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12 mt-2 mb-3">
                        <div class="table-responsive p-3 rounded-2 shadow" style="background-color: white;">
                            <div class="row align-items-center">
                                <div class="col-7">
                                    <p class="fw-medium fs-5" style="color: #090909;">Items</p>
                                </div>
                                <div class="col-5 text-end">
                                    <input type="search" id="tableSearch" class="form-control me-2" placeholder="Search" aria-label="Search" onkeyup="searchTable()">
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <table id="itemsTable" class="table table-hover table-borderless table-responsive" style="background-color: white;">
                                <thead>
                                    <tr>
                                        <th class="fw-bold fs-6" style="color:#090909">SI No.</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Date</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Status</th>
                                        <th class="fw-bold fs-6" style="color:#090909">City</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Product</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Unit Price</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Amount</th>
                                        <th class="fw-bold fs-6" style="color:#090909">VAT Sales</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Total SRP</th>
                                        <th class="fw-bold fs-6" style="color:#090909">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>02/20/2024</td>
                                        <td>N/A</td>
                                        <td>Cabanatuan</td>
                                        <td>Laptop</td>
                                        <td>20000</td>
                                        <td>20000</td>
                                        <td>1000</td>
                                        <td>21000</td>
                                        <td>Cash</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>03/15/2024</td>
                                        <td>Pending</td>
                                        <td>Manila</td>
                                        <td>Phone</td>
                                        <td>15000</td>
                                        <td>15000</td>
                                        <td>800</td>
                                        <td>15800</td>
                                        <td>Credit</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>04/05/2024</td>
                                        <td>Delivered</td>
                                        <td>Quezon City</td>
                                        <td>Tablet</td>
                                        <td>10000</td>
                                        <td>10000</td>
                                        <td>500</td>
                                        <td>10500</td>
                                        <td>Cash</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>05/12/2024</td>
                                        <td>Cancelled</td>
                                        <td>Makati</td>
                                        <td>Monitor</td>
                                        <td>8000</td>
                                        <td>8000</td>
                                        <td>400</td>
                                        <td>8400</td>
                                        <td>Credit</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>06/30/2024</td>
                                        <td>Shipped</td>
                                        <td>Pasig</td>
                                        <td>Keyboard</td>
                                        <td>2000</td>
                                        <td>2000</td>
                                        <td>100</td>
                                        <td>2100</td>
                                        <td>Cash</td>
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

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/generalledger/posting.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>

<script>
    function searchTable() {
        // Declare variables
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("tableSearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("itemsTable");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 1; i < tr.length; i++) { // Start from 1 to skip the table header
            tr[i].style.display = "none"; // Initially hide all rows
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = ""; // Show the row if match is found
                        break; // Stop searching this row as we already found a match
                    }
                }
            }
        }
    }
</script>
