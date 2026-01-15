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
                position: sticky;
                top: 0;
                font-weight: bold;
                color: #090909;
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
                <div class=" p-3 shadow rounded-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="my-2 fs-5">iSynergies Employee</p>
                </div>
                <div class="row mt-4 mb-3">
                    <div class="col-md-12">
                        <div class=" shadow p-3 rounded-3  " style="background-color: white; overflow:auto;">
                            <p class="fw-medium fs-5" style="color: #090909;">Staff List</p>
                            <hr style="height: 1px">
                            <div class="col-md-4 d-flex mb-3" role="search">
                                
                            </div>
                            <table id="staffTbl" class="table table-bordered text-center" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:25%;text-align:center">Employee No.</th>
                                        <th style="width:25%;text-align:center">Employee Name</th>
                                        <th style="width:25%;text-align:center">Employee Status</th>
                                        <th style="width:25%;text-align:center">Designation</th>
                                    </tr>
                                </thead>
                                <tbody id="staffList">
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            
                <div class="row mt-4">
                    <div class="col-md-12">
                        <form class="p-3 needs-validation shadow mb-3" id="staffForm" novalidate method="POST">
                            <input type="hidden" name="id_staff" id="id_staff">
                            <div class=" align-items-center justify-content-between mb-4">
                                <button class="btn btn-primary float-end mx-2" id="editButton" class="btn btn-primary float-end" type="button" disabled><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                <button class="btn btn-success float-end mx-2" id="addNew" type="button" name="addNew" > <i class="fa-solid fa-plus"></i> New</button>
                                <p class="fw-medium fs-5" style="color: #090909;">Employee Information</p>
                            </div>
                            <hr style="height:1px;">
                            <input type="hidden" id="editMode" name="editMode" value="">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="employee_no" class="form-label">Employee No.</label>
                                    <input disabled type="text" class="form-control text-uppercase" name="employee_no" id="employee_no" placeholder="Employee No." required>
                                </div>
                                <div class="col-md-4">
                                    <label for="employee_status" class="form-label">Employee Status</label>
                                    <select class="form-select" name="employee_status" id="employee_status" aria-label="Default select example">
                                        <option value="" selected>Select Employee Status</option>
                                        <option value="REGULAR">REGULAR</option>
                                        <option value="PROBATIONARY">PROBATIONARY</option>
                                        <option value="CONTRACTUAL">CONTRACTUAL</option>
                                        <option value="ON LEAVE">ON LEAVE</option>
                                        <option value="RESIGNED">RESIGNED</option>
                                        <option value="TERMINATED">TERMINATED</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="date_hired" class="form-label">Date Hired</label>
                                    <input disabled type="date" class="form-control" name="date_hired" id="date_hired" required>
                                    <div class="invalid-feedback">
                                        Invalid Date
                                    </div>
                                </div>
                            </div>
            
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input disabled type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" oninput="this.value = this.value.toUpperCase();" required>
                                    <div class="invalid-feedback">
                                        Please provide your first name.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input disabled type="text" class="form-control" name="middle_name" id="middle_name" placeholder="Middle Name" oninput="this.value = this.value.toUpperCase();">
                                    <div class="invalid-feedback">
                                        Please provide your Middle name.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input disabled type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" oninput="this.value = this.value.toUpperCase();" required>
                                    <div class="invalid-feedback">
                                        Please provide your last name.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="birthdate" class="form-label">Birthdate</label>
                                    <input disabled type="date" class="form-control" name="birthdate" id="birthdate" >
                                    <div class="invalid-feedback">
                                        Invalid Date
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" id="age" disabled>
                                    <div class="invalid-feedback">
                                        Enter your Age.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="designation" class="form-label">Designation</label>
                                    <select disabled class="form-select" name="designation" id="designation" aria-label="Default select example" >
                                        <option selected >Select Designation</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="email_address" class="form-label">Email Address</label>
                                    <input disabled type="email" class="form-control" name="email_address" id="email_address" placeholder="https://www.facebook.com/juan" >
                                    <div class="invalid-feedback">
                                        Please provide your Email Address.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="contact_num" class="form-label">Contact No.</label>
                                    <input disabled type="text" class="form-control" name="contact_num" id="contact_num" placeholder="09*********">
                                    <div class="invalid-feedback">
                                        Please provide your Contact.
                                    </div>
                                </div>
                                <div class="col-md-4 overflow-auto">
                                    <label for="pagibig" class="form-label">Pag-ibig Number</label>
                                    <div class="input-group">
                                        <input type="text" id="pag_ibig" name="pag_ibig" class="form-control tin-field" maxlength="14" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 overflow-auto">
                                    <label for="tin" class="form-label">TIN</label>
                                    <div class="input-group">
                                        <input type="text" id="tin" name="tin" class="form-control tin-field" maxlength="16" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 overflow-auto">
                                    <label for="philhealth" class="form-label">PhilHealth</label>
                                    <div class="input-group">
                                        <input type="text" id="philhealth" name="philhealth" class="form-control philhealth-field" maxlength="15" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 overflow-auto">
                                    <label for="sss" class="form-label">SSS</label>
                                    <div class="input-group">
                                        <input type="text" id="sss" name="sss" class="form-control sss1-field" maxlength="15" disabled>
                                    </div>
                                </div>
                            </div>
                            <hr style="height:1px;">
                            <p class=" fs-5" style="color: #090909;">Employee Address</p>
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="Street" class="form-label">Street/House No./ Zone</label>
                                    <input type="text" class="form-control" id="Street" name="Street" placeholder="Street/House No./Zone" oninput="this.value = this.value.toUpperCase();" disabled>
                                    <div class="invalid-feedback">
                                        Please enter street.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 float-end mt-3">
                                    <button id="updateButton" disabled class="btn btn-primary mx-2 float-end" style="display: none;" type="button" name="updateButton" form="staffForm"><i class="fa-solid fa-rotate-right"></i> Update</button>
                                    
                                    <button id="submitButton" disabled class="btn btn-primary mx-2 float-end" type="button" name="submitButton" form="staffForm"> <i class="fa-solid fa-check-circle"></i> Submit</button>
                                
                                    <button class="btn btn-danger float-end" type="button" id="cancel" disabled hidden onclick="Cancel();"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../js/maintenance.js"></script>
        <script src="../../js/profiling/isynstaff.js"></script>

        <script>
            // Function to format pagibig
            document.getElementById('pag_ibig').addEventListener('input', function(e) {
                let inputValue = e.target.value.replace(/\D/g, '').substring(0, 12);
                let formattedValue = '';
                for (let i = 0; i < inputValue.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += '-';
                    }
                    formattedValue += inputValue[i];
                }
                e.target.value = formattedValue;
            });
        
            // Function to format TIN
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
        
            // Function to format philhealth
            document.getElementById('philhealth').addEventListener('input', function(e) {
                let inputValue = e.target.value.replace(/\D/g, '').substring(0, 12);
                let formattedValue = '';
                for (let i = 0; i < inputValue.length; i++) {
                    if (i === 2 || i === 11) {
                        formattedValue += '-';
                    }
                    formattedValue += inputValue[i];
                }
                e.target.value = formattedValue;
            });
        
            // Function to format sss
            document.getElementById('sss').addEventListener('input', function(e) {
                let inputValue = e.target.value.replace(/\D/g, '').substring(0, 12);
                let formattedValue = '';
                for (let i = 0; i < inputValue.length; i++) {
                    if (i === 2 || i === 11) {
                        formattedValue += '-';
                    }
                    formattedValue += inputValue[i];
                }
                e.target.value = formattedValue;
            });

            document.getElementById('contact_num').addEventListener('input', function(e) {
                let inputValue = e.target.value.replace(/\D/g, '').substring(0, 11); // Remove non-numeric characters and limit to 11 digits
                e.target.value = inputValue;
            });

            document.getElementById("birthdate").addEventListener("change", function() {
                var birthdate = new Date(this.value);
                var today = new Date();
                var age = today.getFullYear() - birthdate.getFullYear();
                var m = today.getMonth() - birthdate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
                    age--;
                }
                document.getElementById("age").value = age;
            });
        
            document.getElementById("contact_num").addEventListener("input", function(event) {
                // Get the current value of the input field
                let currentValue = event.target.value;
                // Remove any non-numeric characters from the value
                let numericValue = currentValue.replace(/\D/g, '');
                // Update the input field with the numeric value
                event.target.value = numericValue;
            });
        </script>
        
    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>
