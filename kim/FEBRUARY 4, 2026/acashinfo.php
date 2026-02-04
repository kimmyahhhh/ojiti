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
            /* Custom CSS for User Request */
            .dataTables_wrapper .row:nth-of-type(2) {
                overflow-x: auto;
                flex-wrap: nowrap;
            }
            
            /* Position Search Bar on Tab Line */
            #acash-info .dataTables_wrapper .row:first-child,
            #ecpay-transaction .dataTables_wrapper .row:first-child {
                position: absolute !important;
                top: 5px !important; 
                right: 180px !important;
                width: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                z-index: 100;
            }

            .tab-pane .dataTables_wrapper .dataTables_length {
                display: none !important;
            }
            
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
            
            .table {
                font-size: 14px;
                background-color: #ffffff;
            }
            
            .table th {
                background-color: #f8f9fa;
                font-weight: 600;
                color: #495057;
                border-bottom: 2px solid #dee2e6;
                padding: 12px 8px;
            }
            
            .table td {
                padding: 10px 8px;
                vertical-align: middle;
                border-color: #e9ecef;
            }
            
            .table tbody tr:hover {
                background-color: #f8f9fa;
            }
            
            .nav-tabs .nav-link {
                font-size: 15px;
                font-weight: 500;
            }
            
            .nav-tabs .nav-link.active {
                font-weight: 600;
            }

            #upload-status-notification {
                position: fixed;
                top: 80px;
                right: 20px;
                z-index: 9999;
                min-width: 250px;
                display: none;
            }

            /* Increase size of Top 5 List items */
            #acash-analytics-list .list-group-item,
            #ecpay-analytics-list .list-group-item {
                font-size: 1.2rem; 
                font-weight: 500;
                padding: 15px;
            }

            #acash-analytics-list .badge,
            #ecpay-analytics-list .badge {
                font-size: 1.0rem; 
                padding: 8px 12px;
            }
        </style>

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

        <!-- Notification Container -->
        <div id="upload-status-notification" class="alert alert-info shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status" id="upload-spinner">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <i class="fa-solid fa-check-circle me-2 d-none" id="upload-success-icon"></i>
                <i class="fa-solid fa-circle-exclamation me-2 d-none" id="upload-error-icon"></i>
                <span id="upload-status-text">Uploading file...</span>
            </div>
        </div>

            <div class="container-fluid mt-4 mb-5">
                <div class=" p-3 shadow rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="my-2 fs-5"><i class="fa-solid fa-money-bill-wave me-2"></i>ACash Module</p>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class=" shadow p-3 rounded-3" style="background-color: white;">
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs mb-3" id="acashTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="acash-info-tab" data-bs-toggle="tab" data-bs-target="#acash-info" type="button" role="tab" aria-controls="acash-info" aria-selected="true">
                                        <i class="fa-solid fa-money-bill-wave me-2"></i>Acash Information
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ecpay-transaction-tab" data-bs-toggle="tab" data-bs-target="#ecpay-transaction" type="button" role="tab" aria-controls="ecpay-transaction" aria-selected="false">
                                        <i class="fa-solid fa-money-bill-wave me-2"></i>ECpay Transaction
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="acashTabsContent">
                                <!-- Acash Information Tab -->
                                <div class="tab-pane fade show active" id="acash-info" role="tabpanel" aria-labelledby="acash-info-tab">
                                    <div class=" d-flex align-items-center justify-content-between mb-3">
                                        <p class="fw-medium fs-5" style="color: #090909;">Acash Information</p>
                                        <div>
                                            <button class="btn btn-secondary mb-2 me-1" onclick="PrintAcashInfo()"><i class="fa-solid fa-print"></i> Print</button>
                                            <label for="acash-upload-custom" class="btn btn-info mb-2 me-1"><i class="fa-solid fa-upload"></i> Upload Custom</label>
                                            <input id="acash-upload-custom" type="file" style="display: none;">
                                            <label for="acash-upload-raw" class="btn btn-info mb-2"><i class="fa-solid fa-upload"></i> Upload Raw</label>
                                            <input id="acash-upload-raw" type="file" style="display: none;">
                                        </div>
                                    </div>
                                    <hr style="height:1px;">

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Filter By</label>
                                            <select id="acash-analytics-filter" class="form-select">
                                                <option value="Overview">Overview (All Categories)</option>
                                                <option value="HEADOFFICE">HEADOFFICE</option>
                                                <option value="EXTERNAL CLIENT">EXTERNAL CLIENT</option>
                                                <option value="MFI BRANCHES">MFI BRANCHES</option>
                                                <option value="STAFF">STAFF</option>
                                                <option value="BUSINESS UNIT">BUSINESS UNIT</option>
                                                <option value="OTHERS">OTHERS</option>
                                                <option value="INDIVIDUAL">INDIVIDUAL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                             <label class="form-label">Metric</label>
                                             <select id="acash-analytics-metric" class="form-select">
                                                 <option value="all">All (Amount & Count)</option>
                                                 <option value="amount">Total Amount</option>
                                                 <option value="count">Transaction Count</option>
                                             </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-primary w-100" onclick="LoadSpecificAnalytics('acash', '#acash-analytics-chart', '#acash-analytics-list')">Apply Filter</button>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Acash Top Performers</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div id="acash-analytics-chart" style="min-height: 350px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Top 5 List</h4>
                                                </div>
                                                <div class="card-body">
                                                    <ul id="acash-analytics-list" class="list-group list-group-flush">
                                                        <li class="list-group-item text-center">Loading...</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="position-relative">
                                        <ul class="nav nav-tabs mb-3" id="acashSubTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="acash-main-tab" data-bs-toggle="tab" data-bs-target="#acash-main" type="button" role="tab" aria-controls="acash-main" aria-selected="true">Main</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="acash-custom-tab" data-bs-toggle="tab" data-bs-target="#acash-custom" type="button" role="tab" aria-controls="acash-custom" aria-selected="false">Custom</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="acash-raw-tab" data-bs-toggle="tab" data-bs-target="#acash-raw" type="button" role="tab" aria-controls="acash-raw" aria-selected="false">Raw</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="acashSubTabsContent">
                                            <div class="tab-pane fade show active" id="acash-main" role="tabpanel" aria-labelledby="acash-main-tab">
                                                <table id="AcashInfoTbl" class="table table-bordered text-center" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:20%;text-align:center">Date</th>
                                                            <th style="width:20%;text-align:center">Branch</th>
                                                            <th style="width:20%;text-align:center">Fund</th>
                                                            <th style="width:20%;text-align:center">Acct No</th>
                                                            <th style="width:20%;text-align:center">Acct Title</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="AcashInfoList">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="acash-custom" role="tabpanel" aria-labelledby="acash-custom-tab">
                                                <table id="AcashInfoTblCustom" class="table table-bordered text-center" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:20%;text-align:center">Date</th>
                                                            <th style="width:20%;text-align:center">Branch</th>
                                                            <th style="width:20%;text-align:center">Fund</th>
                                                            <th style="width:20%;text-align:center">Acct No</th>
                                                            <th style="width:20%;text-align:center">Acct Title</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="AcashInfoListCustom">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="acash-raw" role="tabpanel" aria-labelledby="acash-raw-tab">
                                                <table id="AcashInfoTblRaw" class="table table-bordered text-center" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:20%;text-align:center">Date</th>
                                                            <th style="width:20%;text-align:center">Branch</th>
                                                            <th style="width:20%;text-align:center">Fund</th>
                                                            <th style="width:20%;text-align:center">Acct No</th>
                                                            <th style="width:20%;text-align:center">Acct Title</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="AcashInfoListRaw">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <!-- ECpay Transaction Tab -->
                                <div class="tab-pane fade" id="ecpay-transaction" role="tabpanel" aria-labelledby="ecpay-transaction-tab">
                                    <div class=" d-flex align-items-center justify-content-between mb-3">
                                        <p class="fw-medium fs-5" style="color: #090909;">ECpay Transaction</p>
                                        <div>
                                            <button class="btn btn-secondary mb-2 me-1" onclick="PrintEcpayTxn()"><i class="fa-solid fa-print"></i> Print</button>
                                            <label for="ecpay-upload-custom" class="btn btn-info mb-2 me-1"><i class="fa-solid fa-upload"></i> Upload Custom</label>
                                            <input id="ecpay-upload-custom" type="file" accept=".xlsx, .xls, .csv" style="display: none;">
                                            <label for="ecpay-upload-raw" class="btn btn-info mb-2"><i class="fa-solid fa-upload"></i> Upload Raw</label>
                                            <input id="ecpay-upload-raw" type="file" accept=".xlsx, .xls, .csv" style="display: none;">
                                        </div>
                                    </div>
                                    <hr style="height:1px;">

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Filter By</label>
                                            <select id="ecpay-analytics-filter" class="form-select">
                                                <option value="Overview">Overview (All Categories)</option>
                                                <option value="HEADOFFICE">HEADOFFICE</option>
                                                <option value="EXTERNAL CLIENT">EXTERNAL CLIENT</option>
                                                <option value="MFI BRANCHES">MFI BRANCHES</option>
                                                <option value="STAFF">STAFF</option>
                                                <option value="BUSINESS UNIT">BUSINESS UNIT</option>
                                                <option value="OTHERS">OTHERS</option>
                                                <option value="INDIVIDUAL">INDIVIDUAL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                             <label class="form-label">Metric</label>
                                             <select id="ecpay-analytics-metric" class="form-select">
                                                 <option value="all">All (Amount & Count)</option>
                                                 <option value="amount">Total Amount</option>
                                                 <option value="count">Transaction Count</option>
                                             </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-primary w-100" onclick="LoadSpecificAnalytics('ecpay', '#ecpay-analytics-chart', '#ecpay-analytics-list')">Apply Filter</button>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">ECPay Top Performers</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div id="ecpay-analytics-chart" style="min-height: 350px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Top 5 List</h4>
                                                </div>
                                                <div class="card-body">
                                                    <ul id="ecpay-analytics-list" class="list-group list-group-flush">
                                                        <li class="list-group-item text-center">Loading...</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="position-relative" style="position: relative;">
                                        <ul class="nav nav-tabs mb-3" id="ecpaySubTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="ecpay-main-tab" data-bs-toggle="tab" data-bs-target="#ecpay-main" type="button" role="tab" aria-controls="ecpay-main" aria-selected="true">Main</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ecpay-custom-tab" data-bs-toggle="tab" data-bs-target="#ecpay-custom" type="button" role="tab" aria-controls="ecpay-custom" aria-selected="false">Custom</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ecpay-raw-tab" data-bs-toggle="tab" data-bs-target="#ecpay-raw" type="button" role="tab" aria-controls="ecpay-raw" aria-selected="false">Raw</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="ecpaySubTabsContent">
                                            <div class="tab-pane fade show active" id="ecpay-main" role="tabpanel" aria-labelledby="ecpay-main-tab">
                                                <table id="EcpayTxnTbl" class="table table-bordered text-center" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:20%;text-align:center">Date</th>
                                                            <th style="width:20%;text-align:center">Branch</th>
                                                            <th style="width:20%;text-align:center">Payee</th>
                                                            <th style="width:20%;text-align:center">Explanation</th>
                                                            <th style="width:20%;text-align:center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="EcpayTxnList">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="ecpay-custom" role="tabpanel" aria-labelledby="ecpay-custom-tab">
                                                <table id="EcpayTxnTblCustom" class="table table-bordered text-center" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:20%;text-align:center">Date</th>
                                                            <th style="width:20%;text-align:center">Branch</th>
                                                            <th style="width:20%;text-align:center">Payee</th>
                                                            <th style="width:20%;text-align:center">Explanation</th>
                                                            <th style="width:20%;text-align:center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="EcpayTxnListCustom">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="ecpay-raw" role="tabpanel" aria-labelledby="ecpay-raw-tab">
                                                <table id="EcpayTxnTblRaw" class="table table-bordered text-center" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:20%;text-align:center">Date</th>
                                                            <th style="width:20%;text-align:center">Branch</th>
                                                            <th style="width:20%;text-align:center">Payee</th>
                                                            <th style="width:20%;text-align:center">Explanation</th>
                                                            <th style="width:20%;text-align:center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="EcpayTxnListRaw">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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

        <script src="../../js/profiling/acashinfo.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
?>

