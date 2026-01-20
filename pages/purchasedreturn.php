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
            // // Fetch categories with VAT
            // $sql = "SELECT DISTINCT Category FROM tbl_invlist WHERE Type = 'With VAT' ORDER BY Category ASC";
            // $result = mysqli_query($connection, $sql);
            // $categoriesWithVAT = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $categoriesWithVAT[] = $row['Category'];
            //     }
            // }

            // // Fetch categories without VAT
            // $sql = "SELECT DISTINCT Category FROM tbl_invlist WHERE Type = 'Non-VAT' ORDER BY Category ASC";
            // $result = mysqli_query($connection, $sql);
            // $categoriesNonVAT = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $categoriesNonVAT[] = $row['Category'];
            //     }
            // }

            // //Fetch type based on Branch
            // $sql = "SELECT DISTINCT Type FROM tbl_invlist WHERE Branch = 'HEAD OFFICE'";
            // $result = mysqli_query($connection, $sql);
            // $typeHeadOffice = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $typeHeadOffice[] = $row['Type'];
            //     }
            // }

            // $sql = "SELECT DISTINCT Type FROM tbl_invlist WHERE Branch = 'ISYN-SANTIAGO'";
            // $result = mysqli_query($connection, $sql);
            // $typeIsynSantiago = [];
            // if (mysqli_num_rows($result) > 0) {
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         $typeIsynSantiago[] = $row['Type'];
            //     }
            // }


            // $sql = "SELECT max(TransactionNo) FROM tbl_purchasereturned ";
            // $result = mysqli_query($connection, $sql);
            // if (mysqli_num_rows($result) > 0) {
            //     $row = mysqli_fetch_assoc($result);
            //     $transactionNo = $row['max(TransactionNo)'];
            //     $transactionNo++;
            // }
        ?>

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
                height: auto;
            }

            th {
                font-weight: bold;
                color: #090909;
                position: sticky;
                top: 0;
                background-color: #f8f9fa;
            }
            .fw-medium.fs-5 { margin-left: 6px; }
            label.form-label { margin-left: 6px; }
            .content-wrapper { max-width: 96%; margin: 0 auto; }
            .row.form-row { gap: 12px; }
            .shadow.p-3 { padding: 12px !important; }
            .rounded-3 { border-radius: 8px !important; }
            .row > [class*='col-'] { padding-left: 8px; padding-right: 8px; }
            hr { margin: 8px 0 !important; }
            .container { max-width: 96%; margin: 0 auto; padding-left: 8px; padding-right: 8px; }
            form label { margin-left: 6px; }

            td,
            th {
                color: #090909;
                word-wrap: break-word;
                word-wrap: break-word;
                overflow-wrap: break-word;
                white-space: normal;
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

            .table {
                border-spacing: 0px;
                table-layout: auto;
                table-layout: fixed;
                width: 100%;
                margin-left: auto;
                margin-right: auto;
            }

            .table th,
            .table td {
                word-wrap: break-word;
                overflow-wrap: break-word;
                white-space: normal;
            }

            .table td {
                padding: 8px;
            }

            /* Style the modal */
            /*
            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgb(0,0,0);
                background-color: rgba(0,0,0,0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }*/
        </style>

            <div class="container mt-4">
                <div class="shadow rounded-3 p-3 mb-2" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Purchased Return</p>
                </div>

                <div class="row">
                    <!-- Search Product -->
                    <div class="col-md-4">
                        <div class="shadow p-3 rounded-3 mb-2" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-2">
                                <p class="fw-medium fs-5" style="color: #090909;">Search Product</p>
                                <hr style="height: 1px; margin: 8px 0;">
                            </div>
                            <form method="post" nonvalidate id="searchProduct">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-section mb-2">
                                            <label for="return-type" class="form-label">Return type:</label>
                                            <select id="return-type" name="return-type" class="form-select">
                                                <option value="" selected disabled>Select</option>
                                                <option value="Returned">Returned</option>
                                                <option value="Refund">Refund</option>
                                            </select>
                                        </div>
                                        <div class="form-section mb-2">
                                            <label for="isynBranch" class="form-label">ISYN branch:</label>
                                            <select id="isynBranch" name="isynBranch" class="form-select">
                                                <option value="" selected disabled>Select</option>
                                                <?php
                                                // $query = "SELECT DISTINCT Branch FROM tbl_invlist ORDER BY Branch";
                                                // $query_run = mysqli_query($connection, $query);
                                                // if (mysqli_num_rows($query_run) > 0) {
                                                //     while ($row = mysqli_fetch_assoc($query_run)) {
                                                ?>
                                                        <!-- <option value=" -->
                                                        <?php
                                                        //  echo $row['Branch'] 
                                                         ?>
                                                         <!-- "> -->
                                                         <?php
                                                        //   echo $row['Branch'] 
                                                          ?>
                                                          <!-- </option> -->
                                                <?php
                                                //     }
                                                // }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-section mb-2">
                                            <label for="type" class="form-label">Type:</label>
                                             <select class="form-select" name="type" id="type" onchange="LoadCategory(this.value);">
                                                <option value="" selected>Select</option>
                                                <option value="With VAT">With VAT</option>
                                                <option value="Non-VAT">Non-VAT</option>
                                            </select>
                                        </div>
                                        <div class="form-section mb-2">
                                            <label for="category" class="form-label">Category:</label>
                                            <select class="form-select" aria-label="Category" name="category" id="category" onchange="LoadSerialProduct(this.value);">
                                                <option value="" selected>Select</option>
                                                <option value="Battery">Battery</option>
                                                <option value="Cable">Cable</option>
                                                <option value="Cartridge">Cartridge</option>
                                                <option value="Connector">Connector</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="mt-2 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" id="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                            </div>
                        </div>
                    </div>
                    <!-- List of Search Items -->
                    <div class="col-md-8">
                        <div class="shadow p-3 rounded-3 mb-2" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-2">
                                <p class="fw-medium fs-5" style="color: #090909;">List</p>
                            </div>
                            <hr style="height: 1px; margin: 8px 0;">
                            <div class="overflow-auto" style="height: 360px; max-height: 360px;">
                                <table class="table table-hover table-borderless" style="background-color: white;" id="searchtable">
                                    <thead>
                                        <tr>
                                            <th style="width: 43%">Product</th>
                                            <th style="width: 20%">SI No.</th>
                                            <th style="width: 25%">Serial No.</th>
                                            <th style="width: 12%">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Product Details -->
                    <div class="col-md-4">
                        <div class="shadow p-3 rounded-3 mb-2" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-2">
                                <p class="fw-medium fs-5" style="color: #090909;">Product Details</p>
                                <hr style="height: 1px; margin: 8px 0;">
                            </div>
                            <div class="container mt-2">
                                <form id="productDetails" nonvalidate>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label for="SIno">SI No:</label>
                                                <input type="num" id="SInoDisplay" name="SIno" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="serialNo">Serial No.:</label>
                                                <input type="num" id="serialNoDisplay" name="serialNo" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="product">Product:</label>
                                                <input type="text" id="productDisplay" name="product" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="supplier">Supplier:</label>
                                                <input type="text" id="supplierDisplay" name="supplier" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="category">Category:</label>
                                                <input type="text" id="categoryDisplay" name="category" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="type">Type:</label>
                                                <input type="text" id="typeDisplay" name="type" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="isyn-branch-product">ISYN branch:</label>
                                                <input type="text" id="branchDisplay" name="isyn-branch-product" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="dealer-price">Dealer price:</label>
                                                <input type="number" id="dealers_priceDisplay" name="dealer-price" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="srp">SRP:</label>
                                                <input type="number" id="srpDisplay" name="srp" class="form-control" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <label for="quantity">Quantity:</label>
                                                <input type="number" id="quantityDisplay" name="quantity" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Product List of Return -->
                    <div class="col-md-8">
                        <div class="shadow p-3 rounded-3 mb-2" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-2">
                                <p class="fw-medium fs-5" style="color: #090909;">Product List to Return</p>
                            </div>
                            <hr style="height: 1px; margin: 8px 0;">
                            <div class="overflow-auto" style="height: 590px; max-height: 590px;">
                                <table class="table table-hover table-borderless" style="background-color: white;" id="returnTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 38%">Product</th>
                                            <th style="width: 15%">SI No.</th>
                                            <th style="width: 20%">Serial No.</th>
                                            <th style="width: 12%">Quantity</th>
                                            <th style="width: 15%">Return Type</th>
                                        </tr>
                                    </thead>
                                    <tbody id="returnList">
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <input class="form-control" id="returnReceiptID" value="<?php echo $transactionNo; ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-warning shadow text-white" onclick="submitData()"><i class="fa-solid fa-upload"></i> Return</button>
                                        <button type="button" class="btn btn-danger me-2 shadow" onclick="removeData()"><i class="fa-solid fa-trash-can"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Quantity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="quantityForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="quantityInput" class="form-label mt-2">Enter Product Quantity to Return</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="num" id="quantityInput" name="quantityInput" class="form-control float-start">
            
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="justify-content-center">
                                            <button class="btn btn-danger mx-2 float-end" id="cancelButton" type="button" onclick="clearField()">
                                                <i class="fa-solid fa-ban"></i> Cancel
                                            </button>
                                            <button class="btn btn-success mx-2 float-end" id="saveButton" type="button" data-bs-dismiss="modal" onclick="saveDataToTable()">
                                                <i class="fa-solid fa-save"></i> Okay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
    // Function to filter categories based on selected type
    function filterType() {
        var branch = document.getElementById("isynBranch").value;
        console.log(branch);

        var typeHeadOffice = <?php echo json_encode($typeHeadOffice); ?>;
        var typeIsynSantiago = <?php echo json_encode($typeIsynSantiago); ?>;
        var typeSelect = document.getElementById("type");

        typeSelect.innerHTML = ' <option value="" selected disabled>Select</option>';

        if (branch === "HEAD OFFICE") {
            typeHeadOffice.forEach(function(type) {
                var option = document.createElement("option");
                option.text = type;
                option.value = type;
                typeSelect.appendChild(option);
            });
        } else if (branch === "ISYN-SANTIAGO") {
            typeIsynSantiago.forEach(function(type) {
                var option = document.createElement("option");
                option.text = type;
                option.value = type;
                typeSelect.appendChild(option);
            });
        }
    }

    function filterCategories() {
        var type = document.getElementById("type").value;
        console.log(type);

        var categoriesWithVAT = <?php echo json_encode($categoriesWithVAT); ?>;
        var categoriesNonVAT = <?php echo json_encode($categoriesNonVAT); ?>;
        var categoriesSelect = document.getElementById("category");

        // Clear existing options
        categoriesSelect.innerHTML = '<option value="" selected disabled>Select</option>';

        // Populate options based on selected type
        if (type === "WITH VAT") {
            categoriesWithVAT.forEach(function(category) {
                var option = document.createElement("option");
                option.text = category;
                option.value = category;
                categoriesSelect.appendChild(option);
            });
        } else if (type === "NON-VAT") {
            categoriesNonVAT.forEach(function(category) {
                var option = document.createElement("option");
                option.text = category;
                option.value = category;
                categoriesSelect.appendChild(option);
            });
        }
    }


    // Attach event listener when the document is loaded
    document.addEventListener("DOMContentLoaded", function() {
        var branchSelect = document.getElementById("isynBranch");
        branchSelect.addEventListener("change", filterType);

        var typeSelect = document.getElementById("type");
        typeSelect.addEventListener("change", filterCategories);

    });
</script>

<script>
    $(document).ready(function() {
        $('#search-btn').click(function() {
            var branch = $('#isynBranch').val();
            var type = $('#type').val();
            var category = $('#category').val();

            $.ajax({
                method: 'POST',
                url: './ajax-inventory/product-search.php',
                data: {
                    branch: branch,
                    type: type,
                    category: category
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    // Clear previous results
                    $('#tableList').empty();

                    // Check if response contains data
                    if (response.length > 0) {
                        // Iterate through each object in the array
                        $.each(response, function(index, item) {
                            var row = '<tr class="clickable-row" id="doubleclickrow">' +
                                '<td class="ProductSelected">' + item.Product + '</td>' +
                                '<td class="SInoSelect">' + item.SIno + '</td>' +
                                '<td class="SerialnoSelected">' + item.Serialno + '</td>' +
                                '<td>' + item.Quantity + '</td>' +
                                '</tr>';
                            $('#tableList').append(row);
                        });

                        // Register click event for dynamically created rows
                        $('.clickable-row').click(function() {
                            var selectedSIno = $(this).find('.SInoSelect').text(); // Get SIno from clicked row
                            var selectedSerialNo = $(this).find('.SerialnoSelected').text();
                            var selectedProduct = $(this).find('.ProductSelected').text();
                            console.log('Selected SIno:', selectedSIno);
                            console.log('Selected Serial No.:', selectedSerialNo);
                            console.log('Selected Product:', selectedProduct);

                            // Ajax call to fetch product summary
                            $.ajax({
                                type: 'POST',
                                url: './ajax-inventory/product-summary.php',
                                data: {
                                    SIno: selectedSIno,
                                    Serialno: selectedSerialNo,
                                    Product: selectedProduct
                                },
                                dataType: 'json',
                                success: function(productSummary) {
                                    console.log(productSummary);
                                    if (productSummary.error) {
                                        alert(productSummary.error);
                                    } else {
                                        updateProductSummary(productSummary);
                                    }
                                },
                                error: function() {
                                    alert('Error fetching product summary');
                                }
                            });
                        });
                    } else {
                        $('#tableList').html('<tr><td colspan="4">No products found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: 'error',
                        title: 'Product not found'
                    });
                }
            });
        });

        function updateProductSummary(productSummary) {
            $('#serialNoDisplay').val(productSummary.Serialno);
            $('#SInoDisplay').val(productSummary.SIno);
            $('#productDisplay').val(productSummary.product);
            $('#supplierDisplay').val(productSummary.Supplier);
            $('#categoryDisplay').val(productSummary.Category);
            $('#typeDisplay').val(productSummary.Type);
            $('#branchDisplay').val(productSummary.Branch);
            $('#dealers_priceDisplay').val(productSummary.DealerPrice);
            $('#srpDisplay').val(productSummary.SRP);
            $('#quantityDisplay').val(productSummary.Quantity);
        }
    });


    /*
    //for opening quantity modal
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('searchtable');
        const modal = document.getElementById('quantityModal');
        const closeBtn = document.querySelector('.close');
        const modalText = document.getElementById('modal-text');

        // Handle double-click on table rows
        table.addEventListener('dblclick', (event) => {
            const targetRow = event.target.closest('tr');
            if (targetRow) {
                const cells = targetRow.getElementsByTagName('td');
                let rowData = '';
                for (let cell of cells) {
                    rowData += cell.innerText + ' ';
                }
                //modalText.innerText = rowData.trim();
                //modal.style.display = 'block';
            }
        });

        // Close the modal
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Close the modal when clicking outside of the modal
        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    */


    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('searchtable');
        const modal = new bootstrap.Modal(document.getElementById('quantityModal'));


        // Handle double-click on table rows
        table.addEventListener('dblclick', (event) => {
            const targetRow = event.target.closest('tr');
            if (targetRow) {
                const cells = targetRow.getElementsByTagName('td');
                let rowData = '';
                for (let cell of cells) {
                    rowData += cell.innerText + ' ';
                }
                modal.show();
            }
        });
    });


    function clearField() {
        document.getElementById("quantityInput").value = "";
        $('quantityModal').modal('hide');
    }

    function saveDataToTable() {
        var quantity = document.getElementById('quantityInput').value;
        var product = document.getElementById("productDisplay").value;
        var sino = document.getElementById("SInoDisplay").value;
        var serialno = document.getElementById("serialNoDisplay").value;
        var returntype = document.getElementById("return-type").value;

        var newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td>${product}</td>
            <td>${sino}</td>
            <td>${serialno}</td>
            <td>${quantity}</td>
            <td>${returntype}</td>
        `;

        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        tableBody.appendChild(newRow);
    }

    //return btn function
    function submitData() {
        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        var dataArray = [];

        tableBody.querySelectorAll("tr").forEach(function(row) {
            var cells = row.querySelectorAll("td");
            var data = {
                Product: cells[0] ? cells[0].innerText || '' : '',
                SIno: cells[1] ? cells[1].innerText || '' : '',
                SerialNo: cells[2] ? cells[2].innerText || '' : '',
                Quantity: cells[3] ? cells[3].innerText || '' : '',
                TransactionType: cells[4] ? cells[4].innerText || '' : '',
                TransactionNo: document.getElementById("returnReceiptID").value,
                Type: document.getElementById("type").value,
                Category: document.getElementById("category").value,
                Supplier: document.getElementById("supplierDisplay").value,
            };
            dataArray.push(data);
            console.log(data);
        });

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "./ajax-inventory/submit-purchase-return.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "success",
                    title: "Successfully saved!",
                });
            } else {
                // console.error('Error:', xhr.status, xhr.statusText);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: 'error',
                    title: 'Error inserting data'
                });
            }
        };
        xhr.send(JSON.stringify(dataArray));
        tableBody.innerHTML = "";
        var tableBody2 = document.getElementById("searchtable").querySelector("tbody");
        tableBody2.innerHTML = "";
        document.getElementById("searchProduct").reset();
        document.getElementById("productDetails").reset();
    }

    //function for removing data from product list to return
    function removeData() {
        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        tableBody.innerHTML = "";
    }
</script>
