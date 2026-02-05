let SelectedDate;
let Mode;
let transactionTbl;
let clientno, loanid, nature, fund, cdate;
let xhrOrTypes;
let xhrTransactions;

Swal.fire({
    title: 'Please Select Date of Transaction',
    html: '<input id="DateTransaction" readonly class="swal2-input">',
    confirmButtonText: 'Set',
    didOpen:function(){
        $('#DateTransaction').datetimepicker({
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
        });
    },
    allowOutsideClick: false,
}).then((result) => {
    if (!result.isConfirmed) return;
    const raw = ($("#DateTransaction").val() || '').trim();
    if (raw === ''){
        location.reload();
        return;
    }
    SelectedDate = raw;
    $("#transactionList").empty().append("<tr><td colspan='7'>Select OR Type.</td></tr>");
    $("#transactionNotice").text('');
    resetform();
    LoadInit();
});

function LoadInit (){
    if (xhrOrTypes && xhrOrTypes.readyState !== 4) xhrOrTypes.abort();
    xhrOrTypes = $.ajax({
        url: "../../routes/cashier/modifytransaction.route.php",
        type: "POST",
        data: {action:"LoadORTypes", SelectedDate:SelectedDate},
        dataType: "JSON",
        success: function(response) {
            if (response && response.BLOCKED == 1){
                $("#orTypes").empty().append("<option value='' selected disabled> Select OR Type</option>");
                $("#transactionList").empty().append("<tr><td colspan='7'>No transactions found.</td></tr>");
                $("#transactionNotice").text(response.MESSAGE || 'Date is closed.');
                $("#transactionPager").addClass('d-none');
                resetform();
                return;
            }
            $("#transactionNotice").text('');
            $("#orTypes").empty().append("<option value='' selected disabled> Select OR Type</option>");
            var list = (response && response.ORTYPES) ? response.ORTYPES : [];
            $.each(list,function(key,value){
                $("#orTypes").append(`<option value="${value["ORTYPE"]}">${value["ORTYPE"]}</option>`);
            });
        },
        error: function(err) {
            console.log(err)
        }
    });
}

function LoadTransactions (type){
    if (!SelectedDate) return;
    if (xhrTransactions && xhrTransactions.readyState !== 4) xhrTransactions.abort();
    xhrTransactions = $.ajax({
        url: "../../routes/cashier/modifytransaction.route.php",
        type: "POST",
        data: {action:"LoadTransactions", type:type, SelectedDate:SelectedDate},
        dataType: "JSON",
        beforeSend: function() {
            $("#transactionNotice").text('');
            if ( $.fn.DataTable.isDataTable( '#transactionTbl' ) ) {
                $('#transactionTbl').DataTable().clear();
                $('#transactionTbl').DataTable().destroy();
            }
            $("#transactionList").empty();
            $("#transactionList").append("<tr><td colspan='7'>Loading..</td></tr>");
        },
        success: function(response) {
            if (response && response.BLOCKED == 1){
                $("#transactionList").empty().append("<tr><td colspan='7'>No transactions found.</td></tr>");
                $("#transactionNotice").text(response.MESSAGE || 'Date is closed.');
                $("#transactionPager").addClass('d-none');
                resetform();
                return;
            }

            if ( $.fn.DataTable.isDataTable( '#transactionTbl' ) ) {
                $('#transactionTbl').DataTable().clear();
                $('#transactionTbl').DataTable().destroy();
            }

            $("#transactionList").empty();
            var list = (response && response.ORLIST) ? response.ORLIST : [];
            if (!Array.isArray(list) || list.length === 0){
                $("#transactionList").append("<tr><td colspan='7'>No transactions found.</td></tr>");
                $("#transactionNotice").text('');
                $("#transactionPager").addClass('d-none');
                resetform();
                return;
            }
            if (response && response.TRUNCATED == 1){
                $("#transactionNotice").text("Showing first "+(response.MAXROWS || list.length)+" records. Narrow the date range to see more.");
            } else {
                $("#transactionNotice").text('');
            }
            $.each(list,function(key,value){
                $("#transactionList").append(`
                    <tr>
                        <td>${value["ORNO"]}</td>
                        <td>${value["PAYEE"]}</td>
                        <td>${value["CLIENTNO"]}</td>
                        <td>${value["LOANID"]}</td>
                        <td>${value["NATURE"]}</td>
                        <td>${value["FUND"]}</td>
                        <td>${value["CDATE"]}</td>
                    </tr>
                `);
            });

            resetform();

            if ($.fn && $.fn.DataTable){
                transactionTbl = $('#transactionTbl').DataTable({
                    scrollX: true,
                    scrollCollapse: true,
                    paging: true,
                    pageLength: 7,
                    lengthChange: false,
                    searching: false,
                    info: false,
                });
                bindPager();
            } else {
                transactionTbl = null;
                $("#transactionPager").addClass('d-none');
            }
        },
        error: function(err) {
            console.log(err)
        }
    });
}

function bindPager(){
    if (!transactionTbl || typeof transactionTbl.page !== 'function'){
        $("#transactionPager").addClass('d-none');
        return;
    }

    $("#transactionPager").removeClass('d-none');
    $('#transactionTbl_paginate').addClass('d-none');

    function updatePager(){
        const info = transactionTbl.page.info();
        const pages = Math.max(1, info.pages || 1);
        const page = Math.min(pages, (info.page || 0) + 1);
        $("#pageCount").text(pages);
        $("#pageNumber").attr('max', pages).val(page);
        $("#pagePrev").prop('disabled', page <= 1);
        $("#pageNext").prop('disabled', page >= pages);
    }

    $("#pagePrev").off('click').on('click', function(){
        transactionTbl.page('previous').draw('page');
    });
    $("#pageNext").off('click').on('click', function(){
        transactionTbl.page('next').draw('page');
    });
    $("#pageNumber").off('keydown').on('keydown', function(e){
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const pages = Math.max(1, transactionTbl.page.info().pages || 1);
        let target = parseInt(($("#pageNumber").val() || '1'), 10);
        if (isNaN(target)) target = 1;
        target = Math.max(1, Math.min(pages, target));
        transactionTbl.page(target - 1).draw('page');
    });
    $("#pageNumber").off('change').on('change', function(){
        const pages = Math.max(1, transactionTbl.page.info().pages || 1);
        let target = parseInt(($("#pageNumber").val() || '1'), 10);
        if (isNaN(target)) target = 1;
        target = Math.max(1, Math.min(pages, target));
        transactionTbl.page(target - 1).draw('page');
    });

    transactionTbl.off('draw.dt._customPager').on('draw.dt._customPager', function(){
        updatePager();
    });
    updatePager();
}

function getRowDataFromDom(tr){
    var $td = $(tr).children('td');
    if ($td.length < 7) return null;
    return [
        $td.eq(0).text().trim(),
        $td.eq(1).text().trim(),
        $td.eq(2).text().trim(),
        $td.eq(3).text().trim(),
        $td.eq(4).text().trim(),
        $td.eq(5).text().trim(),
        $td.eq(6).text().trim()
    ];
}

$('#transactionTbl').on('click', 'tbody tr', function(e) {
    var count = 0;
    if (transactionTbl && typeof transactionTbl.rows === 'function'){
        count = transactionTbl.rows().count();
    } else {
        count = $('#transactionList tr').length;
    }
    if (count === 0) return;

    var classList = e.currentTarget.classList;
    if (classList.contains('selected')) {
        classList.remove('selected');
        resetform();
        return;
    }

    $('#transactionTbl tbody tr').removeClass('selected');
    classList.add('selected');

    var rowData = null;
    if (transactionTbl && typeof transactionTbl.row === 'function'){
        rowData = transactionTbl.row(this).data();
    }
    if (!rowData){
        rowData = getRowDataFromDom(this);
    }
    if (!rowData) return;

    var orno = rowData[0];
    clientno = rowData[2];
    loanid = rowData[3];
    nature = rowData[4];
    fund = rowData[5];
    cdate = rowData[6];

    if (nature == "LOAN AMORTIZATION"){
        $('#deleteTransaction').prop('disabled', true);
        $('#cancelTransaction').prop('disabled', true);
    } else {
        $('#deleteTransaction').prop('disabled', false);
        $('#cancelTransaction').prop('disabled', false);
    }

    $.ajax({
        url: "../../routes/cashier/modifytransaction.route.php",
        type: "POST",
        data: {action:"GetORData", orno:orno,cdate:cdate},
        dataType: "JSON",
        success: function(response) {
            $("#orno").val(orno);
            $("#fund").val(response.FUND || '');
            $("#po").val(response.PO || '');
            $("#nature").val(response.NATURE || '');

            $("#principal").val(response.PRINCIPAL || '');
            $("#interest").val(response.INTEREST || '');
            $("#cbu").val(response.CBU || '');
            $("#penalty").val(response.PENALTY || '');
            $("#mba").val(response.MBA || '');
            $("#total").val(response.TOTAL || '');
        },
        error: function(err) {
            console.log(err)
        }
    });
});

function CancelTransaction () {
    let type = $("#orTypes").val();
    let orno = $("#orno").val();
    let fund = $("#fund").val();
    let po = $("#po").val();
    let nature = $("#nature").val();

    if (orno == "" || fund == "" || po == "" || nature == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Payment Details.',
        })
        return;
    } else {
        Swal.fire({
            icon: 'question',
            title: 'Cancel this payment transaction?',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Yes, proceed!',
            // allowOutsideClick: false,
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/cashier/modifytransaction.route.php",
                    type: "POST",
                    data: {action:"CancelTransaction", orno:orno,fund:fund,po:po,nature:nature,clientno:clientno,loanid:loanid,cdate:cdate},
                    dataType: 'JSON',
                }).then(function(response){
                    if (response && response.STATUS == 'SUCCESS') {
                        LoadTransactions (type);
                        resetform();
                    }
                    return response;
                }).catch(function(err){
                    console.log(err);
                    Swal.showValidationMessage('Request failed');
                });
            },
        }).then(function(result) {
            if (result.isConfirmed) {
                if (result.value && result.value.STATUS == 'SUCCESS') {
                    Swal.fire({
                      icon: "success",
                      text: "Transaction cancelled.",
                  });
                } else {
                    Swal.fire({
                        icon: "warning",
                        text: "Failed to cancel transaction.",
                    });
                }
            }
        });
    }
}

function DeleteTransaction () {
    let type = $("#orTypes").val();
    let orno = $("#orno").val();
    let fund = $("#fund").val();
    let po = $("#po").val();
    let nature = $("#nature").val();

    if (orno == "" || fund == "" || po == "" || nature == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Payment Details.',
        })
        return;
    } else {
        Swal.fire({
            icon: 'question',
            title: 'Delete this payment transaction?',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Yes, proceed!',
            // allowOutsideClick: false,
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/cashier/modifytransaction.route.php",
                    type: "POST",
                    data: {action:"DeleteTransaction", orno:orno,cdate:cdate},
                    dataType: 'JSON',
                }).then(function(response){
                    if (response && response.STATUS == 'SUCCESS') {
                        LoadTransactions (type);
                        resetform();
                    }
                    return response;
                }).catch(function(err){
                    console.log(err);
                    Swal.showValidationMessage('Request failed');
                });
            },
        }).then(function(result) {
            if (result.isConfirmed) {
                if (result.value && result.value.STATUS == 'SUCCESS') {
                    Swal.fire({
                      icon: "success",
                      text: "Transaction deleted.",
                  });
                } else {
                    Swal.fire({
                        icon: "warning",
                        text: "Failed to delete transaction.",
                    });
                }
            }
        });
    }
}

function resetform (){
    $("#orno").val("");
    $("#fund").val("");
    $("#po").val("");
    $("#nature").val("");

    $("#principal").val("");
    $("#interest").val("");
    $("#cbu").val("");
    $("#penalty").val("");
    $("#mba").val("");
    $("#total").val("");
    $('#deleteTransaction').prop('disabled', true);
    $('#cancelTransaction').prop('disabled', true);
    clientno = loanid = nature = fund = cdate = undefined;
    $('#transactionTbl tbody tr').removeClass('selected');
}

function formatInput(input) {
    let cleanValue = input.value.replace(/[^0-9.,]/g, '');
    cleanValue = cleanValue.replace(/,/g, '');
    if (cleanValue !== '') {
        input.value = cleanValue;
    } else {
        input.value = '0';
    }
}
