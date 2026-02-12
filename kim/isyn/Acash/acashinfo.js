var AcashInfoTbl;
var EcpayTxnTbl;

$(document).ready(function() {
    LoadAcashInfo();
    LoadEcpayTransactions();
    
    // Load Custom and Raw tables
    LoadAcashInfoCustom();
    LoadAcashInfoRaw();
    LoadEcpayTransactionsCustom();
    LoadEcpayTransactionsRaw();
    LoadEcpayTransactionsEcpay();

    $('#ecpay-category-filter').change(function(){
        LoadEcpayTransactionsEcpay();
    });

    // Load dynamic filter values (Branches)
    LoadFilterValues();

    // Load Emails for Maintenance Tab
    LoadEmails();
    LoadBranches();
    
    // Reload emails when Maintenance tab is shown
    $('#maintenance-tab').on('shown.bs.tab', function (e) {
        LoadEmails();
        LoadBranches();
        LoadMaintenanceTable();
    });
    
    // Add Maintenance Button Click
    $('#btn-add-maintenance').click(function() {
        var email = $('#email-search-dropdown').val();
        var branch = $('#branch-search-dropdown').val();
        var ownerName = $('#owner-name-input').val();
        
        if (!email || !branch) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Input',
                text: 'Please select both an Email and a Branch.'
            });
            return;
        }
        
        $.ajax({
            url: "../../routes/profiling/acashinfo.route.php",
            type: "POST",
            data: { 
                action: "SaveMaintenanceData",
                email: email,
                branch: branch,
                owner_name: ownerName
            },
            dataType: "JSON",
            success: function(response) {
                if (response.STATUS === "SUCCESS") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.MESSAGE,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Refresh the table and clear inputs
                    LoadMaintenanceTable();
                    LoadEmails(); // Refresh email list to remove the one just added
                    
                    // Reload all transaction tables to reflect the changes immediately
                    LoadAcashInfo(); 
                    LoadEcpayTransactions();
                    LoadAcashInfoCustom();
                    LoadAcashInfoRaw();
                    LoadEcpayTransactionsCustom();
                    LoadEcpayTransactionsRaw();
                    LoadEcpayTransactionsEcpay();

                    $('#email-search-dropdown').val('').trigger('change');
                    $('#branch-search-dropdown').val('').trigger('change');
                    $('#owner-name-input').val('');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.MESSAGE
                    });
                }
            },
            error: function(xhr) {
            console.error("SaveMaintenanceData error", xhr);
            Swal.fire({
                icon: 'error',
                title: 'System Error',
                text: 'Failed to save data. Check console for details.'
            });
        }
    });
});

// Row Click to Edit
$(document).on('click', '#maintenance-list tr', function() {
    // Get data from cells
    var email = $(this).find('td:eq(0)').text();
    var ownerName = $(this).find('td:eq(1)').text();
    var branch = $(this).find('td:eq(2)').text();
    
    // Only proceed if we have valid data (ignore "No mapped data found" row)
    if (!email || email === "No mapped data found") return;
    
    var emailDropdown = $('#email-search-dropdown');
    var branchDropdown = $('#branch-search-dropdown');
    
    // Set Email
    // Since mapped emails are removed from the list, we need to add this one back temporarily
    if (emailDropdown.find("option[value='" + email + "']").length) {
        emailDropdown.val(email).trigger('change');
    } else { 
        // Create a new option if it doesn't exist
        var newOption = new Option(email, email, true, true);
        emailDropdown.append(newOption).trigger('change');
    }
    
    // Set Branch
    branchDropdown.val(branch).trigger('change');
    
    // Set Owner Name
    $('#owner-name-input').val(ownerName);
});

// Initialize tables if not loaded via functions (fallback, though functions should handle it)
    // InitializeOtherTables(); // Disabled to prevent race condition with AJAX loads

    // Helper to show notification
    function showUploadNotification(message, type) {
        var notif = $("#upload-status-notification");
        var text = $("#upload-status-text");
        var spinner = $("#upload-spinner");
        var successIcon = $("#upload-success-icon");
        var errorIcon = $("#upload-error-icon");

        text.text(message);
        notif.removeClass("alert-info alert-success alert-danger");
        spinner.addClass("d-none");
        successIcon.addClass("d-none");
        errorIcon.addClass("d-none");

        if (type === 'loading') {
            notif.addClass("alert-info");
            spinner.removeClass("d-none");
            notif.fadeIn();
        } else if (type === 'success') {
            notif.addClass("alert-success");
            successIcon.removeClass("d-none");
            setTimeout(function() { notif.fadeOut(); }, 5000);
        } else if (type === 'error') {
            notif.addClass("alert-danger");
            errorIcon.removeClass("d-none");
            setTimeout(function() { notif.fadeOut(); }, 7000);
        }
    }

    // Unified Upload Handler
    $('#btn-upload-file').click(function(){
        var fileInput = $('#upload_file')[0];
        if (fileInput.files.length === 0) {
            showUploadNotification("Please select a file.", "error");
            return;
        }

        var file = fileInput.files[0];
        var fileName = file.name;
        var validExtensions = [".xlsx", ".csv"];
        var fileExtension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();

        if ($.inArray(fileExtension, validExtensions) == -1) {
            showUploadNotification("Invalid file type. Please upload only .xlsx or .csv files.", "error");
            return;
        }

        var category = $('#upload_category').val();
        var action = (category === 'Raw') ? 'UploadRaw' : 'UploadCustom';
        
        var formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('action', action);
        formData.append('category', category); // Pass category for custom handling

        // Close modal
        var modalEl = document.getElementById('uploadModal');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        $.ajax({
            url: "../../routes/profiling/acashinfo.route.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function(){
                showUploadNotification("Uploading file...", "loading");
            },
            success: function(response){
                if(response.STATUS == "SUCCESS"){
                    showUploadNotification(response.MESSAGE || "Upload Successful", "success");
                    // Reload all tables to be safe
                    LoadAcashInfo(); 
                    LoadEcpayTransactions();
                    LoadAcashInfoCustom();
                    LoadAcashInfoRaw();
                    LoadEcpayTransactionsCustom();
                    LoadEcpayTransactionsRaw();
                    LoadEcpayTransactionsEcpay();
                } else {
                    showUploadNotification("Upload Failed: " + response.MESSAGE, "error");
                }
            },
            error: function(xhr){
                console.error("Upload error", xhr);
                var msg = "Server Error";
                if(xhr.responseJSON && xhr.responseJSON.MESSAGE){
                    msg = xhr.responseJSON.MESSAGE;
                } else if (xhr.statusText === "parsererror" || xhr.status === 200) {
                     var snippet = xhr.responseText ? xhr.responseText.substring(0, 100) : "Empty Response";
                     snippet = snippet.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                     msg += " (Invalid JSON: " + snippet + "...)";
                } else if (xhr.statusText) {
                     msg += " (" + xhr.status + " " + xhr.statusText + ")";
                }
                showUploadNotification("Upload Failed: " + msg, "error");
            }
        });
        
        // Reset input
        $('#upload_file').val('');
    });

    // Analytics Event Listeners - REMOVED

});

function LoadAcashInfoCustom(){
    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadAcashInfo", type:"Custom", limit: 5000},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable && $.fn.DataTable.isDataTable( '#AcashInfoTblCustom' ) ) {
                $('#AcashInfoTblCustom').DataTable().clear();
                $('#AcashInfoTblCustom').DataTable().destroy();
            }
        },
        success:function(response){
            $("#AcashInfoListCustom").empty();
            if(response && response.ACASHINFO){
                $.each(response.ACASHINFO,function(key,value){
                    $("#AcashInfoListCustom").append(`
                        <tr>
                            <td>${value["CDate"] || ""}</td>
                            <td>${value["Branch"] || ""}</td>
                            <td>${value["Fund"] || ""}</td>
                            <td>${value["AcctNo"] || value["Identity"] || ""}</td>
                            <td>${value["AcctTitle"] || value["OwnerName"] || ""}</td>
                        </tr>
                    `);
                });
            }
            InitializeTable('#AcashInfoTblCustom');
        }
    });
}

function LoadAcashInfoRaw(){
    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadAcashInfo", type:"Raw", limit: 5000},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable && $.fn.DataTable.isDataTable( '#AcashInfoTblRaw' ) ) {
                $('#AcashInfoTblRaw').DataTable().clear();
                $('#AcashInfoTblRaw').DataTable().destroy();
            }
        },
        success:function(response){
            $("#AcashInfoListRaw").empty();
            if(response && response.ACASHINFO){
                $.each(response.ACASHINFO,function(key,value){
                    $("#AcashInfoListRaw").append(`
                        <tr>
                            <td>${value["CDate"] || ""}</td>
                            <td>${value["Branch"] || ""}</td>
                            <td>${value["Fund"] || ""}</td>
                            <td>${value["AcctNo"] || value["Identity"] || ""}</td>
                            <td>${value["AcctTitle"] || value["OwnerName"] || ""}</td>
                        </tr>
                    `);
                });
            }
            InitializeTable('#AcashInfoTblRaw');
        }
    });
}

function LoadEcpayTransactionsCustom(){
    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadEcpayTransactions", type:"Custom", limit: 5000},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable && $.fn.DataTable.isDataTable( '#EcpayTxnTblCustom' ) ) {
                $('#EcpayTxnTblCustom').DataTable().clear();
                $('#EcpayTxnTblCustom').DataTable().destroy();
            }
        },
        success:function(response){
            $("#EcpayTxnListCustom").empty();
            if(response && response.ECPAYTXNS){
                $.each(response.ECPAYTXNS,function(key,value){
                     var amount = value["DrOther"] || value["CrOther"] || "";
                    $("#EcpayTxnListCustom").append(`
                        <tr>
                            <td>${value["CDate"] || ""}</td>
                            <td>${value["Branch"] || ""}</td>
                            <td>${value["Identity"] || ""}</td>
                            <td>${value["OwnerName"] || ""}</td>
                            <td>${amount}</td>
                        </tr>
                    `);
                });
            }
            InitializeTable('#EcpayTxnTblCustom');
        }
    });
}

function LoadEcpayTransactionsRaw(){
    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadEcpayTransactions", type:"Raw", limit: 5000},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable && $.fn.DataTable.isDataTable( '#EcpayTxnTblRaw' ) ) {
                $('#EcpayTxnTblRaw').DataTable().clear();
                $('#EcpayTxnTblRaw').DataTable().destroy();
            }
        },
        success:function(response){
            $("#EcpayTxnListRaw").empty();
            if(response && response.ECPAYTXNS){
                $.each(response.ECPAYTXNS,function(key,value){
                     var amount = value["DrOther"] || value["CrOther"] || "";
                    $("#EcpayTxnListRaw").append(`
                        <tr>
                            <td>${value["CDate"] || ""}</td>
                            <td>${value["Branch"] || ""}</td>
                            <td>${value["Identity"] || ""}</td>
                            <td>${value["OwnerName"] || ""}</td>
                            <td>${amount}</td>
                        </tr>
                    `);
                });
            }
            InitializeTable('#EcpayTxnTblRaw');
        }
    });
}

function LoadEcpayTransactionsEcpay(){
    var category = $('#ecpay-category-filter').val();
    var table = $('#EcpayTxnTblEcpay');
    var thead = table.find('thead tr');
    var tbody = $("#EcpayTxnListEcpay");

    if (!category) {
        if ($.fn.DataTable.isDataTable('#EcpayTxnTblEcpay')) {
            table.DataTable().clear().draw();
        } else {
             tbody.empty();
        }
        return;
    }

    // Define headers based on category
    var headers = [];
    if (category === 'LOADS') {
        headers = ['Date', 'Branch', 'User', 'Telco', 'Variant', 'Mobile No', 'Trace No', 'Amount Transacted', 'Amount Deducted', 'Commission', 'Convenience Fee', 'Total', 'Status', 'month', 'year'];
    } else if (category === 'PAYBILLS') {
        headers = ['Date', 'Branch', 'User', 'Biller', 'Account No', 'Reference No', 'Amount Transacted', 'Amount Deducted', 'Commission', 'Convenience Fee', 'Total', 'Status', 'month', 'year'];
    } else if (category === 'SERVICES') {
        headers = ['Date', 'Branch', 'User', 'Service', 'AccountNo', 'Reference', 'ServiceRef', 'Amount Transacted', 'Amount Deducted', 'Commission', 'Service Charge', 'Total', 'Status', 'month', 'year'];
    }

    // Reset Table
    if ( $.fn.DataTable.isDataTable( '#EcpayTxnTblEcpay' ) ) {
        table.DataTable().clear();
        table.DataTable().destroy();
    }
    
    // Completely Rebuild Table Structure to avoid column count mismatch
    table.empty();
    
    var theadHtml = '<thead><tr>';
    headers.forEach(h => { theadHtml += `<th>${h}</th>`; });
    theadHtml += '</tr></thead>';
    
    table.append(theadHtml);
    table.append('<tbody id="EcpayTxnListEcpay"></tbody>');
    
    var tbody = $("#EcpayTxnListEcpay"); // Re-select tbody after rebuild

    var limit = (category === 'PAYBILLS') ? 10 : 5000;

    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadEcpayTransactions", type:"ECPAY", limit: limit, category: category},
        dataType:"JSON",
        success:function(response){
            tbody.empty();
            if(response && response.ECPAYTXNS){
                $.each(response.ECPAYTXNS,function(key,value){
                    var row = '<tr>';
                    // Map values based on category
                    if (category === 'LOADS') {
                 // Format Date to m/d/Y H:i if possible
                 var displayDate = value["CDate"] || "";
                 if (displayDate) {
                     var d = new Date(displayDate);
                     if (!isNaN(d.getTime())) {
                         var mo = d.getMonth() + 1;
                         var da = d.getDate();
                         var yr = d.getFullYear();
                         var hr = d.getHours();
                         var mn = d.getMinutes();
                         mn = mn < 10 ? '0' + mn : mn;
                         // Handle 24h format from backend to what user likely wants (24h or 12h? Screenshot shows 20:49 which is 24h)
                         displayDate = mo + '/' + da + '/' + yr + ' ' + hr + ':' + mn;
                     }
                 }
                 row += `<td>${displayDate}</td>`;
                 row += `<td>${value["Branch"] || ""}</td>`;
                         row += `<td>${value["User"] || ""}</td>`;
                         row += `<td>${value["Telco"] || ""}</td>`;
                         row += `<td>${value["Variant"] || ""}</td>`;
                         row += `<td>${value["MobileNo"] || ""}</td>`;
                         row += `<td>${value["TraceNo"] || ""}</td>`;
                         row += `<td>${value["DrOther"] || "0.00"}</td>`; // Amount Transacted
                         row += `<td>${value["CrOther"] || "0.00"}</td>`; // Amount Deducted
                         row += `<td>${value["Commission"] || "0.00"}</td>`;
                         row += `<td>${value["ConvenienceFee"] || "0.00"}</td>`;
                         row += `<td>${value["TransactionTotal"] || "0.00"}</td>`;
                         row += `<td>${value["Status"] || ""}</td>`;
                         row += `<td>${value["TransactionMonth"] || ""}</td>`;
                         row += `<td>${value["TransactionYear"] || ""}</td>`;
                    } else if (category === 'PAYBILLS') {
                         row += `<td>${value["CDate"] || ""}</td>`;
                         row += `<td>${value["Branch"] || ""}</td>`;
                         row += `<td>${value["User"] || ""}</td>`;
                         row += `<td>${value["Biller"] || ""}</td>`;
                         row += `<td>${value["AccountNo"] || ""}</td>`;
                         row += `<td>${value["ReferenceNo"] || ""}</td>`;
                         row += `<td>${value["DrOther"] || "0.00"}</td>`;
                         row += `<td>${value["CrOther"] || "0.00"}</td>`;
                         row += `<td>${value["Commission"] || "0.00"}</td>`;
                         row += `<td>${value["ConvenienceFee"] || "0.00"}</td>`;
                         row += `<td>${value["TransactionTotal"] || "0.00"}</td>`;
                         row += `<td>${value["Status"] || ""}</td>`;
                         row += `<td>${value["TransactionMonth"] || ""}</td>`;
                         row += `<td>${value["TransactionYear"] || ""}</td>`;
                    } else if (category === 'SERVICES') {
                         row += `<td>${value["CDate"] || ""}</td>`;
                         row += `<td>${value["Branch"] || ""}</td>`;
                         row += `<td>${value["User"] || ""}</td>`;
                         row += `<td>${value["Service"] || ""}</td>`;
                         row += `<td>${value["AccountNo"] || ""}</td>`;
                         row += `<td>${value["ReferenceNo"] || ""}</td>`;
                         row += `<td>${value["ServiceRef"] || ""}</td>`;
                         row += `<td>${value["DrOther"] || "0.00"}</td>`;
                         row += `<td>${value["CrOther"] || "0.00"}</td>`;
                         row += `<td>${value["Commission"] || "0.00"}</td>`;
                         row += `<td>${value["ServiceCharge"] || "0.00"}</td>`;
                         row += `<td>${value["TransactionTotal"] || "0.00"}</td>`;
                         row += `<td>${value["Status"] || ""}</td>`;
                         row += `<td>${value["TransactionMonth"] || ""}</td>`;
                         row += `<td>${value["TransactionYear"] || ""}</td>`;
                    }
                    row += '</tr>';
                    tbody.append(row);
                });
            }
            InitializeTable('#EcpayTxnTblEcpay');
        }
    });
}

function LoadAcashInfo(){
    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadAcashInfo", limit: 5000},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable && $.fn.DataTable.isDataTable( '#AcashInfoTbl' ) ) {
                $('#AcashInfoTbl').DataTable().clear();
                $('#AcashInfoTbl').DataTable().destroy();
            }
        },
        success:function(response){
            $("#AcashInfoList").empty();
            if(response && response.ACASHINFO){
                $.each(response.ACASHINFO,function(key,value){
                    $("#AcashInfoList").append(`
                        <tr>
                            <td>${value["CDate"] || ""}</td>
                            <td>${value["Branch"] || ""}</td>
                            <td>${value["Fund"] || ""}</td>
                            <td>${value["AcctNo"] || ""}</td>
                            <td>${value["AcctTitle"] || value["Payee"] || ""}</td>
                        </tr>
                    `);
                });
            }

            if ($.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#AcashInfoTbl')) {
                    $('#AcashInfoTbl').DataTable().destroy();
                }
                AcashInfoTbl = $('#AcashInfoTbl').DataTable({
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    lengthChange: false,
                    info: true,
                    paging: true,
                    responsive: true,
                });
            }
        },
        error:function(xhr){
            console.error("LoadAcashInfo error", xhr);
        }
    });
}

function LoadEcpayTransactions(){
    $.ajax({
        url:"../../routes/profiling/acashinfo.route.php",
        type:"POST",
        data:{action:"LoadEcpayTransactions", limit: 5000},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable && $.fn.DataTable.isDataTable( '#EcpayTxnTbl' ) ) {
                $('#EcpayTxnTbl').DataTable().clear();
                $('#EcpayTxnTbl').DataTable().destroy();
            }
        },
        success:function(response){
            $("#EcpayTxnList").empty();
            if(response && response.ECPAYTXNS){
                $.each(response.ECPAYTXNS,function(key,value){
                    var amount = "";
                    if (value["DrOther"] && value["DrOther"] !== "0" && value["DrOther"] !== 0) {
                        amount = value["DrOther"];
                    } else if (value["CrOther"] && value["CrOther"] !== "0" && value["CrOther"] !== 0) {
                        amount = value["CrOther"];
                    }
                    $("#EcpayTxnList").append(`
                        <tr>
                            <td>${value["CDate"] || ""}</td>
                            <td>${value["Branch"] || ""}</td>
                            <td>${value["AcctNo"] || ""}</td>
                            <td>${value["Payee"] || ""}</td>
                            <td>${amount}</td>
                        </tr>
                    `);
                });
            }

            if ($.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#EcpayTxnTbl')) {
                    $('#EcpayTxnTbl').DataTable().destroy();
                }
                EcpayTxnTbl = $('#EcpayTxnTbl').DataTable({
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    lengthChange: false,
                    info: true,
                    paging: true,
                    responsive: true,
                });
            }
        },
        error:function(xhr){
            console.error("LoadEcpayTransactions error", xhr);
        }
    });
}

function InitializeOtherTables(){
    var ids = ['#AcashInfoTblCustom', '#AcashInfoTblRaw', '#EcpayTxnTblCustom', '#EcpayTxnTblRaw'];
    if ($.fn.DataTable) {
        $.each(ids, function(index, id) {
            if (!$.fn.DataTable.isDataTable(id)) {
                $(id).DataTable({
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    lengthChange: false,
                    info: false,
                    paging: true,
                    responsive: true,
                });
            }
        });
    }
}

function InitializeTable(id) {
    if ($.fn.DataTable) {
        if (!$.fn.DataTable.isDataTable(id)) {
            $(id).DataTable({
                pageLength: 10,
                searching: true,
                ordering: true,
                lengthChange: false,
                info: true,
                paging: true,
                responsive: true,
            });
        }
    }
}

function PrintAcashInfo() {
    var type = "Main";
    if ($('#acash-custom-tab').hasClass('active')) {
        type = "Custom";
    } else if ($('#acash-raw-tab').hasClass('active')) {
        type = "Raw";
    }
    
    window.open('../../routes/profiling/acashinfo.route.php?action=PrintAcashReport&type=' + type, '_blank');
}

function PrintEcpayTxn() {
    var type = "Main";
    if ($('#ecpay-custom-tab').hasClass('active')) {
        type = "Custom";
    } else if ($('#ecpay-raw-tab').hasClass('active')) {
        type = "Raw";
    } else if ($('#ecpay-ecpay-tab').hasClass('active')) {
        type = "ECPAY";
    }
    
    window.open('../../routes/profiling/acashinfo.route.php?action=PrintEcpayReport&type=' + type, '_blank');
}

// Analytics Logic

function LoadSpecificAnalytics(source, chartId, listId) {
    var filterId = source === 'acash' ? '#acash-analytics-filter' : '#ecpay-analytics-filter';
    var metricId = source === 'acash' ? '#acash-analytics-metric' : '#ecpay-analytics-metric';
    var yearId = source === 'acash' ? '#acash-chart-year' : '#ecpay-chart-year';
    var monthId = source === 'acash' ? '#acash-chart-month' : '#ecpay-chart-month';

    var selectedFilter = $(filterId).val();
    var metric = $(metricId).val();
    var year = $(yearId).val();
    var month = $(monthId).val();

    var requestType = '';
    var requestFilterType = '';
    var requestFilterValue = '';
    
    var knownCategories = ['HEADOFFICE', 'ISYNERGIES, INC', 'EXTERNAL CLIENT', 'MFI BRANCHES', 'STAFF', 'BUSINESS UNIT', 'OTHERS', 'INDIVIDUAL', 'LOADS', 'PAYBILLS', 'SERVICES'];

    if (selectedFilter === 'Overview') {
        // Show all branches
        requestType = 'branch';
        requestFilterType = '';
        requestFilterValue = '';
    } else if (knownCategories.includes(selectedFilter)) {
        // Show individuals within the selected category
        requestType = 'individual'; 
        requestFilterType = 'category';
        requestFilterValue = selectedFilter;
    } else {
        // Show individuals within the selected branch
        requestType = 'individual'; 
        requestFilterType = 'branch';
        requestFilterValue = selectedFilter;
    }

    $.ajax({
        url: "../../process/analytics/analytics.process.php",
        type: "GET",
        data: {
            action: "get_top_performers",
            type: requestType,
            filter_type: requestFilterType,
            filter_value: requestFilterValue,
            metric: metric,
            source: source,
            limit: 1000, // Show all in graph
            year: year,
            month: month
        },
        dataType: "JSON",
        success: function(response) {
            RenderAnalyticsChart(response, metric, source, chartId);
            RenderAnalyticsList(response.slice(0, 5), metric, listId);
        },
        error: function(xhr) {
            console.error(source + " Analytics Load Error", xhr);
        }
    });
}

function LoadFilterValues() {
    // Load ACash Filters
    $.ajax({
        url: "../../process/analytics/analytics.process.php",
        type: "GET",
        data: { action: "get_filters", source: "acash" },
        dataType: "JSON",
        success: function(response) {
            populateFilterDropdown('#acash-analytics-filter', response);
        }
    });

    // Load ECpay Filters
    $.ajax({
        url: "../../process/analytics/analytics.process.php",
        type: "GET",
        data: { action: "get_filters", source: "ecpay" },
        dataType: "JSON",
        success: function(response) {
            populateFilterDropdown('#ecpay-analytics-filter', response);
        }
    });
}

function populateFilterDropdown(selector, data) {
    var $dropdown = $(selector);
    $dropdown.empty();
    $dropdown.append('<option value="">Overview (All Branches)</option>');
    
    if (data.categories && data.categories.length > 0) {
        $.each(data.categories, function(index, cat) {
            $dropdown.append(`<option value="${cat}">${cat}</option>`);
        });
    }
    
    if (data.branches && data.branches.length > 0) {
        var branchGroup = '<optgroup label="Branches">';
        $.each(data.branches, function(index, branch) {
            branchGroup += `<option value="${branch}">${branch}</option>`;
        });
        branchGroup += '</optgroup>';
        $dropdown.append(branchGroup);
    }
}

function RenderAnalyticsChart(data, metric, source, containerId) {
    var categories = [];
    var seriesData = [];
    var seriesData2 = [];
    
    $.each(data, function(index, item) {
        categories.push(item.Name || "Unknown");
        if (metric === 'all') {
            seriesData.push(parseFloat(item.TotalAmount));
            seriesData2.push(parseInt(item.TransactionCount));
        } else {
            seriesData.push(metric === 'amount' ? parseFloat(item.TotalAmount) : parseInt(item.TransactionCount));
        }
    });

    var series = [];
    var yaxis = [];
    var colors = [];

    if (metric === 'all') {
        series = [
            { name: 'Amount', type: 'column', data: seriesData },
            { name: 'Count', type: 'line', data: seriesData2 }
        ];
        colors = ['#3a57e8', '#f16a1b']; // Professional Blue & Accent Orange
        yaxis = [
            {
                title: { 
                    text: 'Amount',
                    style: { color: '#3a57e8', fontWeight: 600 }
                },
                labels: {
                    style: { colors: '#3a57e8' },
                    formatter: function (value) {
                        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 }).format(value);
                    }
                }
            },
            {
                opposite: true,
                title: { 
                    text: 'Count',
                    style: { color: '#f16a1b', fontWeight: 600 }
                },
                labels: {
                    style: { colors: '#f16a1b' },
                    formatter: function (value) {
                        return Math.floor(value);
                    }
                }
            }
        ];
    } else {
        series = [{
            name: metric === 'amount' ? 'Amount' : 'Count',
            data: seriesData
        }];
        colors = ["#3a57e8"];
        yaxis = {
             title: {
                text: metric === 'amount' ? 'Amount' : 'Count',
                style: { color: '#3a57e8', fontWeight: 600 }
            },
            labels: {
                style: { colors: '#3a57e8' },
                formatter: function (value) {
                    if (metric === 'amount') return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 }).format(value);
                    return value;
                }
            }
        };
    }

    var sourceName = source === 'acash' ? 'ACash ' : (source === 'ecpay' ? 'ECPay ' : '');
    var titleText = sourceName + (metric === 'amount' ? 'Transaction Amount' : (metric === 'count' ? 'Transaction Count' : 'Transaction Amount & Count'));

    var options = {
        series: series,
        chart: {
            type: metric === 'all' ? 'line' : 'bar',
            height: 350,
            fontFamily: 'inherit',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        grid: {
            borderColor: '#e0e0e0',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } }   
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '60%',
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: metric === 'all' ? [0, 3] : 0,
            curve: 'smooth'
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: -45,
                rotateAlways: false,
                hideOverlappingLabels: true,
                trim: true,
                maxHeight: 100,
                style: {
                    fontSize: '12px',
                    fontFamily: 'inherit'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: yaxis,
        colors: colors,
        title: {
             text: titleText,
             align: 'left',
             style: {
                 fontSize: '16px',
                 fontWeight: '600',
                 color: '#333'
             }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetY: -20
        },
        tooltip: {
             theme: 'light',
             y: {
                 formatter: function (val, { seriesIndex, w }) {
                     if (metric === 'all') {
                         if (seriesIndex === 0) return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val);
                         return val + " txns";
                     }
                     return metric === 'amount' 
                        ? new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val)
                        : val + " txns";
                 }
             }
        }
    };

    if (document.querySelector(containerId)) {
        document.querySelector(containerId).innerHTML = "";
        if (ApexCharts) {
            var chart = new ApexCharts(document.querySelector(containerId), options);
            chart.render();
        }
    }
}

function RenderAnalyticsList(data, metric, containerId) {
    var list = $(containerId);
    list.empty();
    
    if (data.length === 0) {
        list.append('<li class="list-group-item text-center p-2">No data available</li>');
        return;
    }

    $.each(data, function(index, item) {
        var valueHtml = "";
        if (metric === 'all') {
             var amount = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(item.TotalAmount);
             valueHtml = `
                <div class="d-flex flex-column align-items-end">
                    <span class="badge bg-primary rounded-pill mb-1" style="font-size: 12px;">${amount}</span>
                    <span class="badge bg-secondary rounded-pill" style="font-size: 12px;">${item.TransactionCount} txns</span>
                </div>`;
        } else {
             var value = metric === 'amount' 
                ? new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(item.TotalAmount)
                : item.TransactionCount + " txns";
             valueHtml = `<span class="badge bg-primary rounded-pill" style="font-size: 12px;">${value}</span>`;
        }
            
        list.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                <div class="d-flex align-items-center text-truncate" style="max-width: 120px;">
                    <span class="me-1" style="font-size: 13px; font-weight: bold;">${index + 1}.</span>
                    <span class="text-truncate" style="font-size: 13px;" title="${item.Name || "Unknown"}">${item.Name || "Unknown"}</span>
                </div>
                ${valueHtml}
            </li>
        `);
    });
}

$(document).ready(function() {
    
    // Initial Load for Active Tab (Default: Acash Info)
    if ($('#acash-info-tab').hasClass('active')) {
        LoadSpecificAnalytics('acash', '#acash-analytics-chart', '#acash-analytics-list');
    } else if ($('#ecpay-transaction-tab').hasClass('active')) {
        LoadSpecificAnalytics('ecpay', '#ecpay-analytics-chart', '#ecpay-analytics-list');
    }
    
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.id === 'acash-info-tab') {
             LoadSpecificAnalytics('acash', '#acash-analytics-chart', '#acash-analytics-list');
        } else if (e.target.id === 'ecpay-transaction-tab') {
             LoadSpecificAnalytics('ecpay', '#ecpay-analytics-chart', '#ecpay-analytics-list');
        }
    });
});

function LoadEmails() {
    $.ajax({
        url: "../../routes/profiling/acashinfo.route.php",
        type: "POST",
        data: { action: "LoadEmails" },
        dataType: "JSON",
        success: function(response) {
            if (response.STATUS === "SUCCESS") {
                var dropdown = $("#email-search-dropdown");
                var currentVal = dropdown.val();
                
                dropdown.empty().append('<option value="">Select Email...</option>');
                
                if (response.EMAILS && response.EMAILS.length > 0) {
                    $.each(response.EMAILS, function(index, email) {
                        dropdown.append(`<option value="${email}">${email}</option>`);
                    });
                }
                
                if ($.fn.select2) {
                    dropdown.select2({
                        placeholder: "Select Email...",
                        allowClear: true,
                        dropdownParent: $('#maintenance'),
                        width: '100%'
                    });
                }
                
                if (currentVal) {
                    dropdown.val(currentVal).trigger('change');
                }
            }
        },
        error: function(xhr) {
            console.error("LoadEmails error", xhr);
        }
    });
}

function LoadBranches() {
    $.ajax({
        url: "../../routes/profiling/acashinfo.route.php",
        type: "POST",
        data: { action: "LoadBranches" },
        dataType: "JSON",
        success: function(response) {
            if (response.STATUS === "SUCCESS") {
                var dropdown = $("#branch-search-dropdown");
                var currentVal = dropdown.val();
                
                dropdown.empty().append('<option value="">Select Branch...</option>');
                
                // Add fixed options as requested
                var fixedOptions = [
                    'HEADOFFICE', 
                    'INDIVIDUAL', 
                    'EXTERNAL CLIENT', 
                    'OTHERS',
                    'MFI BRANCHES', 
                    'BUSINESS UNIT'
                ];
                
                $.each(fixedOptions, function(index, opt) {
                    dropdown.append(`<option value="${opt}">${opt}</option>`);
                });
                
                if ($.fn.select2) {
                    dropdown.select2({
                        placeholder: "Select Branch...",
                        allowClear: true,
                        dropdownParent: $('#maintenance'),
                        width: '100%'
                    });
                }
                
                if (currentVal) {
                    dropdown.val(currentVal).trigger('change');
                }
            }
        },
        error: function(xhr) {
            console.error("LoadBranches error", xhr);
        }
    });
}

function LoadMaintenanceTable() {
    $.ajax({
        url: "../../routes/profiling/acashinfo.route.php",
        type: "POST",
        data: { action: "LoadMaintenanceData" },
        dataType: "JSON",
        success: function(response) {
            if (response.STATUS === "SUCCESS") {
                var tbody = $("#maintenance-list");
                tbody.empty();
                
                if (response.DATA && response.DATA.length > 0) {
                    $.each(response.DATA, function(index, item) {
                        tbody.append(`
                            <tr>
                                <td>${item.email}</td>
                                <td>${item.full_name || ''}</td>
                                <td>${item.branch_name}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">No mapped data found</td></tr>');
                }
            }
        },
        error: function(xhr) {
            console.error("LoadMaintenanceTable error", xhr);
        }
    });
}
