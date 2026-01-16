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
        <style>
        .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
    </style>

            <div class="container mt-4">
                <div class="p-3 shadow rounded-2" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Data Configuration</p>
                </div>

                <div class="shadow rounded-2 mb-3" style="background-color: white;">
                    <div class="mt-3">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="beginning-balance-tab" data-bs-toggle="tab" data-bs-target="#beginning-balance-tab-pane" type="button" role="tab" aria-controls="beginning-balance-tab-pane" aria-selected="true">Beginning Balance Data</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sl-balance-tab" data-bs-toggle="tab" data-bs-target="#sl-balance-tab-pane" type="button" role="tab" aria-controls="sl-balance-tab-pane" aria-selected="false">SL Balance</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="year-end-balance-data-tab" data-bs-toggle="tab" data-bs-target="#year-end-balance-data-tab-pane" type="button" role="tab" aria-controls="year-end-balance-data-tab-pane" aria-selected="false">Year End Balance Data</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="budget-variance-data-tab" data-bs-toggle="tab" data-bs-target="#budget-variance-data-tab-pane" type="button" role="tab" aria-controls="budget-variance-data-tab-pane" aria-selected="false">Budget Variance Data</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="peso-data-tab" data-bs-toggle="tab" data-bs-target="#peso-data-tab-pane" type="button" role="tab" aria-controls="peso-data-tab-pane" aria-selected="false">PESO Data</button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="myTabContent">
                        <!-- BEGINNING BALANCE DATA TAB -->
                        <div class="tab-pane fade show active" id="beginning-balance-tab-pane" role="tabpanel" aria-labelledby="beginning-balance-tab" tabindex="0">
                            <div class="row">
                                <form action="" class="needs-validation" novalidate>
                                    <div class="p-3">
                                        <div class="mb-2">
                                            <label for="region" class="form-label mt-2 mx-2">Funding:</label>
                                            <select class="form-select" aria-label="Default select example" required>
                                                <option value="" selected>Select</option>
                                                <option value="">ACASH</option>
                                                <option value="">ISYN-ILIGAN</option>
                                                <option value="">ISYN-SANTIAGO</option>
                                                <option value="">ISYNERGIES</option>
                                            </select>
                                            <div class="invalid-feedback">Please choose funding</div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless bg-white">
                                                <thead>
                                                    <tr>
                                                        <th>Account No.</th>
                                                        <th>Account Title</th>
                                                        <th>Beginning Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>123456</td>
                                                        <td>Cash</td>
                                                        <td>1000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>234567</td>
                                                        <td>Accounts Receivable</td>
                                                        <td>2000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>345678</td>
                                                        <td>Inventory</td>
                                                        <td>1500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>456789</td>
                                                        <td>Accounts Payable</td>
                                                        <td>500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>567890</td>
                                                        <td>Sales Revenue</td>
                                                        <td>3000</td>
                                                    </tr>
                                                    <tr>
                                                        <td>678901</td>
                                                        <td>Service Revenue</td>
                                                        <td>2500</td>
                                                    </tr>
                                                    <tr>
                                                        <td>789012</td>
                                                        <td>Rent Expense</td>
                                                        <td>700</td>
                                                    </tr>
                                                    <tr>
                                                        <td>890123</td>
                                                        <td>Utilities Expense</td>
                                                        <td>600</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-end mt-3">
                                                <button class="text-white btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="p-3">
                                            <p class="fw-medium fs-5 my-2 mb-3">Edit Amount</p>
                                            <hr style="height: 1px">
                                            <div class="form-group row mb-2">
                                                <label for="accountNo" class="col-sm-2 col-form-label">Account No.</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="accountNo" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label for="accountTitle" class="col-sm-2 col-form-label">Account Title</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="accountTitle" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label for="beginningBalance" class="col-sm-2 col-form-label">Beginning Balance</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="beginningBalance" required>
                                                </div>
                                                <div class="col-md-5 text-end justify-content-end">
                                                    <button class="btn btn-primary px-3 py-2" type="button"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- SL BALANCE DATA TAB -->
                        <div class="tab-pane fade" id="sl-balance-tab-pane" role="tabpanel" aria-labelledby="sl-balance-tab" taBbindex="0">
                            <div class="row">
                                <form action="" novalidate class="needs-validation">
                                    <div class="p-3">
                                        <div class=" col-md-6 mb-2">
                                            <label for="region" class="form-label mt-2 mx-2">Funding: </label>
                                            <select class="form-select" aria-label="Default select example" required>
                                                <option value="" selected>Select</option>
                                                <option value="">ACASH</option>
                                                <option value="">ISYN-ILIGAN</option>
                                                <option value="">ISYN-SANTIAGO</option>
                                                <option value="">ISYNERGIES</option>
                                            </select>
                                            <div class="invalid-feedback"> Please choose funding</div>
                                        </div>

                                        <div class=" col-md-6 mb-2">
                                            <label for="region" class="form-label mt-2 mx-2">Account Code </label>
                                            <select class="form-select" aria-label="Default select example" required>
                                                <option value="" selected>Select</option>
                                                <option value="">...</option>
                                                <option value="">...</option>
                                                <option>
                                                <option value="">...</option>
                                                <option value="">...</option>
                                            </select>
                                            <div class="invalid-feedback"> Please choose account code</div>
                                        </div>

                                        <div>
                                            <table class="table table-borderless" style="background-color: white;">
                                                <thead>
                                                    <tr>
                                                        <th class="fw-bold fs-6" style="color:#090909">
                                                            SL No.
                                                        </th>
                                                        <th class="fw-bold fs-6" style="color:#090909">
                                                            SL Name
                                                        </th>
                                                        <th class="fw-bold fs-6" style="color:#090909">
                                                            Beginning Balance
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="row">
                                                <div class="col-12 text-end mt-3">
                                                    <button class="btn btn-primary px-3 py-2" type="submit"> <i class="fa-solid fa-floppy-disk"></i> Save </button>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        
                                        <div class="col-md-12">
                                            <p class="fw-medium fs-5 my-2">Edit Amount</p>
                                            <hr style="height: 1px">
                                            <div class="form-group row mb-2">
                                                <label for="" class="col-sm-2 col-form-label">SL NO.</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="" required>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-2">
                                                <label for="" class="col-sm-2 col-form-label">SL Name</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="" required>
                                                </div>
                                                <div class="col-md-5 text-end justify-content-end">
                                                    <button class="btn btn-primary px-3 py-2" type="button"><i class="fa-solid fa-pen-to-square"></i>
                                                        Edit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- YEAR END BALANCE DATA TAB -->
                        <div class="tab-pane fade" id="year-end-balance-data-tab-pane" role="tabpanel" aria-labelledby="year-end-balance-data-tab" tabindex="0">
                            <div class="row">
                                <form action="" novalidate class="needs-validation">
                                    <div class="p-3">
                                        <div class="row">
                                            <div class=" col-md-6 mb-2">
                                                <label for="region" class="form-label mt-2 mx-2">Month: </label>
                                                <input type="date" class="form-control" required>
                                                <div class="invalid-feedback"> Please select month</div>
                                            </div>
                                            <div class=" col-md-6 mb-2">
                                                <label for="region" class="form-label mt-2 mx-2">Funding: </label>
                                                <select name="" class="form-select" id="" required>
                                                    <option value="" selected>Select</option>
                                                    <option value="">...</option>
                                                    <option value="">...</option>
                                                </select>
                                                <div class="invalid-feedback"> Please select funding</div>
                                            </div>
                                        </div>

                                        <table class="table table-borderless " style="background-color: white;">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Account No.
                                                    </th>
                                                    <th>
                                                        Account Title
                                                    </th>
                                                    <th>
                                                        Beginning Balance
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="col-md-12 mt-4 mb-5">
                                            <p class="fw-medium fs-5 my-2">Edit Amount</p>
                                            <hr style="height: 1px">
                                            <div class="form-group row mb-2">
                                                <label for="date" class="col-sm-2 col-form-label"> Account No.</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="date" required>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-2">
                                                <label for="date" class="col-sm-2 col-form-label"> Account Title</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="date" required>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-2">
                                                <label for="date" class="col-sm-2 col-form-label"> Beginning Balance</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="" required>
                                                </div>
                                                <div class="col-md-5 text-end justify-content-end">
                                                    <button class="text-white btn btn-primary px-3 py-2" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- BUDGET VARIANCE DATA TAB -->
                    <div class="tab-pane fade" id="budget-variance-data-tab-pane" role="tabpanel" aria-labelledby="budget-variance-data-tab" tabindex="0">
                        <div class="row">
                            <form action="" novalidate class="needs-validation">
                                <div class="p-3">
                                    <div class=" col-md-6 mb-2">
                                        <label for="region" class="form-label mt-2 mx-2">For the Month: </label>
                                        <select name="" class="form-select" id="" required>
                                            <option value="" selected>Select</option>
                                            <option value="">...</option>
                                            <option value="">...</option>
                                            <option value="">...</option>
                                        </select>
                                        <div class="invalid-feedback"> Please select month</div>
                                    </div>


                                    <table class="table table-hover  table-borderless " style="background-color: white;">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Account No.
                                                </th>
                                                <th>
                                                    Account Title
                                                </th>
                                                <th>
                                                    Month
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- <div class="row">
                                                <div class="col-12 text-end mt-3">
                                                    <button class="btn btn-primary" type="submit"> <i class="fa-solid fa-floppy-disk"></i> Save</button>
                                                </div>
                                            </div> -->

                                    <div class="col-md-12 mt-4">
                                        <p class="fw-medium fs-5 my-2">Edit Amount</p>
                                        <hr style="height: 1px">
                                        <div class="form-group row mb-2">
                                            <label for="date" class="col-sm-2 col-form-label"> Account No.</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" id="date" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-2">
                                            <label for="date" class="col-sm-2 col-form-label"> Account Title</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" id="date" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-2">
                                            <label for="date" class="col-sm-2 col-form-label"> Beginning Balance</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" id="" required>
                                            </div>
                                            <div class="col-md-5 text-end justify-content-end">
                                                <button class="text-white btn btn-primary px-3 py-2" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                                <button class="btn btn-primary px-3 py-2" type="submit"><i class="fa-solid fa-calculator"></i>
                                                    Compute</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- PESO DATA TAB -->
                    <div class="tab-pane fade" id="peso-data-tab-pane" role="tabpanel" aria-labelledby="peso-data-tab" tabindex="0">
                        <div class="container mt-4">
                            <table class="table" style="background-color: white;">
                                <tbody>
                                    <tr>
                                        <td>
                                            Number of Account Officers - AKP
                                        </td>
                                        <td>0</td>

                                    </tr>
                                    <tr>
                                        <td>
                                            Number of Account Officers - ILP
                                        </td>
                                        <td>0</td>

                                    </tr>
                                    <tr>
                                        <td>
                                            Inflation Rate
                                        </td>
                                        <td>0</td>

                                    </tr>
                                    <tr>
                                        <td>GNP Capital</td>
                                        <td>0</td>

                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <div class="row mb-4">
                                    <hr style="height:1px">
                                    <div class="col-md-6">
                                        <label for="" class="form-label">Item</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="form-label">Ammount</label>
                                        <input type="text" class="form-control" name="" id="">
                                    </div>
                                    <div class="col-md-5 mt-2">
                                        <button class="btn btn-primary" type="submit"> <i class="fa-solid fa-floppy-disk"></i> Save</button>
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

        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/outgoinginventory.js?<?= time() ?>"></script>

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

<script>
    const triggerTabList = document.querySelectorAll('#myTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)

        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>
