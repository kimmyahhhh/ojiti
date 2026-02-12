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
      <!-- Add Google Font -->
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <body class="">
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
                --primary-color: #435ebe; /* Royal Blue */
                --secondary-color: #6c757d;
                --success-color: #198754;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #0dcaf0;
                --background-color: #f2f7ff; /* Very Light Blue-Gray */
                --card-bg: #ffffff;
                --text-main: #25396f;
                --text-secondary: #7c8db5;
                --border-color: #eef2f6;
                --input-bg: #f8f9fa;
            }

            body {
                background-color: var(--background-color);
                font-family: 'Inter', sans-serif;
                color: var(--text-main);
            }

            /* Layout & Spacing */
            .main-container {
                padding: 2rem;
            }

            /* Cards */
            .card-modern {
                background: var(--card-bg);
                border: none;
                border-radius: 16px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
                margin-bottom: 1.5rem;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            
            .card-header-modern {
                background: transparent;
                border-bottom: 1px solid var(--border-color);
                padding: 1.25rem 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .card-title-modern {
                font-size: 1.1rem;
                font-weight: 700;
                color: var(--text-main);
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .card-body-modern {
                padding: 1.5rem;
            }

            /* Form Elements */
            .form-label {
                font-weight: 600;
                font-size: 0.85rem;
                color: var(--text-secondary);
                margin-bottom: 0.5rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .form-control, .form-select {
                background-color: var(--input-bg);
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 0.6rem 1rem;
                font-size: 0.95rem;
                font-weight: 500;
                color: var(--text-main);
                transition: all 0.2s ease;
            }
            
            .form-control:focus, .form-select:focus {
                background-color: #fff;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 4px rgba(67, 94, 190, 0.1);
            }

            .form-control:disabled, .form-select:disabled {
                background-color: #e9ecef;
                opacity: 0.8;
            }

            /* Buttons */
            .btn {
                border-radius: 8px;
                padding: 0.5rem 1rem;
                font-weight: 600;
                font-size: 0.9rem;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
                border: none;
            }
            
            .btn:active {
                transform: scale(0.98);
            }

            .btn-primary {
                background-color: var(--primary-color);
                box-shadow: 0 4px 12px rgba(67, 94, 190, 0.3);
            }
            
            .btn-success {
                background-color: var(--success-color);
                box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
            }

            /* Tables */
            .table-responsive-custom {
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid var(--border-color);
            }

            .table-custom {
                width: 100%;
                margin-bottom: 0;
                border-collapse: separate;
                border-spacing: 0;
            }

            .table-custom thead th {
                background-color: #f8f9fa;
                color: var(--text-secondary);
                font-weight: 700;
                text-transform: uppercase;
                font-size: 0.75rem;
                padding: 1rem;
                border-bottom: 2px solid var(--border-color);
                letter-spacing: 0.5px;
                white-space: nowrap;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .table-custom tbody td {
                padding: 1rem;
                vertical-align: middle;
                border-bottom: 1px solid var(--border-color);
                color: var(--text-main);
                font-size: 0.9rem;
                background-color: #fff;
            }

            .table-custom tbody tr:hover td {
                background-color: #f1f5f9;
                cursor: pointer;
            }

            /* Header Section */
            .page-header-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
            }
            
            .page-heading {
                font-size: 1.75rem;
                font-weight: 800;
                color: var(--text-main);
                letter-spacing: -0.5px;
                margin: 0;
            }
            
            .breadcrumb-modern {
                color: var(--text-secondary);
                font-size: 0.9rem;
                margin: 0;
            }
            
            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: transparent; 
            }
            
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1; 
                border-radius: 3px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8; 
            }
            
            /* Checkbox/Radio Styling */
            .form-check-input:checked {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }
        </style>

        <div class="container-fluid main-container">
            <!-- Page Header -->
            <div class="card-modern">
                <div class="card-body-modern py-3">
                    <div class="page-header-container m-0">
                        <div>
                            <h1 class="page-heading mb-1" style="font-size: 1.5rem;">Inventory Management</h1>
                            <p class="breadcrumb-modern m-0">View and filter inventory reports</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Product Filter Column -->
                <div class="col-lg-4 col-md-12">
                    <div class="card-modern h-100">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern"><i class="fa-solid fa-filter text-primary"></i> Product Filter</h5>
                        </div>
                        <div class="card-body-modern">
                            <form id="productFilterForm" method="POST">
                                <div class="mb-4">
                                    <label class="form-label" for="isynBranch">Isyn Branch</label>
                                    <select class="form-select" name="isynBranch" id="isynBranch">
                                        <option value="" selected disabled>Select</option>
                                    </select>
                                </div>

                                <div class="p-3 bg-light rounded-3 mb-4 border">
                                    <div class="form-check mb-2">
                                        <input type="radio" id="isPreset" name="selection" class="form-check-input" value="Yes" checked onclick="isPresetSelect();">
                                        <label class="form-check-label fw-bold" for="isPreset">Preset Reports</label>
                                    </div>
                                    <div class="ps-4 mb-3">
                                        <select class="form-select mb-3" name="presetSelect" id="presetSelect" onchange="PresetSelectVal(this.value);">
                                            <option value="" selected>Select Preset</option>
                                            <option value="CURRENT INVENTORY">CURRENT INVENTORY</option>
                                            <option value="ENDING INVENTORY">ENDING INVENTORY</option>
                                            <option value="INCOMING INVENTORY">INCOMING INVENTORY</option>
                                            <option value="OUTGOING INVENTORY">OUTGOING INVENTORY</option>
                                            <option value="PREVIOUS INVENTORY">PREVIOUS INVENTORY</option>
                                        </select>
                                        
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input type="checkbox" id="WithoutConsignment" name="WithoutConsignment" class="form-check-input" value="Yes" onclick="WithoutConsign();">
                                                    <label class="form-check-label" style="font-size: 0.85rem;" for="WithoutConsignment">No Consign</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input type="checkbox" id="onlyConsignment" name="onlyConsignment" class="form-check-input" value="Yes" onclick="ConsignOnly();">
                                                    <label class="form-check-label" style="font-size: 0.85rem;" for="onlyConsignment">Consign Only</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input type="checkbox" id="noFreebies" name="noFreebies" class="form-check-input" value="Yes">
                                                    <label class="form-check-label" style="font-size: 0.85rem;" for="noFreebies">Exclude Freebies</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input type="checkbox" id="onlyFreebies" name="onlyFreebies" class="form-check-input" value="Yes">
                                                    <label class="form-check-label" style="font-size: 0.85rem;" for="onlyFreebies">Freebies Only</label>
                                                </div>
                                            </div>
                                            <div class="col-12" id="TransProdDiv" style="display: none;">
                                                <div class="form-check">
                                                    <input type="checkbox" id="incTransProd" name="incTransProd" class="form-check-input" value="Yes">
                                                    <label class="form-check-label" style="font-size: 0.85rem;" for="incTransProd">Include Transfer Product</label>
                                                </div>
                                            </div>
                                            <div class="col-12" id="DiscProdDiv" style="display: none;">
                                                <div class="form-check">
                                                    <input type="checkbox" id="discProd" name="discProd" class="form-check-input" value="Yes">
                                                    <label class="form-check-label" style="font-size: 0.85rem;" for="discProd">Discounted Products</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="form-check mb-2">
                                        <input type="radio" id="isCustom" name="selection" class="form-check-input" value="Yes" onclick="isCustomSelect();">
                                        <label class="form-check-label fw-bold" for="isCustom">Custom Data Filter</label>
                                    </div>
                                    <div class="ps-4">
                                        <div class="mb-2">
                                            <select class="form-select" name="customSelect" id="customSelect" disabled onchange="PresetCustomVal(this.value);">
                                                <option value="" selected>Select Source</option>
                                                <option value="CURRENT INVENTORY">CURRENT INVENTORY</option>
                                                <option value="OUTGOING INVENTORY">OUTGOING INVENTORY</option>
                                                <option value="PREVIOUS INVENTORY">PREVIOUS INVENTORY</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label mb-1" style="font-size: 0.8rem;">Column</label>
                                            <select class="form-select" name="customColumns" id="customColumns" onchange="LoadCustomColumnValue(this.value);" disabled>
                                                <option value="" selected>Select</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label mb-1" style="font-size: 0.8rem;">Value</label>
                                            <select class="form-select" name="customValues" id="customValues" disabled>
                                                <option value="" selected>Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Date Filter & Summary Totals -->
                <div class="col-lg-8 col-md-12">
                    <!-- Combined Container -->
                    <div class="card-modern h-100">
                        <div class="card-body-modern d-flex flex-column h-100 p-0">
                            <!-- Section 1: Date Filter -->
                            <div class="p-4">
                                <h5 class="card-title-modern mb-3"><i class="fa-regular fa-calendar-days text-primary"></i> Date Filter</h5>
                                <div class="bg-light rounded-3 p-4">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-5">
                                            <label class="form-label mb-2 text-uppercase fw-bold text-secondary" style="font-size: 0.8rem;">From Date</label>
                                            <input type="text" id="fromDate" name="fromDate" class="form-control form-control-lg Date bg-white border-0 shadow-sm" placeholder="mm/dd/yyyy">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label mb-2 text-uppercase fw-bold text-secondary" style="font-size: 0.8rem;">To Date</label>
                                            <input type="text" id="toDate" name="toDate" class="form-control form-control-lg Date bg-white border-0 shadow-sm" placeholder="mm/dd/yyyy">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" id="searchBtn" name="searchBtn" class="btn btn-primary w-100 btn-lg shadow-sm" onclick="SearchBtn();" title="Search">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Summary Totals -->
                            <div class="flex-grow-1 d-flex flex-column px-4 pb-4">
                                <h5 class="card-title-modern mb-3"><i class="fa-solid fa-chart-pie text-primary"></i> Summary Totals</h5>
                                <div class="bg-light rounded-3 p-4 flex-grow-1 d-flex flex-column justify-content-center">
                                    <div class="row g-4">
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Total Qty</label>
                                            <input type="text" id="totalQuantity" name="totalQuantity" class="form-control fw-bold text-primary form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Dealer's Price</label>
                                            <input type="text" id="dealersPrice" name="dealersPrice" class="form-control fw-bold form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Total DP</label>
                                            <input type="text" id="totalDP" name="totalDP" class="form-control fw-bold form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">VAT Sales</label>
                                            <input type="text" id="vatSales" name="vatSales" class="form-control fw-bold form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                        
                                        <div class="col-md-4 col-sm-6">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">SRP</label>
                                            <input type="text" id="srp" name="srp" class="form-control fw-bold form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Total SRP</label>
                                            <input type="text" id="totalsrp" name="totalsrp" class="form-control fw-bold form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <label class="form-label text-secondary mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">VAT</label>
                                            <input type="text" id="vat" name="vat" class="form-control fw-bold form-control-lg border-0 shadow-sm py-3 bg-white" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="col-12">
                    <div class="card-modern">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern"><i class="fa-solid fa-table-list text-primary"></i> Data Inventory</h5>
                            <button type="button" id="print" name="print" class="btn btn-primary btn-sm" onclick="PrintInventoryReportDB();">
                                <i class="fa fa-print me-2"></i> Print Report
                            </button>
                        </div>
                        <div class="card-body-modern p-0">
                            <div class="table-responsive-custom">
                                <table id="particularsTbl" class="table table-custom table-hover" style="width:100%;">
                                    <thead>
                                        <tr></tr>
                                    </thead>
                                    <tbody id="particularsList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/inventory.js?<?= time() ?>"></script>

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
