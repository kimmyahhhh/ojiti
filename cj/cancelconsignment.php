<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
?>
<!doctype html>
<html lang="en" dir="ltr">
    <?php
        include('../../includes/pages.header.php');
    ?>

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
                height: 100% ;
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

            td,
            th {
                color: #090909;
                word-wrap: break-word;
                word-wrap: break-word;
                overflow-wrap: break-word;
                white-space: normal;
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

            .table tbody tr.selected {
                background-color: #d3d3d3;
                color: #000;

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
                <div class="shadow rounded-3 p-3 mb-4" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Cancel Consignment</p>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <!--Search Product -->
                        <div class="shadow p-3 rounded-3 mb-3" style="background-color: white;">
                            <div class="align-items-center justify-content-between mb-3">
                                <p class="fw-medium fs-5" style="color: #090909;">Search Product</p>
                                <hr style="height: 1px">
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-section mb-2">
                                        <label for="isynBranch" class="form-label">ISYN branch:</label>
                                        <select id="isynBranch" name="isynBranch" class="form-select">
                                            <option value="" selected disabled>Select</option>
                                            <option value="OVERALL">OVERALL</option>
                                            <?php
                                            // $query = "SELECT DISTINCT Branch FROM tbl_invlistconsign ORDER BY Branch";
                                            // $query_run = mysqli_query($connection, $query);
                                            // if (mysqli_num_rows($query_run) > 0) {
                                            //     while ($row = mysqli_fetch_assoc($query_run)) {
                                            ?>
                                                    <!-- <option value="<?php 
                                                    // echo $row['Branch'] ?>"><?php 
                                                    // echo $row['Branch'] ?></option> -->
                                            <?php
                                                // }
                                            // }
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
                            <div class="mt-2 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" id="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="overflow-auto" style="height: 280px; max-height: 280px;">
                                <table class="table table-hover table-borderless" style="background-color: white;" id="consignTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 70%">Other Details</th>
                                            <th style="width: 30%">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableList">
                                        <tr>
                                            <td>Total DP:</td>
                                        </tr>
                                        <tr>
                                            <td>Total Qty:</td>
                                        </tr>
                                        <tr>
                                            <td>Total SRP:</td>
                                        </tr>
                                        <tr>
                                            <td>Total Markup:</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!--List of Product Return -->
                <div class="shadow p-3 rounded-3 " style="background-color: white;">
                    <div class="align-items-center justify-content-between mb-3">
                        <p class="fw-medium fs-5" style="color: #090909;">Product List</p>
                    </div>
                    <hr style="height: 1px">
                    <div class="overflow-auto" style="height: 350px; max-height: 700px;">
                        <table class="table table-hover table-borderless" id="list" style="background-color: white;">
                            <thead>
                                <tr>
                                    <th style="width:12%">SI No.</th>
                                    <th style="width:15%">Serial No.</th>
                                    <th style="width:19%">Product</th>
                                    <th style="width:15%">Category</th>
                                    <th style="width:15%">Branch</th>
                                    <th style="width:12%">Quantity</th>
                                    <th style="width:12%">Dealer Price</th>
                                </tr>
                            </thead>
                            <tbody id="searchResult">
                                <tr>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr style="height: 1px">
                    <div class="row mt-3">
                        <div class="text-end">
                            <button class="btn btn-warning mx-2 text-white" id="removeButton" type="button" onclick="disableData()">
                                <i class="fa-solid fa-xmark"></i> Remove
                            </button>
                            <button class="btn btn-danger mx-2" id="cancelButton" type="button" onclick="cancelConsignment()">
                                <i class="fa-solid fa-trash-can"></i> Cancel Consignment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../js/maintenance.js"></script>
        <!-- <script src="../../js/profiling/shareholderinfo.js"></script> -->
        
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
        var typeOVERALL = <?php echo json_encode($typeOVERALL); ?>;
        var typeSelect = document.getElementById("type");

        typeSelect.innerHTML = '<option value="" selected disabled>Select</option><option value="OVERALL">OVERALL</option>';

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
        } else if (branch === "OVERALL") {
            typeOVERALL.forEach(function(type) {
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
        var categoriesOVERALL = <?php echo json_encode($categoriesOVERALL); ?>;
        var categoriesSelect = document.getElementById("category");

        // Clear existing options
        categoriesSelect.innerHTML = '<option value="" selected disabled>Select</option><option value="OVERALL">OVERALL</option>';

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
        } else if (type === "OVERALL") {
            categoriesOVERALL.forEach(function(category) {
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
            console.log(branch);
            console.log(category);

            if (branch === "OVERALL") {
                branch = "";
            }
            if (type === "OVERALL") {
                type = "";
            }
            if (category === "OVERALL") {
                category = "";
            }
            

            $.ajax({
                method: 'POST',
                url: './ajax-edit-transaction/consignment-product-search.php',
                data: {
                    branch: branch,
                    type: type,
                    category: category
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    // Clear previous results
                    $('#searchResult').empty();

                    // Check if response contains data
                    if (response.length > 0) {
                        // Iterate through each object in the array
                        $.each(response, function(index, item) {
                            if (index === response.length - 1) {
                                return true;
                            }
                            var row = '<tr>' +
                                '<td class="SInoSelect">' + item.SIno + '</td>' +
                                '<td>' + item.Serialno + '</td>' +
                                '<td>' + item.Product + '</td>' +
                                '<td>' + item.Category + '</td>' +
                                '<td>' + item.Branch + '</td>' +
                                '<td>' + item.Quantity + '</td>' +
                                '<td>' + item.DealerPrice + '</td>' +
                                '</tr>';
                            $('#searchResult').append(row);
                            
                        });
                        $.each(response, function(index, item) {

                            if (response) {
                                $('#tableList').empty();
                            }
                            
                            //adds search results
                            var total = '<tr>' +
                                    '<td>' + 'Total DP:' + '</td>' +
                                    '<td>' + item.totalDP + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>' + 'Total Qty:' + '</td>' +
                                    '<td>' +  item.totalQty + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>' + 'Total SRP:' + '</td>' +
                                    '<td>' +  item.totalSRP + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>' + 'Total Markup:' + '</td>' +
                                    '<td>' +  item.totalMarkup + '</td>' +
                                '</tr>';
                            $('#tableList').append(total);
                            
                        });
                    } else {
                        $('#searchResult').html('<tr><td colspan="4">No products found</td></tr>');
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

        // Function to handle row selection
        $('#list tbody').on('click', 'tr', function() {
            $('#list tbody tr').removeClass('selected'); // Remove 'selected' class from all rows
            $(this).addClass('selected'); // Add 'selected' class to the clicked row
        });
    });

    //cancel consignment btn
    function cancelConsignment() {
        var tableBody = document.getElementById("list").querySelector("tbody");
        var data = [];

        tableBody.querySelectorAll("tr.selected").forEach(function(row) {
            var cells = row.querySelectorAll("td");
            var rowData = {
                sino: cells[0] ? cells[0].innerText || '' : '',
                serialno: cells[1] ? cells[1].innerText || '' : '',
                product: cells[2] ? cells[2].innerText || '' : '',
                category: cells[3] ? cells[3].innerText || '' : '',
                branch: cells[4] ? cells[4].innerText || '' : '',
                quantity: cells[5] ? cells[5].innerText || '' : '',
                dealerprice: cells[6] ? cells[6].innerText || '' : ''
            };
            data.push(rowData);
            console.log('product:', rowData);
        });

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "./ajax-edit-transaction/cancel-consignment-btn.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);

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
                        icon: 'success',
                        title: 'Added successfully'
                    });
                } else {
                    console.error('Error:', xhr.status, xhr.statusText);
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
            }
        };

        tableBody.innerHTML = "";
        var tableBody2 = document.getElementById("consignTable").querySelector("tbody");
        tableBody2.innerHTML = "";

        var jsonData = JSON.stringify(data);
        xhr.send(jsonData);
        
    }
</script>

<script>
    // Function to handle row selection and scrolling into view
    function selectRow(event) {
        $(event.currentTarget).addClass('selected');
        $(event.currentTarget).get(0).scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    // Function to remove the selected row
    function removeSelectedRow() {
        var selectedRow = $('#list tbody tr.selected');
        if (selectedRow.length > 0) {
            selectedRow.remove();
        } else {
            alert('Please select a row to remove.');
        }
    }

    $(document).ready(function() {
        $('#list tbody').on('click', 'tr', selectRow);
        $('#removeButton').click(removeSelectedRow);
    });
</script>
