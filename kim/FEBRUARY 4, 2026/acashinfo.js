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

    // Load dynamic filter values (Branches)
    LoadFilterValues();

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

    // Custom Upload Handlers
    $("#acash-upload-custom, #ecpay-upload-custom").change(function(){
        var formData = new FormData();
        formData.append('file', $(this)[0].files[0]);
        formData.append('action', 'UploadCustom');

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
                    LoadAcashInfo(); // Reload Main (since it saves to DB)
                    LoadEcpayTransactions(); // Reload Main (since it saves to DB)
                    LoadAcashInfoCustom();
                    LoadEcpayTransactionsCustom();
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
        // Reset input so same file can be selected again
        $(this).val('');
    });

    // Raw Upload Handlers
    $("#acash-upload-raw, #ecpay-upload-raw").change(function(){
        var formData = new FormData();
        formData.append('file', $(this)[0].files[0]);
        formData.append('action', 'UploadRaw');

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
                    LoadAcashInfo(); 
                    LoadEcpayTransactions();
                    LoadAcashInfoRaw();
                    LoadEcpayTransactionsRaw();
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
        $(this).val('');
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
                            <td>${value["AcctNo"] || ""}</td>
                            <td>${value["AcctTitle"] || ""}</td>
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
                            <td>${value["AcctNo"] || ""}</td>
                            <td>${value["AcctTitle"] || ""}</td>
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
                            <td>${value["Payee"] || ""}</td>
                            <td>${value["Explanation"] || ""}</td>
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
                            <td>${value["Payee"] || ""}</td>
                            <td>${value["Explanation"] || ""}</td>
                            <td>${amount}</td>
                        </tr>
                    `);
                });
            }
            InitializeTable('#EcpayTxnTblRaw');
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
                            <td>${value["AcctTitle"] || ""}</td>
                        </tr>
                    `);
                });
            }

            if ($.fn.DataTable) {
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
                            <td>${value["Payee"] || ""}</td>
                            <td class="explanation-cell" data-full-text="${value["Explanation"] || ""}">${value["Explanation"] || ""}</td>
                            <td>${amount}</td>
                        </tr>
                    `);
                });
            }

            if ($.fn.DataTable) {
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
    }
    
    window.open('../../routes/profiling/acashinfo.route.php?action=PrintEcpayReport&type=' + type, '_blank');
}

// Analytics Logic

function LoadSpecificAnalytics(source, chartId, listId) {
    var filterId = source === 'acash' ? '#acash-analytics-filter' : '#ecpay-analytics-filter';
    var metricId = source === 'acash' ? '#acash-analytics-metric' : '#ecpay-analytics-metric';

    var selectedFilter = $(filterId).val();
    var metric = $(metricId).val();

    var requestType = '';
    var requestFilterType = '';
    var requestFilterValue = '';
    
    var knownCategories = ['HEADOFFICE', 'EXTERNAL CLIENT', 'MFI BRANCHES', 'STAFF', 'BUSINESS UNIT', 'OTHERS', 'INDIVIDUAL'];

    if (selectedFilter === 'Overview') {
        // Show all categories
        requestType = 'category';
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
            limit: 5
        },
        dataType: "JSON",
        success: function(response) {
            RenderAnalyticsChart(response, metric, source, chartId);
            RenderAnalyticsList(response, metric, listId);
        },
        error: function(xhr) {
            console.error(source + " Analytics Load Error", xhr);
        }
    });
}

function LoadFilterValues() {
    $.ajax({
        url: "../../process/analytics/analytics.process.php",
        type: "GET",
        data: { action: "get_filters", type: "branch" },
        dataType: "JSON",
        success: function(response) {
            if (response && response.length > 0) {
                var branchOptions = '<optgroup label="Branches">';
                $.each(response, function(index, branch) {
                    branchOptions += `<option value="${branch}">${branch}</option>`;
                });
                branchOptions += '</optgroup>';
                
                $('#acash-analytics-filter').append(branchOptions);
                $('#ecpay-analytics-filter').append(branchOptions);
            }
        },
        error: function(xhr) {
            console.error("LoadFilterValues Error", xhr);
        }
    });
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
        colors = ['#008FFB', '#FEB019'];
        yaxis = [
            {
                title: { text: 'Amount' },
                labels: {
                    formatter: function (value) {
                        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 }).format(value);
                    }
                }
            },
            {
                opposite: true,
                title: { text: 'Count' }
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
                text: metric === 'amount' ? 'Amount' : 'Count'
            }
        };
    }

    var sourceName = source === 'acash' ? 'ACash ' : (source === 'ecpay' ? 'ECPay ' : '');
    var titleText = sourceName + (metric === 'amount' ? 'Top Performers by Amount' : (metric === 'count' ? 'Top Performers by Volume' : 'Top Performers (Amount & Volume)'));

    var options = {
        series: series,
        chart: {
            type: metric === 'all' ? 'line' : 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '55%',
            }
        },
        dataLabels: {
            enabled: metric !== 'all' // Disable data labels for mixed chart to avoid clutter
        },
        stroke: {
            width: metric === 'all' ? [0, 4] : 0
        },
        xaxis: {
            categories: categories,
        },
        yaxis: yaxis,
        colors: colors,
        title: {
             text: titleText,
             align: 'center'
        },
        tooltip: {
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
        list.append('<li class="list-group-item text-center">No data available</li>');
        return;
    }

    $.each(data, function(index, item) {
        var valueHtml = "";
        if (metric === 'all') {
             var amount = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(item.TotalAmount);
             valueHtml = `
                <div class="d-flex flex-column align-items-end">
                    <span class="badge bg-primary rounded-pill mb-1">${amount}</span>
                    <span class="badge bg-secondary rounded-pill">${item.TransactionCount} txns</span>
                </div>`;
        } else {
             var value = metric === 'amount' 
                ? new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(item.TotalAmount)
                : item.TransactionCount + " txns";
             valueHtml = `<span class="badge bg-primary rounded-pill">${value}</span>`;
        }
            
        list.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
                ${item.Name || "Unknown"}
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
