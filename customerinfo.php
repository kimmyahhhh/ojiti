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
            td {
                font-weight: 400;
            }

            form {
                width: 100%;
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

            .hidden_data {
                display: none;
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
                <div class="shadow rounded-3 p-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2 ">Customer Information</p>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="shadow p-3 rounded-3" style="background-color: white; overflow: auto;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Customer List</p>
                            </div>
                            <hr style="height: 1px">
                            <table id="CustomerInfoTbl" class="table table-bordered text-center" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:20%;text-align:center">Customer No.</th>
                                        <th style="width:20%;text-align:center">Name</th>
                                        <th style="width:20%;text-align:center">Customer Type</th>
                                        <th style="width:20%;text-align:center">Mobile Number</th>
                                        <th style="width:20%;text-align:center">Email</th>
                                    </tr>
                                </thead>
                                <tbody id="CustomerInfoList">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="row mt-4">
                    <div class="col-md-12">
                        <form class="p-3 needs-validation shadow mb-3" novalidate method="post" id="customerinfo" autocomplete="off">
                            
                            <div class=" align-items-center justify-content-between mb-4">
                                <button class="btn btn-primary float-end mx-2" id="editButton" name="editButton" class="btn btn-primary float-end" type="button" disabled><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                <button class="btn btn-success float-end mx-2" id="addNew" type="button" name="addNew"> <i class="fa-solid fa-plus"></i> New</button>
                                <p class="fw-medium fs-5" style="color: #090909;">Customer Information</p>
                            </div>
                            <hr style="height: 1px">
                            <input type="hidden" id="customerID" name="customerID" value="">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="customerType" class="form-label">Customer Type</label>
                                    <select class="form-select" name="customerType" id="customerType" required disabled>
                                        <option value="" selected disabled>Select</option>
                                        <option value="Individual">Individual</option>
                                        <option value="Corporate">Corporate</option>
                                        <option value="Government">Government</option>
                                        <option value="SME">SME (Small/Medium Enterprise)</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Wholesale">Wholesale</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="customerNo" class="form-label">Customer No</label>
                                    <input type="text" name="customerNo" class="form-control" id="customerNo" placeholder="Customer No" disabled value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="firstName" class="form-label">First name</label>
                                    <input type="text" name="firstName" class="form-control" id="firstName" placeholder="First Name" required disabled>
                                    <div class="invalid-feedback">Please enter a valid first name</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="middleName" class="form-label">Middle name</label>
                                    <input type="text" name="middleName" class="form-control" id="middleName" placeholder="Middle Name" disabled>
                                    <div class="invalid-feedback">Please enter a valid middle name</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="lastName" class="form-label">Last name</label>
                                    <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Last Name" required disabled>
                                    <div class="invalid-feedback">Please enter a valid last name</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <label for="birthdate" class="form-label">Birthdate</label>
                                    <input type="date" name="birthdate" class="form-control" id="birthdate" max="<?php echo date('Y-m-d'); ?>" disabled>
                                    <div class="invalid-feedback">Please enter a valid birthdate (cannot be a future date)</div>
                                </div>
                                <div class="col-md-2">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="text" name="age" class="form-control" id="age" disabled>
                                    <div class="invalid-feedback">Please enter a valid age</div>
                                </div>
                                <div class="col-md-2">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select mb-2" id="gender" name="gender" aria-label="Default select example"  disabled>
                                    <option value="" selected>Select</option>
                                    </select>
                                    <div class="invalid-feedback">Please select gender</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="mobileNumber" class="form-label">Mobile Number</label>
                                    <input type="text" name="mobileNumber" class="form-control" id="mobileNumber" placeholder="09*********"  disabled maxlength="11">
                                    <div class="invalid-feedback">Please enter a valid mobile number</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <input type="text" name="companyName" id="companyName" class="form-control" placeholder="Company Name" disabled>
                                    <div class="invalid-feedback">Please enter a valid company name</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="exmpl@gmail.com"  disabled>
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                                <div class="col-md-4 overflow-auto">
                                    <label for="tin" class="form-label">TIN</label>
                                    <div class="input-group">
                                        <input type="text" id="tin" name="tin" class="form-control tin-field" maxlength="16" placeholder="###-###-###-###"  disabled>
                                    </div>
                                </div>
                            </div>

                            <hr style="height: 1px;">
                            <p class="fw-medium heading fs-5 ">Address</p>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="Region" class="form-label">Region</label>
                                    <select class="form-select mb-2" id="Region" name="Region" aria-label="Default select example"  disabled>
                                        <option value="" selected>Select</option>
                                    
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="Province" class="form-label">Province</label>
                                    <select class="form-select" id="Province" name="Province" aria-label="Default select example"  disabled>
                                        <option value="" selected>Select</option>
                                    
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="CityTown" class="form-label">City/Town</label>
                                    <select class="form-select" id="CityTown" name="CityTown" aria-label="Default select example"  disabled>
                                        <option value="" selected>Select</option>
                                        
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="Barangay" class="form-label">Barangay</label>
                                    <select class="form-select" id="Barangay" name="Barangay" aria-label="Default select example"  disabled>
                                        <option value="" selected>Select</option>
                                        
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="street" class="form-label mt-2">Street/House No./ Zone</label>
                                        <input type="text" class="form-control" id="street" name="street" placeholder="Street/House No./Zone" disabled>
                                        <div class="invalid-feedback">
                                            Please enter street.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px;">
                            <p class="fw-medium heading fs-5 ">Product Information</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="productInfo" class="form-label mt-2">Product/Services Availed</label>
                                    <input type="text" class="form-control" id="productInfo" name="productInfo" placeholder="Product/Service" disabled>
                                    <div class="invalid-feedback">
                                        Please enter product/service.
                                    </div>

                                    <!-- replace with dropdown-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <button id="updateButton" class="btn btn-primary mx-2 float-end" style="display: none;" type="button" disabled><i class="fa-solid fa-upload"></i>Update</button>

                                    <button id="submitButton" class="btn btn-primary mx-2 float-end" type="button" disabled><i class="fa-solid fa-check-circle"></i> Submit</button>
                                    
                                    <button class="btn btn-danger float-end" type="button" id="cancel" hidden onclick="Cancel();"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
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
        <script src="../../js/profiling/customerinfo.js?<?= time() ?>"></script>

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

            // Set max date for birthdate to today
            document.addEventListener('DOMContentLoaded', function() {
                const birthdateInput = document.getElementById('birthdate');
                const today = new Date().toISOString().split('T')[0];
                birthdateInput.setAttribute('max', today);

                // Calculate age when birthdate changes
                birthdateInput.addEventListener('change', function() {
                    calculateAge();
                });
            });

            // Function to calculate age from birthdate
            function calculateAge() {
                const birthdateInput = document.getElementById('birthdate');
                const ageInput = document.getElementById('age');
                
                if (birthdateInput.value) {
                    const birthDate = new Date(birthdateInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();
                    
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    
                    ageInput.value = age;
                } else {
                    ageInput.value = '';
                }
            }

            // Customer No - only numbers
            document.getElementById('customerNo').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });

            // First name - only letters and spaces
            document.getElementById('firstName').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            });

            // Middle name - only letters and spaces
            document.getElementById('middleName').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            });

            // Last name - only letters and spaces
            document.getElementById('lastName').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            });

            // Mobile Number - only numbers, must start with 09, exactly 11 digits
            document.getElementById('mobileNumber').addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                
                // Must start with 09
                if (value.length > 0 && !value.startsWith('09')) {
                    value = '09' + value.replace(/^09/, '').substring(0, 9);
                }
                
                // Limit to 11 digits
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                
                e.target.value = value;
            });

            // Mobile Number validation on blur
            document.getElementById('mobileNumber').addEventListener('blur', function(e) {
                const value = e.target.value;
                if (value && (value.length !== 11 || !value.startsWith('09'))) {
                    e.target.setCustomValidity('Mobile number must start with 09 and be exactly 11 digits');
                    e.target.classList.add('is-invalid');
                } else {
                    e.target.setCustomValidity('');
                    e.target.classList.remove('is-invalid');
                }
            });

            // Company Name - only letters and spaces
            document.getElementById('companyName').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            });

            // Email - must contain @gmail.com
            document.getElementById('email').addEventListener('input', function(e) {
                const value = e.target.value;
                if (value && !value.includes('@gmail.com')) {
                    e.target.setCustomValidity('Email must contain @gmail.com');
                    e.target.classList.add('is-invalid');
                } else {
                    e.target.setCustomValidity('');
                    e.target.classList.remove('is-invalid');
                }
            });

            // Email validation on blur
            document.getElementById('email').addEventListener('blur', function(e) {
                const value = e.target.value;
                if (value && !value.includes('@gmail.com')) {
                    e.target.setCustomValidity('Email must contain @gmail.com');
                    e.target.classList.add('is-invalid');
                } else {
                    e.target.setCustomValidity('');
                    e.target.classList.remove('is-invalid');
                }
            });

            // Street/House No./Zone - letters and numbers, max 3 numbers, no special characters
            document.getElementById('street').addEventListener('input', function(e) {
                let value = e.target.value;
                // Remove special characters (keep only letters, numbers, and spaces)
                value = value.replace(/[^a-zA-Z0-9\s]/g, '');
                
                // Limit to maximum 3 numeric digits
                let numbersFound = 0;
                value = value.split('').filter(char => {
                    if (/\d/.test(char)) {
                        if (numbersFound < 3) {
                            numbersFound++;
                            return true;
                        }
                        return false; // Skip this number if we already have 3
                    }
                    return true; // Keep all letters and spaces
                }).join('');
                
                e.target.value = value;
            });
        </script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>