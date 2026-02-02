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
                    width: 95%;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                }

                main {
                    background-color: #EAEAF6;
                    height: 100%;
                }
                form-label {
                    color: #090909;
                }
                 .container { max-width: 95%; width: 95%; margin: 0 auto; padding-left: 0; padding-right: 0; }
                 .equal-square { position: relative; width: 100%; }
                 .equal-square::before { content: ""; display: block; padding-top: 100%; }
                 .equal-square .square-inner { position: absolute; inset: 0; display: flex; flex-direction: column; border-radius: 10px; }
                 .equal-square .square-content { flex: 1 1 auto; overflow: auto; }
                 .equal-short { position: relative; width: 100%; }
                 .equal-short::before { content: ""; display: block; padding-top: 60%; }
                 .equal-short .square-inner { position: absolute; inset: 0; display: flex; flex-direction: column; border-radius: 10px; }
                 .equal-short .square-content { flex: 1 1 auto; overflow: auto; }
                 .equal-medium { position: relative; width: 100%; }
                 .equal-medium::before { content: ""; display: block; padding-top: 80%; }
                 .equal-medium .square-inner { position: absolute; inset: 0; display: flex; flex-direction: column; border-radius: 10px; }
                 .equal-medium .square-content { flex: 1 1 auto; overflow: auto; }
                 #totalPriceLabel, #totalSRPLabel, #totalMarkupLabel, #totalQuantityLabel { font-size: 20px; font-weight: 700; }
                 #totalSummaryCard .form-label { font-size: 1.15rem; }
                 #summaryPie { width: 100%; height: 300px; }
                 .client-table { table-layout: fixed; }
                 .client-table col.customer { width: 50%; }
                 .client-table col.total { width: 50%; }
                 .client-table th, .client-table td { padding-left: 12px; padding-right: 12px; }
                 .client-table td:nth-child(2), .client-table th:nth-child(2) { text-align: right; }
                 .client-table td:first-child, .client-table th:first-child { text-align: left; }
 
            </style>

            <div class="container mt-4">
                <div class=" shadow p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Paid/Unpaid Items Report</p>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="rounded-2 shadow equal-short">
                            <div class="square-inner p-3" style="background-color: white;">
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
                             <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckIndeterminate">
                                        <label class="form-check-label" for="flexCheckIndeterminate">WITH SI</label>
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
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="rounded-2 shadow equal-short" id="totalSummaryCard">
                            <div class="square-inner p-3" style="background-color: white;">
                            <div class="col-md-6">
                                <p class="fw-medium fs-5" style="color: #090909;">Total Summary</p>
                            </div>
                            <hr style="height: 1px">
                            <div class="row align-items-center">
                                <div class="col-md-7 order-md-2">
                                    <div id="summaryPie"></div>
                                </div>
                                <div class="col-md-5 order-md-1 d-flex align-items-center">
                                    <div class="w-100">
                                        <div class="mt-2">
                                            <label class="form-label ">Total Price: <span id="totalPriceLabel">0.00</span></label>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label">Total SRP: <span id="totalSRPLabel">0.00</span></label>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label">Total Markup: <span id="totalMarkupLabel">0.00</span></label>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label">Total Quantity: <span id="totalQuantityLabel">0</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client List moved to bottom row -->


                    <!-- Bottom row: Client List + Items side by side -->
                    <div class="col-md-12 mt-2 mb-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="rounded-2 shadow equal-medium">
                                    <div class="square-inner p-3" style="background-color: white;">
                                    <div class="row align-items-center">
                                        <div class="col-7">
                                            <p class="fw-medium fs-5" style="color: #090909;">Client List</p>
                                        </div>
                                        <div class="col-5 text-end">
                                            <input type="search" id="clientSearch" class="form-control" placeholder="Search" aria-label="Search">
                                        </div>
                                    </div>
                                    <hr style="height: 1px">
                                   
                                    
                                    <div class="square-content">
                                        <table class="table table-hover table-borderless table-responsive client-table" style="background-color: white;">
                                            <colgroup>
                                                <col class="customer">
                                                <col class="total">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="fw-bold fs-6" style="color: #090909;">Customer</th>
                                                    <th class="fw-bold fs-6" style="color: #090909;">Total Amount Payables</th>
                                                </tr>
                                            </thead>
                                            <tbody id="clientListTbody"></tbody>
                                        </table>
                                        <div id="clientPagination" class="mt-2"></div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Total Clients</label>
                                                <input type="text" class="form-control" id="totalClientsCount" placeholder="" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Total</label>
                                                    <input type="text" class="form-control" id="totalPayables" placeholder="0.00" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="rounded-2 shadow equal-medium">
                                    <div class="square-inner p-3" style="background-color: white;">
                                    <div class="row align-items-center">
                                        <div class="col-7">
                                            <p class="fw-medium fs-5" style="color: #090909;">Items</p>
                                        </div>
                                        <div class="col-5 text-end">
                                            <input type="search" id="tableSearch" class="form-control me-2" placeholder="Search" aria-label="Search" onkeyup="searchTable()">
                                        </div>
                                    </div>
                                    <hr style="height: 1px">
                                    <div class="table-responsive square-content">
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
                                            <tbody id="itemsTableBody"></tbody>
                                        </table>
                                        <div id="itemsPagination" class="mt-2"></div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<!-- Item Details Modal -->
<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itemDetailsLabel">Item Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="itemDetailsBody"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Client Details Modal -->
<div class="modal fade" id="clientDetailsModal" tabindex="-1" aria-labelledby="clientDetailsLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientDetailsLabel">Client Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="clientDetailsBody"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../assets/js/charts/apexcharts.js"></script>
        <script src="../../js/inventorymanagement/paidunpaiditems.js?<?= time() ?>"></script>

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
