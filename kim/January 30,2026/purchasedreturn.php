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

            include_once("../../database/connection.php");
            $db = new Database();
            $conn = $db->conn;

            // Fetch next transaction number
            $sql = "SELECT MAX(CAST(SUBSTRING(TransactionNo, 4) AS UNSIGNED)) as max_num FROM tbl_purchasereturned WHERE TransactionNo LIKE 'PRN%'";
            $result = $conn->query($sql);
            $transactionNo = 'PRN000001';
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $nextNum = ($row['max_num'] ?? 0) + 1;
                $transactionNo = 'PRN' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            }
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

            /* Custom styles to adjust container margins */
            .container {
                max-width: 95% !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* Reduce padding in shadow containers */
            .shadow {
                padding: 15px !important;
            }

            /* Style for selected rows */
            .clickable-row.selected {
                background-color: #e3f2fd !important;
                cursor: pointer;
            }

            .clickable-row:hover {
                background-color: #f5f5f5;
                cursor: pointer;
            }

            th {
                font-weight: bold;
                color: #090909;
                position: sticky;
                top: 0;
                background-color: #f8f9fa;
            }

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
            /* Custom Tab Styles */
            #returnTabs .nav-link {
                color: #090909;
                font-weight: 500;
            }

            #returnTabs .nav-link.active {
                color: white !important;
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
        </style>

            <div class="container-fluid mt-4">
                <div class="shadow rounded-3 p-3 mb-4" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2"><i class="fa-solid fa-undo me-2"></i>Purchased Return</p>
                </div>

            <!-- Product Details Modal -->
            <div class="modal fade" id="productDetailsModal" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productDetailsModalLabel">Product Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
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
                                            <input type="number" id="quantityDisplay" name="quantity" class="form-control" min="1">
                                            <input type="hidden" id="maxQuantityDisplay">
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary w-100" onclick="returnSingleItem()">
                                                <i class="fa-solid fa-save"></i> Save & Add to List
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row">
                    <!-- Search Product -->
                    <div class="col-md-4">
                        <div class="shadow p-3 rounded-3 mb-3 h-100" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Search Product</p>
                                <hr style="height: 1px">
                            </div>
                            <form method="post" nonvalidate id="searchProduct">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-section mb-2">
                                            <label for="return-type" class="form-label">Return type:</label>
                                            <select id="return-type" name="return-type" class="form-select">
                                                <option value="" selected disabled>Select</option>
                                            </select>
                                        </div>
                                        <div class="form-section mb-2">
                                            <label for="isynBranch" class="form-label">ISYN branch:</label>
                                            <select id="isynBranch" name="isynBranch" class="form-select">
                                                <option value="" selected disabled>Select</option>
                                                <?php
                                                $query = "SELECT DISTINCT Branch FROM tbl_invlist ORDER BY Branch";
                                                $query_run = $conn->query($query);
                                                if ($query_run && $query_run->num_rows > 0) {
                                                    while ($row = $query_run->fetch_assoc()) {
                                                ?>
                                                        <option value="<?php echo $row['Branch']; ?>"><?php echo $row['Branch']; ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-section mb-2">
                                            <label for="type" class="form-label">Type:</label>
                                            <select class="form-select" aria-label="type" required name="productType[]" id="type">
                                                <option value="" selected disabled>Select</option>
                                            </select>
                                        </div>
                                        <div class="form-section mb-2">
                                            <label for="category" class="form-label">Category:</label>
                                            <select id="category" name="category" class="form-select">
                                                <option value="" selected disabled>Select</option>
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
                        <div class="shadow p-3 rounded-3 mb-3 h-100" style="background-color: white;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="fw-medium fs-5 mb-0" style="color: #090909;">List</p>
                                <button type="button" class="btn btn-sm btn-primary" id="listActionBtn">
                                    <i class="fa-solid fa-eye"></i> View Details
                                </button>
                            </div>
                            <hr style="height: 1px">
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

                <div class="row mt-4">
                    <!-- Product List of Return -->
                    <div class="col-md-12">
                        <div class="shadow p-3 rounded-3 mb-3" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Product List to Return</p>
                            </div>
                            <hr style="height: 1px">
                            
                            <ul class="nav nav-tabs mb-3" id="returnTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="to-return-tab" data-bs-toggle="tab" data-bs-target="#to-return-pane" type="button" role="tab" aria-controls="to-return-pane" aria-selected="true">To Return</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned-pane" type="button" role="tab" aria-controls="returned-pane" aria-selected="false">Returned</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="returnTabsContent">
                                <div class="tab-pane fade show active" id="to-return-pane" role="tabpanel" aria-labelledby="to-return-tab">
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
                                </div>
                                <div class="tab-pane fade" id="returned-pane" role="tabpanel" aria-labelledby="returned-tab">
                                    <div class="overflow-auto" style="height: 590px; max-height: 590px;">
                                        <table class="table table-hover table-borderless" style="background-color: white;" id="archivedTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 38%">Product</th>
                                                    <th style="width: 15%">SI No.</th>
                                                    <th style="width: 20%">Serial No.</th>
                                                    <th style="width: 12%">Quantity</th>
                                                    <th style="width: 15%">Return Type</th>
                                                </tr>
                                            </thead>
                                            <tbody id="archivedList">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <input class="form-control" id="returnReceiptID" value="<?php echo $transactionNo; ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <select id="printCategoryFilter" class="form-select">
                                        <option value="All">All Categories</option>
                                        <?php
                                        // Get all unique categories
                                        $sql = "SELECT DISTINCT Category FROM tbl_purchasereturned WHERE (Status IS NULL OR Status != 'Archived') ORDER BY Category ASC";
                                        $result = $conn->query($sql);
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row['Category']) . '">' . htmlspecialchars($row['Category']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="float-end">
                                        <button type="button" class="btn btn-dark shadow text-white" id="printBtn" onclick="printData()"><i class="fa-solid fa-print"></i> Return & Print</button>
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
        <!-- <script src="../../js/inventorymanagement/outgoinginventory.js?<?= time() ?>"></script> -->
        <script src="../../js/inventorymanagement/purchasedreturned_maintenance.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>

<script>
    $(document).ready(function() {
        // Add click event for View Details button
        $('#listActionBtn').click(function() {
            // Check if there's a selected row
            var selectedRow = $('.clickable-row.selected');
            if (selectedRow.length > 0) {
                var selectedSIno = selectedRow.find('.SInoSelect').text();
                var selectedSerialNo = selectedRow.find('.SerialnoSelected').text();
                var selectedProduct = selectedRow.find('.ProductSelected').text();
                
                // Fetch product summary and display in Product Details
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
                            var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                            modal.show();
                        }
                    },
                    error: function() {
                        alert('Error fetching product summary');
                    }
                });
            } else {
                alert('Please select a product from the list first');
            }
        });

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
                            // Remove previous selection
                            $('.clickable-row').removeClass('selected');
                            
                            // Add selection to clicked row
                            $(this).addClass('selected');
                            
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
                                        var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                                        modal.show();
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
            $('#maxQuantityDisplay').val(productSummary.Quantity);
            $('#quantityDisplay').attr('max', productSummary.Quantity);
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


    /*
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
    */


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
        var dealerPrice = document.getElementById("dealers_priceDisplay").value;
        var srp = document.getElementById("srpDisplay").value;
        var branch = document.getElementById("branchDisplay").value;

        // Calculate totals
        var totalDealerPrice = (parseFloat(dealerPrice) || 0) * (parseInt(quantity) || 0);
        var totalSRP = (parseFloat(srp) || 0) * (parseInt(quantity) || 0);

        var newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td>${product}</td>
            <td>${sino}</td>
            <td>${serialno}</td>
            <td>${quantity}</td>
            <td>${returntype}</td>
            <td style="display:none;">${dealerPrice}</td>
            <td style="display:none;">${srp}</td>
            <td style="display:none;">${branch}</td>
            <td style="display:none;">${totalDealerPrice}</td>
            <td style="display:none;">${totalSRP}</td>
        `;

        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        tableBody.appendChild(newRow);
    }

    //return btn function
    function submitData() {
        var tableBody = document.getElementById("returnTable").querySelector("tbody");
        var dataArray = [];

        tableBody.querySelectorAll("tr").forEach(function(row) {
            // Skip rows that are already saved
            if (row.getAttribute('data-saved') === 'true') {
                return;
            }

            var cells = row.querySelectorAll("td");
            var data = {
                Product: cells[0] ? cells[0].innerText || '' : '',
                SIno: cells[1] ? cells[1].innerText || '' : '',
                SerialNo: cells[2] ? cells[2].innerText || '' : '',
                Quantity: cells[3] ? cells[3].innerText || '' : '',
                TransactionType: cells[4] ? cells[4].innerText || '' : '',
                DealerPrice: cells[5] ? cells[5].innerText || '' : '',
                SRP: cells[6] ? cells[6].innerText || '' : '',
                Branch: cells[7] ? cells[7].innerText || '' : '',
                TotalPrice: cells[8] ? cells[8].innerText || '' : '',
                TotalSRP: cells[9] ? cells[9].innerText || '' : '',
                TransactionNo: document.getElementById("returnReceiptID").value,
                Type: document.getElementById("type").value,
                Category: document.getElementById("category").value,
                Supplier: document.getElementById("supplierDisplay").value,
            };
            dataArray.push(data);
            console.log(data);
        });

        if (dataArray.length === 0) {
            // If all items are already saved, just reload or show message
            // But we should probably check if there were any items at all
            if (tableBody.querySelectorAll("tr").length > 0) {
                // All items were already saved
                 Swal.fire({
                    icon: "info",
                    title: "Items already saved",
                    text: "All items in the list have already been returned."
                });
                setTimeout(() => { location.reload(); }, 2000);
            } else {
                 Swal.fire({
                    icon: "warning",
                    title: "No items",
                    text: "Please add items to return."
                });
            }
            return;
        }

        // Ask for Reason
        Swal.fire({
            title: 'Reason for Return',
            input: 'textarea',
            inputLabel: 'Please enter the reason for returning these items',
            inputPlaceholder: 'Type your reason here...',
            inputAttributes: {
                'aria-label': 'Type your reason here'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit Return',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('You need to write a reason!')
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var reason = result.value;
                
                // Add reason to each item
                dataArray.forEach(function(item) {
                    item.Reason = reason;
                });

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./ajax-inventory/submit-purchase-return.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
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
                            
                            
                            // Reload data from database
                            loadReturnedItems();

                            // Clear search inputs/results but keep return table
                            var tableBody2 = document.getElementById("searchtable").querySelector("tbody");
                            tableBody2.innerHTML = "";
                            document.getElementById("searchProduct").reset();
                            document.getElementById("productDetails").reset();
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
                                title: 'Error inserting data: ' + (xhr.responseText || xhr.statusText)
                            });
                        }
                    }
                };
                xhr.send(JSON.stringify(dataArray));
            }
        });
    }

    // Function to filter return table based on category
    function filterReturnTable() {
        var selectedCategory = document.getElementById('printCategoryFilter').value;
        // Select rows from both tables or just the active one?
        // Let's filter both so when user switches tab, it's already filtered.
        var returnRows = document.getElementById('returnTable').querySelectorAll('tbody tr');
        var archivedRows = document.getElementById('archivedTable').querySelectorAll('tbody tr');
        
        var applyFilter = function(rows) {
            rows.forEach(function(row) {
                var categoryCell = row.querySelector('.item-category');
                if (categoryCell) {
                    var category = categoryCell.textContent;
                    if (selectedCategory === 'All' || category === selectedCategory) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        };

        applyFilter(returnRows);
        applyFilter(archivedRows);
    }

    // Attach event listener to the category filter
    document.addEventListener('DOMContentLoaded', function() {
        var categoryFilter = document.getElementById('printCategoryFilter');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterReturnTable);
        }
        
        // Tab switching logic for print button visibility
        var printBtn = document.getElementById('printBtn');
        var toReturnTab = document.getElementById('to-return-tab');
        var returnedTab = document.getElementById('returned-tab');
        
        if (toReturnTab && returnedTab && printBtn) {
            toReturnTab.addEventListener('shown.bs.tab', function (e) {
                printBtn.style.display = 'block';
            });
            returnedTab.addEventListener('shown.bs.tab', function (e) {
                printBtn.style.display = 'none';
            });
        }
    });

    // Function to print data
    function printData() {
        var selectedCategory = document.getElementById('printCategoryFilter').value;
        var tableRows = document.getElementById('returnTable').querySelectorAll('tbody tr');
        var idsToPrint = [];

        // Collect IDs of visible rows
        tableRows.forEach(function(row) {
            // Check if row is visible (not hidden by filter)
            if (row.style.display !== 'none') {
                var id = row.getAttribute('data-id');
                if (id) {
                    idsToPrint.push(id);
                }
            }
        });

        if (idsToPrint.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No items',
                text: 'No visible items to print and archive.'
            });
            return;
        }

        Swal.fire({
            title: 'Print & Archive?',
            text: "This will print the visible items and mark them as returned (archived). Continue?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Print & Archive'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send to server to archive and prepare print session
                fetch('./ajax-inventory/process-print-archive.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: idsToPrint }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Open print window in session mode
                        var printUrl = 'print-returned-items.php?mode=session';
                        // Keep category for header info if needed, though session ids determine content
                        if (selectedCategory && selectedCategory !== 'All') {
                            printUrl += '&category=' + encodeURIComponent(selectedCategory);
                        }
                        window.open(printUrl, '_blank');
                        
                        // Reload the table to reflect archived status (items will disappear)
                        loadReturnedItems();
                        loadArchivedItems(); // Reload archived list to show new items
                        
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: "success",
                            title: "Items archived and ready to print",
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to process: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    // Function to archive a single item
    function archiveSingleItem(batchNo) {
        Swal.fire({
            title: 'Archive Item?',
            text: "Do you want to archive this specific item?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('./ajax-inventory/archive-single-return-item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ batchNo: batchNo }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                         Swal.fire({
                            icon: 'success',
                            title: 'Archived!',
                            text: 'Item has been archived.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadReturnedItems(); // Reload table
                        loadArchivedItems();
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to archive: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    //function for archiving data
    function archiveData() {
        var transactionNo = document.getElementById("returnReceiptID").value;
        if (!transactionNo) return;

        Swal.fire({
            title: 'Are you sure?',
            text: "This will archive all returned items for this transaction. They will be hidden from the list but kept in the database.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('./ajax-inventory/archive-return-items.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ transactionNo: transactionNo }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                         Swal.fire(
                            'Archived!',
                            'Your returned items have been archived.',
                            'success'
                        );
                        loadReturnedItems(); // Reload table (will exclude archived items)
                        loadArchivedItems();
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to archive: ' + data.message,
                            'error'
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred.',
                        'error'
                    );
                });
            }
        });
    }

    function loadReturnedItems() {
        // Fetch all data as requested
        fetch(`./ajax-inventory/fetch-return-items.php`)
            .then(response => response.json())
            .then(data => {
                var tableBody = document.getElementById("returnTable").querySelector("tbody");
                tableBody.innerHTML = ""; // Clear current view

                if (data.length > 0) {
                    data.forEach(item => {
                        var newRow = document.createElement("tr");
                        newRow.setAttribute("data-saved", "true");
                        // Add pointer cursor to indicate clickability - Removed as per request
                        // newRow.style.cursor = "pointer";
                        
                        // Robust way to find the Batch ID regardless of case or hidden characters
                        var batchId = item.Batchno || item.BatchNo || item.batchno || item.id || item.ID;
                        if (batchId === undefined || batchId === null) {
                            var key = Object.keys(item).find(k => k.toLowerCase() === 'batchno');
                            if (key) batchId = item[key];
                        }
                        
                        // Set data-id for easier access
                        newRow.setAttribute("data-id", batchId);
                        
                        // Click to archive removed as per request
                        // newRow.onclick = function() { archiveSingleItem(batchId); };

                        newRow.innerHTML = `
                            <td>${item.Product}</td>
                            <td>${item.SIno}</td>
                            <td>${item.Serialno}</td>
                            <td>${item.Quantity}</td>
                            <td>${item.Type}</td>
                            <td style="display:none;">${item.DealerPrice}</td>
                            <td style="display:none;">${item.SRP}</td>
                            <td style="display:none;">${item.Branch}</td>
                            <td style="display:none;">${item.TotalPrice}</td>
                            <td style="display:none;">${item.TotalSRP}</td>
                            <td style="display:none;" class="item-category">${item.Category}</td>
                        `;
                        tableBody.appendChild(newRow);
                    });
                    
                    // Apply filter if one is selected
                    if (typeof filterReturnTable === 'function') {
                        filterReturnTable();
                    }
                }
            })
            .catch(error => console.error('Error loading items:', error));
    }

    function loadArchivedItems() {
        // Fetch archived data
        fetch(`./ajax-inventory/fetch-archived-items.php`)
            .then(response => response.json())
            .then(data => {
                var tableBody = document.getElementById("archivedTable").querySelector("tbody");
                tableBody.innerHTML = ""; // Clear current view

                if (data.length > 0) {
                    data.forEach(item => {
                        var newRow = document.createElement("tr");
                        
                        // Archived items might not need to be clickable to archive again, 
                        // but maybe clickable to view details? 
                        // For now, let's just display them.
                        // If they need to be "un-archived" or something, that's a future feature.

                        newRow.innerHTML = `
                            <td>${item.Product}</td>
                            <td>${item.SIno}</td>
                            <td>${item.Serialno}</td>
                            <td>${item.Quantity}</td>
                            <td>${item.Type}</td>
                            <td style="display:none;">${item.DealerPrice}</td>
                            <td style="display:none;">${item.SRP}</td>
                            <td style="display:none;">${item.Branch}</td>
                            <td style="display:none;">${item.TotalPrice}</td>
                            <td style="display:none;">${item.TotalSRP}</td>
                            <td style="display:none;" class="item-category">${item.Category}</td>
                        `;
                        tableBody.appendChild(newRow);
                    });
                    
                    // Apply filter if one is selected
                    if (typeof filterReturnTable === 'function') {
                        filterReturnTable();
                    }
                }
            })
            .catch(error => console.error('Error loading archived items:', error));
    }
    
    // Load items on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadReturnedItems();
        loadArchivedItems();
        loadAllProducts(); // Load all products into the List container
    });

    function loadAllProducts() {
        // Load all products from database to display in the List container
        $.ajax({
            method: 'POST',
            url: './ajax-inventory/load-all-products.php',
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
                                    // Show the Product Details modal
                                    var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                                    modal.show();
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
                $('#tableList').html('<tr><td colspan="4">Error loading products</td></tr>');
            }
        });
    }

    function showProductDetails(SIno, Serialno, Product) {
        // Fetch product summary and display in the Product Details container
        $.ajax({
            type: 'POST',
            url: './ajax-inventory/product-summary.php',
            data: {
                SIno: SIno,
                Serialno: Serialno,
                Product: Product
            },
            dataType: 'json',
            success: function(productSummary) {
                console.log(productSummary);
                if (productSummary.error) {
                    alert(productSummary.error);
                } else {
                    updateProductSummary(productSummary);
                    // Show the Product Details modal
                    var modal = new bootstrap.Modal(document.getElementById('productDetailsModal'), { focus: false });
                    modal.show();
                }
            },
            error: function() {
                alert('Error fetching product summary');
            }
        });
    }

    function returnSingleItem() {
        var product = document.getElementById("productDisplay").value;
        var sino = document.getElementById("SInoDisplay").value;
        var serialno = document.getElementById("serialNoDisplay").value;
        var quantity = document.getElementById("quantityDisplay").value;
        var maxQuantity = document.getElementById("maxQuantityDisplay").value;
        var returntype = document.getElementById("return-type").value;
        var dealerPrice = document.getElementById("dealers_priceDisplay").value;
        var srp = document.getElementById("srpDisplay").value;
        var branch = document.getElementById("branchDisplay").value;
        var type = document.getElementById("type").value;
        var category = document.getElementById("category").value;
        var supplier = document.getElementById("supplierDisplay").value;
        var transactionNo = document.getElementById("returnReceiptID").value;

        if (!product || !sino) {
            Swal.fire({
                icon: "warning",
                title: "Missing Information",
                text: "Please select a product first."
            });
            return;
        }

        if (!returntype) {
            Swal.fire({
                icon: "warning",
                title: "Missing Information",
                text: "Please select a Return Type."
            });
            return;
        }

        if (parseInt(quantity) <= 0) {
             Swal.fire({
                icon: "warning",
                title: "Invalid Quantity",
                text: "Quantity must be greater than 0."
            });
            return;
        }

        if (parseInt(quantity) > parseInt(maxQuantity)) {
             Swal.fire({
                icon: "warning",
                title: "Invalid Quantity",
                text: "Quantity cannot exceed the available quantity (" + maxQuantity + ")."
            });
            return;
        }

        // Ask for Reason
        Swal.fire({
            title: 'Reason for Return',
            input: 'textarea',
            inputLabel: 'Please enter the reason for returning this item',
            inputPlaceholder: 'Type your reason here...',
            inputAttributes: {
                'aria-label': 'Type your reason here'
            },
            showCancelButton: true,
            confirmButtonText: 'Save & Return',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('You need to write a reason!')
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var reason = result.value;

                // Calculate totals
                var totalDealerPrice = (parseFloat(dealerPrice) || 0) * (parseInt(quantity) || 0);
                var totalSRP = (parseFloat(srp) || 0) * (parseInt(quantity) || 0);

                var data = [{
                    Product: product,
                    SIno: sino,
                    SerialNo: serialno,
                    Quantity: quantity,
                    TransactionType: returntype,
                    DealerPrice: dealerPrice,
                    SRP: srp,
                    Branch: branch,
                    TotalPrice: totalDealerPrice,
                    TotalSRP: totalSRP,
                    TransactionNo: transactionNo,
                    Type: type,
                    Category: category,
                    Supplier: supplier,
                    Reason: reason
                }];

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "./ajax-inventory/submit-purchase-return.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            
                            // Reload data from database
                            loadReturnedItems();
                            
                            // Close the modal
                            var modalEl = document.getElementById('productDetailsModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }

                            Swal.fire({
                                icon: "success",
                                title: "Returned & Saved",
                                text: "Item has been successfully returned and saved to database.",
                                timer: 1500
                            });
                            
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to save: ' + (xhr.responseText || xhr.statusText)
                            });
                        }
                    }
                };
                xhr.send(JSON.stringify(data));
            }
        });
    }
</script>
