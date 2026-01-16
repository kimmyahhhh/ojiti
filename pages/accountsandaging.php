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

            <?php
                // include('../../includes/header.pages.php');

                // function fetchDataAccounts($connection)
                // {
                //     $sqlAccounts = "SELECT * FROM tbl_aging ORDER BY FULLNAME";
                //     $result = $connection->query($sqlAccounts);
                //     if ($result) {
                //         return $result->fetch_all(MYSQLI_ASSOC);
                //     } else {
                //         return array();
                //     }
                // }


                // // function getDataTransaction($connection){
                // //     $sqlTransaction = "SELECT * FROM tbl_loans ORDER BY FullName";
                // // }

                // $accountsData = fetchDataAccounts($connection);

                // // echo json_encode($accountsData);

                // function fetchLoansDataAccounts($connection)
                // {
                //     $sqlAccounts = "SELECT * FROM tbl_loans ORDER BY FULLNAME";
                //     $result = $connection->query($sqlAccounts);
                //     if ($result) {
                //         return $result->fetch_all(MYSQLI_ASSOC);
                //     } else {
                //         return array();
                //     }
                // }

                // $loansdata = fetchLoansDataAccounts($connection);


                // function fetchBooks($connection) 
                // {
                //     $sqlBooks = "SELECT BOOKTYPE,CDATE,GLNO,CRDR,NATURE,TAG,SLDRCR,ORNO,JVNO,CVNO FROM tbl_books WHERE CLIENTNO=?ClientNo AND LOAN=?LoanID AND SLNO=?ClientNo ORDER BY CDATE ASC, BOOKTYPE ASC";
                //     $result = $connection->query($sqlBooks);
                //     if ($result) {
                //         return $result->fetch_all(MYSQLI_ASSOC);
                //     } else {
                //         return array();
                //     }
                // }

                // $slpreviewdata = fetchBooks($connection);
            ?>

            <style>
                #myBar {
                    width: 0%;
                    height: 40px;
                    border-radius: 3px;
                    background-color: #04AA6D;
                    text-align: center;
                    /* To center it horizontally (if you want) */
                    line-height: 40px;
                    /* To center it vertically */
                    color: white;
                    transition: width 0.5s ease;
                    /* Add smooth transition effect */
                }
            
                main {
                    background-color: #EAEAF6;
                    height: 100% ;
                }
            
                th {
                    position: sticky;
                    top: 0;
                    font-weight: bold;
                    color: #090909;
                }
            
                .container { max-width:100%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
            
            </style>

            <div class="container mt-4 mb-3">
                <div class=" shadow p-3 shadow rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2 mb-3">Accounts and Aging Information</p>
                </div>
            </div>

            <div class="container">
                <ul class="nav nav-tabs " id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="aging" data-bs-toggle="tab" data-bs-target="#aging-pane" type="button" role="tab" aria-controls="aging-pane" aria-selected="true">Aging</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subsidiary-ledgers" data-bs-toggle="tab" data-bs-target="#subsidiary-ledgers-pane" type="button" role="tab" aria-controls="subsidiary-ledgers-pane" aria-selected="false">Subsidiary Ledgers</button>
                    </li>
                </ul>

                <div class="tab-content p-3" id="myTabContent">

                    <!--Aging-->
                    <div class="tab-pane fade show active " id="aging-pane" role="tabpanel" aria-labelledby="aging" tabindex="0">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="shadow p-3 rounded-3 mb-4" style="background-color: white;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div id="myProgress">
                                                <div id="myBar"></div>
                                                <span id="updateStatus"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button id="updateBtn" name="updateAging" class="btn btn-primary w-100"><i class ="fa-solid fa-refresh"></i> Update</button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-info w-100" id="printStatement"><i class ="fa-solid fa-print"></i> Print Statement of Account</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="">    
                            <!-- Accounts -->
                            <div class="row"> 
                                <div class="col-md-12">
                                    <div class="shadow p-3 rounded-3 mb-4" style="background-color: white;">
                                        <h5>Accounts</h5>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <button type="button" disabled class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#exampleModal" id="editAccount"> <i class ="fa-solid fa-pen-to-square"></i> Edit </button>
                                            
                                            </div>
                                            <div class="col-md-2">
                                                <button disabled class="text-white btn btn-danger w-100" id="removeAccount" onclick="clearAccountDetails()"><i class="fa-regular fa-circle-xmark"></i> Remove</button>
                                            </div>
                                            <div class="col-md-3">
                                                <button disabled type="button" class="btn btn-danger" id="deleteAccount"><i class="fa-solid fa-trash-can"></i> Delete Cleared Account</button>
                                            </div>

                                        <!-- <div class="col-md-2">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="">View</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <!~~ <select name="" class="form-select" id="">
                                                            <option value="" selected>CURRENT</option>
                                                            <?php
                                                            // $query = "SELECT DISTINCT Region FROM tbl_barangays ORDER BY Region";
                                                            // $query_run = mysqli_query($connection, $query);

                                                            // if (mysqli_num_rows($query_run) > 0) {
                                                            //     while ($row = mysqli_fetch_assoc($query_run)) {
                                                            ?>
                                                                    <option value="<?php 
                                                                    // echo $row['Region'] ?>"><?php 
                                                                    // echo $row['Region'] ?></option>
                                                            <?php
                                                            //     }
                                                            // }
                                                            ?>
                                                        </select> ~~>

                                                        <!~~ <select name="" class="form-select" id="">
                                                            <option value="" selected> SELECT</option>
                                                            <option value="">CURRENT</option>
                                                            <option value=""> HISTORY</option>
                                                            <option value="">WRITEOFF</option>
                                                        </select> ~~>
                                                    </div>
                                                </div>
                                            </div>-->
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-5 text-end">
                                                        <label for="">View Accounts by</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <select name="" class="form-select w-100" id="selectionOrder">
                                                            <option value="name" selected>Name</option>
                                                            <option value="clientNo">ClientNo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="shadow p-3 rounded mb-4" style="background-color: white;">
                                        <div class="col-md-4 d-flex mb-3" role="search">
                                            <input type="search" class="form-control me-2" placeholder="Search" aria-level="Search" id="searchInput" placeholder="Search">
                                            <!-- <button class="btn btn-outline-success" type="button">Search</button> -->
                                        </div>
                                        <div class="mt-2" style="height:300px; overflow:auto;">
                                            <table id="myTable" class="table table-hover table-borderless p-3 table-selected" style="background-color: white;" >
                                                <thead id="tableHead">
                                                    <tr>
                                                        <!-- <th>ID</th> -->
                                                        <th>Name</th>
                                                        <th>ClientNo</th>
                                                        <th>LoanID</th>
                                                        <th>Date Released</th>
                                                        <th>Loan Amount</th>
                                                        <th>Loan Product</th>
                                                        <th>Addtl</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableBody">
                                                    <!-- PHP loop to populate table rows -->
                                                    <?php
                                                    //  foreach ($accountsData as $row) : ?>
                                                        <!-- <tr class="table-row-clickable"  -->
                                                        <!-- data-id="<?php
                                                        // echo $row['ID']; ?>" 
                                                        data-program="<?php 
                                                        // echo $row['PROGRAM']; ?>" 
                                                        data-product="<?php 
                                                        // echo $row['PRODUCT']; ?>" 
                                                        data-availment="<?php 
                                                        // echo $row['LOANAVAILMENT']; ?>" 
                                                        data-date-release="<?php 
                                                        // echo $row['DATERELEASE']; ?>" 
                                                        data-date-mature="<?php 
                                                        // echo $row['DATEMATURE']; ?>" 
                                                        data-mode="<?php 
                                                        // echo $row['MODE']; ?>" 
                                                        data-term="<?php 
                                                        // echo $row['TERM']; ?>" 
                                                        data-po="<?php 
                                                        // echo $row['PO']; ?>" 
                                                        data-fund="<?php 
                                                        // echo $row['FUND']; ?>" 
                                                        data-pnno="<?php 
                                                        // echo $row['PNNO']; ?>" 
                                                        data-tag="<?php 
                                                        // echo $row['TAG']; ?>"
                                                        data-loan-amount="<?php 
                                                        // echo $row['LOANAMOUNT']; ?>" 
                                                        data-interest-amount="<?php 
                                                        // echo number_format($row['INTEREST'], 2, '.', ','); ?>" 

                                                        data-cbu="<?php 
                                                        // echo $row['CBU']; ?>" 
                                                        data-ef="<?php 
                                                        // echo $row['EF']; ?>" 
                                                        data-mba="<?php 
                                                        // echo $row['MBA']; ?>" 

                                                        data-interest-rate="<?php 
                                                        // echo $row['INTERESTRATE']; ?>" 
                                                        data-int-computation="<?php 
                                                        // echo $row['INTCOMPUTATION']; ?>" 

                                                        data-principal-paid="<?php 
                                                        // echo number_format($row['AmountPaid'], 2, '.', ','); ?>" 
                                                        data-interest-paid="<?php 
                                                        // echo number_format($row['InterestPaid'], 2, '.', ','); ?>" 
                                                        data-cbu-paid="<?php 
                                                        // echo number_format($row['CBUPaid'], 2, '.', ','); ?>" 
                                                        data-ef-paid="<?php 
                                                        // echo number_format($row['EFPaid'], 2, '.', ','); ?>" 
                                                        data-mba-paid="<?php 
                                                        // echo number_format($row['MBAPaid'], 2, '.', ','); ?>" 
                                                        data-penalties-paid="<?php 
                                                        // echo number_format($row['PenaltyPaid'], 2, '.', ','); ?>" 

                                                        data-principal-balance="<?php 
                                                        // echo number_format($row['Balance'], 2, '.', ','); ?>"

                                                        data-date-restructured="<?php 
                                                        // echo $row['RESTRUCTUREDATE']; ?>"
                                                        data-date-writtenoff="<?php 
                                                        // echo $row['WRITEOFFDATE']; ?>"
                                                        data-date-dropped="<?php 
                                                        // echo $row['DATEDROPPED']; ?>" 

                                                        data-due-date="<?php 
                                                        // echo date('m-d-Y', strtotime($row['DueDate'])); ?>" 
                                                        data-principal="<?php 
                                                        // echo number_format($row['AmountDue'], 2, '.', ','); ?>" 
                                                        data-interest-due="<?php 
                                                        // echo number_format($row['InterestDue'], 2, '.', ','); ?>" 
                
                                                        data-cbu-due="<?php 
                                                        // echo number_format($row['CBUDue'], 2, '.', ','); ?>" 
                                                        data-ef-due="<?php 
                                                        // echo number_format($row['EFDue'], 2, '.', ','); ?>" 
                                                        data-mba-due="<?php 
                                                        // echo number_format($row['MBADue'], 2, '.', ','); ?>" 
                                                        data-penalty-due="<?php 
                                                        // echo number_format($row['PenaltyDue'], 2, '.', ','); ?>" 
                                                        
                                                        data-one-thirty="<?php 
                                                        // echo $row['DAYS130']; ?>"   
                                                        data-thirtyone-to-sixty="<?php 
                                                        // echo $row['DAYS3160']; ?>"   
                                                        data-sixtyone-to-ninety="<?php 
                                                        // echo $row['DAYS6190']; ?>"   
                                                        data-ninetyone-to-onetwenty="<?php 
                                                        // echo $row['DAYS91120']; ?>"   
                                                        data-onetwentyone-to-onefifty="<?php 
                                                        // echo $row['DAYS121150']; ?>"   
                                                        data-onefiftyone-to-oneeighty="<?php 
                                                        // echo $row['DAYS151180']; ?>"   
                                                        data-over-oneeighty="<?php 
                                                        // echo $row['DAYSOver180']; ?>"   
                                                        data-total-arrears="<?php 
                                                        // echo $row['TotalArrears']; ?>"   
                                                        data-par="<?php 
                                                        // echo $row['PAR']; ?>"   
                                                        
                                                        onclick="fillFormFields(this)">
                                                        <!--
                                                        TO ADD:
                                                        data-date-deleted="<?php 
                                                        // echo $row['DAREDELETED']; ?>"
                                                        data-cumulative-cbu="<?php 
                                                        // echo $row['CUMMCBU']; ?>"
                                                        data-cbu-intEarned="<?php 
                                                        // echo $row['CUMMCBUINTEREST']; ?>"
                                                        data-service-fee="<?php 
                                                        // echo number_format($row['SERVICEFEE'], 2, '.', ','); ?>" 
                                                        -->
                                                            <td><?php 
                                                            // echo $row['FULLNAME']; ?></td>
                                                            <td><?php 
                                                            // echo $row['ClientNo']; ?></td>
                                                            <td><?php 
                                                            // echo $row['LoanID']; ?></td>
                                                            <td><?php 
                                                            // echo date('m-d-Y', strtotime($row['DATERELEASE'])) ?></td>
                                                            <td><?php 
                                                            // echo number_format($row['LOANAMOUNT'], 2, '.', ','); ?></td>
                                                            <td><?php 
                                                            // echo $row['PRODUCT']; ?></td>
                                                            <td><?php 
                                                            // echo $row['ADDITIONAL']; ?></td>
                                                        </tr>
                                                    <?php 
                                                // endforeach; ?>

                                                    <tr id="noDataMessage" style="display: none;">
                                                        <td colspan="6" class="text-center" style="color:red; font-size:50px">NO DATA AVAILABLE</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tex-center" colspan="6">
                                                            <?php
                                                            // if (is_array($accountsData) && !empty($accountsData)) {
                                                            //     foreach ($accountsData as $row) {
                                                            //     }
                                                            // } else {
                                                            //     echo '<p style="color:red; text-align: center; font-size: 20px">NO DATA AVAILABLE</p>';
                                                            // }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                
                                <!--Account Details-->
                                <div class="col-md-4">
                                    <div class="p-3 shadow rounded mb-4" style="background-color: white;">
                                        <p class="fw-medium fs-5" style="color: #090909;">Account Details</p>
                                        <hr style="height: 1px">

                                        <!--Primary Details-->
                                        <div class="mb-4">
                                            <h6>Primary Details</h6>
                                            <hr style="height: 1px;">

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Loan Program</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="program">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Loan Product</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="product">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Loan Availments</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="availment">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Release Date</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="date" class="form-control" id="date-release">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Maturity Date</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="date" class="form-control" id="date-mature">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Mode</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="mode">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Term</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="term">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">PO</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="PO">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Fund</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="fund">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">PN No.</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="PNNo">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Tag</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="tag">
                                                </div>
                                            </div>
                                        </div>

                                        <!--Amounts-->
                                        <div class="mb-4">
                                            <h6>Amounts</h6>
                                            <hr style="height: 1px;">
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Loan Amount</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="loan-amount">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Interest</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="interest-amount">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Service Fee</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="service-fee">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">CBU</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="cbu">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">EF</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="ef">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">MBA</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="mba">
                                                </div>
                                            </div>
                                        </div>     
                                    </div>
                                </div>

                                <!--Account Status-->
                                <div class="col-md-4">
                                    <div class="shadow p-3 shadow rounded mb-4" style="background-color: white;">
                                        <p class="fw-medium fs-5" style="color: #090909;">Account Status</p>
                                        <hr style="height: 1px">

                                        <!--Payment Made-->
                                        <div class="mb-4">
                                            <h6>Payments Made</h6>
                                            <hr style="height: 1px;">

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Principal Paid</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="principal-paid">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Interest Paid</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="interest-paid">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">CBU Paid</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="cbu-paid">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">EF Paid</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="ef-paid">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">MBA Paid</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="mba-paid">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Penalties Paid</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="penalties-paid">
                                                </div>
                                            </div>
                                        </div>

                                        <!--Status-->
                                        <div class="mb-4">
                                            <h6>Status</h6>
                                            <hr style="height: 1px;">
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Principal Balance</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="principal-balance">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Cumulative CBU</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="cumulativeCBU">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">CBU Int. Earned</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="cbuIntEarned">
                                                </div>
                                            </div>

                                        </div>

                                        <!--Date Modified-->
                                        <div class="mb-4">
                                            <h6>Date Modified</h6>
                                            <hr style="height: 1px;">
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Date Deleted</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="date-deleted">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Date Restructured</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="date-restructured">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Date Written-off</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="date-writtenOff">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Date Dropped</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="date-dropped">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                
                            
                                <!--Dues + Arrears and PAR-->
                                <div class="col-md-4">
                                    <!--Dues-->
                                    <div class="p-3 shadow rounded mb-4" style="background-color:white">
                                        <p class="fw-medium fs-5" style="color: #090909;">Dues</p>
                                        <hr style="height: 1px">

                                        <div class="mb-3">
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Due Date</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="due-date">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Principal</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="principal">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Interest</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="interest-due">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Service Fee</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="service-fee">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">CBU</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="cbuDue">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">EF</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="efDue">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">MBA</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="mbaDue">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Penalty</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="penalty-due">
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label for="">Total</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input readonly type="text" class="form-control" id="total">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!--Arrears and par-->
                                    <div class="p-3 shadow rounded mb-4" style="background-color:white">
                                        <p class="fw-medium fs-5" style="color: #090909;">Arrears and PAR</p>
                                        <hr style="height: 1px">
                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">1-30 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="one-thirty">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">31-60 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="thirty-one-to-sixty">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">61-90 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="sixtyone-to-ninety">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">91-120 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="ninetyone-onetwenty">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">121-150 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="onetwentyone-onefifty">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">151-180 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="onefiftyone-oneeighty">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">Over 180 Days</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="over-oneeighty">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">Total Arrears</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="total-arrears">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-3">
                                                <label for="">PAR</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input readonly type="text" class="form-control" id="par">
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                
                                <div class="col-md-4">
                                    
                                </div>
                            </div>
                        </form>
                    </div>



                    <!--Subsidiary Ledgers-->

                    <div class="tab-pane fade" id="subsidiary-ledgers-pane" role="tabpanel" aria-labelledby="subsidiary-ledgers" tabindex="0">
                        <div class="container">

                            <!--Row 1-->
                            <div class="row">
                                <div class="mb-3">
                                    <button class="btn btn-info w-25 float-end"><i class="fa-solid fa-print"></i> Print</button>
                                </div>
                                <hr style="height: 1px;">
                            </div>
                            

                            <div class="row mb-3">
                                <div class="col-md-8 mb-3">

                                    <!--Select Client-->
                                    <div class="row mb-3">
                                        <div class="col-md-3 d-flex justify-content-center align-items-center" style="background-color: white;">
                                            <p class="fs-6">Select Client</p>
                                        </div>
                                        <div class="col-md-9">
                                            <select id="selectName" class="form-select" onchange="filterTableByClient()">
                                                <option value="all" selected>Show All</option>
                                                <?php
                                                // $query = "SELECT DISTINCT FullName FROM tbl_loans ORDER BY FullName";
                                                // $query_run = mysqli_query($connection, $query);

                                                // if (mysqli_num_rows($query_run) > 0) {
                                                //     while ($row = mysqli_fetch_assoc($query_run)) {
                                                //         echo '<option value="' . $row['FullName'] . '">' . $row['FullName'] . '</option>';
                                                //     }
                                                // }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!--Table-->
                                    <div class="overflow-auto mb-3" style="max-height: 600px;">
                                        <table id="loanTable" class="table table-hover shadow table-borderless p-3 table-selected overflow-auto" style="background-color: white;">
                                            <thead>
                                                <tr>
                                                    <!--<th>Name</th>-->
                                                    <!--<th>Client No</th>-->
                                                    <th>LoanID</th>
                                                    <th>Loan Product</th>
                                                    <th>Date Released</th>
                                                    <th>Loan Type</th>
                                                    <!--<th>Loan Amount</th>
                                                    
                                                    <th>Date Mature</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data will be populated here dynamically -->
                                                <!--<?php
                                                //  foreach ($loansdata as $row) : ?>
                                                <tr data-id="<?php
                                                    //  echo $row['ID']; ?>"
                                                data-slclient-no="<?php
                                                //  echo $row['ClientNo']; ?>"
                                                data-slprogram="<?php
                                                //  echo $row['Program']; ?>"
                                                data-slproduct="<?php
                                                //  echo $row['Product']; ?>"
                                                data-sldate-released="<?php
                                                //  echo $row['DateRelease']; ?>"
                                                data-sldate-matured="<?php
                                                //  echo $row['DateMature']; ?>"
                                                data-slloan-amount="<?php
                                                //  echo $row['LoanAmount']; ?>"
                                                data-slinterest="<?php
                                                //  echo $row['Interest']; ?>"
                                                data-slcbu="<?php
                                                //  echo $row['CBU']; ?>"
                                                data-slpnno="<?php
                                                //  echo $row['PNNo']; ?>"
                                                data-slpo="<?php
                                                //  echo $row['PO']; ?>"
                                                onclick="populateAccountDetails(this)"
                                                >
                                                <!~~To Add:
                                                data-slservice-fee="<?php
                                                //  echo $row['ServiceFee']; ?>"~~>
                                                    <!~~<td><?php
                                                    //  echo $row['FullName']; ?></td>
                                                    <td><?php
                                                    //  echo $row['ClientNo']; ?></td>~~>
                                                    <td><?php
                                                    //  echo $row['LoanID']; ?></td>
                                                    <td><?php
                                                    //  echo $row['Product']; ?></td>
                                                    <td><?php
                                                    //  echo $row['DateRelease']; ?></td>
                                                    <td><?php
                                                    //  echo $row['LoanType']; ?></td>                                    
                                                    <!~~<td><?php
                                                    //  echo $row['LoanAmount']; ?></td>                                    
                                                    
                                                    <td><?php
                                                    //  echo $row['DateMature']; ?></td>~~>
                                                </tr>
                                                <?php
                                            //  endforeach; ?>-->
                                                
                                                <?php
                                                //  foreach ($loansdata as $row) : ?>
                                                <tr data-id="<?php
                                                    //  echo $row['ID']; ?>"
                                                    data-slfull-name="<?php
                                                    //  echo $row['FullName']; ?>"
                                                    data-slclient-no="<?php
                                                    //  echo $row['ClientNo']; ?>"
                                                    data-slprogram="<?php
                                                    //  echo $row['Program']; ?>"
                                                    data-slproduct="<?php
                                                    //  echo $row['Product']; ?>"
                                                    data-sldate-released="<?php
                                                    //  echo $row['DateRelease']; ?>"
                                                    data-sldate-matured="<?php
                                                    //  echo $row['DateMature']; ?>"
                                                    data-slloan-amount="<?php
                                                    //  echo $row['LoanAmount']; ?>"
                                                    data-slinterest="<?php
                                                    //  echo $row['Interest']; ?>"
                                                    data-slcbu="<?php
                                                    //  echo $row['CBU']; ?>"
                                                    data-slpnno="<?php
                                                    //  echo $row['PNNo']; ?>"
                                                    data-slpo="<?php
                                                    //  echo $row['PO']; ?>"
                                                    onclick="populateAccountDetails(this)">
                                                    <td><?php
                                                    //  echo $row['LoanID']; ?></td>
                                                    <td><?php
                                                    //  echo $row['Product']; ?></td>
                                                    <td><?php
                                                    //  echo $row['DateRelease']; ?></td>
                                                    <td><?php
                                                    //  echo $row['LoanType']; ?></td>
                                                </tr>
                                                <?php 
                                            // endforeach; ?>


                                                <tr id="noDataMessage" style="display: none;">
                                                    <td colspan="6" class="text-center" style="color:red; font-size:100px">No data available</td>
                                                </tr>
                                                <tr>
                                                    <td class="tex-center" colspan="6">
                                                        <?php
                                                        // if (is_array($loansdata) && !empty($loansdata)) {
                                                        //     foreach ($loansdata as $row) {
                                                        //     }
                                                        // } else {
                                                        //     echo '<p style="color:red; text-align: center; font-size: 20px">No data available</p>';
                                                        // }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!--Account Details-->
                                    <div class="p-3 shadow" style="background-color: white">
                                        <div class="mb-3">
                                            <p class="fw-medium fs-5" style="color: #090909;">Account Details</p>
                                            <hr style="height: 1px">

                                            <div class="row mt-2">
                                                <div class="col-md-4">Client No.</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="clientDetails" id="clientDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">Program</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="programDetails" id="programDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">Product</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="productDetails" id="productDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">Date Released</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="dateReleaseDetails" id="dateReleaseDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">Date Mature</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="dateMatureDetails" id="dateMatureDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">Loan Amount</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="loanAmountDetails" id="loanAmountDetails">
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-2">
                                                <div class="col-md-4">Interest</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="interestDetails" id="interestDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">Service Fee</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="serviceFeeDetails" id="serviceFeeDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">CBU</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="cbuDetails" id="cbuDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">PN No.</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="pnnoDetails" id="pnnoDetails">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">PO</div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control-plaintext" readonly name="poDetails" id="poDetails">
                                                </div>
                                            </div>

                                            
                                        </div>

                                    
                                    </div>
                                </div>
                                
                            </div>

                            <!--SL Preview-->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-medium fs-5" style="color: #090909;">SL Preview</p>
                                        <h6 class="text-muted text-end">View all transactions</h6>
                                    </div>
                                    <hr style="height: 1px">
                                </div>

                                <div class="col-md-12">
                                    <div class="">
                                        <table class="table table-hover table-borderless p-3 table-selected" style="background-color: white;">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Ref</th>
                                                    <th>Penalty</th>
                                                    <th>Interest</th>
                                                    <th>Principal</th>
                                                    <th>LR</th>
                                                    <th>Balance</th>
                                                    <th>CBU</th>
                                                    <th>MBAPr</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                //  foreach ($slpreviewdata as $row) : 
                                                ?>
                                                <tr data-id="<?php 
                                                // echo $row['ID']; 
                                                ?>"
                                                    onclick="populateSLPreview(this)">
                                                    <td><?php 
                                                    // echo $row['CDate']; 
                                                    ?></td> 
                                                    <td><?php 
                                                    // echo $row['AcctNo']; 
                                                    ?></td>
                                                    <td><?php 
                                                    // echo $row['ID']; 
                                                    ?></td>
                                                    <td><?php 
                                                    // echo $row['Interest']; ?></td>
                                                    <td><?php 
                                                    // echo $row['Program']; ?></td>
                                                    <td><?php 
                                                    // echo $row['LRSType']; ?></td>
                                                    <td><?php 
                                                    // echo $row['CreditLimited']; ?></td>
                                                    <td><?php 
                                                    // echo $row['CBU']; ?></td>
                                                    <td><?php 
                                                    // echo $row['EF']; ?></td>
                                                </tr>
                                                <?php 
                                            // endforeach; ?>

                                                <tr id="noDataMessage" style="display: none;">
                                                    <td colspan="6" class="text-center" style="color:red; font-size:100px">No data available</td>
                                                </tr>
                                                <tr>
                                                    <td class="tex-center" colspan="6">
                                                        <?php
                                                        // if (is_array($slpreviewdata) && !empty($slpreviewdata)) {
                                                        //     foreach ($slpreviewdata as $row) {
                                                        //     }
                                                        // } else {
                                                        //     echo '<p style="color:red; text-align: center; font-size: 20px">No data available</p>';
                                                        // }
                                                        ?>
                                                    </td>
                                                </tr> 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Account</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Client No</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Loan ID</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-2">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">
                                                    Client Name
                                                </label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Date Release</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Date Mature</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Loan Ammount</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Loan Avail</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">CDP (if any)</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <hr style="height:1px">
                                    <h6>Center Assignment</h6>
                                    <hr style="height:1px">

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">Center</label>
                                            </div>
                                            <div class="col-md-10">
                                                <select name="" class="form-select" id="">
                                                    <option value="">SANTIAGO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">Group</label>
                                            </div>
                                            <div class="col-md-10">
                                                <select name="" class="form-select" id="">
                                                    <option value="">SANTIAGO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <hr style="height:1px">
                                    <h6>Business Particulars</h6>
                                    <hr style="height:1px">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label for="">Sector</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <select name="" class="form-select" id="">
                                                        <option value="">EMPLOYEE</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Age (Year/Month)</label>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="" id="">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="" id="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label for="">Nature</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <select name="" class="form-select" id="">
                                                        <option value="">EMPLOYEE</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Capital</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="" id="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label for="">Type</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <select name="" class="form-select" id="">
                                                        <option value="">ASKI EMPLOYEE</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Workers</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="" id="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="">Prod/Svc</label>
                                                </div>
                                                <div class="col-md-9">
                                                    <select name="" class="form-select" id="">
                                                        <option value="">EMPLOYEE</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Mo. of Income</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="" id="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <hr style="height:1px">
                                    <div class="d-flex justify-content-between">
                                        <h6>Loan Particulars</h6>
                                        <h6>Total / Amortization</h6>
                                    </div>

                                    <hr style="height:1px">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Fund</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="" class="form-select" id="">
                                                    <option value="">ISYNERGIES</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Principal</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Loan Type</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="" class="form-select" id="">
                                                    <option value="">NEW</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Interest</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Program</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="" class="form-select" id="">
                                                    <option value="">ISYN</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">CBU</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">PO</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="" class="form-select" id="">
                                                    <option value="">ISYNERGIES</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">EF</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Mode</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="" class="form-select" id="">
                                                    <option value="">SEMI MONTHLY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">MBA</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">TERMS</label>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="" id="" class="form-select">
                                                    <option value="">6</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select name="" id="" class="form-select">
                                                    <option value="">3</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="">%</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">TOTAL</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="" id="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">INT COMP.</label>
                                            </div>
                                            <div class="col-md-8">
                                                <button class="btn btn-primary w-100 ">ADD-ON</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <input type="checkbox" name="" id="">
                                                <label for="">This is an Additional Loan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- end of container -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <!-- <script src="../../js/generalledger/posting.js?<?= time() ?>"></script> -->

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
<!-- <script>
    function enableFormFields() {
        var formElements = document.querySelectorAll('#form-renewal input, #form-renewal select');

        formElements.forEach(function(element) {
            element.removeAttribute('disabled');
        });

        
    }
</script>
<script>
    function populateFormFields(row) {
        var userId = row.dataset.userId;
        document.getElementById('userIdInput').value = userId;

        console.log(userId);

        if (userId) {
            // Make an AJAX request to fetch user details
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var userData = JSON.parse(xhr.responseText);

                        enableFormFields();

                        // Populate form fields with retrieved user data
                        document.getElementById('loanType').value = userData.LoanType;
                        document.getElementById('tag').value = userData.Tag;
                        document.getElementById('poFco').value = userData.PO;
                        document.getElementById('program').value = userData.Program;
                        document.getElementById('product').value = userData.Product;
                        document.getElementById('mode').value = userData.Mode;
                        document.getElementById('termRate').value = userData.Term;
                        document.getElementById('availment').value = userData.LoanAvailment;
                        document.getElementById('amount').value = userData.LoanAmount;

                        var product = userData.Product;
                        var percentage = parseFloat(product.split(' ')[1].replace('%', ''));
                        document.getElementById('rate').value = percentage + "%";

                        document.getElementById('sector').value = userData.Sector;
                        document.getElementById('ageYear').value = userData.BizAgeYr;
                        document.getElementById('ageMonth').value = userData.BizAgeMo;
                        document.getElementById('nature').value = userData.BizNature;
                        document.getElementById('capital').value = userData.BizCapital;
                        document.getElementById('type').value = userData.BizType;
                        document.getElementById('workers').value = userData.Workers;
                        document.getElementById('productServices').value = userData.ProductService;
                        document.getElementById('moIncome').value = userData.MoIncome;
                        document.getElementById('group').value = userData.GroupName;


                        document.getElementById('lastname').value = userData.LastName;
                        document.getElementById('firstname').value = userData.FirstName;
                        document.getElementById('middlename').value = userData.MiddleName;

                        originalValues.loanType = userData.LoanType;
                        originalValues.tag = userData.Tag;
                        originalValues.poFco = userData.PO;
                        originalValues.program = userData.Program;
                        originalValues.product = userData.Product;
                        originalValues.mode = userData.Mode;
                        originalValues.termRate = userData.Term;
                        originalValues.availment = userData.LoanAvailment;
                        originalValues.amount = userData.LoanAmount;
                        originalValues.rate = percentage + "%"; // Store rounded percentage
                        originalValues.sector = userData.Sector;
                        originalValues.ageYear = userData.BizAgeYr;
                        originalValues.ageMonth = userData.BizAgeMo;
                        originalValues.nature = userData.BizNature;
                        originalValues.capital = userData.BizCapital;
                        originalValues.type = userData.BizType;
                        originalValues.workers = userData.Workers;
                        originalValues.productServices = userData.ProductService;
                        originalValues.moIncome = userData.MoIncome;
                        originalValues.group = userData.GroupName;

                        originalValues.lastname = userData.LastName;
                        originalValues.firstname = userData.FirstName;
                        originalValues.middlename = userData.MiddleName;

                    } else {
                        console.error('Failed to fetch user details');
                    }
                }
            };

            xhr.open('GET', '/isyn-app/src/includes/accounts-monitoring/ajax/ajax_loan_transaction.php?userId=' + userId, true);
            xhr.send();
        } else {
            clearFormFields();

            document.getElementById('addNew').disabled = false;
        }
    }
</script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    function updateAccountDetails() {
        var tableRows = document.getElementById('loanTable').getElementsByTagName('tr');
        for (var i = 0; i < tableRows.length; i++) {
            tableRows[i].addEventListener('click', function(event) {
                // Check if the clicked row is not a header row
                if (!this.parentElement.tagName || this.parentElement.tagName.toLowerCase() !== 'thead') {
                    // Extract data from the clicked row
                    var rowData = this.cells;
                    document.getElementById('programDetails').value = rowData[0].innerText;
                    document.getElementById('productDetails').value = rowData[2].innerText;
                    document.getElementById('dateReleaseDetails').value = rowData[3].innerText;
                    document.getElementById('dateMatureDetails').value = rowData[4].innerText;
                    document.getElementById('loanDetails').value = rowData[5].innerText;
                    document.getElementById('PNNoDetails').value = rowData[6].innerText;
                    document.getElementById('poDetails').value = rowData[7].innerText;

                }
            });
        }
    }

    // Function to clear account details
    function clearAccountDetails() {
        document.getElementById('programDetails').value = '';
        document.getElementById('productDetails').value = '';
        document.getElementById('dateReleaseDetails').value = '';
        document.getElementById('dateMatureDetails').value = '';
        document.getElementById('loanDetails').value = '';
        document.getElementById('PNNoDetails').value = '';
        document.getElementById('PNNoDetails').value = '';
        document.getElementById('poDetails').value = '';


        // Clear more input fields if needed
    }
</script>
<script>
    // Function to handle click event on table rows
    function fillFormFields(row) {
        //var id = $(row).data('ID');
        var id = row.dataset.id;
        console.log("Clicked ID:", id);

        // Remove 'selected' class from all rows
        $('.table-row-clickable').removeClass('selected');
        // Add 'selected' class to the clicked row
        $(row).addClass('selected');

        var editAccount = document.getElementById('editAccount');
        editAccount.disabled = false;

        // Accessing row data and filling form fields
        var program = $(row).data('program');
        var product = $(row).data('product');
        var availment = $(row).data('availment');
        var dateRelease = $(row).data('date-release');
        var dateMature = $(row).data('date-mature');
        var mode = $(row).data('mode');
        var term = $(row).data('term');
        var po = $(row).data('po');
        var fund = $(row).data('fund');
        var pnno = $(row).data('pnno');
        var tag = $(row).data('tag');

        var loanAmount = parseFloat($(row).data('loan-amount'));
        var interestAmount = $(row).data('interest-amount');

        //var serviceFee = $(row).data('service-fee');

        var cbu = $(row).data('cbu');
        var ef = $(row).data('ef');
        var mba = $(row).data('mba');

        var principalPaid = $(row).data('principal-paid');
        var interestPaid = $(row).data('interest-paid');
        var cbuPaid = $(row).data('cbu-paid');
        var efPaid = $(row).data('ef-paid');
        var mbaPaid = $(row).data('mba-paid');
        var penaltiesPaid = $(row).data('penalties-paid');

        var principalBalance = $(row).data('principal-balance');
        var cumulativeCBU = $(row).data('cumulative-cbu');
        var cbuIntEarned = $(row).data('cbu-intEarned');

        var dateDeleted = $(row).data('date-deleted');
        var dateRestructured = $(row).data('date-restructured');
        var dateWrittenOff = $(row).data('date-writtenoff');
        var dateDropped = $(row).data('date-dropped');

        var dueDateDisplay = $(row).data('due-date');
        var principalAmo = $(row).data('principal');
        var interestDue = $(row).data('interest-due');
        var serviceFee = $(row).data('service-fee');
        var cbuDue = $(row).data('cbu-due');
        var efDue = $(row).data('ef-due');
        var mbaDue = $(row).data('mba-due');
        var penalty = $(row).data('penalty-due');
        const pA = parseFloat(principalAmo.replace(",", ""));
        const iD = parseFloat(interestDue.replace(",", ""));
        var f = (pA + iD);
        let amountTotal = Intl.NumberFormat('en-US', {minimumFractionDigits: 2,});
        var total = amountTotal.format(f);
        console.log((principalAmo) + ' + ' + (interestDue)  + ' = ' + total);

        var interestRate = $(row).data('interest-rate');
        var intComputation = $(row).data('int-computation');
        
        var oneToThirty = $(row).data('one-thirty');
        var thirtyOnetoSixty = $(row).data('thirtyone-to-sixty');
        var sixtyOnetoNinety = $(row).data('sixtyone-to-ninety');
        var ninetyOneToOneTwenty = $(row).data('ninetyone-to-onetwenty');
        var oneTwentyOneToOneFifty = $(row).data('onetwentyone-to-onefifty');
        var oneFiftyOneToOneEighty = $(row).data('onefiftyone-to-oneeighty');
        var overOneEighty = $(row).data('over-oneeighty');
        var totalArrears = $(row).data('total-arrears');
        var par = $(row).data('par');


        /*
        // Filling form fields
            document.getElementById('program').value = program || '';
            document.getElementById('product').value = product || '';
            document.getElementById('intRate').value = interestRate || '';
            document.getElementById('intComputation').value = intComputation || '';
            document.getElementById('dateRelease').value = dateRelease ? dateRelease.split(" ")[0] : '';
            document.getElementById('dateMature').value = dateMature || '';
            document.getElementById('mode').value = mode || '';
            document.getElementById('term').value = term || '';
            document.getElementById('PO').value = po || '';
            document.getElementById('PNNo').value = pnno || '';
            document.getElementById('tag').value = tag || '';
            document.getElementById('availment').value = availment || '';
            document.getElementById('fund').value = fund || '';
            document.getElementById('loanAmount').value = loanAmount ? loanAmount.toLocaleString() : '';
            document.getElementById('interest').value = interestAmount || '';
            document.getElementById('cbu').value = cbu || '0.00';
            document.getElementById('ef').value = ef || '0.00';
            document.getElementById('mba').value = mba || '0.00';
            document.getElementById('dueDateDisplay').value = dueDateDisplay || '';
            document.getElementById('principalAmo').value = principalAmo || '';
            document.getElementById('interestDue').value = interestDue || '';
            document.getElementById('cbuDue').value = cbu || '0.00';
            document.getElementById('efDue').value = ef || '0.00';
            document.getElementById('mbaDue').value = mba || '0.00';
            document.getElementById('penaltyDue').value = penaltyDue || '0.00';

            document.getElementById('interestPaid').value = interestPaid || '';
            document.getElementById('cbuPaid').value = cbuPaid || '0.00';
            document.getElementById('efPaid').value = efPaid || '0.00';
            document.getElementById('mbaPaid').value = mbaPaid || '0.00';
            document.getElementById('penaltiesPaid').value = penaltiesPaid || '0.00';

            document.getElementById('OneToThirty').value = oneToThirty || '';
            document.getElementById('ThirtyOneToSixty').value = thirtyOnetoSixty || '';
            document.getElementById('SixtyOneToNinety').value = sixtyOneToNinety || '';
            document.getElementById('NinetyOneToOneTwenty').value = ninetyOneToOneTwenty || '';
            document.getElementById('OneTwentyOneToOneFifty').value = oneTwentyOneToOneFifty || '';
            document.getElementById('OneFiftyOneToOneEighty').value = oneFiftyOneToOneEighty || '';
            document.getElementById('OverOneEighty').value = overOneEighty || '';
            document.getElementById('totalArrears').value = totalArrears || '';
            document.getElementById('par').value = par || '';
        */

        setFieldValue('program', program);
        setFieldValue('product', product);
        setFieldValue('availment', availment);
        setFieldValue('date-release', dateRelease);
        setFieldValue('date-mature', dateMature);
        setFieldValue('mode', mode);
        setFieldValue('term', term);
        setFieldValue('PO', po);
        setFieldValue('fund', fund);
        setFieldValue('PNNo', pnno);
        setFieldValue('tag', tag);
        setFieldValue('loan-amount', loanAmount);
        setFieldValue('interest-amount', interestAmount);
        //setFieldValue('service-fee', serviceFee);
        setFieldValue('cbu', cbu);
        setFieldValue('ef', ef);
        setFieldValue('mba', mba);
        setFieldValue('principal-paid', principalPaid);
        setFieldValue('interest-paid', interestPaid);
        setFieldValue('cbu-paid', cbuPaid);
        setFieldValue('ef-paid', efPaid);
        setFieldValue('mba-paid', mbaPaid);
        setFieldValue('penalties-paid', penaltiesPaid);
        setFieldValue('principal-balance', principalBalance);
        setFieldValue('cumulative-cbu', cumulativeCBU);
        setFieldValue('cbu-intEarned', cbuIntEarned);
        //setFieldValue('date-deleted', dateDeleted);
        setFieldValue('date-restructured', dateRestructured);
        setFieldValue('date-writtenOff', dateWrittenOff);
        setFieldValue('date-dropped', dateDropped);
        setFieldValue('due-date', dueDateDisplay);
        setFieldValue('principal', principalAmo);
        setFieldValue('interest-due', interestDue);
        setFieldValue('service-fee', serviceFee);
        setFieldValue('cbuDue', cbuDue);
        setFieldValue('mbaDue', mbaDue);
        setFieldValue('efDue', efDue);
        setFieldValue('penalty-due', penalty);
        setFieldValue('total', total);
        setFieldValue('interest-rate', interestRate);
        setFieldValue('int-computation', intComputation);
        setFieldValue('one-thirty', oneToThirty);
        setFieldValue('thirty-one-to-sixty', thirtyOnetoSixty);
        setFieldValue('sixtyone-to-ninety', sixtyOnetoNinety);
        setFieldValue('ninetyone-onetwenty', ninetyOneToOneTwenty);
        setFieldValue('onetwentyone-onefifty', oneTwentyOneToOneFifty);
        setFieldValue('onefiftyone-oneeighty', oneFiftyOneToOneEighty);
        setFieldValue('over-oneeighty', overOneEighty);
        setFieldValue('total-arrears', totalArrears);
        setFieldValue('par', par);


    }

    function setFieldValue(elementId, value) {
        var element = document.getElementById(elementId);
        if (element) {
            element.value = value;
        } else {
            console.error('Element with ID ' + elementId + ' not found.');
        }
    }

    function populateAccountDetails(row) {
        //var id = $(row).data('ID');
        var id = row.dataset.id;
        console.log("Clicked ID:", id);

        // Remove 'selected' class from all rows
        $('.table-row-clickable').removeClass('selected');
        // Add 'selected' class to the clicked row
        $(row).addClass('selected');

        var slclientNo = $(row).data('slclient-no');
        var slprogram = $(row).data('slprogram');
        var slproduct = $(row).data('slproduct');
        var sldateReleased = $(row).data('sldate-released');
        var sldateMatured = $(row).data('sldate-matured');
        var slloanAmount = $(row).data('slloan-amount');
        var slInterest = $(row).data('slinterest');
        //var slServiceFee = $(row).data('slservice-fee');
        var slcbu = $(row).data('slcbu');
        var slpnno = $(row).data('slpnno');
        var slpo = $(row).data('slpo');

        setFieldValue('clientDetails',slclientNo);
        setFieldValue('programDetails',slprogram);
        setFieldValue('productDetails',slproduct);
        setFieldValue('dateReleaseDetails',sldateReleased);
        setFieldValue('dateMatureDetails',sldateMatured);
        setFieldValue('loanAmountDetails',slloanAmount);
        setFieldValue('interestDetails',slInterest);
        //setFieldValue('serviceFeeDetails',slServiceFee);
        setFieldValue('cbuDetails',slcbu);
        setFieldValue('pnnoDetails',slpnno);
        setFieldValue('poDetails',slpo);


    }




    // Event listener for selecting table rows  
    $(document).on('click', '.table-row-clickable', function() {
        // Call fillFormFields function with the clicked row
        fillFormFields(this);
        // Enable the delete button
        $('#deleteAccount').prop('disabled', false);
        //$('#removeAccount').prop('disabled', false);
    });
</script>


<script>
    // Event listener for delete button
    $('#deleteAccount').click(function() {
        // Get the ID of the selected data
        var id = $('.table-row-clickable.selected').data('id');
        console.log("ID to delete:", id); // Add this line for debugging

        // Show confirmation dialog using SweetAlert
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed deletion, send AJAX request to delete data
                $.ajax({
                    url: './ajax/ajax_delete_aging.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        // Handle success response
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response,
                        }).then(() => {
                            // Remove the deleted row from the table
                            $('.table-row-clickable.selected').remove();
                            // Disable delete button
                            $('#deleteAccount').prop('disabled', true);
                            // Clear form fields
                            clearFormFields();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    // Function to clear form fields
    function clearFormFields() {
        // Clear all form fields
        $('input[type="text"], input[type="number"]').val('');
        $('select').val('');
    }
</script>
<script>
    // Function to simulate update progress
    function updateProgress() {
        var elem = document.getElementById("myBar");
        var width = 0;
        var id = setInterval(frame, 10);

        function frame() {
            if (width >= 100) {
                clearInterval(id);
            } else {
                width++;
                elem.style.width = width + "%";
                elem.innerHTML = width + "%";
            }
        }
    }

    // Function to handle the update button click
    document.getElementById("updateBtn").addEventListener("click", function() {
        document.getElementById("updateBtn").disabled = true;
        document.getElementById("updateStatus").textContent = "Updating...";
        updateProgress();

        setTimeout(function() {
            document.getElementById("updateBtn").disabled = true;
            document.getElementById("updateStatus").textContent = "Successfully updated!!";
        }, 3000);
    });
</script>
<script>
    // document.getElementById('updateBtn').addEventListener('click', function() {
    //     var xhr = new XMLHttpRequest();
    //     xhr.onreadystatechange = function() {
    //         if (xhr.readyState === 4) {
    //             if (xhr.status === 200) {
    //                 alert("UPDATING: " + xhr.responseText); // Displaying success message
    //             } else {
    //                 alert("ERROR: Unable to update. Please try again."); // Displaying error message
    //             }
    //         }
    //     };
    //     xhr.open('POST', 'updateAging.php', true);
    //     xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    //     xhr.send('btnUpdate=true'); // Sending the button update as a POST parameter
    // });

    document.getElementById('updateBtn').addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'updateAging.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                } else {
                    console.error('Error: ' + xhr.status);
                }
            }
        };
        xhr.send();
    });
</script>
    

<script>
    //For sorting the table based on option value "Name" or "ClientNo"
    $(document).ready(function() {
        $('#selectionOrder').change(function() {
            var selectedValue = $(this).val();
            sortTable(selectedValue);
        });

        function sortTable(option) {
            var rows = $('#myTable tbody tr').get();

            rows.sort(function(a, b) {
                var A = $(a).find('td:eq(' + getColumnIndex(option) + ')').text().toUpperCase();
                var B = $(b).find('td:eq(' + getColumnIndex(option) + ')').text().toUpperCase();

                if (A < B) {
                    return -1;
                }
                if (A > B) {
                    return 1;
                }
                return 0;
            });

            $.each(rows, function(index, row) {
                $('#myTable').children('tbody').append(row);
            });
        }

        function getColumnIndex(option) {
            switch (option) {
                case 'name':
                    return 0; // Index of the Name column (0-based index)
                case 'clientNo':
                    return 1; // Index of the Client No. column (0-based index)
                default:
                    return 0; // Default to Name column if something goes wrong
            }
        }
    });


    //For filtering table data based on selected client name
    function filterTableByClient() {
        var selectBox = document.getElementById("selectName");
        var selectedValue = selectBox.options[selectBox.selectedIndex].value;
        var tableRows = document.getElementById("loanTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");

        for (var i = 0; i < tableRows.length; i++) {
            var row = tableRows[i];
            var fullName = row.getAttribute("data-slfull-name");

            if (selectedValue === "all" || fullName === selectedValue) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
</script>

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


<!-- 
data-one-thirty="<?php echo $row['DAYS130']; ?>" 
                                     data-thirty-one-to-sixty="<?php echo $row['DAYS3160']; ?>" 
                                     data-sixtyone-to-ninety="<?php echo $row['DAYS6190']; ?>" 
                                     data-ninetyone-to-onetwenty="<?php echo $row['DAYS91120']; ?>" 
                                     data-onetwentyone-to-onefifty="<?php echo $row['DAYS121150']; ?>" 
                                     data-onefiftyone-to-oneeighty="<?php echo $row['DAYS151180']; ?>" 
                                     data-over-oneeighty="<?php echo $row['DaysOver180']; ?>" 
                                     data-total-arrears="<?php echo $row['TotalArrears']; ?>" 
                                     data-par="<?php echo $row['PAR']; ?>"  -->
