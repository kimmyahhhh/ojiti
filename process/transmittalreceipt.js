var itemsTbl, itemsTblValue = "", SelectedFromItems = "", isConsign = "No", serialProductList = "", productSINoList = "", selectBy = "", mark = 0, Tmark = 0, Area = "-", Warranty = "-";

var branchConsignTbl, branchConsignTblValue = "", SelectedFromBranchConsign = "", ConsignBranchList, totalAddedConsignItem = 0;

var listTbl;
var productTbl, SelectedFromTransProd = "";

$("#category").select2({
    width: '100%',
});
$("#SerialProduct").select2({
    width: '100%',
});
$("#SINo").select2({
    width: '100%',
});

SetTransactionDate();

Initialize();

function SetTransactionDate(){
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
    };

    $('#transmittalDateFrom').datetimepicker(options);
    $('#transmittalDateTo').datetimepicker(options);
    $('#dateCarrier').datetimepicker(options);
    $('#dateReceivedBy').datetimepicker(options);

    // Swal.fire({
    //     title: 'Please Select Transaction Date.',
    //     html: '<input id="DateTransaction" readonly class="swal2-input">',
    //     confirmButtonText: 'Set',
    //     showLoaderOnConfirm: false,
    //     didOpen:function(e){
    //         $('#DateTransaction').datetimepicker(options);
    //     },
    //     allowOutsideClick: false,
    // }).then((result) => {
    //     if (result.isConfirmed) {

    //         if($("#DateTransaction").val() == ""){
    //             location.reload();
    //         }

    //         var date = new Date($("#DateTransaction").val());

    //         $("#transactionDate").val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());

    //         $("#transmittalDateFrom").val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());

    //         $("#transmittalDateTo").val(((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear());
    //     }
    // })
}

function Initialize(){
    $.ajax({
        url:"../../routes/inventorymanagement/transmittalreceipt.route.php",
        type:"POST",
        data:{action:"Initialize"},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#transmittalNo").val(response.TRANSMITNO);
            $("#fromRep").val(response.ORGNAME);

            $("#isynBranch").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.ISYNBRANCH,function(key,value){
                $("#isynBranch").append(`
                    <option value="${value["Stock"]}">
                        ${value["Stock"]}
                    </option>
                `);
            });

            $("#type").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.PRODTYPE,function(key,value){
                $("#type").append(`
                    <option value="${value["Type"]}">
                        ${value["Type"]}
                    </option>
                `);
            });

            itemsTbl = $('#itemsTbl').DataTable({
                searching:false,
                ordering:false,
                info:false,
                paging:false,
                lengthChange:false,
                scrollY: '230px',
                scrollX: true,  
                scrollCollapse: true,
                responsive:false,
                // columnDefs: [
                //     { targets: [ 0,1,2 ], visible:false, searchable:false }
                // ],
            });
        }, 
    })
}

function isConsignmentBox() {
    if ($("#isConsignment").is(":checked")) {
        isConsign = "Yes";
        $('#consignmentBranchContainer').show();
    } else {
        isConsign = "No";
        $('#consignmentBranchContainer').hide();
    }
    $("#isynBranch").val("");
    $("#consignmentBranch").empty().append(`<option value="" disabled selected>Select</option>`);
    $("#type").val("");
    $("#category").empty();
    $("#serialNo").prop("checked", false)
    $("#productName").prop("checked", false)
    $("#SerialProduct").empty();
    $("#SINo").empty();
    ClearProductSummary();
}

function LoadBranch(value){
    if ($("#isConsignment").is(":checked")) {
        $.ajax({
            url:"../../routes/inventorymanagement/transmittalreceipt.route.php",
            type:"POST",
            data:{action:"LoadBranch", value:value},
            dataType:"JSON",
            beforeSend:function(){
            },
            success:function(response){
                $("#consignmentBranch").empty().append(`<option value="" disabled selected>Select</option>`);
                $.each(response.BRANCH,function(key,value){
                    $("#consignmentBranch").append(`
                        <option value="${value["Stock"]}">
                            ${value["Stock"]}
                        </option>
                    `);
                });
    
            }, 
        })
    }
    $("#type").val("");
    $("#category").empty();
    $("#serialNo").prop("checked", false)
    $("#productName").prop("checked", false)
    $("#SerialProduct").empty();
    $("#SINo").empty();
    ClearProductSummary();
}

function forBranchClear(){
    $("#type").val("");
    $("#category").empty();
    $("#serialNo").prop("checked", false)
    $("#productName").prop("checked", false)
}

function LoadCategory(type){
    var isynBranch = $("#isynBranch").val();
    var consignBranch = $("#consignmentBranch").val();

    $.ajax({
        url:"../../routes/inventorymanagement/transmittalreceipt.route.php",
        type:"POST",
        data:{action:"LoadCategory", isConsign:isConsign, type:type, isynBranch:isynBranch, consignBranch:consignBranch},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#category").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.CATEG,function(key,value){
                $("#category").append(`
                    <option value="${value["Category"]}">
                        ${value["Category"]}
                    </option>
                `);
            });
        }, 
    })
    $("#serialNo").prop("checked", false)
    $("#productName").prop("checked", false)
    $("#SerialProduct").empty();
    $("#SINo").empty();
    ClearProductSummary();
}

function LoadSerialProduct(category){
    var type = $("#type").val();
    var isynBranch = $("#isynBranch").val();
    var consignBranch = $("#consignmentBranch").val();
    $("#productName").prop("checked", true)
    selectBy = "productName";

    $.ajax({
        url:"../../routes/inventorymanagement/transmittalreceipt.route.php",
        type:"POST",
        data:{action:"LoadSerialProduct", isConsign:isConsign, type:type, category:category, isynBranch:isynBranch, consignBranch:consignBranch},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            serialProductList = response.SRKPRDT;
            productSINoList = response.PRDTSINO;

            $("#SerialProduct").empty().append(`<option value="" disabled selected>Select</option>`);
            $("#SINo").empty().append(`<option value="" disabled selected>Select</option>`);
            $.each(response.SRKPRDT,function(key,value){
                $("#SerialProduct").append(
                    $('<option></option>')
                        .val(value["Product"])
                        .text(value["Product"])
                );
            });
        }, 
    })
    ClearProductSummary();
}

$("input[name='selectBy']").change(function(){
    $("#SerialProduct").empty().append(`<option value="" disabled selected>Select</option>`);
    $("#SINo").empty().append(`<option value="" disabled selected>Select</option>`);
    selectBy = $(this).val(); 
    if($(this).val() == "serial"){
        $.each(serialProductList,function(key,value){
            $("#SerialProduct").append(`
                <option value="${value["Serialno"]}">
                    ${value["Serialno"]}
                </option>
            `);
        });
    }else if($(this).val() == "productName"){
        $.each(serialProductList,function(key,value){
            $("#SerialProduct").append(
                $('<option></option>')
                    .val(value["Product"])
                    .text(value["Product"])
            );
        });
    }
    ClearProductSummary();
})

function LoadProductSINo (SerialProduct){
    $("#SINo").empty().append(`<option value="" disabled selected>Select</option>`);
    $.each(serialProductList,function(key,value){
        if(value["Serialno"] == SerialProduct || value["Product"] == SerialProduct){
            $("#SINo").append(`
                <option value="${value["SIno"]}">
                    ${value["SIno"]}
                </option>
            `);
        }
    });
    ClearProductSummary();
}

function LoadProductSummary(){
    var isynBranch = $("#isynBranch").val();
    var consignBranch = $("#consignmentBranch").val();
    var type = $("#type").val();
    var category = $("#category").val();
    var serialProduct = $("#SerialProduct").val();
    var SINo = $("#SINo").val();

    $.ajax({
        url:"../../routes/inventorymanagement/transmittalreceipt.route.php",
        type:"POST",
        data:{action:"LoadProductSummary", isConsign:isConsign, selectBy:selectBy, type:type, category:category, serialProduct:serialProduct, SINo:SINo, isynBranch:isynBranch, consignBranch:consignBranch},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            let info =  response.PSUMMARY;
            $('#psSupplierSI').val(info["SIno"]);
            $('#psSerialNo').val(info["Serialno"]);
            $('#psProduct').val(info["Product"]);
            $('#psSupplier').val(info["Supplier"]);
            $('#psSRP').val(formatAmtVal(info["SRP"]));
            $('#srpMerchSales').val(formatAmtVal(info["SRP"]));
            $('#psQuantity').val(info["Quantity"]);
            $('#psDealerPrice').val(formatAmtVal(info["DealerPrice"]));
            $('#psTotalPrice').val(formatAmtVal(info["TotalPrice"]));
            $('#finalSRP').val(formatAmtVal(info["SRP"]));
            Warranty = info["Warranty"];
        }, 
    })
}

function ComputeAvailQty(qty){
    var psQty = $('#psQuantity').val();

    if (qty == 0 || qty == null || qty == "") {
        Swal.fire({
            icon: "warning",
            title: "Please enter desired quantity.",
        });
        return false;
    }

    if (parseFloat(qty) > parseFloat(psQty)) {  
        Swal.fire({
            icon: "warning",
            title: "Insufficient available product quantity.",
        });
        $('#saleQty').val("");
        return false;
    }
}

function isEditSRPBTN() {
    if ($("#isEditSRP").is(":checked")) {
        $('#finalSRP').prop("disabled", false);
    } else {
        let  actualSRP = $('#psSRP').val();
        $('#finalSRP').prop("disabled", true);
        $('#finalSRP').val(actualSRP);
    }
}

function ClearProductSummary(){
    $('#psSupplierSI').val("");
    $('#psSerialNo').val("");
    $('#psProduct').val("");
    $('#psSupplier').val("");
    $('#psSRP').val("");
    $('#psQuantity').val("");
    $('#psDealerPrice').val("");
    $('#psTotalPrice').val("");
    $('#saleQty').val("");
    $('#finalSRP').val("");
}

function ClearAllFields(){
    isConsign = "No";    
    $("#isConsignment").prop("checked", false)
    $('#consignmentBranchContainer').hide();
    $("#isynBranch").val("");
    $("#consignmentBranch").empty().append(`<option value="" disabled selected>Select</option>`);
    $("#type").val("");
    $("#category").empty();
    $("#serialNo").prop("checked", false)
    $("#productName").prop("checked", false)
    $("#SerialProduct").empty();
    $("#SINo").empty();
    ClearProductSummary();
}

// =======================================================================================

function addItem() {
    let transactionDate = $('#transactionDate').val();
    let isConsignment = "-";
    let branch = "-";
    if ($('#isConsignment').is(':checked')){
        branch = $('#consignmentBranch').val();
    } else {
        branch = $('#isynBranch').val();
    }
    let type = $('#type').val();
    let categ = $('#category').val();
    let serialProduct = $('#SerialProduct').val();
    let SINo = $('#SINo').val();

    let supplierSI = $('#psSupplierSI').val();
    let serialNo = $('#psSerialNo').val();
    let productName = $('#psProduct').val();
    let supplierName = $('#psSupplier').val();
    let psSRP = $('#psSRP').val().replace(/,/g, '');
    let psQty = $('#psQuantity').val();
    let psDealerPrice = $('#psDealerPrice').val().replace(/,/g, '');
    let psTotalPrice = $('#psTotalPrice').val().replace(/,/g, '');
    
    let saleQty = $('#saleQty').val();
    let editSRP = $('#finalSRP').val().replace(/,/g, '');

    let total = 0;
    if ($('#isEditSRP').is(':checked')){
        total = parseFloat(saleQty) * parseFloat(editSRP);
    } else {
        total = parseFloat(saleQty) * parseFloat(psSRP);
    }

    let ProductSerialNo = "";
    if (serialNo == "-"){
        ProductSerialNo = productName;
    } else {
        ProductSerialNo = productName + "S/N:" + serialNo;
    }

    if (isynBranch == null || type == null || categ == null || serialProduct == null || SINo == null) {
        Swal.fire({
            icon: 'warning',
            text: 'Please complete Product Information.',
        });
        return;
    }
    
    if (saleQty == "" || saleQty == null) {
        Swal.fire({
            icon: 'warning',
            text: 'Please enter desired quantity amount.',
        });
        return;
    }

    var values = [supplierSI, serialNo, categ, type, branch];
    var indexes = [3, 4, 7, 8, 9];

    // Get all rows' data
    var rows = itemsTbl.rows().data().toArray();

    // Check each row
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var allMatch = true;

        // Compare each value with the corresponding column in this row
        for (var j = 0; j < values.length; j++) {
            var columnValue = row[indexes[j]];
            var searchValue = values[j];

            // Case-insensitive comparison; modify as needed
            if (columnValue.toString().toLowerCase() !== searchValue.toString().toLowerCase()) {
                allMatch = false;
                break;
            }
        }

        if (allMatch) {
            Swal.fire({
                icon: 'warning',
                title: 'Item is already added, please check.',
            })
            return;
        }
    }
    
    itemsTbl.row.add([
        ProductSerialNo,
        saleQty,
        formatAmtVal(total),
        supplierSI,
        serialNo,
        productName,
        supplierName,
        categ,
        type,
        branch,
    ]).draw(false);

    ClearAllFields();
};

$('#itemsTbl tbody').on('click', 'tr',function(e){
    if(itemsTbl.rows().count() !== 0){
        let classList = e.currentTarget.classList;
        if (classList.contains('selected')) {
            classList.remove('selected');
            $("#cancelProduct").attr("disabled",true);
            itemTblValue = "";
            SelectedFromItems = "";
        } else {
            itemsTbl.rows('.selected').nodes().each((row) => {
                row.classList.remove('selected');
            });
            classList.add('selected');
            $("#cancelProduct").attr("disabled",false);
            itemsTblValue = $('#itemsTbl').DataTable().row(this).data();
            SelectedFromItems = this;
        }
    }
});

function cancelProduct(){
    if  (SelectedFromItems != "") {
        itemsTbl.row(SelectedFromItems).remove().draw(false);
        SelectedFromItems = "";
        $("#cancelProduct").attr("disabled",true);
    } else {
        SelectedFromItems = "";
        Swal.fire({
            icon: 'warning',
            title: 'Please select from the Items to delete',
        })
    }
}

function ClearPurchasedBy(){
    $('#customerType').val("").prop('disabled', false);
    $('#customerNameInputDiv').show();
    $('#customerNameInput').prop('disabled', false);
    $('#customerNameInput').prop('readonly', true);
    $('#customerNameInput').val("");
    $('#customerNameSelectDiv').hide();
    $('#customerNameSelect').val("").prop('disabled', false);

    $("#staffCheckbox").prop("checked", false);
    $("#branchUsed").prop("checked", false);
    $("mfiUsed").prop("checked", false);
    $("#staffCheckbox").prop("disabled", false);
    $("#branchUsed").prop("disabled", false);
    $("mfiUsed").prop("disabled", false);

    $('#tinNoinput').val("").prop('disabled', false);
    $('#fullAddress').val("").prop('disabled', false);
    $('#status').val("").prop('disabled', false);
}

function parseDate(dateStr) {
    let [month, day, year] = dateStr.split("/").map(Number);
    return new Date(year, month - 1, day); // Month is 0-based in JS
}

function SubmitBtn(){
    var clientTo = $('#toRep').val();

    if (clientTo == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Please enter a recipient name',
        })
        return;
    }

    if ($('#isOtherDetails').is(':checked')){
        var carrier = $('#carrier').val();
        var receiver = $('#receivedBy').val();
        if (carrier == "" || receiver == ""){
            Swal.fire({
                icon: 'warning',
                title: 'Please fill in the Carrier and Received By fields',
            })
            return;
        }
    }

    if(itemsTbl.rows().count() !== 0){
        let Data = itemsTbl.rows().data().toArray();
        let formdata = new FormData();
        formdata.append("action","SubmitInvOut");
        formdata.append("DATA",JSON.stringify(Data));

        Swal.fire({
            icon: 'info',
            title: 'Proceed to submit entries?',
            showCancelButton: true,
            // allowOutsideClick: false,
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/inventorymanagement/transmittalreceipt.route.php",
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
                        if(response.STATUS != "success"){
                            Swal.showValidationMessage(
                                response.MESSAGE,
                            )
                        }
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
                    window.open("../../routes/inventorymanagement/transmittalreceipt.route.php?type=PrintSalesInvoice");
                    LoadTransaction();
                    ClearAllFields();
                    ClearPurchasedBy();
                    Initialize();
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
        Swal.fire({
            icon: 'warning',
            title: 'No Entries available.',
        });
     }
}

// =======================================================================================

function SearchTransmittal(){    
    $("#listList").empty();

    $("#productList").empty();

    $("#rePrint").prop("disabled", true);

    $("#SearchTransmittalMDL").modal("show");
}

function TransmittalSearch(){
    var dateFrom = $("#transmittalDateFrom").val();
    var dateTo = $("#transmittalDateTo").val();

    if (dateFrom == "" || dateTo == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Please select date from and date to.',
        })
        return;
    }

    if (parseDate(dateTo) < parseDate(dateFrom)) {
        Swal.fire({
            icon: 'warning',
            title: 'Date To is lower than Date From.',
        })
        return;
    }

    $.ajax({
        url:"../../routes/inventorymanagement/transmittalreceipt.route.php",
        type:"POST",
        data:{action:"TransmittalSearch",dateFrom:dateFrom,dateTo:dateTo},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable.isDataTable( '#listTbl' ) ) {
                $('#listTbl').DataTable().clear();
                $('#listTbl').DataTable().destroy(); 
            }

            if ( $.fn.DataTable.isDataTable( '#productTbl' ) ) {
                $('#productTbl').DataTable().clear();
                $('#productTbl').DataTable().destroy(); 
            }
            $("#productList").empty();

            $("#loadToList").prop("disabled", true);
        },
        success:function(response){
            $("#listList").empty();
            $.each(response.TRANSACTIONLIST,function(key,value){
                $("#listList").append(`
                    <tr>
                        <td>${value["TransmittalNO"]}</td>
                        <td>${value["NameTO"]}</td>
                        <td>${value["DatePrepared"]}</td>
                        <td>${value["isOUT"]}</td>
                        <td>${value["SalesInvoice"]}</td>
                    </tr>
                `);
            });

            listTbl = $('#listTbl').DataTable({
                searching:false,
                ordering:false,
                info:false,
                paging:false,
                lengthChange:false,
                scrollY: '230px',
                scrollX: true,
                scrollCollapse: true,
                responsive:false,
            });
        }, 
    })
}

$('#listTbl tbody').on('click', 'tr',function(e){
    if(listTbl.rows().count() !== 0){
        let classList = e.currentTarget.classList;
        if (classList.contains('selected')) {
            classList.remove('selected');
            $("#rePrint").prop("disabled", true);
            $("#productList").empty();
        } else {
            listTbl.rows('.selected').nodes().each((row) => {
                row.classList.remove('selected');
            });
            classList.add('selected');
            var val = $('#listTbl').DataTable().row(this).data();
            var transNo = val[0];
            var clientName = val[1];
            var date = val[2];
            $("#rePrint").prop("disabled", false);
        }
    }
});

// =======================================================================================

function isOtherDetailsBox() {
    if ($("#isOtherDetails").is(":checked")) {
        $("#carrier").prop("disabled", false);
        $("#dateCarrier").prop("disabled", false);
        $("#receivedBy").prop("disabled", false);
        $("#dateReceivedBy").prop("disabled", false);
    } else {
        $("#carrier").prop("disabled", true);
        $("#dateCarrier").prop("disabled", true);
        $("#receivedBy").prop("disabled", true);
        $("#dateReceivedBy").prop("disabled", true);
    }
}
// =======================================================================================

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