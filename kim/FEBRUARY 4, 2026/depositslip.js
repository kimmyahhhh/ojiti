let SelectedDate;
var undeptodayTbl, undepprevTbl;
var selrowdatepaid, selrowdsno, selrowtype, selrowamount, selrowlastdsno, selrowbank, selrowfun;

initundepTdyTbl();
initundepPrevTbl();

Swal.fire({
    title: 'Please Select Date of Transaction',
    html: '<input id="DateTransaction" readonly class="swal2-input">',
    confirmButtonText: 'Set',
    didOpen:function(e){
        $('#DateTransaction').datetimepicker({
            value: new Date(),
            rtl: false,
            format: 'm/d/Y',
            timepicker: false,
            datepicker: true,
            startDate: false,
            maxDate: 0,
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
    if (result.isConfirmed) {

        if($("#DateTransaction").val() == ""){
            location.reload();
        }

        var date = new Date($("#DateTransaction").val());
        SelectedDate = ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear();

        $("#lastdsno").val(SelectedDate);
        $('#lastdsno').datetimepicker({
            timepicker: false,
            datepicker: true,
            format: 'm/d/Y',
            maxDate: 0,
            onSelectDate: function(ct, $i) {
                var date = new Date(ct);
                SelectedDate = ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear();
                $("#lastdsno").val(SelectedDate);
                // Trigger reload if type is selected
                if ($("#type").val() != "Select Type" && $("#type").val() != null) {
                    LoadDepositDetails($("#type").val());
                }
                loadUndepPrev();
            }
        });

        Loadfund();
        loadUndepPrev();
    }
})

function initundepTdyTbl () {
    undeptodayTbl = $('#undeptodayTbl').DataTable({
    scrollY: '200px',
    scrollX: true,
    scrollCollapse: true,
    paging: false,
    bFilter:false,
    info:true,
    });
}

function initundepPrevTbl () {
    undepprevTbl = $('#undepprevTbl').DataTable({
    scrollY: '200px',
    scrollX: true,
    scrollCollapse: true,
    paging: false,
    bFilter:false,
    info:true,
    });
}

function loadUndepPrev (){
    $.ajax({
        url: "../../routes/cashier/depositslip.route.php",
        type: "POST",
        data: {action:"LoadUndepPrev", SelectedDate:SelectedDate},
        dataType: "JSON",
        beforeSend: function() {
            console.log('loading Undepprev...')
            // undepprevTbl.clear().draw();
            $("#undepprevList").empty();
            $("#undepprevList").append("<tr><td class='text-center' colspan='7'>Loading..</td></tr>");
        },
        success: function(response) {

            if ( $.fn.DataTable.isDataTable( '#undepprevTbl' ) ) {
                $('#undepprevTbl').DataTable().clear();
                $('#undepprevTbl').DataTable().destroy();
            }

            $.each(response.UNDEPPREVLIST,function(key,value){
                $("#undepprevList").append(`
                    <tr>
                        <td>${value["CDATE"]}</td>
                        <td>${value["PARTICULARS"]}</td>
                        <td>${value["PAYMENTTYPE"]}</td>
                        <td>${formatAmtVal(value["TOTALAMOUNT"])}</td>
                        <td>${value["STATUS"]}</td>
                        <td>${value["FUND"]}</td>
                        <td>${value["BANK"]}</td>
                        <td>${value["LASTDSNO"]}</td>
                    </tr>
                `);
            });

            if (response.UNDEPPREVLIST.length === 0) {
                $('#depositPrevAll').prop('disabled', true);
            } else {
                $('#depositPrevAll').prop('disabled', false);
            }

            initundepPrevTbl();
        },
        error: function(err) {
            console.log(err)
        }
    });
}

$('#undepprevTbl tbody').on('click', 'tr', function(e) {
    if(undepprevTbl.rows().count() !== 0){
        let classList = e.currentTarget.classList;
        if (classList.contains('selected')) {
            classList.remove('selected');
            $('#depositPrev').prop('disabled', true);
            selrowdatepaid = "";
            selrowdsno = "";
            selrowtype = "";
            selrowamount = "";
            selrowfund = "";
            selrowbank = "";
            selrowlastdsno = "";
        } else {
            undepprevTbl.rows('.selected').nodes().each((row) => {
                row.classList.remove('selected');
            });
            classList.add('selected');
            $('#depositPrev').prop('disabled', false);
            var rowData = undepprevTbl.row(this).data();
            selrowdatepaid = rowData[0];
            selrowdsno = rowData[1];
            selrowtype = rowData[2];
            selrowamount = rowData[3];
            selrowfund = rowData[5];
            selrowbank = rowData[6];
            selrowlastdsno = rowData[7];

            // Auto-populate form
            var dateParts = selrowdatepaid.split("-");
            var formattedDate = dateParts[1] + '/' + dateParts[2] + '/' + dateParts[0];
            
            $("#lastdsno").val(formattedDate);
            SelectedDate = formattedDate;

            $("#fund").val(selrowfund);
            GetBanks(selrowfund, function(){
                $("#bank").val(selrowbank);
                $("#type").val(selrowtype);
                LoadDepositDetails(selrowtype);
                loadUndepPrev();
            });
        }
    }
});

function Loadfund(){
    $.ajax({
        url: "../../routes/cashier/depositslip.route.php",
        type: "POST",
        data: {action:"LoadFund", SelectedDate:SelectedDate},
        dataType: "JSON",
        beforeSend: function() {
            console.log('loading types...')
        },
        success: function(response) {
            $("#fund").empty().append(`<option value="" selected></option>`);
            $.each(response.FUND, function(key,value){
                $("#fund").append("<option value='"+value["FUND"]+"'>"+value["FUND"]+"</option>");
            })
        },
        error: function(err) {
            console.log(err)
        }
    });
}

function GetBanks(fund, callback = null){
    $.ajax({
        url: "../../routes/cashier/depositslip.route.php",
        type: "POST",
        data: {action:"GetBanks", Fund:fund,SelectedDate:SelectedDate},
        dataType: "JSON",
        beforeSend: function() {
            console.log('loading banks...')
        },
        success: function(response) {
            const bankCount = response.BANKS.length;

            if (bankCount === 1){
                $("#bank").empty();
                $("#bank").prop("disabled", true);
                $.each(response.BANKS, function(key,value){
                    console.log(value["BANK"]);
                    $("#bank").append("<option value='"+value["BANK"]+"'>"+value["BANK"]+"</option>");
                })
            } else {
                $("#bank").empty().append(`<option value="" selected></option>`);
                $("#bank").prop("disabled", false);
                $.each(response.BANKS, function(key,value){
                    $("#bank").append("<option value='"+value["BANK"]+"'>"+value["BANK"]+"</option>");
                })
            }
            if(callback) callback();
        },
        error: function(err) {
            console.log(err)
        }
    });
}

function ReloadDepositDetails() {
    let type = $("#type").val();
    if (type == null || type == "Select Type") {
         Swal.fire({
            icon: 'info',
            title: 'Please select a Type first',
        });
        return;
    }
    LoadDepositDetails(type);
}

function LoadDepositDetails(type){
    let fund = $("#fund").val();
    let bank = $("#bank").val();

    if (fund == "" || fund == null) {
        Swal.fire({
            icon: 'warning',
            title: 'Select a Fund',
        })
        $("#type").val("")
        return;
    } 
    /*
    else if (bank == "" || bank == null) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Bank',
        })
        $("#type").val("")
        return;
    } 
    */
    else {
        $.ajax({
            url: "../../routes/cashier/depositslip.route.php",
            type: "POST",
            data: {action:"LoadDepositDetails", Type:type,Fund:fund,Bank:bank,SelectedDate:SelectedDate},
            dataType: "JSON",
            beforeSend: function() {
                $("#undeptodayList").empty();
                $("#undeptodayList").append("<tr><td class='text-center' colspan='3'>Loading..</td></tr>");
            },
            success: function(response) {
                if ( $.fn.DataTable.isDataTable( '#undeptodayTbl' ) ) {
                    $('#undeptodayTbl').DataTable().clear();
                    $('#undeptodayTbl').DataTable().destroy();
                }

                $.each(response.UNDEPTODAY,function(key,value){
                    $("#undeptodayList").append(`
                        <tr>
                            <td>${value["ORNO"]}</td>
                            <td>${value["PAYEE"]}</td>
                            <td>${formatAmtVal(value["DROTHER"])}</td>
                        </tr>
                    `);
                });

                const total = response.UNDEPTODAYTOTAL.TOTAL;

                if (total > 0) {
                    $("#amount").val(formatAmtVal(total));
                    SetDSNO();
                } else {
                    $("#amount").val("");
                    $("#depositSlipNo").val("");
                }


                initundepTdyTbl();
            },
            error: function(err) {
                console.log(err)
            }
        });
    }
}

function SetDSNO () {
    let fund = $("#fund").val();
    let bank = $("#bank").val();
    let type = $("#type").val();
    $.ajax({
            url: "../../routes/cashier/depositslip.route.php",
            type: "POST",
            data: {action:"SetDSNO", Type:type,Fund:fund,Bank:bank,SelectedDate:SelectedDate},
            dataType: "JSON",
            beforeSend: function() {
            },
            success: function(response) {
                // $("#lastdsno").val(response.LASTDSNO);
                $("#depositSlipNo").val(response.DSNOENTRY);

            },
            error: function(err) {
                console.log(err)
            }
        });
}

 function DepositToday () {
    let lastdsno = $("#lastdsno").val();
    let fund = $("#fund").val();
    let bank = $("#bank").val();
    let type = $("#type").val();
    let depositslipno = $("#depositSlipNo").val();
    let amount = $("#amount").val();

    // Ensure SelectedDate is updated from the input field
    if(lastdsno) {
        var date = new Date(lastdsno);
        SelectedDate = ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear();
    }

    if (fund == "" || fund == null) {
        Swal.fire({
            icon: 'warning',
            title: 'Select a Fund',
        })
        return;
    }
    if (bank == "" || bank == null) {
        Swal.fire({
            icon: 'warning',
            title: 'Select a Bank',
        })
        return;
    }
    if (type == "" || type == null) {
        Swal.fire({
            icon: 'warning',
            title: 'Select a Type',
        })
        return;
    }
    if (amount == "" || amount == null) {
        Swal.fire({
            icon: 'warning',
            title: 'No amount to deposit',
        })
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Deposit it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../../routes/cashier/depositslip.route.php",
                type: "POST",
                data: {action:"SaveDepositSlip", lastdsno:lastdsno, fund:fund, bank:bank, type:type, depositslipno:depositslipno, selectedDate:SelectedDate, amount:amount},
                dataType: "JSON",
                beforeSend: function() {

                },
                success: function(response) {
                    if (response.STATUS == "SUCCESS") {
                        Swal.fire(
                            'Deposited!',
                            'Transaction has been deposited.',
                            'success'
                        )
                        LoadDepositDetails(type);
                        loadUndepPrev();
                    } else {
                        Swal.fire(
                            'Error!',
                            'Transaction has not been deposited.',
                            'error'
                        )
                    }
                },
                error: function(err) {
                    console.log(err)
                }
            });
        }
    })
}

function DepositPrev () {
    if (selrowdatepaid == "" || selrowdatepaid == null) {
        Swal.fire({
            icon: 'warning',
            title: 'Select a Transaction',
        })
        return;
    }
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Deposit it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../../routes/cashier/depositslip.route.php",
                type: "POST",
                data: {action:"DSUndepPrevious", SelectedDate:SelectedDate, setdatepaid:selrowdatepaid, dsentry:selrowdsno, type:selrowtype, fund:selrowfund, bank:selrowbank, lastdsno:selrowlastdsno, amount:selrowamount},
                dataType: "JSON",
                beforeSend: function() {

                },
                success: function(response) {
                    if (response.STATUS == "SUCCESS") {
                        Swal.fire(
                            'Deposited!',
                            'Transaction has been deposited.',
                            'success'
                        )
                        loadUndepPrev();
                        $('#depositPrev').prop('disabled', true);
                    } else {
                        Swal.fire(
                            'Error!',
                            'Transaction has not been deposited.',
                            'error'
                        )
                    }
                },
                error: function(err) {
                    console.log(err)
                }
            });
        }
    })
}

function DepositPrevAll () {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Deposit all!'
    }).then((result) => {
        if (result.isConfirmed) {
            let tblData = JSON.stringify(undepprevTbl.rows().data().toArray());
            $.ajax({
                url: "../../routes/cashier/depositslip.route.php",
                type: "POST",
                data: {action:"DSUndepPreviousALL", SelectedDate:SelectedDate, tblData:tblData},
                dataType: "JSON",
                beforeSend: function() {

                },
                success: function(response) {
                    if (response.STATUS == "SUCCESS") {
                        Swal.fire(
                            'Deposited!',
                            'All Transactions has been deposited.',
                            'success'
                        )
                        loadUndepPrev();
                        $('#depositPrevAll').prop('disabled', true);
                    } else {
                        Swal.fire(
                            'Error!',
                            'Transaction has not been deposited.',
                            'error'
                        )
                    }
                },
                error: function(err) {
                    console.log(err)
                }
            });
        }
    })
}

function formatAmtVal(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
