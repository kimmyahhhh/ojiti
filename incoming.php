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
                label {
                    color: #090909;
                }
                .form-control {
                    color: #090909 !important;
                    border:1px solid #000000 !important;
                }
                .form-select {
                    color: #090909 !important;
                    border:1px solid #000000 !important;
                }
                /* Selection box */
                .select2-container--default .select2-selection--single {
                    border: 1px solid #000000 !important;
                }

                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #090909 !important;
                }

                /* Dropdown */
                .select2-dropdown {
                    border: 1px solid #000000 !important;
                }

                .select2-results__option {
                    color: #090909 !important;
                }

                /* Optional: Highlighted option */
                .select2-results__option--highlighted {
                    background-color: #e0e0e0 !important;
                    color: #090909 !important;
                }
                form {
                    width: 100%;
                    padding: 20px;
                    background-color: white;
                    border-radius: 10px;
                }
                th {
                    font-weight: bold;
                    color: #090909;
                    position: sticky;
                    top: 0;
                }
            </style>

            <div class="container-fluid mt-1">
                <div class="shadow p-3 rounded-3" style="background-color: white;">
                    <p class="fs-5 my-2" style="color: blue; font-weight: bold;">Incoming</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 mt-2">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="fw-medium fs-5" style="color: #090909;">Particulars</p>
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-danger px-3 py-2 mx-1" type="button" id="cancel" onclick="Cancel()" disabled hidden> Cancel</button>

                                        <button id="addToList" class="btn btn-primary px-3 py-2 mx-1" type="button" disabled hidden><i class="fa-solid fa-plus"></i> Add to List </button>
                                        
                                        <button id="save" class="btn btn-success px-3 py-2 mx-1" type="button" onclick="Save();" disabled hidden><i class="fa-solid fa-floppy-disk"></i> Save </button>
                                        
                                        <button type="button" id="addNew" class="btn btn-success px-3 py-2 mx-1">
                                            <i class="fa-solid fa-plus"></i> New
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <form id="inventoryinForm" method="POST" enctype="multipart/form-data">
                                <div class="mb-2 row">
                                    <label for="branch" class="col-sm-4 col-form-label">Isyn Branch: </label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="branch" name="branch" aria-label="Default select example" required disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="type" class="col-sm-4 col-form-label">Product Type: </label>
                                    <div class="col-sm-8">
                                        <select class="form-select" aria-label="Type" required name="type" id="type" onchange="LoadProdCateg(this.value);" disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="category" class="col-sm-4 col-form-label">Product Category: </label>
                                    <div class="col-sm-8">
                                        <select class="form-select" aria-label="Category" required name="category" id="category" onchange="LoadProdName(this.value)" disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="product" class="col-sm-4 col-form-label">Product Name: </label>
                                    <div class="col-sm-8 mt-2">
                                        <select class="form-select" aria-label="product" required name="product" id="product" onchange="LoadSupplier(this.value)" disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="supplier" class="col-sm-4 col-form-label">Supplier: </label>
                                    <div class="col-sm-8 mt-2">
                                        <select class="form-select" aria-label="supplier" required name="supplier" id="supplier" disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="suppliersSI" class="col-sm-4 col-form-label">Supplier(s) SI: </label>
                                    <div class="col-md-8">
                                        <select class="form-select" id="suppliersSI" required name="suppliersSI" disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="serialNo" class="col-sm-4 col-form-label">Serial Number: </label>
                                    <div class="col-md-8">
                                        <select class="form-select" id="serialNo" required name="serialNo" disabled>
                                            <option value="" selected disabled>Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="purchaseDate" class="col-sm-4 col-form-label">Purchase Date: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="purchaseDate" name="purchaseDate" placeholder="" required disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="warranty" class="col-sm-4 col-form-label">Warranty: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="warranty" name="warranty" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="imageName" class="col-sm-4 col-form-label">Image Name: </label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" id="imageName" name="imageName" accept=".jpg,.jpeg,.png" disabled>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="dateEncoded" class="col-sm-4 col-form-label">Date Encoded: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="dateEncoded" name="dateEncoded" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="dealersPrice" class="col-sm-4 col-form-label">Dealer(s) Price: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="dealersPrice" name="dealersPrice" placeholder="0.00" onchange="formatInput(this);Compute()" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="srp" class="col-sm-4 col-form-label">SRP: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="srp" name="srp" placeholder="0.00" onchange="formatInput(this);Compute()" disabled>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="quantity" class="col-sm-4 col-form-label">Quantity: </label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="0" onchange="Compute()" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="totalPrice" class="col-sm-4 col-form-label">Total Price: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="totalPrice" name="totalPrice" placeholder="0.00" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="totalSRP" class="col-sm-4 col-form-label">Total SRP: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="totalSRP" name="totalSRP" placeholder="0.00" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="mpi" class="col-sm-4 col-form-label">MPI: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="mpi" name="mpi" placeholder="0.00" disabled>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="totalmarkup" class="col-sm-4 col-form-label">Total Markup: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="totalmarkup" name="totalmarkup" placeholder="0.00" disabled>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!--Data Inventory-->
                    <div class="col-lg-8 mt-2">
                        <div class="shadow p-3 rounded-3 mb-2" style="background-color: white;overflow:auto;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="fw-medium fs-5" style="color: #090909;">Data Inventory</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-info px-3 py-2 mx-1" id="printBtn" name="printBtn" type="button" onclick="PrintSupplierSalesInvoice();" disabled><i class="fa-solid fa-print"></i> Print</button>
                                        <button class="btn btn-danger px-3 py-2 mx-1" name="DeleteFromDataInvBtn" id="DeleteFromDataInvBtn" type="button" onclick="DeleteFromDataInv()" disabled><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <div class="">
                                <table id="dataInvTbl" style="width:100%;" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SI No.</th>
                                            <th>Serial No.</th>
                                            <th>Product Name</th>
                                            <th>Supplier</th>
                                            <th>Purchase Date</th>
                                            <th>Quantity</th>
                                            <th>Dealer(s) Price</th>
                                            <th>Total Price</th>
                                            <th>Vatable Sales</th>
                                            <th>VAT</th>
                                            <th>Amount Due</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataInvList">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="shadow p-3 rounded-3 mb-2" style="background-color: white;overflow:auto;">
                            <div class="row">
                                <div class="align-items-center justify-content-between">
                                    <button class="btn btn-danger px-3 py-2 mx-1 float-end" name="DeleteFromListBtn" id="DeleteFromListBtn" type="button" onclick="DeleteFromList()" disabled><i class="fa-solid fa-trash"></i></button>
                                    <p class="fw-medium fs-5" style="color: #090909;">Items</p>
                                </div>
                            </div>
                            <hr style="height: 1px">
                            <div class="">
                                <table id="itemTbl" class="table table-bordered" style="width:100%;">
                                    <thead>
                                        <th>Product Name</th>
                                        <th>Serial No.</th>
                                        <th>Warranty</th>
                                        <th>Dealer(s) Price</th>
                                        <th>SRP</th>
                                        <th>Qty</th>
                                        <th>Total Price</th>
                                        <th>Total SRP</th>
                                        <th>MPI</th>
                                        <th>Total Markup</th>
                                        <th>Branch</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Supplier</th>
                                        <th>Supplier(s) SI</th>
                                        <th>Purchase Date</th>
                                        <th>Image Name</th>
                                        <th>Date Encoded</th>

                                        <!-- <th>0</th>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                        <th>9</th>
                                        <th>10</th>
                                        <th>11</th>
                                        <th>12</th>
                                        <th>13</th>
                                        <th>14</th>
                                        <th>15</th>
                                        <th>16</th>
                                        <th>17</th> -->
                                    </thead>
                                    <tbody id="itemList">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
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
        <script src="../../js/inventorymanagement/incominginventory.js?<?= time() ?>"></script>

        <script>
            $(document).ready(function() {
                // Purchase Date - no future dates
                setTimeout(function() {
                    $('#purchaseDate').datetimepicker({
                        format: 'Y-m-d',
                        timepicker: false,
                        maxDate: 0, // 0 means today, prevents future dates
                        scrollInput: false
                    });
                }, 100);

                // Date Encoded - set to today's date automatically when product is selected
                // This will be handled when product is selected from transmittalreceipt data
                function setDateEncoded() {
                    var today = new Date();
                    var year = today.getFullYear();
                    var month = String(today.getMonth() + 1).padStart(2, '0');
                    var day = String(today.getDate()).padStart(2, '0');
                    var dateString = year + '-' + month + '-' + day;
                    $('#dateEncoded').val(dateString);
                }

                // Dealer(s) Price - only accept numbers and decimal point
                $('#dealersPrice').on('input', function() {
                    var value = $(this).val();
                    // Remove all characters except numbers and period
                    value = value.replace(/[^0-9.]/g, '');
                    
                    // Ensure only one decimal point
                    var parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    
                    // Limit decimal places to 2
                    if (parts.length === 2 && parts[1].length > 2) {
                        value = parts[0] + '.' + parts[1].substring(0, 2);
                    }
                    
                    $(this).val(value);
                });

                // SRP - only accept numbers and decimal point
                $('#srp').on('input', function() {
                    var value = $(this).val();
                    // Remove all characters except numbers and period
                    value = value.replace(/[^0-9.]/g, '');
                    
                    // Ensure only one decimal point
                    var parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    
                    // Limit decimal places to 2
                    if (parts.length === 2 && parts[1].length > 2) {
                        value = parts[0] + '.' + parts[1].substring(0, 2);
                    }
                    
                    $(this).val(value);
                });

                // When product is selected, auto-fill warranty and date encoded
                // This should be called from the LoadSupplier or similar function
                // when data is loaded from transmittalreceipt
                window.loadProductDataFromTransmittal = function(productData) {
                    if (productData) {
                        // Auto-fill warranty if available
                        if (productData.warranty) {
                            $('#warranty').val(productData.warranty);
                        }
                        
                        // Set date encoded to today
                        setDateEncoded();
                    }
                };
            });
        </script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>