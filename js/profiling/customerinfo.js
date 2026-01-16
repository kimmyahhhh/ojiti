var CustomerInfoTbl;

LoadCustomerList();

$("#customerType").empty().append('<option value="" disabled selected>Select</option>');
var customerTypes = ["MFI HO", "MFI BRANCH", "BUSINESS UNIT", "DEPARTMENT", "STAFF", "EXTERNAL CUSTOMER"];
customerTypes.forEach(function(type) {
    $("#customerType").append('<option value="' + type + '">' + type + '</option>');
});

$('#customerNo').on('input', function() {
    this.value = this.value.replace(/\D/g, '');
});

$('#firstName, #middleName, #lastName, #companyName').on('input', function() {
    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
});

$('#mobileNumber').on('focus', function() {
    if (this.value.trim() === '') {
        this.value = '09';
    }
});

$('#mobileNumber').on('input', function() {
    var digits = this.value.replace(/\D/g, '');
    digits = '09' + digits.replace(/^0?9?/, '');
    digits = digits.slice(0, 11);
    this.value = digits;
});

$('#mobileNumber').on('blur', function() {
    var digits = this.value.replace(/\D/g, '');
    if (digits.length < 2) {
        this.value = '09';
    }
});

$('#street').on('input', function() {
    var value = this.value.replace(/[^A-Za-z0-9\s]/g, '');
    var digits = value.replace(/[^0-9]/g, '');
    if (digits.length > 3) {
        var digitCount = 0;
        var result = '';
        for (var i = 0; i < value.length; i++) {
            var ch = value.charAt(i);
            if (/[0-9]/.test(ch)) {
                if (digitCount < 3) {
                    digitCount++;
                    result += ch;
                }
            } else {
                result += ch;
            }
        }
        value = result;
    }
    this.value = value;
});

var today = new Date().toISOString().split('T')[0];
$('#birthdate').attr('max', today);

function LoadCustomerList(){
    $.ajax({
        url:"../../routes/profiling/customerinfo.route.php",
        type:"POST",
        data:{action:"LoadCustomerList"},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable.isDataTable( '#CustomerInfoTbl' ) ) {
                $('#CustomerInfoTbl').DataTable().clear();
                $('#CustomerInfoTbl').DataTable().destroy(); 
            }
        },
        success:function(response){

            $("#CustomerInfoList").empty();
            $.each(response.CUSTOMERLIST,function(key,value){
                $("#CustomerInfoList").append(`
                    <tr>
                        <td>${value["clientNo"]}</td>
                        <td>${value["Name"]}</td>
                        <td>${value["customerType"]}</td>
                        <td>${value["mobileNumber"]}</td>
                        <td>${value["email"]}</td>
                    </tr>
                `);
            });

            CustomerInfoTbl = $('#CustomerInfoTbl').DataTable({
                pageLength: 5,
                searching: true,
                ordering: true,
                lengthChange: false,
                info: false,
                paging: true,
                responsive: true,
            });
        }, 
    })
}

$('#CustomerInfoTbl tbody').on('click', 'tr',function(e){
    let classList = e.currentTarget.classList;
    if (classList.contains('selected')) {
        classList.remove('selected');
    } else {
        CustomerInfoTbl.rows('.selected').nodes().each((row) => {
            row.classList.remove('selected');
        });
        classList.add('selected');
    }

    var data = $('#CustomerInfoTbl').DataTable().row(this).data();

    $('#customerType').prop('disabled', true).val('');
    $('#customerNo').prop('disabled', true).val('');
    $('#firstName').prop('disabled', true).val('');
    $('#lastName').prop('disabled', true).val('');
    $('#middleName').prop('disabled', true).val('');
    $('#birthdate').prop('disabled', true).val('');
    //$('#age').prop('disabled', true).val('');
    $('#gender').prop('disabled', true).val('');
    $('#mobileNumber').prop('disabled', true).val('');
    $('#companyName').prop('disabled', true).val('');
    $('#email').prop('disabled', true).val('');
    // $('#time').prop('disabled', true).val('');
    $('#Region').prop('disabled', true).val('');
    $('#Province').prop('disabled', true).val('');
    $('#CityTown').prop('disabled', true).val('');
    $('#Barangay').prop('disabled', true).val('');
    $('#tin').prop('disabled', true).val('');
    $('#street').prop('disabled', true).val('');
    $('#productInfo').prop('disabled', true).val('');

    // Enable edit and update buttons, and show cancel button
    $('#editButton').show().prop('disabled', false);
    $('#addNew').show().prop('disabled', true);
    $('#cancel').prop('hidden', false).prop('disabled', false);
    $('#updateButton').show().prop('disabled', true);
    $('#submitButton').hide();

    var clientNo = data[0];

    $('#customerNo').val(clientNo);

    $.ajax({
        url: '../../routes/profiling/customerinfo.route.php',
        method: 'POST',
        data: { action: "GetCustomerInfo", clientNo: clientNo },
        dataType: 'JSON',
        success: function(response) {

            var INFO = response.INFO;

            $('#customerID').val(INFO.id);
            if ($("#customerType option").filter(function() { return $(this).val() === INFO.customerType; }).length === 0) {
                $("#customerType").append('<option value="' + INFO.customerType + '">' + INFO.customerType + '</option>');
            }
            $('#customerType').val(INFO.customerType);
            $('#customerNo').val(INFO.clientNo);
            $('#firstName').val(INFO.firstName);
            $('#middleName').val(INFO.middleName);
            $('#lastName').val(INFO.lastName);
            $('#birthdate').val(INFO.birthdate);
            $('#age').val(INFO.age);
            $('#gender').val(INFO.gender);
            $('#mobileNumber').val(INFO.mobileNumber);
            $('#companyName').val(INFO.companyName);
            $('#email').val(INFO.email);
            $('#tin').val(INFO.tinNumber);
            $('#productInfo').val(INFO.productInfo);
            $('#street').val(INFO.street);

            $('#Region').val(INFO.Region);

            LoadProvince(INFO.Region, INFO.Province); 
            LoadCitytown(INFO.Province, INFO.CityTown); 
            LoadBrgy(INFO.CityTown, INFO.Barangay);
        },
        error: function(error) {
            console.error('Error fetching customer info:', error);
        }
    });
});

$('#addNew').on('click', function() {    
    $('#customerType').prop('disabled', false);
    $('#customerNo').prop('disabled', false);
    $('#firstName').prop('disabled', false);
    $('#lastName').prop('disabled', false);
    $('#middleName').prop('disabled', false);
    $('#birthdate').prop('disabled', false);
    // $('#age').prop('disabled', false);
    $('#gender').prop('disabled', false);   
    $('#mobileNumber').prop('disabled', false);
    $('#companyName').prop('disabled', false);
    $('#email').prop('disabled', false);
    $('#time').prop('disabled', false);
    $('#Region').prop('disabled', false);
    $('#Province').prop('disabled', false);
    $('#CityTown').prop('disabled', false);
    $('#Barangay').prop('disabled', false);
    $('#tin').prop('disabled', false);
    $('#street').prop('disabled', false);
    $('#productInfo').prop('disabled', false);
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').show();
    $('#submitButton').prop('disabled', false);
    $("#CustomerInfoTbl tbody tr").removeClass("selected");
});

$('#editButton').on('click', function() {
    $('#customerType').prop('disabled', false);
    $('#customerNo').prop('disabled', true);
    $('#firstName').prop('disabled', false);
    $('#lastName').prop('disabled', false);
    $('#middleName').prop('disabled', false);
    $('#birthdate').prop('disabled', false);
    // $('#age').prop('disabled', false);
    $('#gender').prop('disabled', false);
    $('#mobileNumber').prop('disabled', false);
    $('#companyName').prop('disabled', false);
    $('#email').prop('disabled', false);
    // $('#time').prop('disabled', false);
    $('#Region').prop('disabled', false);
    $('#Province').prop('disabled', false);
    $('#CityTown').prop('disabled', false);
    $('#Barangay').prop('disabled', false);
    $('#tin').prop('disabled', false);
    $('#street').prop('disabled', false);
    $('#productInfo').prop('disabled', false);
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').hide();
    $('#updateButton').show();
    $('#updateButton').prop('disabled', false);
});

function Cancel(){
    $('#customerType').prop('disabled', true).val('');
    $('#customerNo').prop('disabled', true).val('');
    $('#firstName').prop('disabled', true).val('');
    $('#lastName').prop('disabled', true).val('');
    $('#middleName').prop('disabled', true).val('');
    $('#birthdate').prop('disabled', true).val('');
    $('#age').prop('disabled', true).val('');
    $('#gender').prop('disabled', true).val('');
    $('#mobileNumber').prop('disabled', true).val('');
    $('#companyName').prop('disabled', true).val('');
    $('#email').prop('disabled', true).val('');
    $('#Region').prop('disabled', true).val('');
    $('#Province').prop('disabled', true).val('');
    $('#CityTown').prop('disabled', true).val('');
    $('#Barangay').prop('disabled', true).val('');
    $('#tin').prop('disabled', true).val('');
    $('#street').prop('disabled', true).val('');
    $('#productInfo').prop('disabled', true).val('');
    $('#cancel').prop('hidden', true).prop('disabled', true);
    $('#updateButton').hide();
    $('#submitButton').show().prop('disabled', true);
    $('#editButton').show().prop('disabled', true);
    $('#addNew').show().prop('disabled', false);
    $("#CustomerInfoTbl tbody tr").removeClass("selected");
}

function ValidateCustomerFields(){
    var customerNo = $('#customerNo').val().trim();
    var firstName = $('#firstName').val().trim();
    var middleName = $('#middleName').val().trim();
    var lastName = $('#lastName').val().trim();
    var birthdate = $('#birthdate').val();
    var mobileNumber = $('#mobileNumber').val().trim();
    var companyName = $('#companyName').val().trim();
    var email = $('#email').val().trim();
    var street = $('#street').val().trim();
    var namePattern = /^[A-Za-z\s]+$/;
    if (customerNo === "" || !/^\d+$/.test(customerNo)) {
        Swal.fire({
            icon: 'warning',
            text: 'Customer No must contain numbers only.',
        });
        return false;
    }
    if (firstName === "" || !namePattern.test(firstName)) {
        Swal.fire({
            icon: 'warning',
            text: 'First name must contain letters only.',
        });
        return false;
    }
    if (lastName === "" || !namePattern.test(lastName)) {
        Swal.fire({
            icon: 'warning',
            text: 'Last name must contain letters only.',
        });
        return false;
    }
    if (middleName !== "" && !namePattern.test(middleName)) {
        Swal.fire({
            icon: 'warning',
            text: 'Middle name must contain letters only.',
        });
        return false;
    }
    if (companyName !== "" && !namePattern.test(companyName)) {
        Swal.fire({
            icon: 'warning',
            text: 'Company Name must contain letters only.',
        });
        return false;
    }
    if (birthdate !== "") {
        var maxDate = new Date().toISOString().split('T')[0];
        if (birthdate > maxDate) {
            Swal.fire({
                icon: 'warning',
                text: 'Birthdate cannot be a future date.',
            });
            return false;
        }
    }
    if (mobileNumber === "" || !/^09\d{9}$/.test(mobileNumber)) {
        Swal.fire({
            icon: 'warning',
            text: 'Mobile Number must start with 09 and be 11 digits.',
        });
        return false;
    }
    if (email === "" || !/^[A-Za-z0-9._%+-]+@gmail\.com$/i.test(email)) {
        Swal.fire({
            icon: 'warning',
            text: 'Email must be a valid @gmail.com address.',
        });
        return false;
    }
    if (street !== "") {
        if (/[^A-Za-z0-9\s]/.test(street)) {
            Swal.fire({
                icon: 'warning',
                text: 'Street/House No./Zone must contain only letters and numbers.',
            });
            return false;
        }
        var digitCount = street.replace(/[^0-9]/g, '').length;
        if (digitCount > 3) {
            Swal.fire({
                icon: 'warning',
                text: 'Street/House No./Zone must contain a maximum of 3 numbers.',
            });
            return false;
        }
    }
    return true;
}

$('#customerType').on('change', function() {
    var customType = $(this).val();

    if(customType == 'MFI HO' || customType == 'MFI BRANCH' || customType == 'BUSINESS UNIT' || customType == 'DEPARTMENT'){
        $('#firstName').prop('disabled', true);
        $('#lastName').prop('disabled', true);
        $('#middleName').prop('disabled', true);
        $('#birthdate').prop('disabled', true);
        $('#age').prop('disabled', true);
        $('#companyName').prop('disabled', false);
        $('#gender').prop('disabled', true);
    }else if(customType == 'STAFF'){
        $('#companyName').prop('disabled', true);
        $('#firstName').prop('disabled', false);
        $('#lastName').prop('disabled', false);
        $('#middleName').prop('disabled', false);
        $('#birthdate').prop('disabled', false);
        // $('#age').prop('disabled', false);
        $('#gender').prop('disabled', false);
        $('#mobileNumber').prop('disabled', false);
    }else if(customType == 'EXTERNAL CUSTOMER'){
        $('#firstName').prop('disabled', false);
        $('#lastName').prop('disabled', false);
        $('#middleName').prop('disabled', false);
        $('#birthdate').prop('disabled', false);
        // $('#age').prop('disabled', false);
        $('#gender').prop('disabled', false);
        $('#mobileNumber').prop('disabled', false);
        $('#companyName').prop('disabled', false);
    }
})

$("#birthdate").on("input",function(){
    var bdate = $(this).val();
    var bdateformat = new Date(bdate);
    var diff_ms =  Date.now() - bdateformat.getTime();
    var age_dt = new Date(diff_ms);
    var age = Math.abs(age_dt.getUTCFullYear() - 1970);
    $("#age").val(age);
})

$("#submitButton").on("click",function(){

    if (!ValidateCustomerFields()) {
        return;
    }

    $('#age').prop('disabled', false);
    
    var form = $('#customerinfo')[0];
    var formData = new FormData(form);
    formData.append('action', 'SaveInfo');

    Swal.fire({
        title: 'Are you sure?',
        icon: 'question',
        text: 'Save New Customer Information?.',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#435ebe',
        confirmButtonText: 'Yes, proceed!',
        // allowOutsideClick: false,
        preConfirm: function() {
            return $.ajax({
                url: "../../routes/profiling/customerinfo.route.php",
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
                LoadCustomerList();
                Cancel();
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

$("#updateButton").on("click",function(){

    $('#customerNo').prop('disabled', false);
    $('#age').prop('disabled', false);

    if (!ValidateCustomerFields()) {
        return;
    }
    
    var form = $('#customerinfo')[0];
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
        allowOutsideClick: false,
        preConfirm: function() {
            return $.ajax({
                url: "../../routes/profiling/customerinfo.route.php",
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
                LoadCustomerList();
                Cancel();
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
