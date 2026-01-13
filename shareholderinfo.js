var shareholderTbl;
var shareholderNames = [];
var selectedName = "";

// Typeahead for the other name field (kept as in original)
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

// Initial load
LoadShareHolderNames();

function LoadShareHolderNames(){
    $.ajax({
        url:"../../routes/profiling/shareholderinfo.route.php",
        type:"POST",
        data:{action:"LoadShareHolderNames"},
        dataType:"JSON",
        success:function(response){
            shareholderNames = response.NAMES || [];
            displayNames(shareholderNames);
        }
    });
}

function displayNames(names) {
    $("#shNamesList").empty();
    if (names.length > 0) {
        $.each(names, function(_, value){
            const isSelected = selectedName === value["fullname"] ? "selected-name" : "";
            $("#shNamesList").append(`
                <div class="list-group-item ${isSelected}" data-name="${value["fullname"]}">
                    ${value["fullname"]}
                </div>
            `);
        });
    } else {
        $("#shNamesList").append(`<div class="list-group-item text-muted">No matching shareholders found</div>`);
    }
}

// Filter as you type
$('#shNames').on('input', function() {
    const term = $(this).val().toLowerCase();
    if (!term) {
        displayNames(shareholderNames);
        return;
    }
    const filtered = shareholderNames.filter(n => n["fullname"].toLowerCase().includes(term));
    displayNames(filtered);
});

// Click a name -> select and load table
$(document).on('click', '#shNamesList .list-group-item', function() {
    const name = $(this).data('name');
    if (!name) return;
    selectedName = name;
    $('#shNames').val(name);
    $('#shNamesList .list-group-item').removeClass('selected-name');
    $(this).addClass('selected-name');
    LoadShareHolderList(name);
});

function LoadShareHolderList(name){
    $.ajax({
        url:"../../routes/profiling/shareholderinfo.route.php",
        type:"POST",
        data:{action:"LoadShareHolderList", name:name},
        dataType:"JSON",
        beforeSend:function(){
            if ($.fn.DataTable.isDataTable('#shareholderTbl')) {
                $('#shareholderTbl').DataTable().clear();
                $('#shareholderTbl').DataTable().destroy();
            }
            Cancel();
        },
        success:function(response){
            $("#shareholderList").empty();
            $.each(response.LIST,function(_,value){
                $("#shareholderList").append(`
                    <tr>
                        <td>${value["shareholderNo"]}</td>
                        <td>${value["fullname"]}</td>
                        <td>${value["shareholder_type"]}</td>
                        <td>${value["noofshare"]}</td>
                        <td>${value["type"]}</td>
                    </tr>
                `);
            });
            shareholderTbl = $('#shareholderTbl').DataTable({
                pageLength: 5,
                searching:false,
                ordering:false,
                lengthChange:false,
                info:false,
                paging:true,
                responsive:true,
            });
        }
    });
}

// Row click handler (unchanged)
$('#shareholderTbl tbody').on('click', 'tr',function(e){
    let classList = e.currentTarget.classList;
    if (classList.contains('selected')) {
        classList.remove('selected');
    } else {
        shareholderTbl.rows('.selected').nodes().each((row) => {
            row.classList.remove('selected');
        });
        classList.add('selected');
    }

    var data = $('#shareholderTbl').DataTable().row(this).data();

    $('#president').prop('disabled', true);
    $("#president").prop('checked', false);
    $('#shareholderName').prop('disabled', true).val('');
    $('#contact_number').prop('disabled', true).val('');
    $('#email').prop('disabled', true).val('');
    $('#facebook_account').prop('disabled', true).val('');
    $('#shareholder_type').prop('disabled', true).val('');
    $('#type').prop('disabled', true).val('');
    $('#noofshare').prop('disabled', true).val('');
    $('#amount_share').prop('disabled', true).val('');
    $('#cert_no').prop('disabled', true).val('');
    $('#emp_resign').prop('disabled', true);
    $("#emp_resign").prop('checked', false);

    $("#printCert").prop('disabled', false);
    $('#addNew').show().prop('disabled', true);
    $('#cancel').prop('hidden', false).prop('disabled', false);
    $('#updateButton').show().prop('disabled', true);
    $('#submitButton').hide();

    var shareholderNo = data[0];
    $('#shareholderID').val(shareholderNo);

    $.ajax({
        url: '../../routes/profiling/shareholderinfo.route.php',
        method: 'POST',
        data: { action: "getShareholderInfo", shareholderNo: shareholderNo },
        dataType: 'JSON',
        success: function(response) {
            var INFO = response.INFO;
            $('#shareID').val(INFO.id);
            $('#shareholderID').val(INFO.shareholderNo);
            $("#president").prop('checked', INFO.OtherSignatories === "Yes");
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
            $('#editButton').show().prop('disabled', INFO.certPrinted === "Yes");
        },
        error: function(error) {
            console.error('Error fetching supplier info:', error);
        }
    });
});

// Add new (unchanged)
$('#addNew').on('click', function() {
    gnrtCertID();
    gnrtSID();
    $('#president').prop('disabled', false);
    $("#president").prop('checked', false);
    $('#shareholderName').prop('disabled', false);
    $('#contact_number').prop('disabled', false);
    $('#email').prop('disabled', false);
    $('#facebook_account').prop('disabled', false);
    $('#shareholder_type').prop('disabled', false);
    $('#type').prop('disabled', false);
    $('#noofshare').prop('disabled', false);
    $('#amount_share').prop('disabled', true);
    $('#cert_no').prop('disabled', true);
    $('#emp_resign').prop('disabled', false);
    $("#emp_resign").prop('checked', false);
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').show();  
    $('#submitButton').prop('disabled', false);
    $("#printCert").prop('disabled', true);
    $("#shNames").val("");
    selectedName = "";
    $('#shNamesList .list-group-item').removeClass('selected-name');
    $("#shareholderTbl tbody tr").removeClass("selected");
    if ($.fn.DataTable.isDataTable('#shareholderTbl')) {
        $('#shareholderTbl').DataTable().clear();
        $('#shareholderTbl').DataTable().destroy();
    }
});

// Edit (unchanged)
$('#editButton').on('click', function() {
    $('#president').prop('disabled', false);
    $('#shareholderName').prop('disabled', false);
    $('#contact_number').prop('disabled', false);
    $('#email').prop('disabled', false);
    $('#facebook_account').prop('disabled', false);
    $('#shareholder_type').prop('disabled', false);
    $('#type').prop('disabled', false);
    $('#noofshare').prop('disabled', false);
    $('#amount_share').prop('disabled', true);
    $('#cert_no').prop('disabled', true);
    $('#emp_resign').prop('disabled', false);
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').show();
    $('#submitButton').prop('disabled', false);
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').hide();
    $('#updateButton').show();
    $('#updateButton').prop('disabled', false);
});

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
    $('#shareholder_type').prop('disabled', true).val('');
    $('#type').prop('disabled', true).val('');
    $('#noofshare').prop('disabled', true).val('');
    $('#amount_share').prop('disabled', true).val('');
    $('#cert_no').prop('disabled', true).val('');
    $('#emp_resign').prop('disabled', true);
    $("#emp_resign").prop('checked', false);
    $('#cancel').prop('hidden', true).prop('disabled', true);
    $('#updateButton').hide();
    $('#submitButton').show().prop('disabled', true);
    $('#editButton').show().prop('disabled', true);
    $('#addNew').show().prop('disabled', false);
    $("#printCert").prop('disabled', true);
    $("#shareholderTbl tbody tr").removeClass("selected");
}

// Config modal (unchanged)
$('#ConfigurationBtn').on('click', function() {
    $.ajax({
        url: '../../routes/profiling/shareholderinfo.route.php',
        method: 'POST',
        data: { action: "getShareholderConfig" },
        dataType: 'JSON',
        success: function(response) {
            var certNo = response.certNo[0];
            var SIGN1 = response.SIGN1[0];
            var SIGN2 = response.SIGN2[0];
            var SIGNSUB2 = response.SIGNSUB2[0];

            $('#signatory1Name').val(SIGN1.Value);
            $('#signatory1Desig').val(SIGN1.SubValue);
            $('#signatory2Name').val(SIGN2.Value);
            $('#signatory2Desig').val(SIGN2.SubValue);
            $('#signatorySub2Name').val(SIGNSUB2.Value);
            $('#signatorySub2Desig').val(SIGNSUB2.SubValue);
            $('#currentCertNo').val(certNo.Value);
        }
    });
    $("#configurationMDL").modal("show");
});

// Submit new (unchanged)
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
        preConfirm: function() {
            return $.ajax({
                url: "../../routes/profiling/shareholderinfo.route.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON'
            });
        },
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.STATUS == 'success') {
                Swal.fire({ icon: "success", title: "Success", text: result.value.MESSAGE });
                LoadShareHolderNames();
                Cancel();
            } else {
                Swal.fire({ icon: "error", title: "Error", text: result.value.MESSAGE });
                $('#shareholderID').prop('disabled', true);
                $('#amount_share').prop('disabled', true);
                $('#cert_no').prop('disabled', true);
            }
        }
    });
});

// Update (unchanged)
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
        showLoaderOnConfirm: true,
        confirmButtonColor: '#435ebe',
        confirmButtonText: 'Yes, proceed!',
        preConfirm: function() {
            return $.ajax({
                url: "../../routes/profiling/shareholderinfo.route.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON'
            });
        },
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.STATUS == 'success') {
                Swal.fire({ icon: "success", title: "Success", text: result.value.MESSAGE });
                LoadShareHolderNames();
                Cancel();
            } else {
                Swal.fire({ icon: "error", title: "Error", text: result.value.MESSAGE });
                $('#shareID').prop('disabled', true);
                $('#amount_share').prop('disabled', true);
                $('#cert_no').prop('disabled', true);
            }
        }
    });
});

// Update config (unchanged)
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
        preConfirm: function() {
            return $.ajax({
                url: "../../routes/profiling/shareholderinfo.route.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON'
            });
        },
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.STATUS == 'success') {
                Swal.fire({ icon: "success", title: "Success", text: result.value.MESSAGE });
            } else {
                Swal.fire({ icon: "error", title: "Error", text: result.value.MESSAGE });
            }
        }
    });
});

function PrintReport(){
    let shareholderNo = $("#shareholderID").val();
    if(shareholderNo == ""){
        Swal.fire({ icon:"warning", text:"No shareholder info retrieved" });
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
                        Swal.showValidationMessage(response.MESSAGE)
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
        success: function(response) {
            $('#cert_no').val(response.certNo);
            $('#actualNo').val(response.actualNo);
        }
    });
}

function gnrtSID(){
    $.ajax({
        url: '../../routes/profiling/shareholderinfo.route.php',
        type: 'POST',
        data: {action:"gnrtSID"},
        dataType: 'JSON',
        success: function(response) {
            $('#shareholderID').val(response.shareNo);
        }
    });
}

function calculateAmount() {
    var noOfShares = document.getElementById('noofshare').value;
    var amountOfShares = noOfShares * 100;
    document.getElementById('amount_share').value = amountOfShares;
}
