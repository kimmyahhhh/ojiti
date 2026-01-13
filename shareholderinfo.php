<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
?>
<!doctype html>
<html lang="en" dir="ltr">
    <?php
        include('../../includes/pages.header.php');
    ?>
    <title>iSyn | Shareholder</title>

    <body class="  ">
        <!-- loader Start -->
        <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
        </div>
        <!-- loader END -->

        <style>
            td {
                font-weight: 400;
            }
        
            form {
                padding: 20px;
                background-color: white;
                border-radius: 10px;
            }
        
            label,
            th {
                color: #090909;
            }
        
            main {
                background-color: #EAEAF6;
            }
        
            th {
                font-weight: bold;
                color: #090909;
                position: sticky;
                top: 0;
            }
        
            .selected td {
                background-color: lightgray;
            }

            /* Names list styles (inside table footer) */
            #shNamesList {
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
                background-color: white;
                max-height: 250px;
                overflow-y: auto;
                margin-top: 5px;
            }

            #shNamesList .list-group-item {
                cursor: pointer;
                padding: 0.5rem 0.75rem;
                border: none;
                border-bottom: 1px solid #e9ecef;
                transition: background-color 0.2s;
                text-align: left;
            }

            #shNamesList .list-group-item:hover {
                background-color: #f8f9fa;
            }

            #shNamesList .list-group-item:last-child {
                border-bottom: none;
            }

            #shNamesList .list-group-item.selected-name {
                background-color: #e7f3ff;
                font-weight: 500;
            }
        </style>

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <div class="container-fluid mt-1">
                <div class="shadow rounded-3 p-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Shareholder Information</p>
                </div>

                <div class="row mt-4 mb-2">
                    <div>
                        <div class="shadow p-3 rounded-2" style="background-color: white;overflow:auto;">
                            <div class="align-items-center justify-content-between mb-3">
                                <button id="ConfigurationBtn" class="btn btn-secondary  px-3 py-2 float-end mx-2" type="button">
                                    <i class="fa-solid fa-gear"></i> Configuration
                                </button>

                                <p class="fw-medium fs-5 mb-4" style="color: #090909;">Shareholder List</p>
                            </div>
                            <hr style="height: 1px">

                            <!-- SEARCH BAR ON TOP (OUTSIDE TABLE) -->
                            <div class="row mb-3">
                                <div class="form-group row">
                                    <label class="col-sm-auto col-form-label">Name</label>
                                    <div class="col-sm-6">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               id="shNames"
                                               placeholder="Search shareholder name..."
                                               autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <!-- TABLE WITH NAMES LIST IN FOOTER -->
                            <table id="shareholderTbl" class="table table-bordered text-center" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:20%;text-align:center">ID</th>
                                        <th style="width:20%;text-align:center">Full Name</th>
                                        <th style="width:20%;text-align:center">Shareholder Type</th>
                                        <th style="width:20%;text-align:center">No. Of Shares</th>
                                        <th style="width:20%;text-align:center">Type</th>
                                    </tr>
                                </thead>

                                <tbody id="shareholderList">
                                    <!-- rows added by JS -->
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <div id="shNamesList" class="list-group">
                                                <!-- Names will be populated here by JS -->
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12 mb-2">
                        <form class="p-3 needs-validation shadow mb-4" novalidate method="POST" id="shareholderInfo">
                            <div class=" align-items-center justify-content-between mb-3">
                                <button type="button" name="printCert" id="printCert" class="btn btn-info px-3 py-2 float-end" onclick="PrintReport();" disabled>
                                    <i class="fa fa-print"></i> Generate Certificate
                                </button>

                                <button id="editButton" class="btn btn-primary  px-3 py-2 float-end mx-2" type="button"  disabled>
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>

                                <button id="addNew" class="btn btn-success  px-3 py-2 float-end mx-2" type="button">
                                    <i class="fa-solid fa-plus"></i> New
                                </button>

                                <p class="fw-medium fs-5 mb-4" style="color: #090909;">Shareholder's Information</p>
                            </div>
                            <hr style="height: 1px">

                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" id="shareID" name="shareID" class="form-control" disabled>
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" id="actualNo" name="actualNo" class="form-control" readonly>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="shareholderID" class="form-label">Shareholder ID</label>
                                    <input type="text" class="form-control" id="shareholderID" name="shareholderID" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label for="cert_no" class="form-label">Issued Certificate No.</label>
                                    <input type="number" class="form-control" id="cert_no" name="cert_no"  disabled>
                                </div>
                                <div class="col-md-4 mt-5">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="Yes" name="president" id="president" disabled>
                                        <label class="form-check-label" for="president">
                                            President
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="shareholderName" class="form-label mt-2">Shareholder Name</label>
                                    <input type="text"
                                           class="form-control searchName"
                                           id="shareholderName"
                                           name="shareholderName"
                                           placeholder="Shareholder Name"
                                           required
                                           autocomplete="off"
                                           disabled>
                                    <div class="invalid-feedback">
                                        Please provide your shareholder name.
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="contact_number" class="form-label mt-2">Contact No.</label>
                                    <input type="text"
                                           class="form-control"
                                           id="contact_number"
                                           name="contact_number"
                                           placeholder="09*********"
                                           maxlength="11"
                                           disabled>
                                    <div class="invalid-feedback">
                                        Please provide valid contact no.
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="email" class="form-label mt-2">Email Address</label>
                                    <input type="email"
                                           class="form-control"
                                           id="email"
                                           name="email"
                                           placeholder="example@gmail.com"
                                           disabled>
                                    <div class="invalid-feedback">
                                        Enter your Email Address.
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="facebook_account" class="form-label mt-2">Facebook Link</label>
                                    <input type="text"
                                           class="form-control"
                                           id="facebook_account"
                                           name="facebook_account"
                                           placeholder="https://www.facebook.com/juan"
                                           disabled>
                                    <div class="invalid-feedback">
                                        Please provide your Facebook Link.
                                    </div>
                                </div>
                            
                                <div class="col-md-4">
                                    <label for="shareholder_type" class="form-label mt-2">Shareholder Type:</label>
                                    <select class="form-select" aria-label="Default select example" id="shareholder_type" name="shareholder_type" disabled>
                                        <option value="" selected>Select</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="type" class="form-label mt-2">Type of Shares:</label>
                                    <select class="form-select" aria-label="Default select example" id="type" name="type" disabled>
                                        <option value="" selected>Select</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="noofshare" class="form-label mt-2">No of Shares:</label>
                                    <input type="number"
                                           class="form-control"
                                           id="noofshare"
                                           name="noofshare"
                                           required
                                           oninput="calculateAmount()"
                                           disabled>
                                    <div class="invalid-feedback">
                                        Please provide your No of Shares.
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="amount_share" class="form-label mt-2">Amount of Shares:</label>
                                    <input type="number"
                                           class="form-control"
                                           id="amount_share"
                                           name="amount_share"
                                           disabled>
                                    <div class="invalid-feedback">
                                        Please provide your Amount of Shares.
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mt-5">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="Yes" name="emp_resign" id="emp_resign" disabled>
                                        <label class="form-check-label" for="emp_resign">
                                            Emp-resigned
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row ">
                                <div class="col-12 text-end mt-3">
                                    <button id="updateButton"
                                            disabled
                                            class="btn btn-primary mx-2 float-end"
                                            style="display: none;"
                                            type="button"
                                            form="shareholderInfo">
                                        <i class="fa-solid fa-upload"></i>Update
                                    </button>

                                    <button id="submitButton"
                                            name="submitButton"
                                            disabled
                                            class="btn btn-primary mx-2 float-end"
                                            type="button"
                                            form="shareholderInfo">
                                        <i class="fa-solid fa-check-circle"></i> Submit
                                    </button>

                                    <button class="btn btn-danger float-end"
                                            type="button"
                                            id="cancel"
                                            hidden
                                            disabled
                                            onclick="Cancel();">
                                        <i class="fa-regular fa-circle-xmark"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="modal fade" id="configurationMDL" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Configuration</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="configurationForm" method="POST">
                                <div class="form-group row">
                                    <label class="form-label">Signatory 1:</label>
                                    <div class="col-sm-6">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="signatory1Name"
                                               id="signatory1Name"
                                               placeholder="Enter Name">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="signatory1Desig"
                                               id="signatory1Desig"
                                               placeholder="Enter Designation">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="form-label">Signatory 2:</label>
                                    <div class="col-sm-6">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="signatory2Name"
                                               id="signatory2Name"
                                               placeholder="Enter Name">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="signatory2Desig"
                                               id="signatory2Desig"
                                               placeholder="Enter Designation">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="form-label">Signatory Sub 2:</label>
                                    <div class="col-sm-6">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="signatorySub2Name"
                                               id="signatorySub2Name"
                                               placeholder="Enter Name">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="signatorySub2Desig"
                                               id="signatorySub2Desig"
                                               placeholder="Enter Designation">
                                    </div>
                                </div>

                                <div class="form-group row mt-3">
                                    <label class="form-label">Current Certificate No:</label>
                                    <div class="col-sm-6">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               name="currentCertNo"
                                               id="currentCertNo">
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="updateConfigBtn" class="btn btn-primary"> Update</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../js/maintenance.js"></script>
        <script src="../../js/profiling/shareholderinfo.js"></script>
        
    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
