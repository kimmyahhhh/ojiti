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
            label {
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
            }


            th {
                font-weight: bold;
                color: #090909;
                position: sticky;
                top: 0;
            }

            .custom-input {
                border: none;
                border-bottom: .1px solid gray;
                outline: none;
                width: 85px;
                text-align: center;
                margin-top: 20px;
            }

            .custom-input:focus {
                border-bottom: 2px solid #0D6EFD;
            }

            .selected td {
                background-color: lightgray;
            } 
        </style>

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <div class="container-fluid mt-1">
                <div class=" shadow p-3 rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Supplier's Information</p>
                </div>
                <div class="row mt-4 ">
                    <div class="col-md-12">
                        <div class="shadow p-3 rounded-3" style="background-color: white; overflow: auto">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Supplier's List</p>
                            </div>
                            <hr style="height: 1px">
                            <table id="SupplierInfoTbl" class="table table-bordered text-center" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width:20%;text-align:center">Supplier No.</th>
                                        <th style="width:20%;text-align:center">Supplier Name</th>
                                        <th style="width:20%;text-align:center">TIN No.</th>
                                        <th style="width:20%;text-align:center">Mobile No./Tel. No.</th>
                                        <th style="width:20%;text-align:center">Email</th>
                                        <th style="width:20%;text-align:center">Social Link</th>
                                        <th style="width:20%;text-align:center">Full Address</th>
                                        <th style="width:20%;text-align:center">Date Encoded</th>                                            
                                    </tr>
                                </thead>
                                <tbody id="SupplierInfoList">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <form class="p-3 needs-validation mb-3 shadow" novalidate method="POST" id="supplierInfo" autocomplete="off">
                            <div class="align-items-center justify-content-between mb-3">
                                <button class="btn btn-primary float-end mx-2" id="editButton" type="button" disabled><i class="fa-solid fa-pen-to-square" ></i> Edit</button>
                                <button class="btn btn-success float-end mx-2" id="addNew" type="button" name="new"> <i class="fa-solid fa-plus"></i> New</button>
                                <p class=" fw-medium fs-5" style="color: #090909;">Supplier's Information</p>
                            </div>
                            <hr style="height: 1px">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="supplierNo" class="form-label">Supplier No.</label>
                                    <input type="num" class="form-control" id="supplierNo" name="supplierNo" placeholder="Supplier No." required disabled>
                                    <div class="invalid-feedback">
                                        Please provide your Supplier No.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="supplierName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="supplierName" name="supplierName" placeholder="Supplier's Name" required disabled>
                                    <div class="invalid-feedback">
                                        Please provide your Company Name.
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="digit" class="form-label">TIN</label>
                                    <input type="text" id="tin" name="tin" class="form-control tin-field" placeholder="###-###-###-###" maxlength="16"  disabled>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-4 mb-2">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="exmple@gmail.com" disabled>
                                    <div class="invalid-feedback">
                                        Enter your email address.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="mobileNumber" class="form-label">Mobile No.</label>
                                            <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" placeholder="09*********" disabled maxlength="11">
                                            <div class="invalid-feedback">
                                                Please provide your Contact No.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telNumber" class="form-label">Telephone No.</label>
                                            <input type="text" class="form-control" id="telNumber" name="telNumber" placeholder="02 ****-****" disabled>
                                            <div class="invalid-feedback">
                                                Please provide your Telephone No.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="facebookAccount" class="form-label">Facebook Link</label>
                                    <input type="text" class="form-control" id="facebookAccount" name="facebookAccount" placeholder="https://www.facebook.com/Juan" disabled>
                                    <div class="invalid-feedback">
                                        Please provide your Facebook Link.
                                    </div>
                                </div>
                            </div>

                            <hr style="height:1px;">
                            
                            <p class="fw-medium fs-5" style="color: #090909;">Address</p>

                            <div class="row">
                                <div class="col-md-3">
                                    <label for="Region" class="form-label">Region</label>
                                    <select class="form-select mb-2" id="Region" name="Region" aria-label="Default select example" required disabled>
                                        <option value="" selected>Select</option>
                                    
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select region.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="Province" class="form-label">Provinces</label>
                                    <select class="form-select" id="Province" name="Province" aria-label="Default select example" required disabled>
                                        <option value="" selected>Select</option>
                                    
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select region.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="CityTown" class="form-label">CityTown</label>
                                    <select class="form-select" id="CityTown" name="CityTown" aria-label="Default select example" required disabled>
                                        <option value="" selected>Select</option>
                                        
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select CityTown.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="Barangay" class="form-label">Barangay</label>
                                    <select class="form-select" id="Barangay" name="Barangay" aria-label="Default select example" required disabled>
                                        <option value="" selected>Select</option>
                                        
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select barangay.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="street" class="form-label mt-2">Street/House No./ Zone</label>
                                    <select class="form-select" id="street" name="street" disabled>
                                        <option value="" selected>Select</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select street or enter manually.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <button id="updateButton" disabled class="btn btn-primary mx-2 float-end" style="display: none;" type="button" form="customerInfo" ><i class="fa-solid fa-upload"></i>Update</button>

                                    <button id="submitButton" name = "submitButton" disabled class="btn btn-primary mx-2 float-end" type="button" form="customerInfo"><i class="fa-solid fa-check-circle"></i> Submit</button>
                                    
                                    <button class="btn btn-danger float-end" type="button" id="cancel"  disabled hidden onclick="Cancel();"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../js/maintenance.js?<?= time() ?>"></script>
        <script src="../../js/profiling/supplierinfo.js?<?= time() ?>"></script>

        <script>
            // Function to format TIN with dashes after every 3 digits
            document.getElementById('tin').addEventListener('input', function(e) {
                let inputValue = e.target.value.replace(/\D/g, '').substring(0, 13);
                let formattedValue = '';
                for (let i = 0; i < inputValue.length; i++) {
                    if (i > 0 && i % 3 === 0 && i < 10) {
                        formattedValue += '-';
                    } else if (i === 9) {
                        formattedValue += '-';
                    }
                    formattedValue += inputValue[i];
                }
        
                e.target.value = formattedValue;
            });
        </script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
