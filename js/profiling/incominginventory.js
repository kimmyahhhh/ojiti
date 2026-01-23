var dataInvTbl, dataInvTblValue = "", itemTbl, SelectedFromList = "", SelectedFromDataInv = "", DataInvSINo = "";
var isUpdate = false;
var origSIno = "", origSerial = "", origProduct = "";

$(document).ready(function() {
    
    $("#category").select2({
        width: '100%',
    });
    // Free-text for supplier and SI; no select2 needed

    Initialize();
    InitializeDataTable(); // Initialize table immediately
    (function(){ try { var data = localStorage.getItem('incoming_itemTbl'); if (data) { var arr = JSON.parse(data); if (Array.isArray(arr)) { arr.forEach(function(entry){ itemTbl.row.add(entry).draw(false); }); } } } catch(e){ localStorage.removeItem('incoming_itemTbl'); } })();
    LoadDataInventory();

    $('#purchaseDate').on('change', function(){
        var val = $(this).val();
        if (val) {
            var d = new Date(val);
            var t = new Date();
            if (d > t) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Future date not allowed',
                });
                var date = t;
                $(this).val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());
            }
        }
    });
    $('#dateEncoded').on('change', function(){
        var val = $(this).val();
        if (val) {
            var d = new Date(val);
            var t = new Date();
            if (d > t) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Future date not allowed',
                });
                var date = t;
                $(this).val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());
            }
        }
    });

    $('#addToList').on('click', function() {
        console.log("Add to List clicked");
        if (!$.fn.DataTable.isDataTable('#itemTbl')) {
             Swal.fire("Error", "Item table not initialized yet. Please refresh.", "error");
             return;
        }
        
        console.log(DataInvSINo);
        let branch = $('#branch').val();
        let type = $('#type').val();
        let categ = $('#category').val();
        let product = $('#product').val();
        let supplier = $('#supplier').val();
        let supplierSI = $('#suppliersSI').val();
        let serialNo = $('#serialNo').val(); 
        let purchaseDate = $('#purchaseDate').val();
        let warranty = $('#warranty').val();
        let imageName = $('#imageName').val();
        let dateEncoded = $('#dateEncoded').val();
        let dealerPrice = $('#dealersPrice').val();
        let srp = $('#srp').val();
        let quantity = $('#quantity').val();
        let totalPrice = $('#totalPrice').val();
        let totalSRP = $('#totalSRP').val();
        let mpi = $('#mpi').val();
        let totalMarkup = $('#totalmarkup').val();

        if (branch == null || type == null || categ == null || product == null || supplier == null || supplierSI == "" || serialNo == "" || purchaseDate == "" || warranty == "" || dateEncoded == "" || supplierSI == "" || dealerPrice == "" || srp == "" || quantity == "" || totalPrice == "" || totalSRP == "" || mpi == "" || totalMarkup == "") {
            Swal.fire({
                icon: 'warning',
                text: 'Please enter required details.',
            });
            return;
        }

        itemTbl.row.add([
            product,
            serialNo,
            warranty,
            dealerPrice,
            srp,
            quantity,
            totalPrice,
            totalSRP,
            mpi,
            totalMarkup,
            branch,
            type,
            categ,
            supplier,
            supplierSI,
            purchaseDate,
            imageName,
            dateEncoded,
        ]).draw(false);

        $('#suppliersSI').prop('disabled',false);
        try { localStorage.setItem('incoming_itemTbl', JSON.stringify(itemTbl.rows().data().toArray())); } catch(e) {}
    });

    $('#editBtn').on('click', function() {
        isUpdate = true;
        
        $('#branch').prop('disabled', false);
        $('#type').prop('disabled', false);
        $('#category').prop('disabled', false);
        $('#product').prop('disabled', false);
        $('#supplier').prop('disabled', false);
        $('#suppliersSI').prop('disabled', false);
        $('#serialNo').prop('disabled', false);   
        $('#purchaseDate').prop('disabled', false);
        $('#warranty').prop('disabled', false);
        $('#imageName').prop('disabled', false);
        $('#dateEncoded').prop('disabled', false);
        $('#dealersPrice').prop('disabled', false);
        $('#srp').prop('disabled', false);
        $('#quantity').prop('disabled', false);

        // Ensure all form inputs are enabled (except readonly ones)
        $('#inventoryinForm input:not([readonly]), #inventoryinForm select, #inventoryinForm textarea').prop('disabled', false);
        
        // Hide/Show Buttons
        $('#editBtn').prop('hidden', true);
        $('#save').prop('hidden', false).prop('disabled', false);
        $('#cancel').prop('hidden', false).prop('disabled', false);
        $('#addToList').prop('hidden', true); // No adding to list in edit mode
    });

    $('#addNew').on('click', function() {
        isUpdate = false;
        $('#branch').prop('disabled', false).val('');
        $('#type').prop('disabled', false).val('');
        $('#category').prop('disabled', false).val('');
        $('#product').prop('disabled', false).val('');
        $('#supplier').prop('disabled', false).val('');
        $('#suppliersSI').prop('disabled', false).val('');
        $('#serialNo').prop('disabled', false).val('');   
        $('#purchaseDate').prop('disabled', false);
        $('#warranty').prop('disabled', false).val('');
        $('#imageName').prop('disabled', false).val('');
        $('#dateEncoded').prop('disabled', false);
        $('#dealersPrice').prop('disabled', false).val('');
        $('#srp').prop('disabled', false).val('');
        $('#quantity').prop('disabled', false).val('');
        $('#totalPrice').prop('readonly', true).val('');
        $('#totalSRP').prop('readonly', true).val('');
        $('#mpi').prop('readonly', true).val('');
        $('#totalmarkup').prop('readonly', true).val('');
        $('#addNew').prop('hidden', true);
        $('#addNew').prop('disabled', true);
        $('#cancel').prop('hidden', false);
        $('#cancel').prop('disabled', false);
        $('#addToList').prop('hidden', false);
        $('#addToList').prop('disabled', false);
        $('#save').prop('hidden', false);
        $('#save').prop('disabled', false);
        $('#editBtn').prop('hidden', true); // Ensure hidden
        // Ensure all form inputs are enabled (except readonly ones)
        $('#inventoryinForm input:not([readonly]), #inventoryinForm select, #inventoryinForm textarea').prop('disabled', false);
        $('#suppliersSI').prop('disabled', false); // Explicitly enable suppliersSI
        $('#supplier').prop('disabled', false); // Explicitly enable supplier
        $('#product').prop('disabled', false); // Explicitly enable product

        // $("#CustomerInfoTbl tbody tr").removeClass("selected");
        var now = new Date();
        var mm = (now.getMonth() > 8) ? (now.getMonth() + 1) : ('0' + (now.getMonth() + 1));
        var dd = (now.getDate() > 9) ? now.getDate() : ('0' + now.getDate());
        var yyyy = now.getFullYear();
        var todayStr = mm + '/' + dd + '/' + yyyy;
        $('#dateEncoded').val(todayStr);
        $('#purchaseDate').val(todayStr);
        if ($("#branch option").filter(function(){ return $(this).text().toUpperCase() === "HEAD OFFICE"; }).length > 0) {
            $('#branch').val("HEAD OFFICE");
        }
        if ($("#type option").filter(function(){ return $(this).text().toUpperCase() === "WITH VAT"; }).length > 0) {
            $('#type').val("WITH VAT");
            LoadProdCateg("WITH VAT");
        }
    });

    // Use event delegation on the document to ensure it works even if the table is redrawn
    $(document).on('click', '#dataInvTbl tr', function(e) {
        // Skip header row
        if ($(this).find('th').length > 0) return;

        // Debugging
        console.log("Row clicked!", this);
        
        let classList = e.currentTarget.classList;
        
        if (classList.contains('selected')) {
            classList.remove('selected');
            $("#DeleteFromDataInvBtn").attr("disabled", true);
            dataInvTblValue = "";
            SelectedFromDataInv = "";
            itemTbl.clear().draw();
            
            // Disable form if deselected
            $('#inventoryinForm input:not([readonly]), #inventoryinForm select, #inventoryinForm textarea').prop('disabled', true);
            $('#addNew').prop('hidden', false).prop('disabled', false);
            $('#save').prop('hidden', true);
            $('#cancel').prop('hidden', true);
            $('#editBtn').prop('hidden', true);

        } else {
            // Deselect others
            if ($.fn.DataTable.isDataTable('#dataInvTbl')) {
                $('#dataInvTbl').DataTable().rows('.selected').nodes().each((row) => {
                    row.classList.remove('selected');
                });
            }
            
            classList.add('selected');
            $("#DeleteFromDataInvBtn").attr("disabled", false);
            
            // Get data from the DOM directly to ensure reliability
            var cells = $(this).find('td');
            var getVal = (index) => cells.eq(index).text().trim();

            if (cells.length > 0) {
                // Extract values using DOM indices
                var d_SIno = getVal(0);
                var d_SerialNo = getVal(1);
                var d_Product = getVal(2);
                var d_Supplier = getVal(3);
                var d_Category = getVal(4);
                var d_Type = getVal(5);
                var d_Branch = getVal(6);
                var d_PurchaseDate = getVal(7);
                var d_Warranty = getVal(8);
                var d_DateEncoded = getVal(9);
                var d_Quantity = getVal(10);
                var d_DealerPrice = getVal(11);
                var d_SRP = getVal(12);
                var d_TotalPrice = getVal(13);
                var d_TotalSRP = getVal(14);
                var d_MPI = getVal(15);
                var d_TotalMarkup = getVal(16);
                var d_ImgName = getVal(20);

                console.log("Selected Row Data (DOM):", d_Product, d_SerialNo);
                
                itemTbl.clear().draw();
                itemTbl.row.add([
                    d_Product,
                    d_SerialNo,
                    d_Warranty,
                    d_DealerPrice,
                    d_SRP,
                    d_Quantity,
                    d_TotalPrice,
                    d_TotalSRP,
                    d_MPI,
                    d_TotalMarkup,
                    d_Branch,
                    d_Type,
                    d_Category,
                    d_Supplier,
                    d_SIno,
                    d_PurchaseDate,
                    d_ImgName,
                    d_DateEncoded,
                ]).draw(false);

                // Populate Form Fields
                $('#branch').val(d_Branch);
                $('#type').val(d_Type);
                
                // Handle Category Dropdown
                if ($('#category option[value="'+d_Category+'"]').length == 0) {
                        $('#category').append('<option value="'+d_Category+'">'+d_Category+'</option>');
                }
                $('#category').val(d_Category).trigger('change');

                $('#product').val(d_Product);
                $('#supplier').val(d_Supplier);
                $('#suppliersSI').val(d_SIno);
                $('#serialNo').val(d_SerialNo);
                $('#purchaseDate').val(d_PurchaseDate);
                $('#warranty').val(d_Warranty);
                $('#dateEncoded').val(d_DateEncoded);
                $('#dealersPrice').val(d_DealerPrice);
                $('#srp').val(d_SRP);
                $('#quantity').val(d_Quantity);
                
                Compute();

                // $('#totalPrice').val(d_TotalPrice);
                // $('#totalSRP').val(d_TotalSRP);
                // $('#mpi').val(d_MPI);
                // $('#totalmarkup').val(d_TotalMarkup);
                
                // Auto Compute Totals based on loaded values
                Compute();
                
                // DISABLE ALL FIELDS INITIALLY (Like Shareholder Info)
                $('#inventoryinForm input:not([readonly]), #inventoryinForm select, #inventoryinForm textarea').prop('disabled', true);

                // Enable/Show Edit Button, Hide Save
                isUpdate = true;
                $('#editBtn').prop('hidden', false).prop('disabled', false);
                $('#save').prop('hidden', true); 
                $('#cancel').prop('hidden', false).prop('disabled', false);
                $('#addNew').prop('hidden', true);
                $('#addToList').prop('hidden', true);
                
                // Store Original Keys
                origSIno = d_SIno;
                origSerial = d_SerialNo;
                origProduct = d_Product;
            }
        }    
    });

    $('#itemTbl tbody').on('click', 'tr',function(e){
        let classList = e.currentTarget.classList;
        if (classList.contains('selected')) {
            classList.remove('selected');
            $("#DeleteFromListBtn").attr("disabled",true);
            SelectedFromList = "";
        } else {
            itemTbl.rows('.selected').nodes().each((row) => {
                row.classList.remove('selected');
            });
            classList.add('selected');
            $("#DeleteFromListBtn").attr("disabled",false);
            SelectedFromList = this;
        }
    });

    $('#searchDataInv').on('keyup', function() {
        if ($.fn.DataTable.isDataTable('#dataInvTbl')) {
            $('#dataInvTbl').DataTable().search(this.value).draw();
        }
    });

});

function Initialize(){
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"Initialize"},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#branch").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.BRANCH,function(key,value){
                    $("#branch").append(`
                        <option value="${value["ItemName"]}">
                            ${value["ItemName"]}
                        </option>
                    `);
            });
            if ($("#branch option").filter(function(){ return $(this).text().toUpperCase() === "HEAD OFFICE"; }).length === 0) {
                $("#branch").append(`<option value="HEAD OFFICE">HEAD OFFICE</option>`);
            }

            $("#type").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.PRODTYPE,function(key,value){
                    $("#type").append(`
                        <option value="${value["Type"]}">
                            ${value["Type"]}
                        </option>
                    `);
            });
            if ($("#type option").filter(function(){ return $(this).text().toUpperCase() === "WITH VAT"; }).length === 0) {
                $("#type").append(`<option value="WITH VAT">WITH VAT</option>`);
            }
            if ($("#type option").filter(function(){ return $(this).text().toUpperCase() === "NON-VAT"; }).length === 0) {
                $("#type").append(`<option value="NON-VAT">NON-VAT</option>`);
            }

            var options = {
                value: new Date(),
                rtl: false,
                format: 'm/d/Y',
                timepicker: false,
                datepicker: true,
                startDate: false,
                closeOnDateSelect: false,
                closeOnTimeSelect: true,
                closeOnWithoutClick: true,
                closeOnInputClick: true,
                openOnFocus: true,
                mask: '99/99/9999',
                maxDate: new Date(),
            };

            $('#purchaseDate').datetimepicker(options);
            $('#dateEncoded').datetimepicker(options);
            $('#purchaseDate').on('change', function(){
                var val = $(this).val();
                if (val) {
                    var d = new Date(val);
                    var t = new Date();
                    if (d > t) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Future date not allowed',
                            text: 'Please select a valid date.',
                        });
                        var date = t;
                        $(this).val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());
                    }
                }
            });
            $('#dateEncoded').on('change', function(){
                var val = $(this).val();
                if (val) {
                    var d = new Date(val);
                    var t = new Date();
                    if (d > t) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Future date not allowed',
                            text: 'Please select a valid date.',
                        });
                        var date = t;
                        $(this).val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());
                    }
                }
            });

            var defaultType = $("#type option:not([disabled])").eq(0).val();
            if (defaultType) {
                $("#type").val(defaultType);
                LoadProdCateg(defaultType);
            }
        }, 
    })
}

function InitializeDataTable() {
    itemTbl = $('#itemTbl').DataTable({
        searching:false,
        ordering:false,
        info:false,
        paging:false,
        lengthChange:false,
        scrollY: '230px',
        scrollX: true,  
        scrollCollapse: true,
        responsive:false,
        columnDefs: [
            { targets: [ 3,4,5,6,7,8,9 ], className: 'dt-right' },
            // { targets: [ 4,5,6,7,8,9,10,11,12 ], visible:false, searchable:false }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            ListTtlDealerPrice = api.column(3).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            ListTtlSRP = api.column(4).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            
            ListTtlQty = api.column(5).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            ListTtlPrice = api.column(6).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            ListFnlTtlSRP = api.column(7).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            ListTtlMPI = api.column(8).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            ListFnlTtlMarkup = api.column(9).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            // Update footer
            $(api.column(3).footer()).html(formatAmtVal(ListTtlDealerPrice));
            $(api.column(4).footer()).html(formatAmtVal(ListTtlSRP));
            $(api.column(5).footer()).html(formatAmtVal(ListTtlQty));
            $(api.column(6).footer()).html(formatAmtVal(ListTtlPrice));
            $(api.column(7).footer()).html(formatAmtVal(ListFnlTtlSRP));
            $(api.column(8).footer()).html(formatAmtVal(ListTtlMPI));
            $(api.column(9).footer()).html(formatAmtVal(ListFnlTtlMarkup));
        },
    });
}

function LoadDataInventory(){
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"LoadDataInventory"},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable.isDataTable( '#dataInvTbl' ) ) {
                $('#dataInvTbl').DataTable().clear();
                $('#dataInvTbl').DataTable().destroy(); 
            }
        },
        success:function(response){

            if (response.DATAINVSINO.length > 0) {
                DataInvSINo = response.DATAINVSINO[0]['SINo'];
            }

            $("#dataInvList").empty();
            $.each(response.DATAINV,function(key,value){
                try {
                    $("#dataInvList").append(`
                        <tr>
                            <td>${value["SIno"] || ''}</td>
                            <td>${value["Serialno"] || ''}</td>
                            <td>${value["Product"] || ''}</td>
                            <td>${value["Supplier"] || ''}</td>
                            <td>${value["Category"] || ''}</td>
                            <td>${value["Type"] || ''}</td>
                            <td>${value["Branch"] || ''}</td>
                            <td>${value["DatePurchase"] || ''}</td>
                            <td>${value["Warranty"] || ''}</td>
                            <td>${value["DateAdded"] || ''}</td>
                            <td>${value["Quantity"] || ''}</td>
                            <td>${value["DealerPrice"] || ''}</td>
                            <td>${value["SRP"] || ''}</td>
                            <td>${value["TotalPrice"] || ''}</td>
                            <td>${value["TotalSRP"] || ''}</td>
                            <td>${value["Markup"] || ''}</td>
                            <td>${value["TotalMarkup"] || ''}</td>
                            <td>${value["VatSales"] || ''}</td>
                            <td>${value["Vat"] || ''}</td>
                            <td>${value["AmountDue"] || ''}</td>
                            <td>${value["imgname"] || ''}</td>
                        </tr>
                    `);
                } catch (e) {
                    console.error("Error appending row:", e, value);
                }
            });

            dataInvTbl = $('#dataInvTbl').DataTable({
                searching: true,
                dom: 'lrtip',
                ordering: false,
                info: false,
                paging: false, // Disable pagination
                scrollY: '230px', // Adjust height for 5 rows
                scrollX: true, // Enable horizontal scrolling
                scrollCollapse: true, // Allow table to shrink if less data is present
                lengthChange: false,
                responsive: false, // Disable responsive behavior for fixed scrolling
            });

            if (response.DATAINV.length > 0) {
                $('#printBtn').prop('disabled', false);
            } else {
                $('#printBtn').prop('disabled', true);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.log(xhr.responseText);
        } 
    })
}

function LoadProdCateg (type){
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"LoadProdCateg", type:type},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#category").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.PRODCATEG,function(key,value){
                    $("#category").append(`
                        <option value="${value["Category"]}">
                            ${value["Category"]}
                        </option>
                    `);
            });
            var requiredCategories = ["Battery","Cable","Cartridge","Connector"];
            requiredCategories.forEach(function(c){
                if ($("#category option").filter(function(){ return $(this).text() === c; }).length === 0) {
                    $("#category").append(`<option value="${c}">${c}</option>`);
                }
            });

            // Product and Supplier are free-text inputs, do not append options
            // $("#product").empty().append(`<option value="" disabled selected>Select</option>`);
            // $("#supplier").empty().append(`<option value="" disabled selected>Select</option>`);
        }, 
    })
}

function LoadProdName (categ){
    // Deprecated for free-text product
    /*
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"LoadProdName", categ:categ},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#product").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.PRODUCT,function(key,value){
                $("#product").append(
                    $('<option></option>')
                        .val(value["Product"])
                        .text(value["Product"])
                );
            });
            
            $("#supplier").empty().append(`<option value="" disabled selected>Select</option>`);
            var firstProduct = $("#product option:not([disabled])").eq(0).val();
            if (firstProduct) {
                $("#product").val(firstProduct);
                LoadSupplier(firstProduct);
            }
        }, 
    })
    */
}

function LoadSupplier (productname){
    // Deprecated for free-text supplier
    /*
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"LoadSupplier", productname:productname},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#supplier").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.SUPPLIER,function(key,value){
                $("#supplier").append(`
                    <option value="${value["Supplier"]}">
                        ${value["Supplier"]}
                    </option>
                `);
            });
            $('#supplier').off('change').on('change', function(){
                AutoFillPricing();
                LoadSupplierSI();
            });
            var firstSupplier = $("#supplier option:not([disabled])").eq(0).val();
            if (firstSupplier) {
                $("#supplier").val(firstSupplier).trigger('change');
            }
        }, 
    })
    */
}

function LoadSupplierSI(){
    var product = $("#product").val();
    var supplier = $("#supplier").val();
    if (!product || !supplier) return;
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"LoadSupplierSI", product:product, supplier:supplier},
        dataType:"JSON",
        success:function(response){
            $("#suppliersSI").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.SUPPLIERSI,function(key,value){
                $("#suppliersSI").append(`<option value="${value["SIno"]}">${value["SIno"]}</option>`);
            });
            var firstSI = $("#suppliersSI option:not([disabled])").eq(0).val();
            if (firstSI) {
                $("#suppliersSI").val(firstSI);
            }
            AutoFillPricing();
            Compute();
        }
    });
}

function AutoFillPricing(){
    var product = $("#product").val();
    var supplier = $("#supplier").val();
    if (!product || !supplier) return;
    $.ajax({
        url:"../../routes/inventorymanagement/incominginventory.route.php",
        type:"POST",
        data:{action:"GetProductPricing", product:product, supplier:supplier},
        dataType:"JSON",
        success:function(response){
            var p = response.PRICING || {};
            var d = p.DealerPrice ? formatAmtVal(p.DealerPrice) : $("#dealersPrice").val();
            var s = p.SRP ? formatAmtVal(p.SRP) : $("#srp").val();
            if (!s || s.trim() === '') { s = d; }
            $("#dealersPrice").val(d);
            $("#srp").val(s);
            Compute();
        }
    });
}

function Compute() {
    try {
        var dealerPriceStr = $('#dealersPrice').val() || '0';
        var dealerPrice = parseFloat(dealerPriceStr.replace(/,/g, '')) || 0;
        
        var srpField = $('#srp');
        var srpVal = srpField.val();
        if (!srpVal || srpVal.trim() === '') {
            srpField.val(formatAmtVal(dealerPrice));
            srpVal = srpField.val();
        }
        var srp = parseFloat(srpVal.replace(/,/g, '')) || 0;
        
        var qtyStr = $('#quantity').val() || '0';
        var quantity = parseFloat(qtyStr.replace(/,/g, '')) || 0;

        if (quantity < 0) {
            Swal.fire({
                icon: 'warning',
                text: 'Negative amount is not allowed. Try Again!',
            });
            $('#quantity').val('');
            return;
        }

        var Total = dealerPrice * quantity;
        var TotalSRP = srp * quantity;
        $('#totalPrice').val(formatAmtVal(Total));
        $('#totalSRP').val(formatAmtVal(TotalSRP));
        var mpi  = srp - dealerPrice;
        $('#mpi').val(formatAmtVal(mpi));
        var totalMarkup = TotalSRP - Total; 
        $('#totalmarkup').val(formatAmtVal(totalMarkup));
    } catch (err) {
        console.error("Compute error:", err);
    }
}

$('#addToList').on('click', function() {
    console.log(DataInvSINo);
    let branch = $('#branch').val();
    let type = $('#type').val();
    let categ = $('#category').val();
    let product = $('#product').val();
    let supplier = $('#supplier').val();
    let supplierSI = $('#suppliersSI').val();
    let serialNo = $('#serialNo').val(); 
    let purchaseDate = $('#purchaseDate').val();
    let warranty = $('#warranty').val();
    let imageName = $('#imageName').val();
    let dateEncoded = $('#dateEncoded').val();
    let dealerPrice = $('#dealersPrice').val();
    let srp = $('#srp').val();
    let quantity = $('#quantity').val();
    let totalPrice = $('#totalPrice').val();
    let totalSRP = $('#totalSRP').val();
    let mpi = $('#mpi').val();
    let totalMarkup = $('#totalmarkup').val();

    if (branch == null || type == null || categ == null || product == null || supplier == null || supplierSI == "" || serialNo == "" || purchaseDate == "" || warranty == "" || dateEncoded == "" || supplierSI == "" || dealerPrice == "" || srp == "" || quantity == "") {
        Swal.fire({
            icon: 'warning',
            text: 'Please enter required details.',
        });
        return;
    }

    itemTbl.row.add([
        product,
        serialNo,
        warranty,
        dealerPrice,
        srp,
        quantity,
        totalPrice,
        totalSRP,
        mpi,
        totalMarkup,
        branch,
        type,
        categ,
        supplier,
        supplierSI,
        purchaseDate,
        imageName,
        dateEncoded,
    ]).draw(false);

    $('#suppliersSI').prop('disabled',true);
    try { localStorage.setItem('incoming_itemTbl', JSON.stringify(itemTbl.rows().data().toArray())); } catch(e) {}

});

$('#addNew').on('click', function() {
    $('#branch').prop('disabled', false);
    $('#type').prop('disabled', false);
    $('#category').prop('disabled', false);
    $('#product').prop('disabled', false);
    $('#supplier').prop('disabled', false);
    $('#suppliersSI').prop('disabled', false);
    $('#serialNo').prop('disabled', false);   
    $('#purchaseDate').prop('disabled', false);
    $('#warranty').prop('disabled', false);
    $('#imageName').prop('disabled', false);
    $('#dateEncoded').prop('disabled', false);
    $('#dealersPrice').prop('disabled', false);
    $('#srp').prop('disabled', false);
    $('#quantity').prop('disabled', false);
    $('#totalPrice').prop('readonly', true);
    $('#totalSRP').prop('readonly', true);
    $('#mpi').prop('readonly', true);
    $('#totalmarkup').prop('readonly', true);
    $('#addNew').prop('hidden', true);
    $('#addNew').prop('disabled', true);
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#addToList').prop('hidden', false);
    $('#addToList').prop('disabled', false);
    $('#save').prop('hidden', false);
    $('#save').prop('disabled', false);
    // $("#CustomerInfoTbl tbody tr").removeClass("selected");
    var now = new Date();
    var mm = (now.getMonth() > 8) ? (now.getMonth() + 1) : ('0' + (now.getMonth() + 1));
    var dd = (now.getDate() > 9) ? now.getDate() : ('0' + now.getDate());
    var yyyy = now.getFullYear();
    var todayStr = mm + '/' + dd + '/' + yyyy;
    $('#dateEncoded').val(todayStr);
    $('#purchaseDate').val(todayStr);
    if ($("#branch option").filter(function(){ return $(this).text().toUpperCase() === "HEAD OFFICE"; }).length > 0) {
        $('#branch').val("HEAD OFFICE");
    }
    if ($("#type option").filter(function(){ return $(this).text().toUpperCase() === "WITH VAT"; }).length > 0) {
        $('#type').val("WITH VAT");
        LoadProdCateg("WITH VAT");
    }
});

function Cancel(){
    isUpdate = false;
    $('#branch').prop('disabled', true).val('');
    $('#type').prop('disabled', true).val('');
    $('#category').prop('disabled', true).val('');
    $('#product').prop('disabled', true).val('');
    $('#supplier').prop('disabled', true).val('');
    $("#category").empty().append(`<option value="" disabled selected>Select</option>`);
    // Product and Supplier are inputs, do not append options
    // $("#product").empty().append(`<option value="" disabled selected>Select</option>`);
    // $("#supplier").empty().append(`<option value="" disabled selected>Select</option>`);
    $('#suppliersSI').prop('disabled', true).val('');
    $('#serialNo').prop('disabled', true).val('');
    $('#purchaseDate').prop('disabled', true).val('');
    $('#warranty').prop('disabled', true).val('');
    $('#imageName').prop('disabled', true).val('');
    $('#dateEncoded').prop('disabled', true).val('');
    $('#dealersPrice').prop('disabled', true).val('');
    $('#srp').prop('disabled', true).val('');
    $('#quantity').prop('disabled', true).val('');
    $('#totalPrice').prop('readonly', true).val('');
    $('#totalSRP').prop('readonly', true).val('');
    $('#mpi').prop('readonly', true).val('');
    $('#totalmarkup').prop('readonly', true).val('');
    $('#addNew').prop('hidden', false).prop('disabled', false);
    $('#cancel').prop('hidden', true).prop('disabled', true);
    $('#addToList').prop('hidden', true).prop('disabled', true);
    $('#save').prop('hidden', true).prop('disabled', true);
    $('#editBtn').prop('hidden', true).prop('disabled', true);
    // $("#CustomerInfoTbl tbody tr").removeClass("selected");
    try { localStorage.removeItem('incoming_itemTbl'); } catch(e) {}
}

$('#dataInvTbl tbody').on('click', 'tr',function(e){
        // Debugging
        console.log("Row clicked!", this);
        
        let classList = e.currentTarget.classList;
        
        if (classList.contains('selected')) {
            classList.remove('selected');
            $("#DeleteFromDataInvBtn").attr("disabled",true);
            dataInvTblValue = "";
            SelectedFromDataInv = "";
            
            // Disable form if deselected
            $('#inventoryinForm input:not([readonly]), #inventoryinForm select, #inventoryinForm textarea').prop('disabled', true);
            $('#addNew').prop('hidden', false).prop('disabled', false);
            $('#save').prop('hidden', true);
            $('#cancel').prop('hidden', true);
            $('#editBtn').prop('hidden', true);
            
            itemTbl.clear().draw();

        } else {
            // Deselect others
            if ($.fn.DataTable.isDataTable('#dataInvTbl')) {
                $('#dataInvTbl').DataTable().rows('.selected').nodes().each((row) => {
                    row.classList.remove('selected');
                });
            } else {
                $('tr.selected').removeClass('selected');
            }
            
            classList.add('selected');
            $("#DeleteFromDataInvBtn").attr("disabled",false);
            SelectedFromDataInv = this;

            // Get data from the DOM directly to ensure reliability
            var cells = $(this).find('td');
            var getVal = (index) => cells.eq(index).text().trim();

            if (cells.length > 0) {
                // Extract values using DOM indices
                var d_SIno = getVal(0);
                var d_SerialNo = getVal(1);
                var d_Product = getVal(2);
                var d_Supplier = getVal(3);
                var d_Category = getVal(4);
                var d_Type = getVal(5);
                var d_Branch = getVal(6);
                var d_PurchaseDate = getVal(7);
                var d_Warranty = getVal(8);
                var d_DateEncoded = getVal(9);
                var d_Quantity = getVal(10);
                var d_DealerPrice = getVal(11);
                var d_SRP = getVal(12);
                var d_TotalPrice = getVal(13);
                var d_TotalSRP = getVal(14);
                var d_MPI = getVal(15);
                var d_TotalMarkup = getVal(16);
                var d_ImgName = getVal(20);

                console.log("Selected Row Data (DOM):", d_Product, d_SerialNo);
                
                // Populate Item Table (Bottom Table)
                itemTbl.clear().draw();
                itemTbl.row.add([
                    d_Product,
                    d_SerialNo,
                    d_Warranty,
                    d_DealerPrice,
                    d_SRP,
                    d_Quantity,
                    d_TotalPrice,
                    d_TotalSRP,
                    d_MPI,
                    d_TotalMarkup,
                    d_Branch,
                    d_Type,
                    d_Category,
                    d_Supplier,
                    d_SIno,
                    d_PurchaseDate,
                    d_ImgName,
                    d_DateEncoded,
                ]).draw(false);

                // Populate Form Fields
                $('#branch').val(d_Branch);
                $('#type').val(d_Type);
                
                // Handle Category Dropdown
                if ($('#category option[value="'+d_Category+'"]').length == 0) {
                        $('#category').append('<option value="'+d_Category+'">'+d_Category+'</option>');
                }
                $('#category').val(d_Category).trigger('change');

                $('#product').val(d_Product);
                $('#supplier').val(d_Supplier);
                $('#suppliersSI').val(d_SIno);
                $('#serialNo').val(d_SerialNo);
                $('#purchaseDate').val(d_PurchaseDate);
                $('#warranty').val(d_Warranty);
                $('#dateEncoded').val(d_DateEncoded);
                $('#dealersPrice').val(d_DealerPrice);
                $('#srp').val(d_SRP);
                $('#quantity').val(d_Quantity);
                
                // Auto Compute Totals based on loaded values
                Compute();
                
                // DISABLE ALL FIELDS INITIALLY (Like Shareholder Info)
                $('#inventoryinForm input:not([readonly]), #inventoryinForm select, #inventoryinForm textarea').prop('disabled', true);

                // Enable/Show Edit Button, Hide Save
                isUpdate = true;
                $('#editBtn').prop('hidden', false).prop('disabled', false);
                $('#save').prop('hidden', true); 
                $('#cancel').prop('hidden', false).prop('disabled', false);
                $('#addNew').prop('hidden', true);
                $('#addToList').prop('hidden', true);
                
                // Store Original Keys
                origSIno = d_SIno;
                origSerial = d_SerialNo;
                origProduct = d_Product;
            }
        }    
    });

function DeleteFromDataInv(){
    if  (SelectedFromDataInv != "") {
        var SINo = dataInvTblValue[0];
        var SerialNo = dataInvTblValue[1];
        var Product = dataInvTblValue[2];
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'This action will delete the selected data from the inventory. Are you sure you want to proceed?',
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/inventorymanagement/incominginventory.route.php",
                    type: "POST",
                    data: {action:"DeleteFromDataInv", SINo:SINo, SerialNo:SerialNo, Product:Product},
                    dataType: 'JSON',
                    beforeSend: function() {
                        console.log('Processing Request...')
                    },
                    success: function(response) {
                    },
                });
            },
        }).then(function(result) {
            if (result.isConfirmed) {
                if (result.value.STATUS == 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: result.value.MESSAGE,
                    });
                    LoadDataInventory();
                    // Cancel();
                    SelectedFromDataInv = "";
                    $("#DeleteFromDataInvBtn").attr("disabled",true);

                } else if (result.value.STATUS != 'success') {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.value.MESSAGE,
                    });
                }                
            }
        });
    } else {
        SelectedFromDataInv = "";
        Swal.fire({
            icon: 'warning',
            title: 'Please select from the Data Inventory to delete',
        })
    }
}

$('#itemTbl tbody').on('click', 'tr',function(e){
    let classList = e.currentTarget.classList;
    if (classList.contains('selected')) {
        classList.remove('selected');
        $("#DeleteFromListBtn").attr("disabled",true);
        SelectedFromList = "";
    } else {
        itemTbl.rows('.selected').nodes().each((row) => {
            row.classList.remove('selected');
        });
        classList.add('selected');
        $("#DeleteFromListBtn").attr("disabled",false);
        SelectedFromList = this;
    }
});

function DeleteFromList(){
    if  (SelectedFromList != "") {
        itemTbl.row(SelectedFromList).remove().draw(false);
        SelectedFromList = "";
        $("#DeleteFromListBtn").attr("disabled",true);
        try { localStorage.setItem('incoming_itemTbl', JSON.stringify(itemTbl.rows().data().toArray())); } catch(e) {}
    } else {
        SelectedFromList = "";
        Swal.fire({
            icon: 'warning',
            title: 'Please select from the Items to delete',
        })
    }
}

function Save(){
    console.log("Save clicked");
    $('#printBtn').prop('disabled', false);

    if (!$.fn.DataTable.isDataTable('#itemTbl')) {
         Swal.fire("Error", "Item table not initialized yet. Please refresh.", "error");
         return;
    }

    if (isUpdate) {
        let branch = $('#branch').val();
        let type = $('#type').val();
        let categ = $('#category').val();
        let product = $('#product').val();
        let supplier = $('#supplier').val();
        let supplierSI = $('#suppliersSI').val();
        let serialNo = $('#serialNo').val(); 
        let purchaseDate = $('#purchaseDate').val();
        let warranty = $('#warranty').val();
        let imageName = $('#imageName').val();
        let dateEncoded = $('#dateEncoded').val();
        let dealerPrice = $('#dealersPrice').val();
        let srp = $('#srp').val();
        let quantity = $('#quantity').val();
        let totalPrice = $('#totalPrice').val();
        let totalSRP = $('#totalSRP').val();
        let mpi = $('#mpi').val();
        let totalMarkup = $('#totalmarkup').val();

        let formData = new FormData();
        formData.append("action", "UpdateInventory");
        formData.append("branch", branch);
        formData.append("type", type);
        formData.append("categ", categ);
        formData.append("product", product);
        formData.append("supplier", supplier);
        formData.append("supplierSI", supplierSI);
        formData.append("serialNo", serialNo);
        formData.append("purchaseDate", purchaseDate);
        formData.append("warranty", warranty);
        formData.append("dateEncoded", dateEncoded);
        formData.append("dealerPrice", dealerPrice);
        formData.append("srp", srp);
        formData.append("quantity", quantity);
        formData.append("totalPrice", totalPrice);
        formData.append("totalSRP", totalSRP);
        formData.append("mpi", mpi);
        formData.append("totalMarkup", totalMarkup);
        formData.append("origSIno", origSIno);
        formData.append("origSerial", origSerial);
        formData.append("origProduct", origProduct);

        let fileInput = $('#imageName')[0];
        if (fileInput.files.length > 0) {
            formData.append("imageName", fileInput.files[0]);
        }

        Swal.fire({
            icon: 'info',
            title: 'Update product details?',
            showCancelButton: true,
            confirmButtonText: 'Yes, update!',
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/inventorymanagement/incominginventory.route.php",
                    type: "POST",
                    data: formData,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                });
            },
        }).then(function(result) {
            if (result.isConfirmed) {
                if (result.value.STATUS == 'success') {
                    Swal.fire("Success", result.value.MESSAGE, "success");
                    LoadDataInventory();
                    Cancel();
                } else {
                    Swal.fire("Error", result.value.MESSAGE, "error");
                }                
            }
        });
        return;
    }

    if(itemTbl.rows().count() === 0){
        // When Item List is empty, this will encode the details in Particulars
        let branch = $('#branch').val();
        let type = $('#type').val();
        let categ = $('#category').val();
        let product = $('#product').val();
        let supplier = $('#supplier').val();
        let supplierSI = $('#suppliersSI').val();
        let serialNo = $('#serialNo').val(); 
        let purchaseDate = $('#purchaseDate').val();
        let warranty = $('#warranty').val();
        let imageName = $('#imageName').val();
        let dateEncoded = $('#dateEncoded').val();
        let dealerPrice = $('#dealersPrice').val();
        let srp = $('#srp').val();
        let quantity = $('#quantity').val();
        let totalPrice = $('#totalPrice').val();
        let totalSRP = $('#totalSRP').val();
        let mpi = $('#mpi').val();
        let totalMarkup = $('#totalmarkup').val();

        if (branch == null || type == null || categ == null || product == null || supplier == null || supplierSI == "" || serialNo == "" || purchaseDate == "" || warranty == "" || dateEncoded == "" || supplierSI == "" || dealerPrice == "" || srp == "" || quantity == "") {
            Swal.fire({
                icon: 'warning',
                text: 'Please enter required details.',
            });
            return;
        }

        let formData = new FormData();
        formData.append("action", "SaveSingle");
        formData.append("branch", branch);
        formData.append("type", type);
        formData.append("categ", categ);
        formData.append("product", product);
        formData.append("supplier", supplier);
        formData.append("supplierSI", supplierSI);
        formData.append("serialNo", serialNo);
        formData.append("purchaseDate", purchaseDate);
        formData.append("warranty", warranty);
        formData.append("dateEncoded", dateEncoded);
        formData.append("dealerPrice", dealerPrice);
        formData.append("srp", srp);
        formData.append("quantity", quantity);
        formData.append("totalPrice", totalPrice);
        formData.append("totalSRP", totalSRP);
        formData.append("mpi", mpi);
        formData.append("totalMarkup", totalMarkup);

        let fileInput = $('#imageName')[0];
        if (fileInput.files.length > 0) {
            formData.append("imageName", fileInput.files[0]);
        }

        Swal.fire({
            icon: 'info',
            title: 'Ready to save the product details?',
            showCancelButton: true, // Add cancel button for better UX
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/inventorymanagement/incominginventory.route.php",
                    type: "POST",
                    // data: {action:"SaveSingle", branch:branch,type:type,categ:categ,product:product,supplier:supplier,supplierSI:supplierSI,serialNo:serialNo,purchaseDate:purchaseDate,warranty:warranty,imageName:imageName,dateEncoded:dateEncoded,dealerPrice:dealerPrice,srp:srp,quantity:quantity,totalPrice:totalPrice,totalSRP:totalSRP,mpi:mpi,totalMarkup:totalMarkup},
                    // dataType: 'JSON',
                    data: formData,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        console.log('Processing Request...')
                    },
                    success: function(response) {
                        return response;
                    },
                    error: function(xhr, status, error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                    }
                });
            },
        }).then(function(result) {
            if (result.isConfirmed) {
                var response = result.value;
                if (response && response.STATUS == 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: response.MESSAGE,
                    });
                    LoadDataInventory();
                    Cancel();
                } else if (response && response.STATUS != 'success') {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.MESSAGE || "Unknown error occurred",
                    });
                }                
            }
        });

    } else {
        // When Item List is not empty, this will encode the Item List and skip what's left in Particulars
        let Data = itemTbl.rows().data().toArray();
    
        let formdata = new FormData();
        formdata.append("action","SaveAll");
        formdata.append("DATA",JSON.stringify(Data));

        Swal.fire({
            icon: 'question',
            title: 'Ready to save all product details?',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Yes, proceed!',
            // allowOutsideClick: false,
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/inventorymanagement/incominginventory.route.php",
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: function() {
                        console.log('Processing Request...')
                    },
                    success: function(response) {
                        // Return response to be handled in .then()
                        return response; 
                    },
                    error: function(xhr, status, error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                    }
                });
            },
        }).then(function(result) {
            if (result.isConfirmed) {
                // Check if the result.value contains the response from AJAX
                var response = result.value;
                if (response && response.STATUS == 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: response.MESSAGE,
                    });
                    LoadDataInventory();
                    Cancel();
                    itemTbl.clear().draw(false);
                } else if (response && response.STATUS != 'success') {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.MESSAGE || "Unknown error occurred",
                    });
                } else {
                     // Fallback for cases where response might not be structured as expected
                     // or if the AJAX failed silently but didn't trigger error callback
                     console.log("Unexpected response:", response);
                }               
            }
        });

     }
}

function PrintSupplierSalesInvoice(){
    if(dataInvTbl.rows().count() === 0){
        // When Data Inv List is empty, this will halt the printing
        Swal.fire({
            icon:'warning',
            title: 'Nothing to print!',
        });
        return;
    } else {
        let Data = dataInvTbl.rows().data().toArray();
        let formdata = new FormData();
        formdata.append("action","PrintSupplierSalesInvoice");
        formdata.append("DATA",JSON.stringify(Data));

        $.ajax({
            url: "../../routes/inventorymanagement/incominginventory.route.php",
            type: "POST",
            data:formdata,
            processData:false,
            cache:false,
            contentType:false,
            dataType:"JSON",
            beforeSend: function() {
                console.log('Processing Request...')
            },
            success: function(response) {
                window.open("../../routes/inventorymanagement/incominginventory.route.php?type=PrintSuppRcpt");
                LoadDataInventory();
                $('#printBtn').prop('disabled', true);
                DataInvSINo = "";
            },
        });
    }
}

function formatAmtVal(value) {
    // Remove any characters that are not digits, commas, or periods
    let cleanValue = value.toString().replace(/[^0-9.,]/g, '');
    // Remove commas for formatting purposes
    cleanValue = cleanValue.replace(/,/g, '');
    if (cleanValue !== '') {
        // Parse the cleaned value to a float and ensure two decimal places
        let formattedValue = parseFloat(cleanValue).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return formattedValue; // Return the formatted value
    }    
    return '0.00'; // Return an empty string if input is invalid or empty
}

function formatInput(input) {
    // Get the value from the input field and remove invalid characters
    let cleanValue = input.value.replace(/[^0-9.,-]/g, '');

    // Check for negative values
    if (cleanValue.includes('-')) {
        // Show SweetAlert message
        Swal.fire({
            icon: 'error',
            title: 'Invalid Amount',
            text: 'Negative amounts are not allowed.',
            confirmButtonText: 'OK'
        });

        // Reset the input field
        input.value = '0.00';
        return;
    }

    // Remove commas for numeric processing
    cleanValue = cleanValue.replace(/,/g, '');

    if (cleanValue !== '') {
        // Parse the cleaned value to a float and ensure two decimal places
        let formattedValue = parseFloat(cleanValue).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        // Set the formatted value back to the input field
        input.value = formattedValue;
    } else {
        input.value = '0.00'; // If empty or invalid, set input to empty
    }
}
