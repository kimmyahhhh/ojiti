

Initialize();

function Initialize(){
    $.ajax({
        url:"../../routes/inventorymanagement/inventorybalancing.route.php",
        type:"POST",
        data:{action:"Initialize"},
        dataType:"JSON",
        beforeSend:function(){
            Clear();
        },
        success:function(response){
            let data = response;
            $("#lastInvEndTotalQty").val(formatAmtVal(data.QTYINVEND));
            $("#lastInvEndTotalDP").val(formatAmtVal(data.DPINVEND));

            $("#InvInTotalQty").val(formatAmtVal(data.QTYINVIN));
            $("#InvInTotalDP").val(formatAmtVal(data.DPINVIN));
            
            $("#transferProductReceivedTotalQty").val(formatAmtVal(data.QTYRECEIVED));
            $("#transferProductReceivedTotalDP").val(formatAmtVal(data.DPRECEIVED));
            
            $("#InvOutTotalQty").val(formatAmtVal(data.QTYINVOUT));
            $("#InvOutTotalDP").val(formatAmtVal(data.DPINVOUT));
            
            $("#transferProductTransferTotalQty").val(formatAmtVal(data.QTYTRANSFER));
            $("#transferProductTransferTotalDP").val(formatAmtVal(data.DPTRANSFER));

            $("#PurchaseReturnedTotalQty").val(formatAmtVal(data.QTYTRANSFER));
            $("#PurchaseReturnedTotalDP").val(formatAmtVal(data.DPTRANSFER));

            $("#EndOfMonthTotalQty").val(formatAmtVal(data.TOTALQTY));
            $("#EndOfMonthTotalDP").val(formatAmtVal(data.TOTALDP));
            
            $("#CurrentInvTotalQty").val(formatAmtVal(data.QTYINVCURR));
            $("#CurrentInvTotalDP").val(formatAmtVal(data.DPINVCURR));

            $("#DifferenceTotalQty").val(formatAmtVal(data.QTYINVDIF));
            $("#DifferenceTotalDP").val(formatAmtVal(data.DPINVDIF));

            if (response.QTYINVDIFSTAT == "Negative" || response.DPINVDIFSTAT == "Negative") {
                $('#diffLbl').addClass('Negative');
            } else {
                $('#diffLbl').removeClass('Negative');
            }

            if (response.QTYINVDIFSTAT == "Negative") {
                $('#DifferenceTotalQty').addClass('Negative');
            } else {
                $('#DifferenceTotalQty').removeClass('Negative');
            }

            if (response.DPINVDIFSTAT == "Negative") {
                $('#DifferenceTotalDP').addClass('Negative');
            } else {
                $('#DifferenceTotalDP').removeClass('Negative');
            }

            $("#ChangeDealerPrice").val(formatAmtVal(data.CHANGEDP));
        }, 
    })
}

function Clear(){
    $("#lastInvEndTotalQty").val("");
    $("#lastInvEndTotalDP").val("");

    $("#InvInTotalQty").val("");
    $("#InvInTotalDP").val("");
    
    $("#transferProductReceivedTotalQty").val("");
    $("#transferProductReceivedTotalDP").val("");
    
    $("#InvOutTotalQty").val("");
    $("#InvOutTotalDP").val("");
    
    $("#transferProductTransferTotalQty").val("");
    $("#transferProductTransferTotalDP").val("");

    $("#PurchaseReturnedTotalQty").val("");
    $("#PurchaseReturnedTotalDP").val("");

    $("#EndOfMonthTotalQty").val("");
    $("#EndOfMonthTotalDP").val("");
    
    $("#CurrentInvTotalQty").val("");
    $("#CurrentInvTotalDP").val("");

    $("#DifferenceTotalQty").val("");
    $("#DifferenceTotalDP").val("");

    $("#ChangeDealerPrice").val("");
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