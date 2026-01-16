var SupplierInfoTbl, selectedSupplier = "None";

LoadSupplierList();

function LoadSupplierList(){
    $.ajax({
        url:"../../routes/profiling/supplierinfo.route.php",
        type:"POST",
        data:{action:"LoadSupplierList"},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable.isDataTable( '#SupplierInfoTbl' ) ) {
                $('#SupplierInfoTbl').DataTable().clear();
                $('#SupplierInfoTbl').DataTable().destroy(); 
            }
        },
        success:function(response){

        $("#SupplierInfoList").empty();
            $.each(response.LIST,function(key,value){
                var fullAddressParts = [];
                if (value["Region"]) fullAddressParts.push(value["Region"]);
                if (value["Province"]) fullAddressParts.push(value["Province"]);
                if (value["CityTown"]) fullAddressParts.push(value["CityTown"]);
                if (value["Barangay"]) fullAddressParts.push(value["Barangay"]);
                if (value["street"]) fullAddressParts.push(value["street"]);
                var fullAddress = fullAddressParts.join(", ");
                $("#SupplierInfoList").append(`
                    <tr>
                        <td>${value["supplierNo"]}</td>
                        <td>${value["supplierName"]}</td>
                        <td>${value["tinNumber"]}</td>
                        <td>${value["mobileNumber"]}/${value["telephoneNumber"]}</td>
                        <td>${value["email"] || ""}</td>
                        <td>${value["facebookAccount"] || ""}</td>
                        <td>${fullAddress}</td>
                        <td>${value["dateEncoded"]}</td>
                    </tr>
                `);
            });

            SupplierInfoTbl = $('#SupplierInfoTbl').DataTable({
                pageLength: 5,
                searching:true,
                ordering:false,
                lengthChange:false,
                info:false,
                paging:true,
                responsive:true,
            });
        }, 
    })
}

$('#SupplierInfoTbl tbody').on('click', 'tr',function(e){
    let classList = e.currentTarget.classList;
    
    $('#supplierNo').prop('disabled', true).val('');
    $('#supplierName').prop('disabled', true).val('');
    $('#tin').prop('disabled', true).val('');
    $('#email').prop('disabled', true).val('');
    $('#mobileNumber').prop('disabled', true).val('');
    $('#Region').prop('disabled', true).val('');
    $('#Province').prop('disabled', true).empty();
    $('#CityTown').prop('disabled', true).empty();
    $('#Barangay').prop('disabled', true).empty();
    $('#street').prop('disabled', true).val('');
    $('#telNumber').prop('disabled', true).val('');
    $('#facebookAccount').prop('disabled', true).val('');    
    
    if (classList.contains('selected')) {
        classList.remove('selected');
        Cancel();
    } else {
        SupplierInfoTbl.rows('.selected').nodes().each((row) => {
            row.classList.remove('selected');
        });
        classList.add('selected');
        
        var data = $('#SupplierInfoTbl').DataTable().row(this).data();
    
        // Enable edit and update buttons, and show cancel button
        $('#editButton').show().prop('disabled', false);
        $('#addNew').show().prop('disabled', true);
        $('#cancel').prop('hidden', false).prop('disabled', false);
        $('#updateButton').show().prop('disabled', true);
        $('#submitButton').hide();

        var supplierNo = data[0];
    
        $('#supplierNo').val(supplierNo);
    
        $.ajax({
            url: '../../routes/profiling/supplierinfo.route.php',
            method: 'POST',
            data: { action: "GetSupplierInfo", supplierNo: supplierNo },
            dataType: 'JSON',
            success: function(response) {
    
                var INFO = response.INFO;
    
                selectedSupplier = INFO.id;
                $('#supplierNo').val(INFO.supplierNo);
                $('#supplierName').val(INFO.supplierName);
                $('#tin').val(INFO.tinNumber);
                $('#email').val(INFO.email);
                $('#mobileNumber').val(INFO.mobileNumber);
                $('#telNumber').val(INFO.telephoneNumber);
                $('#facebookAccount').val(INFO.facebookAccount);
                
                $('#Region').val(INFO.Region);
                $('#street').val(INFO.street);
                
                LoadProvince(INFO.Region, INFO.Province); 
                LoadCitytown(INFO.Province, INFO.CityTown); 
                LoadBrgy(INFO.CityTown, INFO.Barangay);
            },
            error: function(error) {
                console.error('Error fetching supplier info:', error);
            }
        });
    }

});

$('#addNew').on('click', function() {    
    $('#supplierNo').prop('disabled', false);
    $('#supplierName').prop('disabled', false);
    $('#mobileNumber').prop('disabled', false);
   
    $('#email').prop('disabled', false);
    $('#prodCategory').prop('disabled', false);
    $('#productName').prop('disabled', false);
    $('#Region').prop('disabled', false);
    $('#Province').prop('disabled', false);
    $('#CityTown').prop('disabled', false);
    $('#Barangay').prop('disabled', false);
    $('#tin').prop('disabled', false);
    $('#street').prop('disabled', false);
    $('#telNumber').prop('disabled', false);
    $('#facebookAccount').prop('disabled', false);

    $("#SupplierInfoTbl tbody tr").removeClass("selected");

    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').show();
    $('#submitButton').prop('disabled', false);

    $.ajax({
        url: '../../routes/profiling/supplierinfo.route.php',
        method: 'POST',
        data: { action: "GenerateSupplierNo" },
        dataType: 'JSON',
        success: function(response) {
            if (response.NEXT) {
                $('#supplierNo').val(response.NEXT).prop('disabled', true);
            }
        }
    });
    // Ensure regions are populated for new entry
    $.ajax({
        url:"../../routes/maintenance.route.php",
        type:"POST",
        data:{maintenance_action:"getRegion"},
        dataType:"JSON",
        success:function(data){
            $('#Region').prop('disabled', false).empty().append("<option value='' disabled selected>Select</option>");
            $.each(data, function(key,value){
                $("#Region").append("<option value='"+value+"'>"+value+"</option>");
            })
        }
    })
});

$('#editButton').on('click', function() {
    $('#supplierNo').prop('disabled', true);
    $('#supplierName').prop('disabled', false);
    $('#mobileNumber').prop('disabled', false);
   
    $('#email').prop('disabled', false);
    $('#prodCategory').prop('disabled', false);
    $('#productName').prop('disabled', false);
    $('#Region').prop('disabled', false);
    $('#Province').prop('disabled', false);
    $('#CityTown').prop('disabled', false);
    $('#Barangay').prop('disabled', false);
    $('#tin').prop('disabled', false);
    $('#street').prop('disabled', false);
    $('#telNumber').prop('disabled', false);
    $('#facebookAccount').prop('disabled', false);
    
    $('#cancel').prop('hidden', false);
    $('#cancel').prop('disabled', false);
    $('#submitButton').hide();
    $('#updateButton').show();
    $('#updateButton').prop('disabled', false);
});

$(document).ready(function(){
    function PopulateRegionsIfEmpty(){
        if ($('#Region option').length <= 1) {
            $.ajax({
                url:"../../routes/maintenance.route.php",
                type:"POST",
                data:{maintenance_action:"getRegion"},
                dataType:"JSON",
                success:function(data){
                    $('#Region').empty().append("<option value='' disabled selected>Select</option>");
                    $.each(data, function(key,value){
                        $("#Region").append("<option value='"+value+"'>"+value+"</option>");
                    })
                }
            })
        }
    }
    PopulateRegionsIfEmpty();
    if (typeof LoadProvince === 'function' && typeof LoadCitytown === 'function' && typeof LoadBrgy === 'function') {
        $('#Region').off('change').on('change', function(){
            var r = $(this).val();
            LoadProvince(r, null);
            $('#Province').prop('disabled', false);
        });
        $('#Province').off('change').on('change', function(){
            var p = $(this).val();
            LoadCitytown(p, null);
            $('#CityTown').prop('disabled', false);
        });
        $('#CityTown').off('change').on('change', function(){
            var c = $(this).val();
            LoadBrgy(c, null);
            $('#Barangay').prop('disabled', false);
        });
        $('#Barangay').off('change').on('change', function(){
            var b = $(this).val();
            $.ajax({
                url:"../../routes/maintenance.route.php",
                type:"POST",
                data:{maintenance_action:"get_street",barangay_selected:b},
                dataType:"JSON",
                success:function(data){
                    $('#street').prop('disabled', false).empty().append("<option value='' disabled selected>-</option>");
                    $.each(data, function(key,value){
                        $("#street").append("<option value='"+value+"'>"+value+"</option>");
                    })
                }
            })
        });
    }
});
function Cancel() {
    selectedSupplier = "None";
    $('#supplierNo').prop('disabled', true).val('');
    $('#supplierName').prop('disabled', true).val('');
    $('#mobileNumber').prop('disabled', true).val('');
    
    $('#email').prop('disabled', true).val('');
    $('#prodCategory').prop('disabled', true);
    $('#productName').prop('disabled', true);
    $('#Region').prop('disabled', true).val('');
    $('#Province').prop('disabled', true).empty();
    $('#CityTown').prop('disabled', true).empty();
    $('#Barangay').prop('disabled', true).empty();
    $('#tin').prop('disabled', true).val('');
    $('#street').prop('disabled', true).val('');
    $('#telNumber').prop('disabled', true).val('');
    $('#facebookAccount').prop('disabled', true).val('');
    
    $("#SupplierInfoTbl tbody tr").removeClass("selected");
    
    $('#cancel').prop('hidden', true).prop('disabled', true);
    $('#updateButton').hide();
    $('#submitButton').show().prop('disabled', true);
    $('#editButton').show().prop('disabled', true);
    $('#addNew').show().prop('disabled', false);
}

$("#submitButton").on("click",function(){
    var form = $('#supplierInfo')[0];
    var formData = new FormData(form);
    formData.append('action', 'SaveInfo');
    formData.set('supplierNo', $('#supplierNo').val());
    formData.set('Region', $('#Region').val());
    formData.set('Province', $('#Province').val());
    formData.set('CityTown', $('#CityTown').val());
    formData.set('Barangay', $('#Barangay').val());
    formData.set('street', $('#street').val());
    formData.set('telNumber', $('#telNumber').val());

    Swal.fire({
        title: 'Are you sure?',
        icon: 'question',
        text: 'Save New Supplier Information?.',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#435ebe',
        confirmButtonText: 'Yes, proceed!',
        // allowOutsideClick: false,
        preConfirm: function() {
            return $.ajax({
                url: "../../routes/profiling/supplierinfo.route.php",
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
                LoadSupplierList();
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

    $('#supplierNo').prop('disabled', false);
    
    var form = $('#supplierInfo')[0];
    var formData = new FormData(form);
    formData.append('action', 'UpdateInfo');
    formData.append('supplierID', selectedSupplier);

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
                url: "../../routes/profiling/supplierinfo.route.php",
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
                LoadSupplierList();
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
