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

        <style>
            .content-inner span {
                font-size: 11px;
            }
            .icon-xxl {
                font-size: 3rem; /* Larger than fs-1 (approx 2.5rem usually) */
            }
            .price-xxl {
                font-size: 2rem; /* Larger than h6 */
                margin-bottom: 0;
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
                                                    <div id="circle-progress-01" class="text-center circle-progress-01 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="0" data-type="percent"></div>
                                                    <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center me-3 me-md-4">
                                                            <div>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa-solid fa-sack-dollar me-2 text-primary icon-xxl"></i>
                                                                    <h6 class="price-xxl">...</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <!-- Second item removed or placeholder if needed -->
                                                        </div>
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
                                                    <div id="circle-progress-02" class="text-center circle-progress-02 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="0" data-type="percent"></div>
                                                    <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center me-3 me-md-4">
                                                            <div>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa-solid fa-money-bill-transfer me-2 text-primary icon-xxl"></i>
                                                                    <h6 class="price-xxl">...</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <!-- Removed placeholder -->
                                                        </div>
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
                                                    <div id="circle-progress-03" class="text-center circle-progress-03 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="80" data-type="percent"></div>
                                                    <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center me-3 me-md-4">
                                                            <div>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa-solid fa-wallet me-2 text-primary icon-xxl"></i>
                                                                    <h6 class="price-xxl">...</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <!-- Removed placeholder -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="row row-cols-1">
                                            <div class="overflow-hidden d-slider1 ">
                                                <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">

                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="700">
                                                        <div class="flex-wrap card-header d-flex justify-content-between">
                                                            <div class="header-title">
                                                                <h6 class="card-title">Accounts Receivable
                                                                </h6>
                                                            </div>
                                                        </div>
                                                        <hr class="hr-horizontal">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-04" class="text-center circle-progress-04 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="0" data-type="percent"></div>
                                                                <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center me-3 me-md-4">
                                                                        <div>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="fa-solid fa-hand-holding-dollar me-2 text-primary icon-xxl"></i>
                                                                                <h6 class="price-xxl">...</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <!-- Removed placeholder -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="700">
                                                        <div class="flex-wrap card-header d-flex justify-content-between">
                                                            <div class="header-title">
                                                                <h6 class="card-title">Accounts Payable</h6>
                                                            </div>
                                                        </div>
                                                        <hr class="hr-horizontal">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-05" class="text-center circle-progress-05 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="80" data-type="percent"></div>
                                                                <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center me-3 me-md-4">
                                                                        <div>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="fa-solid fa-file-invoice-dollar me-2 text-primary icon-xxl"></i>
                                                                                <h6 class="price-xxl">...</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <!-- Removed placeholder -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="700">
                                                        <div class="flex-wrap card-header d-flex justify-content-between">
                                                            <div class="header-title">
                                                                <h6 class="card-title">Income Budget</h6>
                                                            </div>
                                                        </div>
                                                        <hr class="hr-horizontal">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-06" class="text-center circle-progress-06 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="40" data-type="percent"></div>
                                                                <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center me-3 me-md-4">
                                                                        <div>
                                                                            <span class="text-gray">-</span>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="fa-solid fa-boxes-stacked me-2 text-primary icon-xxl"></i>
                                                                                <h6 class="price-xxl">...</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <!-- Removed placeholder -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="700">
                                                        <div class="flex-wrap card-header d-flex justify-content-between">
                                                            <div class="header-title">
                                                                <h6 class="card-title">Expenses Budget</h6>
                                                            </div>
                                                        </div>
                                                        <hr class="hr-horizontal">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-07" class="text-center circle-progress-07 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="0" data-type="percent"></div>
                                                                <div class="flex-wrap mx-4 d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center me-3 me-md-4">
                                                                        <div>
                                                                            <span class="text-gray">-</span>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="fa-solid fa-cart-shopping me-2 text-primary icon-xxl"></i>
                                                                                <h6 class="price-xxl">...</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <!-- Removed placeholder -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="1100">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-08" class="text-center circle-progress-01 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="50" data-type="percent">
                                                                    <!-- <svg class="card-slie-arrow icon-24" width="24px" viewBox="0 0 24 24">
                                                                        <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                                                    </svg> -->
                                                                </div>
                                                                <div class="progress-detail">
                                                                    <p class="mb-2">Net Income</p>
                                                                    <div class="d-flex align-items-center justify-content-center">
                                                                        <i class="fa-solid fa-chart-line me-2 text-primary icon-xxl"></i>
                                                                        <h4 class="counter price-xxl">...</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>   

                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="1200">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-09" class="text-center circle-progress-01 circle-progress circle-progress-info" data-min-value="0" data-max-value="100" data-value="0" data-type="percent">
                                                                    <!-- <svg class="card-slie-arrow icon-24" width="24" viewBox="0 0 24 24">
                                                                        <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                                                    </svg> -->
                                                                </div>
                                                                <div class="progress-detail">
                                                                    <p class="mb-2">Today</p>
                                                                    <div class="d-flex align-items-center justify-content-center">
                                                                        <i class="fa-solid fa-calendar-day me-2 text-primary icon-xxl"></i>
                                                                        <h4 class="counter price-xxl">...</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    
                                                    <li class="swiper-slide card card-slide w-20" data-aos="fade-up" data-aos-delay="1300">
                                                        <div class="card-body">
                                                            <div class="progress-widget">
                                                                <div id="circle-progress-10" class="text-center circle-progress-01 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="0" data-type="percent">
                                                                    <!-- <svg class="card-slie-arrow icon-24 " width="24" viewBox="0 0 24 24">
                                                                        <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                                                    </svg> -->
                                                                </div>
                                                                <div class="progress-detail">
                                                                    <p class="mb-2">Members</p>
                                                                    <div class="d-flex align-items-center justify-content-center">
                                                                        <i class="fa-solid fa-users me-2 text-primary icon-xxl"></i>
                                                                        <h4 class="counter price-xxl">...</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="swiper-button swiper-button-next"></div>
                                                <div class="swiper-button swiper-button-prev"></div>
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
