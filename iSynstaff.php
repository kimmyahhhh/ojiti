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
                                    <select class="form-select" name="employee_status" id="employee_status" aria-label="Default select example"  disabled>
                                        <option value="" selected>Select Employee Status</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Probationary">Probationary</option>
                                        <option value="Contractual">Contractual</option>
                                        <option value="Part-Time">Part-Time</option>
                                        <option value="Project-Based">Project-Based</option>
                                        <option value="Intern">Intern</option>
                                        <option value="Resigned">Resigned</option>
                                        <option value="Terminated">Terminated</option>
                                        <option value="Retired">Retired</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="date_hired" class="form-label">Date Hired</label>
                                    <input disabled type="date" class="form-control" name="date_hired" id="date_hired" max="<?php echo date('Y-m-d'); ?>" required>
                                    <div class="invalid-feedback">
                                        Invalid Date (cannot be a future date)
                                    </div>
                                </div>
                            </div>
            
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input disabled type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" required>
                                    <div class="invalid-feedback">
                                        Please provide your first name.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input disabled type="text" class="form-control" name="middle_name" id="middle_name" placeholder="Middle Name">
                                    <div class="invalid-feedback">
                                        Please provide your Middle name.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input disabled type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" required>
                                    <div class="invalid-feedback">
                                        Please provide your last name.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="birthdate" class="form-label">Birthdate</label>
                                    <input disabled type="date" class="form-control" name="birthdate" id="birthdate" max="<?php echo date('Y-m-d'); ?>">
                                    <div class="invalid-feedback">
                                        Invalid Date (cannot be a future date)
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" id="age" disabled readonly>
                                    <div class="invalid-feedback">
                                        Enter your Age.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="designation" class="form-label">Designation</label>
                                    <select disabled class="form-select" name="designation" id="designation" aria-label="Default select example" >
                                        <option value="" selected>Select Designation</option>
                                        <option value="Chief Executive Officer">Chief Executive Officer</option>
                                        <option value="Chief Operating Officer">Chief Operating Officer</option>
                                        <option value="Chief Financial Officer">Chief Financial Officer</option>
                                        <option value="Chief Technology Officer">Chief Technology Officer</option>
                                        <option value="General Manager">General Manager</option>
                                        <option value="Operations Manager">Operations Manager</option>
                                        <option value="HR Manager">HR Manager</option>
                                        <option value="Finance Manager">Finance Manager</option>
                                        <option value="IT Manager">IT Manager</option>
                                        <option value="Sales Manager">Sales Manager</option>
                                        <option value="Marketing Manager">Marketing Manager</option>
                                        <option value="Project Manager">Project Manager</option>
                                        <option value="Team Leader">Team Leader</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Senior Software Engineer">Senior Software Engineer</option>
                                        <option value="Software Engineer">Software Engineer</option>
                                        <option value="Junior Software Engineer">Junior Software Engineer</option>
                                        <option value="Web Developer">Web Developer</option>
                                        <option value="System Administrator">System Administrator</option>
                                        <option value="Network Administrator">Network Administrator</option>
                                        <option value="Database Administrator">Database Administrator</option>
                                        <option value="Business Analyst">Business Analyst</option>
                                        <option value="Data Analyst">Data Analyst</option>
                                        <option value="Quality Assurance Analyst">Quality Assurance Analyst</option>
                                        <option value="Accountant">Accountant</option>
                                        <option value="Bookkeeper">Bookkeeper</option>
                                        <option value="HR Specialist">HR Specialist</option>
                                        <option value="Recruiter">Recruiter</option>
                                        <option value="Sales Executive">Sales Executive</option>
                                        <option value="Marketing Specialist">Marketing Specialist</option>
                                        <option value="Customer Service Representative">Customer Service Representative</option>
                                        <option value="Technical Support">Technical Support</option>
                                        <option value="Administrative Assistant">Administrative Assistant</option>
                                        <option value="Executive Assistant">Executive Assistant</option>
                                        <option value="Secretary">Secretary</option>
                                        <option value="Receptionist">Receptionist</option>
                                        <option value="Office Clerk">Office Clerk</option>
                                        <option value="Intern">Intern</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="email_address" class="form-label">Email Address</label>
                                    <input disabled type="email" class="form-control" name="email_address" id="email_address" placeholder="exmpl@gmail.com" >
                                    <div class="invalid-feedback">
                                        Please provide a valid email address with @gmail.com
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
                                    <input type="text" class="form-control" id="Street" name="Street" placeholder="Street/House No./Zone" disabled>
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

            // Set max date for date_hired and birthdate to today
            document.addEventListener('DOMContentLoaded', function() {
                const dateHiredInput = document.getElementById('date_hired');
                const birthdateInput = document.getElementById('birthdate');
                const today = new Date().toISOString().split('T')[0];
                dateHiredInput.setAttribute('max', today);
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

            // Employee No - only numbers (no letters, no special characters)
            document.getElementById('employee_no').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });

            // First name - only letters and spaces (no numbers, no special characters)
            document.getElementById('first_name').addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
                e.target.value = value.toUpperCase();
            });

            // Middle name - only letters and spaces (no numbers, no special characters)
            document.getElementById('middle_name').addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
                e.target.value = value.toUpperCase();
            });

            // Last name - only letters and spaces (no numbers, no special characters)
            document.getElementById('last_name').addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
                e.target.value = value.toUpperCase();
            });

            // Contact No. - only numbers, must start with 09, exactly 11 digits
            document.getElementById('contact_num').addEventListener('input', function(e) {
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

            // Contact Number validation on blur
            document.getElementById('contact_num').addEventListener('blur', function(e) {
                const value = e.target.value;
                if (value && (value.length !== 11 || !value.startsWith('09'))) {
                    e.target.setCustomValidity('Contact number must start with 09 and be exactly 11 digits');
                    e.target.classList.add('is-invalid');
                } else {
                    e.target.setCustomValidity('');
                    e.target.classList.remove('is-invalid');
                }
            });

            // Email - must contain @gmail.com
            document.getElementById('email_address').addEventListener('input', function(e) {
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
            document.getElementById('email_address').addEventListener('blur', function(e) {
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
            document.getElementById('Street').addEventListener('input', function(e) {
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
                
                e.target.value = value.toUpperCase();
            });
        </script>
        
    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }

?>
