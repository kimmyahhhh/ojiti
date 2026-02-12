<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
?>

<!doctype html>
<html lang="en" dir="ltr">
    <?php
        include('includes/index.header.php');
    ?>
    <body class="  ">
        <!-- loader Start -->
        <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
        </div>
        <!-- loader END -->

        <!-- Datetimepicker CSS -->
        <link rel="stylesheet" href="assets/datetimepicker/jquery.datetimepicker.css">
        <!-- Add Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            :root {
                --primary-color: #3a57e8;
                --secondary-color: #6c757d;
                --success-color: #198754;
                --info-color: #0dcaf0;
                --warning-color: #ffc107;
                --danger-color: #dc3545;
                --light-color: #f8f9fa;
                --dark-color: #212529;
                --border-radius: 16px;
                --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: #f2f7ff;
            }

            .content-inner span {
                font-size: 11px;
            }
            .icon-xxl {
                font-size: 2.2rem; /* Reduced from 3rem */
            }
            .price-xxl {
                font-size: 1.8rem; /* Reduced from 2.5rem */
                margin-bottom: 0;
                font-weight: 800;
                line-height: 1;
            }
            
            /* Overview Card Styling - consistent size */
            .overview-card .icon-xxl {
                font-size: 2.2rem !important; 
            }
            .overview-card .price-xxl {
                font-size: 1.8rem !important; 
                font-weight: 700;
            }
            .overview-card .circle-progress {
                width: 100px !important; 
                height: 100px !important;
            }
            .overview-card h6.text-secondary {
                font-size: 1rem !important; 
                font-weight: 600;
                margin-bottom: 1.5rem !important;
            }
            
            /* Spacing adjustments for the grid */
            .overview-item {
                margin-bottom: 2rem;
            }
            
            /* Card Modernization */
            .card {
                border: none;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                background: #fff;
                margin-bottom: 1.5rem;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                max-width: 90%; 
                margin-left: auto;
                margin-right: auto;
            }
            
            /* Header Styling */
            .card-header {
                background: transparent;
                border-bottom: 1px solid #f1f1f1;
                padding: 1rem; /* Reduced padding */
            }
            
            .header-title .card-title {
                font-size: 1.1rem; /* Reduced font size */
                font-weight: 700;
                margin-bottom: 0;
                color: #2c3e50;
            }
            
            /* Progress Widget */
            .progress-widget {
                padding: 1rem;
                display: flex;
                flex-direction: column;
                justify-content: center; 
                align-items: center; 
                height: 100%; 
                min-height: 100px; /* Further Reduced */
                width: 100%;
            }
            
            .circle-progress {
                margin: 0 auto 0.5rem; /* Reduced margin */
                width: 80px; /* Reduced from 100px */
                height: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .progress-detail {
                width: 100%;
                text-align: center;
                margin-top: 0.5rem;
            }

            .card-body {
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center; /* Ensure body content is centered */
            }
            

            
            .input-group-text {
                background-color: var(--primary-color) !important;
                border: none;
                font-weight: 600;
            }
            
            .form-select {
                border: 1px solid #e0e0e0;
                font-weight: 500;
            }
            
            /* Chart Container */
            .d-main {
                min-height: 260px;
            }
        </style>

        <?php
            include('includes/index.sidebar.php');
            include('includes/index.navbar.php');
        ?>

            <div class="container-fluid content-inner mt-n5 py-0">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card" data-aos="fade-up" data-aos-delay="800">
                                <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                                    <div class="header-title">
                                        <h4 class="card-title">Ratio</h4>
                                    </div>
                                    <div class="d-flex align-items-center align-self-center">
                                        <div class="d-flex align-items-center text-primary">
                                            <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24" fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                            </g>
                                            </svg>
                                            <div class="ms-2">
                                            <span class="text-gray">Product Sold</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center ms-3 text-info">
                                            <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24" fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                            </g>
                                            </svg>
                                            <div class="ms-2">
                                            <span class="text-gray">Total</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="input-group" style="max-width: 220px;">
                                            <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-calendar-days me-2"></i>Year</span>
                                            <select class="form-select" id="dashboard-year">
                                                <?php
                                                    $currentYear = date('Y');
                                                    // Show last 5 years + current + next year maybe?
                                                    // Usually just current and past.
                                                    for($i = $currentYear; $i >= $currentYear - 4; $i--){
                                                        echo "<option value='$i'>$i</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="d-main" class="d-main"></div>
                                </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card" data-aos="fade-up" data-aos-delay="800">
                                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                                        <div class="header-title">
                                            <h4 class="card-title">Inventory Value</h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="d-inventory" class="d-main"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card" data-aos="fade-up" data-aos-delay="900">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Revenue</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-sack-dollar me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-revenue">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-lg-4">
                                        <div class="card" data-aos="fade-up" data-aos-delay="900">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Expenses</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-money-bill-transfer me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-expenses">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-lg-4">
                                        <div class="card" data-aos="fade-up" data-aos-delay="900">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Income</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-wallet me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-income">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Inventory Cost -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Inventory Cost</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-tags me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-inv-cost">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Inventory SRP -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Inventory SRP</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-tag me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-inv-srp">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Accounts Receivable -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Accounts Receivable</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-hand-holding-dollar me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-receivable">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Accounts Payable -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Accounts Payable</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-file-invoice-dollar me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-payable">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Income Budget -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Income Budget</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-boxes-stacked me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-income-budget">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Expenses Budget -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Expenses Budget</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-cart-shopping me-3 text-primary icon-xxl"></i>
                                                        <h6 class="price-xxl" id="val-expenses-budget">...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Net Income -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Net Income</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-chart-line me-3 text-primary icon-xxl"></i>
                                                        <h4 class="counter price-xxl" id="val-net-income">...</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Today -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Today</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-calendar-day me-3 text-primary icon-xxl"></i>
                                                        <h4 class="counter price-xxl" id="val-today">...</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Members -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="card overview-card" data-aos="fade-up" data-aos-delay="700">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h6 class="card-title">Members</h6>
                                                </div>
                                            </div>
                                            <hr class="hr-horizontal">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                                        <i class="fa-solid fa-users me-3 text-primary icon-xxl"></i>
                                                        <h4 class="counter price-xxl" id="val-members">...</h4>
                                                    </div>
                                                </div>
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
            include('includes/index.footer.php');
        ?>
    
  </body>
</html>

<?php
  } else {
    echo '<script> window.location.href = "login.php"; </script>';
  }
?>
