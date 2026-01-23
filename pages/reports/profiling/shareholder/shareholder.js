var shareholderTbl;
var shSearchDebounceTimer;

$(document).ready(function() {

    LoadShareHolderList("");

    $('.searchName').typeahead({
        source: function(name, result){
            $.ajax({
            url:"../../routes/profiling/shareholderinfo.route.php",
            type:"POST",
            data:{action:"searchNames", name:name},
            dataType:"JSON",
                success:function(data){
                    result($.map(data, function(item){
                        return item;
                    }));
                }
            })
        }
    });

    var dtpOptions = { format: 'm/d/Y', timepicker: false, datepicker: true, mask: '99/99/9999' };
    $('#dateEncoded').datetimepicker(dtpOptions);

    LoadShareHolderNames();
    
    $("#shSearch").on("input", function(){
        var q = $(this).val();
        clearTimeout(shSearchDebounceTimer);
        shSearchDebounceTimer = setTimeout(function(){ LoadShareHolderList(q); }, 300);
    });

    $('#shareholderTbl tbody').on('click', 'tr',function(e){
        let classList = e.currentTarget.classList;
        if (classList.contains('selected')) {
            classList.remove('selected');
            Cancel();
        } else {
            shareholderTbl.rows('.selected').nodes().each((row) => {
                row.classList.remove('selected');
            });
            classList.add('selected');
        
            var data = $('#shareholderTbl').DataTable().row(this).data();
        
            $('#president').prop('disabled', true);
            $("#president").prop('checked', false);
            $('#shareholderName').prop('disabled', true).val('');
            $('#contact_number').prop('disabled', true).val('');
            $('#email').prop('disabled', true).val('');
            $('#facebook_account').prop('disabled', true).val('');
            // $('#age').prop('disabled', true);
            $('#shareholder_type').prop('disabled', true).val('');
            $('#type').prop('disabled', true).val('');
            $('#noofshare').prop('disabled', true).val('');
            $('#amount_share').prop('disabled', true).val('');
            $('#cert_no').prop('disabled', true).val('');
            $('#emp_resign').prop('disabled', true);
            $("#emp_resign").prop('checked', false);
        
            // Enable edit and update buttons, and show cancel button
            $("#printCert").prop('disabled', false);
            $('#addNew').show().prop('disabled', false).removeAttr('disabled');
            $('#cancel').prop('hidden', false).prop('disabled', false);
            $('#updateButton').show().prop('disabled', true);
            $('#submitButton').hide();
            $('#editButton').show().prop('disabled', false).removeAttr('disabled'); // Initially disabled, waiting for fetch
            
            // Ensure form fields are disabled initially when clicking a row, until Edit is clicked
            $('#president').prop('disabled', true);
            $('#shareholderName').prop('disabled', true);
            $('#contact_number').prop('disabled', true);
            $('#email').prop('disabled', true);
            $('#facebook_account').prop('disabled', true);
            $('#shareholder_type').prop('disabled', true);
            $('#type').prop('disabled', true);
            $('#noofshare').prop('disabled', true);
            $('#amount_share').prop('disabled', true);
            $('#cert_no').prop('disabled', true);
            $('#emp_resign').prop('disabled', true);
            $('#dateEncoded').prop('disabled', true);
    
            var shareholderNo = data[0];
        
            $('#shareholderID').val(shareholderNo);
    
            if(!shareholderNo) return;
        
            $.ajax({
                url: '../../routes/profiling/shareholderinfo.route.php',
                method: 'POST',
                data: { action: "getShareholderInfo", shareholderNo: shareholderNo },
                dataType: 'JSON',
                success: function(response) {
        
                    var INFO = response.INFO;
        
                    $('#shareID').val(INFO.id);
                    $('#shareholderID').val(INFO.shareholderNo);
                    
                    // Use is_president if available (it should be 1 or 0), otherwise fallback to checking OtherSignatories string
                    var isPres = (INFO.is_president == 1) || (INFO.OtherSignatories === "Yes");
                    $("#president").prop('checked', isPres);
    
                    $('#shareholderName').val(INFO.fullname);
                    $('#contact_number').val(INFO.contact_number);
                    $('#email').val(INFO.email);
                    $('#facebook_account').val(INFO.facebook_account);
                    $('#shareholder_type').val(INFO.shareholder_type);
                    $('#type').val(INFO.type);
                    $('#noofshare').val(INFO.noofshare);
                    $('#amount_share').val(INFO.amount_share);
                    $('#cert_no').val(INFO.cert_no);
                    $("#emp_resign").prop('checked', INFO.emp_resign === "Yes");
                    $('#dateEncoded').val(INFO.dateEncoded);
                    
                    // Always enable edit button first, then disable if needed
                    $('#editButton').show().prop('disabled', false).removeAttr('disabled');
                    
                    // if (INFO.certPrinted === "Yes") {
                    //     $('#editButton').prop('disabled', true);
                    // }
    
                    $('#updateButton').prop('disabled', false); 
                    updateSummaryFromForm();
                },
                error: function(error) {
                    console.error('Error fetching supplier info:', error);
                }
            });
        }
    });

    $('#addNew').on('click', function() {
        $('#shareholderName').val('');
        $('#contact_number').val('');
        $('#email').val('');
        $('#facebook_account').val('');
        $('#shareholder_type').val('');
        $('#type').val('');
        $('#noofshare').val('');
        $('#amount_share').val('');
        $('#dateEncoded').val('');
        
        gnrtCertID();
        gnrtSID();
        $('#president').prop('disabled', false);
        $("#president").prop('checked', false);
        $('#shareholderName').prop('disabled', false);
        $('#contact_number').prop('disabled', false);
        $('#email').prop('disabled', false);
        $('#facebook_account').prop('disabled', false);
        // $('#age').prop('disabled', false);
        $('#shareholder_type').prop('disabled', false);
        $('#type').prop('disabled', false);
        $('#noofshare').prop('disabled', false);
        $('#amount_share').prop('disabled', true);
        $('#cert_no').prop('disabled', true);
        $('#emp_resign').prop('disabled', false);
        $("#emp_resign").prop('checked', false);
        $('#dateEncoded').prop('disabled', false);
        $('#cancel').prop('hidden', false);
        $('#cancel').prop('disabled', false);
        $('#submitButton').show();  
        $('#submitButton').prop('disabled', false);
        $("#printCert").prop('disabled', true);
        $("#shSearch").val("");
        $("#shareholderTbl tbody tr").removeClass("selected");
        if ( $.fn.DataTable.isDataTable( '#shareholderTbl' ) ) {
            $('#shareholderTbl').DataTable().clear();
            $('#shareholderTbl').DataTable().destroy(); 
        }
        clearSummary();
    });

    $('#editButton').on('click', function() {
        $('#addNew').prop('disabled', false).removeAttr('disabled');
        $('#president').prop('disabled', false);
        $('#shareholderName').prop('disabled', false);
        $('#contact_number').prop('disabled', false);
        $('#email').prop('disabled', false);
        $('#facebook_account').prop('disabled', false);
        // $('#age').prop('disabled', false);
        $('#shareholder_type').prop('disabled', false);
        $('#type').prop('disabled', false);
        $('#noofshare').prop('disabled', false);
        $('#amount_share').prop('disabled', true); // Amount is auto-calculated, usually read-only
        $('#cert_no').prop('disabled', true); // Cert no is usually auto-generated
        $('#emp_resign').prop('disabled', false)
        $('#dateEncoded').prop('disabled', true); // Keep disabled during edit
        $('#cancel').prop('hidden', false);
        $('#cancel').prop('disabled', false);
        $('#submitButton').hide(); // Hide submit button
        $('#submitButton').prop('disabled', true);
    
        $('#cancel').prop('hidden', false);
        $('#cancel').prop('disabled', false);
        // $('#submitButton').hide();
        $('#updateButton').show();
        $('#updateButton').prop('disabled', false);
        updateSummaryFromForm();
    });

    $('#ConfigurationBtn').on('click', function() {
        $.ajax({
            url: '../../routes/profiling/shareholderinfo.route.php',
            method: 'POST',
            data: { action: "getShareholderConfig",},
            dataType: 'JSON',
            success: function(response) {
                var certNo = response.certNo[0];
                var SIGN1 = response.SIGN1[0];
                var SIGN2 = response.SIGN2[0];
                var SIGNSUB2 = response.SIGNSUB2[0];
    
                console.log(certNo.Value);
                $('#signatory1Name').val(SIGN1.Value);
                $('#signatory1Desig').val(SIGN1.SubValue);
                $('#signatory2Name').val(SIGN2.Value);
                $('#signatory2Desig').val(SIGN2.SubValue);
                $('#signatorySub2Name').val(SIGNSUB2.Value);
                $('#signatorySub2Desig').val(SIGNSUB2.SubValue);
                $('#currentCertNo').val(certNo.Value);
            },
            error: function(error) {
                console.error('Error fetching supplier info:', error);
            }
        });
        
        $("#configurationMDL").modal("show");
    });

    $("#submitButton").on("click",function(){
        $('#shareholderID').prop('disabled', false);
        $('#amount_share').prop('disabled', false);
        $('#cert_no').prop('disabled', false);
    
        var form = $('#shareholderInfo')[0];
        var formData = new FormData(form);
        formData.append('action', 'SaveInfo');
    
        Swal.fire({
            title: 'Are you sure?',
            icon: 'question',
            text: 'Save New Shareholder Information?.',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Yes, proceed!',
            // allowOutsideClick: false,
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/profiling/shareholderinfo.route.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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
                    LoadShareHolderNames();
                    Cancel();
                    
                } else if (result.value.STATUS != 'success') {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.value.MESSAGE,
                    });
                    $('#shareholderID').prop('disabled', true);
                    $('#amount_share').prop('disabled', true);
                    $('#cert_no').prop('disabled', true);
                }
                
            }
        });
    })

    $("#updateButton").on("click",function(){
        $('#shareID').prop('disabled', false);
        $('#amount_share').prop('disabled', false);
        $('#cert_no').prop('disabled', false);
        
        var form = $('#shareholderInfo')[0];
        var formData = new FormData(form);
        formData.append('action', 'UpdateInfo');
    
        Swal.fire({
            title: 'Are you sure?',
            icon: 'question',
            text: 'Save Changes?.',
            showCancelButton: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Yes, proceed!',
        }).then(function(result) {
            if (result.isConfirmed) {
                
                Swal.fire({
                    title: 'Processing...',
                    html: 'Please wait...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
    
                $.ajax({
                    url: "../../routes/profiling/shareholderinfo.route.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.STATUS == 'success') {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: response.MESSAGE,
                            });
                            LoadShareHolderNames();
                            Cancel();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.MESSAGE || "Unknown Error",
                            });
                            $('#shareID').prop('disabled', true);
                            $('#amount_share').prop('disabled', true);
                            $('#cert_no').prop('disabled', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: "error",
                            title: "Server Error",
                            text: "Status: " + status + " - Error: " + error,
                        });
                         $('#shareID').prop('disabled', true);
                         $('#amount_share').prop('disabled', true);
                         $('#cert_no').prop('disabled', true);
                    }
                });
                
            }
        });
    })
    
    $("#updateConfigBtn").on("click",function(){
        
        var form = $('#configurationForm')[0];
        var formData = new FormData(form);
        formData.append('action', 'UpdateConfig');
    
        Swal.fire({
            title: 'Are you sure?',
            icon: 'question',
            text: 'Save Changes?.',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Yes, proceed!',
            // allowOutsideClick: false,
            preConfirm: function() {
                return $.ajax({
                    url: "../../routes/profiling/shareholderinfo.route.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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
                } else if (result.value.STATUS != 'success') {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.value.MESSAGE,
                    });
                }
                
            }
        });
    })
    
    $('#contact_number').on('input', function(){
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
    });
    $('#contact_number').on('keydown', function(e){
        var k = e.key;
        if (e.ctrlKey || e.metaKey) return;
        if (k === 'Backspace' || k === 'Delete' || k === 'Tab' || k === 'ArrowLeft' || k === 'ArrowRight' || k === 'Home' || k === 'End' || k === 'Enter') return;
        if (!/^\d$/.test(k)) e.preventDefault();
    });
    document.getElementById('contact_number').addEventListener('beforeinput', function(e){
        var t = e.inputType;
        var d = e.data || '';
        if (t === 'insertText' && /[^\d]/.test(d)) e.preventDefault();
        if (t === 'insertFromPaste') {
            var cd = e.clipboardData || window.clipboardData;
            if (cd) {
                var txt = cd.getData('text');
                if (/[^\d]/.test(txt)) e.preventDefault();
            }
        }
    });
    $('#contact_number').on('paste', function(e){
        var cd = e.originalEvent.clipboardData || window.clipboardData;
        var txt = cd ? cd.getData('text') : '';
        if (/[^\d]/.test(txt)) {
            e.preventDefault();
            var digits = txt.replace(/\D/g, '');
            var remaining = 11 - this.value.length;
            this.value = this.value + digits.slice(0, remaining);
        }
    });

    console.log("Shareholder Info JS Loaded & Ready");
});

function LoadShareHolderNames(){
    $.ajax({
        url:"../../routes/profiling/shareholderinfo.route.php",
        type:"POST",
        data:{action:"LoadShareHolderNames"},
        dataType:"JSON",
        beforeSend:function(){
        },
        success:function(response){
            $("#shNamesList").empty();
            $.each(response.NAMES,function(key,value){
                    $("#shNamesList").append(`<option value="${value["fullname"]}">`);
            });
        }, 
    })
}

function LoadShareHolderList(name){
    $.ajax({
        url:"../../routes/profiling/shareholderinfo.route.php",
        type:"POST",
        data:{action:"LoadShareHolderList", name:name},
        dataType:"JSON",
        beforeSend:function(){
            $('#editButton').show().prop('disabled', false).removeAttr('disabled'); // Reset Edit button to disabled on page load/refresh
    if ( $.fn.DataTable.isDataTable( '#shareholderTbl' ) ) {
        $('#shareholderTbl').DataTable().clear();
        $('#shareholderTbl').DataTable().destroy(); 
    }
    Cancel();
        },
        success:function(response){

        $("#shareholderList").empty();
            $.each(response.LIST,function(key,value){
                $("#shareholderList").append(`
                    <tr>
                        <td>${value["shareholderNo"]}</td>
                        <td>${value["fullname"]}</td>
                        <td>${value["shareholder_type"]}</td>
                        <td>${value["noofshare"]}</td>
                        <td>${value["type"]}</td>
                        <td>${value["dateEncoded"]}</td>
                    </tr>
                `);
            });

            if ($.fn.DataTable.isDataTable('#shareholderTbl')) {
                shareholderTbl = $('#shareholderTbl').DataTable({ retrieve: true });
            } else {
                shareholderTbl = $('#shareholderTbl').DataTable({
                    pageLength: 5,
                    searching:true,
                    ordering:false,
                    lengthChange:false,
                    info:false,
                    paging:true,
                    responsive:true,
                    });
            }
            updateSummaryFromFirstRow();
        }, 
    })
}

function Cancel() {
    $('#shareholderID').prop('disabled', true).val('');
    $('#shareID').prop('disabled', true).val('');
    $('#actualNo').val('');
    $('#president').prop('disabled', true);
    $("#president").prop('checked', false);
    $('#shareholderName').prop('disabled', true).val('');
    $('#contact_number').prop('disabled', true).val('');
    $('#email').prop('disabled', true).val('');
    $('#facebook_account').prop('disabled', true).val('');
    // $('#age').prop('disabled', true);
    $('#shareholder_type').prop('disabled', true).val('');
    $('#type').prop('disabled', true).val('');
    $('#noofshare').prop('disabled', true).val('');
    $('#amount_share').prop('disabled', true).val('');
    $('#cert_no').prop('disabled', true).val('');
    $('#emp_resign').prop('disabled', true);
    $("#emp_resign").prop('checked', false);
    $('#dateEncoded').prop('disabled', true).val('');
    $('#cancel').prop('hidden', true).prop('disabled', true);
    $('#updateButton').hide();
    $('#submitButton').show().prop('disabled', true);
    $('#editButton').show().prop('disabled', false).removeAttr('disabled');
    $('#addNew').show().prop('disabled', false).removeAttr('disabled');
    $("#printCert").prop('disabled', true);
    $("#shareholderTbl tbody tr").removeClass("selected");
    clearSummary();
}

function updateSummaryFromForm(){
    $("#sumShareholderID").text($("#shareholderID").val());
    $("#sumFullName").text($("#shareholderName").val());
    $("#sumShareholderType").text($("#shareholder_type").val());
    $("#sumNoOfShares").text($("#noofshare").val());
    $("#sumType").text($("#type").val());
    $("#sumDateEncoded").text($("#dateEncoded").val());
}

function updateSummaryFromFirstRow(){
    var first = $("#shareholderTbl tbody tr").first().children();
    if (first.length >= 5){
        $("#sumShareholderID").text($(first[0]).text());
        $("#sumFullName").text($(first[1]).text());
        $("#sumShareholderType").text($(first[2]).text());
        $("#sumNoOfShares").text($(first[3]).text());
        $("#sumType").text($(first[4]).text());
        $("#sumDateEncoded").text($(first[5]).text());
    } else {
        clearSummary();
    }
}

function clearSummary(){
    $("#sumShareholderID").text("");
    $("#sumFullName").text("");
    $("#sumShareholderType").text("");
    $("#sumNoOfShares").text("");
    $("#sumType").text("");
    $("#sumDateEncoded").text("");
}

function PrintReport(){
    let shareholderNo = $("#shareholderID").val();
  

    if(shareholderNo == ""){

        Swal.fire({
            icon:"warning",
            text:"No shareholder info retrieved"
        })
        return;
    }

    Swal.fire({
        title: 'Select an Format',
        html: `
          <select id="swal-select" class="swal2-input">
            <option value="10M">10M</option>
            <option value="4M">4M</option>
          </select>
        `,
        focusConfirm: false,
        preConfirm: () => {
          const format = document.getElementById('swal-select').value;
          if (!format) {
            Swal.showValidationMessage('Please select an option!');
            return false;
          }
          return format;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const format = result.value;

            $.ajax({
                url:"../../routes/profiling/shareholderinfo.route.php",
                type:"POST",
                data:{action:"ToSession",shareholderNo:shareholderNo, format:format},
                dataType:"JSON",
                success:function(response){
                    if(response.STATUS != "SUCCESS"){
                        Swal.showValidationMessage(
                            response.MESSAGE,
                        )
                    }
                    if(response.STATUS == "SUCCESS"){
                        window.open("../../routes/profiling/shareholderinfo.route.php?type=PrintCertificate");
                    }
                }
            });
        }
    });
      

}

function gnrtCertID(){
    $.ajax({
        url: '../../routes/profiling/shareholderinfo.route.php',
        type: 'POST',
        data: {action:"gnrtCertID"},
        dataType: 'JSON',
        beforeSend: function() {
            console.log(`Certificate No...`)
        },
        success: function(response) {
            $('#cert_no').val(response.certNo);
            $('#actualNo').val(response.actualNo);
        },
    });
}

function gnrtSID(){
    $.ajax({
        url: '../../routes/profiling/shareholderinfo.route.php',
        type: 'POST',
        data: {action:"gnrtSID"},
        dataType: 'JSON',
        beforeSend: function() {
            console.log(`Generating Series No...`)
        },
        success: function(response) {
            $('#shareholderID').val(response.shareNo);
        },
    });
}

function calculateAmount() {
    var noOfShares = document.getElementById('noofshare').value;
    var amountOfShares = noOfShares * 100;
    document.getElementById('amount_share').value = amountOfShares;
}
