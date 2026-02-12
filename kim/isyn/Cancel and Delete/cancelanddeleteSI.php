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
                :root {
                    --primary-color: #0d6efd;
                    --bg-color: #f4f6f9;
                    --card-bg: #ffffff;
                    --text-color: #333;
                    --text-muted: #6c757d;
                    --border-color: #e9ecef;
                    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
                    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
                    --radius-md: 10px;
                    --radius-lg: 15px;
                }

                body {
                    background-color: var(--bg-color);
                    color: var(--text-color);
                    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
                }

                /* Card Styling */
                .custom-card {
                    background-color: var(--card-bg);
                    border-radius: var(--radius-lg);
                    box-shadow: var(--shadow-sm);
                    border: 1px solid rgba(0,0,0,0.02);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    margin-bottom: 1.5rem;
                }

                .custom-card:hover {
                    box-shadow: var(--shadow-md);
                }

                .card-header-title {
                    color: var(--primary-color);
                    font-weight: 600;
                    font-size: 1.1rem;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 1rem;
                }

                .section-title {
                    font-weight: 600;
                    color: var(--text-color);
                    margin-bottom: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                /* Form Elements */
                .form-label {
                    font-weight: 600;
                    font-size: 0.9rem;
                    color: var(--text-muted);
                    margin-bottom: 0.3rem;
                }

                .form-control {
                    border-radius: var(--radius-md);
                    border: 1px solid #dee2e6;
                    padding: 0.6rem 0.8rem;
                    font-size: 0.95rem;
                    transition: all 0.2s;
                }

                .form-control:focus {
                    border-color: var(--primary-color);
                    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
                }

                .form-control[readonly] {
                    background-color: #f8f9fa;
                    border-color: #e9ecef;
                    color: #495057;
                    font-weight: 500;
                }

                /* Table Styling */
                .table-container {
                    border-radius: var(--radius-md);
                    border: 1px solid var(--border-color);
                    background-color: var(--card-bg);
                }

                /* DataTables Customization */
                .dataTables_wrapper .dataTables_paginate .paginate_button {
                    padding: 0.4rem 0.8rem;
                    margin-left: 4px;
                    border-radius: var(--radius-md) !important;
                    border: 1px solid var(--border-color) !important;
                    background: var(--card-bg) !important;
                    color: var(--text-color) !important;
                }

                .dataTables_wrapper .dataTables_paginate .paginate_button.current,
                .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                    background: var(--primary-color) !important;
                    color: white !important;
                    border-color: var(--primary-color) !important;
                }

                .dataTables_wrapper .dataTables_info {
                    padding-top: 1rem;
                    font-size: 0.85rem;
                    color: var(--text-muted);
                }

                .dataTables_wrapper .dataTables_paginate {
                    padding-top: 1rem;
                }

                .table {
                    margin-bottom: 0;
                    table-layout: fixed;
                    width: 100% !important;
                }

                .table th {
                    background-color: #f8f9fa;
                    color: #495057;
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 0.75rem;
                    letter-spacing: 0.5px;
                    padding: 12px 15px;
                    border-bottom: 2px solid var(--border-color);
                    position: sticky;
                    top: 0;
                    z-index: 10;
                }

                .table td {
                    padding: 10px 15px;
                    vertical-align: middle;
                    border-bottom: 1px solid var(--border-color);
                    font-size: 0.9rem;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }

                .table tbody tr:hover {
                    background-color: #f1f8ff;
                    cursor: pointer;
                }

                .table tbody tr.selected {
                    background-color: #e7f1ff;
                    border-left: 4px solid var(--primary-color);
                }

                /* Buttons */
                .btn {
                    border-radius: var(--radius-md);
                    padding: 0.6rem 1.2rem;
                    font-weight: 500;
                    transition: all 0.2s;
                }

                .btn-primary {
                    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
                }

                .btn-danger {
                    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
                }

                .btn-warning {
                    color: #fff;
                    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
                }

                .btn-warning:hover {
                    color: #fff;
                }

                .btn:disabled {
                    opacity: 0.6;
                    box-shadow: none;
                }

                /* Details Section */
                .details-group {
                    margin-bottom: 0.8rem;
                }

                .details-label {
                    font-size: 0.8rem;
                    color: var(--text-muted);
                    font-weight: 600;
                    text-transform: uppercase;
                    margin-bottom: 0.2rem;
                }

                /* Modal Styling */
                .modal-content {
                    border-radius: var(--radius-lg);
                    border: none;
                    box-shadow: var(--shadow-md);
                }

                .modal-header {
                    border-bottom: 1px solid var(--border-color);
                    padding: 1.5rem;
                }

                .modal-body {
                    padding: 1.5rem;
                }

                .modal-footer {
                    border-top: 1px solid var(--border-color);
                    padding: 1rem 1.5rem;
                }
            </style>


            <div class="container-fluid mt-4">
                <div class="custom-card p-3 mb-4">
                    <div class="card-header-title">
                        <i class="fa-solid fa-file-circle-xmark fs-4"></i> <span class="fs-5">Cancel and Delete SI</span>
                    </div>
                </div>

                <div class="row">
                    <!-- filter -->
                    <div class="col-md-12">
                        <div class="custom-card p-4">
                            <div class="section-title">
                                <i class="fa-solid fa-filter text-primary"></i> Product Filter
                            </div>
                            <form class="row g-3 align-items-end" onsubmit="return false;">
                                <div class="col-md-4">
                                    <label class="form-label" for="transactionDate">Transaction Month</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-regular fa-calendar-days text-muted"></i></span>
                                        <input type="text" id="transactionDate" name="transactionDate" class="form-control" onchange="ClearAll();" placeholder="Select month/year">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="searchBtn" name="searchBtn" class="btn btn-primary w-100" onclick="LoadTransactionsOnDate();">
                                        <i class="fa-solid fa-magnifying-glass me-2"></i> Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!--today's transaction-->
                    <div class="col-md-8">
                        <div class="custom-card p-4" id="transactionTableContainer">
                            <div class="section-title">
                                <i class="fa-solid fa-list-check text-primary"></i> Today's Transactions
                            </div>
                            <div class="table-container">
                                <table id="transactionTbl" class="table table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%">SI No</th>
                                            <th style="width: 40%">Product Name</th>
                                            <th style="width: 25%">Sold To</th>
                                            <th style="width: 20%">Date Sold</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactionList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--transaction details-->
                    <div class="col-md-4">
                        <div class="custom-card p-4" id="transactionDetailsContainer">
                            <div class="section-title">
                                <i class="fa-solid fa-circle-info text-primary"></i> Transaction Details
                            </div>

                            <div class="row g-3">
                                <div class="col-12 details-group">
                                    <label class="details-label">Date Sold</label>
                                    <input type="text" class="form-control" id="dateSold" readonly>
                                </div>

                                <div class="col-12 details-group">
                                    <label class="details-label">Sold To</label>
                                    <input type="text" class="form-control" id="soldTo" readonly>
                                </div>

                                <div class="col-12 details-group">
                                    <label class="details-label">Supplier SI</label>
                                    <input type="text" class="form-control" id="supplierSI" readonly>
                                </div>

                                <div class="col-12 details-group">
                                    <label class="details-label">Serial No.</label>
                                    <input type="text" class="form-control" id="SINo" readonly>
                                </div>

                                <div class="col-12 details-group">
                                    <label class="details-label">Product</label>
                                    <input type="text" class="form-control" id="product" readonly>
                                </div>

                                <div class="col-12 details-group">
                                    <label class="details-label">Supplier</label>
                                    <input type="text" class="form-control" id="supplier" readonly>
                                </div>

                                <div class="col-6 details-group">
                                    <label class="details-label">Quantity</label>
                                    <input type="text" class="form-control" id="quantity" readonly>
                                </div>

                                <div class="col-6 details-group">
                                    <label class="details-label">Markup</label>
                                    <input type="text" class="form-control" id="markup" readonly>
                                </div>

                                <div class="col-6 details-group">
                                    <label class="details-label">Dealer Price</label>
                                    <input type="text" class="form-control" id="dealersPrice" readonly>
                                </div>

                                <div class="col-6 details-group">
                                    <label class="details-label">Total Dealer Price</label>
                                    <input type="text" class="form-control" id="totalPrice" readonly>
                                </div>

                                <div class="col-6 details-group">
                                    <label class="details-label">SRP</label>
                                    <input type="text" class="form-control" id="srp" readonly>
                                </div>

                                <div class="col-6 details-group">
                                    <label class="details-label">Total SRP</label>
                                    <input type="text" class="form-control" id="totalsrp" readonly>
                                </div>

                                <div class="col-12 details-group">
                                    <label class="details-label">Total Markup</label>
                                    <input type="text" class="form-control" id="totalMarkup" readonly>
                                </div>

                                <div class="col-12 pt-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button class="btn btn-danger w-100" id="deleteBtn" onclick="DELETE()" disabled>
                                                <i class="fa-solid fa-trash-can me-2"></i> Delete
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-warning w-100" id="cancelBtn" onclick="CANCEL()" disabled>
                                                <i class="fa-solid fa-circle-xmark me-2"></i> Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="cancelReasonMDL" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelReason" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-dark" id="exampleModalLabel">
                                <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Cancel Reason
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="cancelReason" class="form-label">Please provide a reason for cancellation</label>
                                <textarea id="cancelReason" name="cancelReason" class="form-control" rows="3" placeholder="Enter reason here..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary px-4" type="button" id="proceedCancel" name="proceedCancel" onclick="ProceedCancel();">
                                <i class="fa-solid fa-check me-2"></i> Confirm Cancellation
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/cancelanddeleteSI.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
