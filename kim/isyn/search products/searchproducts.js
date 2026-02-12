var productTbl;

Initialize();

function Initialize(){
    $.ajax({
        url:"../../routes/inventorymanagement/searchproducts.route.php",
        type:"POST",
        data:{action:"Initialize"},
        dataType:"JSON",
        beforeSend:function(){
            if ( $.fn.DataTable.isDataTable( '#productTbl' ) ) {
                $('#productTbl').DataTable().clear();
                $('#productTbl').DataTable().destroy(); 
            }
        },
        success:function(response){
            $("#productList").empty();
            $.each(response.LIST,function(key,value){
                $("#productList").append(`
                    <tr>
                        <td>${value["Product"]}</td>
                        <td>${value["Category"]}</td>
                        <td>${value["Quantity"]}</td>
                        <td>${value["SRP"]}</td>
                        <td>${value["TotalSRP"]}</td>
                        <td>${value["DealerPrice"]}</td>
                        <td>${value["TotalPrice"]}</td>
                        <td>${value["Warranty"]}</td>
                    </tr>
                `);
            });

            productTbl = $('#productTbl').DataTable({
                pageLength: 10,
                searching: true,
                ordering: false,
                info: false,
                paging: true,
                lengthChange: false,
                columnDefs: [
                    { targets: [ 4,5,6,7 ], visible:false, searchable:false }
                ],
            });
            
            // Bind custom search input
            $('#customSearch').on('keyup', function() {
                productTbl.search(this.value).draw();
            });
        }
    })
}

$('#productTbl tbody').on('click', 'tr',function(e){
    if(productTbl.rows().count() !== 0){
        let classList = e.currentTarget.classList;
        if (classList.contains('selected')) {
            classList.remove('selected');
            $('#productName').val("");
            $('#category').val("");
            $('#warranty').val("");
            $('#quantity').val("");
            $('#dealerPrice').val("");
            $('#totalDP').val("");
            $('#totalSRP').val("");
            $('#SRP').val("");
        } else {
            productTbl.rows('.selected').nodes().each((row) => {
                row.classList.remove('selected');
            });
            classList.add('selected');
            var data = $('#productTbl').DataTable().row(this).data();

            $('#productName').val(data[0]);
            $('#category').val(data[1]);
            $('#warranty').val(data[7]);
            $('#quantity').val(data[2]);
            $('#dealerPrice').val(data[5]);
            $('#totalDP').val(data[6]);
            $('#totalSRP').val(data[4]);
            $('#SRP').val(data[3]);
        }
    }
});

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

// Custom pagination functions
function nextPage() {
    if (productTbl) {
        productTbl.page('next').draw('page');
        updatePaginationInfo();
    }
}

function previousPage() {
    if (productTbl) {
        productTbl.page('previous').draw('page');
        updatePaginationInfo();
    }
}

function updatePaginationInfo() {
    if (productTbl) {
        const pageInfo = productTbl.page.info();
        const currentPage = pageInfo.page + 1; // DataTables uses 0-based indexing
        const totalPages = pageInfo.pages;
        
        document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
        
        // Enable/disable buttons based on current page
        document.getElementById('prevBtn').disabled = (currentPage === 1);
        document.getElementById('nextBtn').disabled = (currentPage === totalPages || totalPages === 0);
    }
}

// Update pagination info after table initialization
$(document).ready(function() {
    setTimeout(function() {
        updatePaginationInfo();
    }, 500);
});
