$(document).ready(function() {
    console.log('purchasedreturned_maintenance.js loaded');

    // TEMPORARY: Disable Select2 initialization to verify data loading
    /*
    if ($.fn.select2) {
        $('#category').select2({
            width: '100%',
            placeholder: 'Select Category'
        });
        
        $('#return-type, #type').select2({
            width: '100%',
            placeholder: 'Select'
        });
    }
    */

    function loadMaintenanceOptions(itemName, elementId) {
        console.log('Fetching options for: ' + itemName);
        $.ajax({
            url: './ajax-inventory/fetch-maintenance-options.php',
            type: 'POST',
            data: { item_name: itemName },
            dataType: 'json',
            success: function(data) {
                console.log('Received data for ' + itemName + ':', data);
                var select = $(elementId);
                
                // Clear existing options but keep the first one
                select.find('option:not(:first)').remove();

                if (data.error) {
                    console.error('Error fetching ' + itemName + ':', data.error);
                    return;
                }

                if (Array.isArray(data)) {
                    data.forEach(function(value) {
                        // Check if option already exists to avoid duplicates
                        if (select.find("option[value='" + value + "']").length === 0) {
                            var newOption = new Option(value, value, false, false);
                            select.append(newOption);
                        }
                    });
                    
                    // If Select2 was active, this would update it.
                    // For standard select, this does nothing harmful.
                    // select.trigger('change'); 
                } else {
                    console.error('Data is not an array for ' + itemName, data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error for ' + itemName + ':', error);
                console.log('Response:', xhr.responseText);
                // Fallback: Try absolute path if relative fails
                if (this.url.startsWith('./')) {
                    console.log('Retrying with absolute path...');
                    this.url = '/isyn/pages/inventorymanagement/ajax-inventory/fetch-maintenance-options.php';
                    $.ajax(this);
                }
            }
        });
    }

    loadMaintenanceOptions('Return Type', '#return-type');
    loadMaintenanceOptions('Type', '#type');
    loadMaintenanceOptions('Category', '#category');
});
