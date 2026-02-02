$(function(){
  $("#searchButton").on("click", function(){
    runPaidUnpaidSearch();
  });
  runPaidUnpaidSearch();
});

var summaryChart = null;

function fmt(n){
  var x = parseFloat(n||0);
  return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderSummaryPie(totals){
  var series = [
    parseFloat(totals.TotalPrice || 0),
    parseFloat(totals.TotalSRP || 0),
    parseFloat(totals.TotalMarkup || 0)
  ];
  var options = {
    chart: { type: 'pie', height: 300 },
    labels: ['Total Price','Total SRP','Total Markup'],
    series: series,
    legend: { position: 'bottom' },
    dataLabels: { enabled: true },
    tooltip: { y: { formatter: function(val){ return fmt(val); } } }
  };
  if (summaryChart){
    summaryChart.updateOptions(options, false, true);
  } else {
    summaryChart = new ApexCharts(document.querySelector("#summaryPie"), options);
    summaryChart.render();
  }
}

function renderSlice(rowsHtmlArray, tbodySelector, page, pageSize, emptyColspan){
  var start = page * pageSize;
  var end = Math.min(start + pageSize, rowsHtmlArray.length);
  var slice = rowsHtmlArray.slice(start, end);
  $(tbodySelector).html(slice.length ? slice.join("") : `<tr><td colspan="${emptyColspan}" class="text-center text-muted">No results</td></tr>`);
}

function setupPagination(containerId, total, pageSize, onPageChange){
  var $c = $("#"+containerId);
  if (!$c.length){
    onPageChange(0);
    return;
  }
  var pages = Math.ceil(total / pageSize);
  if (pages <= 1){
    $c.html("");
    onPageChange(0);
    return;
  }
  var current = 0;
  function renderControls(){
    $c.html(`
      <div class="d-flex justify-content-end align-items-center gap-2 mt-2">
        <button class="btn btn-sm btn-outline-secondary" id="${containerId}-prev" ${current===0?'disabled':''}>Prev</button>
        <span class="small">Page ${current+1} of ${pages}</span>
        <div class="input-group input-group-sm" style="width: 150px;">
          <input type="number" min="1" max="${pages}" class="form-control" id="${containerId}-jump" placeholder="Page">
          <button class="btn btn-outline-secondary" id="${containerId}-go">Go</button>
        </div>
        <button class="btn btn-sm btn-outline-secondary" id="${containerId}-next" ${current>=pages-1?'disabled':''}>Next</button>
      </div>
    `);
    $("#"+containerId+"-prev").off("click").on("click", function(){
      if (current > 0){
        current--;
        onPageChange(current);
        renderControls();
      }
    });
    $("#"+containerId+"-next").off("click").on("click", function(){
      if (current < pages - 1){
        current++;
        onPageChange(current);
        renderControls();
      }
    });
    $("#"+containerId+"-go").off("click").on("click", function(){
      var v = parseInt($("#"+containerId+"-jump").val(), 10);
      if (!isNaN(v)){
        var target = Math.min(Math.max(v-1, 0), pages-1);
        if (target !== current){
          current = target;
          onPageChange(current);
          renderControls();
        }
      }
    });
    $("#"+containerId+"-jump").off("keydown").on("keydown", function(e){
      if (e.key === "Enter"){
        $("#"+containerId+"-go").click();
      }
    });
  }
  onPageChange(current);
  renderControls();
}

function runPaidUnpaidSearch(){
  var fromDate = $("#fromDate").val();
  var toDate = $("#toDate").val();
  var isConsign = $("#consignmentCheckbox").is(":checked") ? "Yes" : "No";
  var typeVal = $("#typeSelect").val(); // "1" paid, "2" unpaid
  var withSI = $("#flexCheckIndeterminate").is(":checked");
  
  if ((fromDate && !toDate) || (!fromDate && toDate)){
    Swal.fire({icon:'warning', title:'Complete the date range'});
    return;
  }
  
  $.ajax({
    url: "../../routes/inventorymanagement/paidunpaiditems.route.php",
    type: "POST",
    data: { action: "SearchPaidUnpaid", fromDate: fromDate, toDate: toDate, isConsign: isConsign, typeVal: typeVal, withSI: withSI ? "Yes" : "No" },
    dataType: "JSON",
    beforeSend: function(){
      $("#itemsTableBody").html(`<tr><td colspan="10" class="text-center text-muted">Loading...</td></tr>`);
      $("#clientListTbody").html("");
      $("#itemsPagination").html("");
      $("#clientPagination").html("");
      if (summaryChart){ try { summaryChart.destroy(); } catch(e){} summaryChart = null; }
      $("#totalClientsCount").val("");
      $("#totalPayables").val("");
      $("#totalPriceLabel").text("0.00");
      $("#totalSRPLabel").text("0.00");
      $("#totalMarkupLabel").text("0.00");
      $("#totalQuantityLabel").text("0");
    },
    success: function(res){
      var items = res.items || [];
      var totals = res.totals || {};
      var clients = res.clients || [];
      
      var itemRows = items.map(function(x){
        return `<tr class="item-row" data-si="${x.SI || ''}" data-date="${x.DateAdded || ''}" data-branch="${x.Branch || ''}">
          <td>${x.SI || "-"}</td>
          <td>${x.DateAdded || "-"}</td>
          <td>${x.Status || "-"}</td>
          <td>${x.Branch || "-"}</td>
          <td>${x.Product || "-"}</td>
          <td class="text-end">${fmt(x.DealerPrice)}</td>
          <td class="text-end">${fmt(x.TotalPrice)}</td>
          <td class="text-end">${fmt(x.VatSales)}</td>
          <td class="text-end">${fmt(x.TotalSRP)}</td>
          <td>${x.Type || "-"}</td>
        </tr>`;
      });
      setupPagination("itemsPagination", itemRows.length, 6, function(page){
        renderSlice(itemRows, "#itemsTableBody", page, 6, 10);
      });
      
      var clientCount = 0;
      var clientTotal = 0;
      var clientRows = clients.map(function(c){
        clientCount++;
        clientTotal += parseFloat(c.TotalPayables || 0);
        return `<tr class="client-row" data-customer="${c.Customer}">
          <td class="text-start">${c.Customer}</td>
          <td class="text-end">${fmt(c.TotalPayables)}</td>
        </tr>`;
      });
      setupPagination("clientPagination", clientRows.length, 5, function(page){
        renderSlice(clientRows, "#clientListTbody", page, 5, 2);
      });
      $("#clientSearch").off("input").on("input", function(){
        var q = ($(this).val() || "").toUpperCase();
        var filtered = clientRows.filter(function(html){ return html.toUpperCase().indexOf(q) > -1; });
        setupPagination("clientPagination", filtered.length, 5, function(page){
          renderSlice(filtered, "#clientListTbody", page, 5, 2);
        });
      });
      
      $("#totalClientsCount").val(clientCount);
      $("#totalPayables").val(fmt(clientTotal));
      $("#totalPriceLabel").text(fmt(totals.TotalPrice || 0));
      $("#totalSRPLabel").text(fmt(totals.TotalSRP || 0));
      $("#totalMarkupLabel").text(fmt(totals.TotalMarkup || 0));
      $("#totalQuantityLabel").text(parseInt(totals.TotalQty || 0));
      renderSummaryPie(totals);
    },
    error: function(xhr, status, error){
      $("#itemsTableBody").html(`<tr><td colspan="10" class="text-center text-danger">Error: ${error}</td></tr>`);
    }
  });
}

// Delegate row click to fetch details
$(document).on("click", "#itemsTableBody tr.item-row", function(){
  var si = $(this).data("si") || "";
  var date = $(this).data("date") || "";
  var branch = $(this).data("branch") || "";
  $.ajax({
    url: "../../routes/inventorymanagement/paidunpaiditems.route.php",
    type: "POST",
    data: { action: "GetItemDetails", si: si, date: date, branch: branch },
    dataType: "JSON",
    beforeSend: function(){
      $("#itemDetailsBody").html('<div class="text-center text-muted">Loading...</div>');
    },
    success: function(res){
      if (res && res.item) {
        var item = res.item;
        var html = '<table class="table table-sm table-borderless">';
        Object.keys(item).forEach(function(k){
          html += `<tr><th class="text-muted" style="width: 30%">${k}</th><td>${item[k] ?? ''}</td></tr>`;
        });
        html += '</table>';
        $("#itemDetailsBody").html(html);
      } else {
        $("#itemDetailsBody").html('<div class="text-center text-muted">No details found</div>');
      }
      var modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
      modal.show();
    },
    error: function(xhr, status, error){
      $("#itemDetailsBody").html(`<div class="text-center text-danger">Error: ${error}</div>`);
      var modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
      modal.show();
    }
  });
});

$(document).on("click", "#clientListTbody tr.client-row", function(){
  var customer = $(this).data("customer") || "";
  var fromDate = $("#fromDate").val();
  var toDate = $("#toDate").val();
  var isConsign = $("#consignmentCheckbox").is(":checked") ? "Yes" : "No";
  var typeVal = $("#typeSelect").val();
  var withSI = $("#flexCheckIndeterminate").is(":checked") ? "Yes" : "No";
  $.ajax({
    url: "../../routes/inventorymanagement/paidunpaiditems.route.php",
    type: "POST",
    data: { action: "GetClientDetails", customer: customer, fromDate: fromDate, toDate: toDate, isConsign: isConsign, typeVal: typeVal, withSI: withSI },
    dataType: "JSON",
    beforeSend: function(){
      $("#clientDetailsBody").html('<div class="text-center text-muted">Loading...</div>');
    },
    success: function(res){
      var items = res.items || [];
      var total = res.total || 0;
      var header = `
        <div class="mb-3">
          <div class="fw-bold">Customer: ${customer}</div>
          <div>Total Payables: ${fmt(total)}</div>
        </div>`;
      var table = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>SI</th><th>Date</th><th>Branch</th><th>Status</th><th>Product</th><th class="text-end">Quantity</th><th class="text-end">Amount Due</th><th class="text-end">Total Price</th><th class="text-end">Total SRP</th><th>Type</th></tr></thead><tbody>';
      items.forEach(function(x){
        table += `<tr>
          <td>${x.SI || '-'}</td>
          <td>${x.DateAdded || '-'}</td>
          <td>${x.Branch || '-'}</td>
          <td>${x.Status || '-'}</td>
          <td>${x.Product || '-'}</td>
          <td class="text-end">${fmt(x.Quantity)}</td>
          <td class="text-end">${fmt(x.AmountDue)}</td>
          <td class="text-end">${fmt(x.TotalPrice)}</td>
          <td class="text-end">${fmt(x.TotalSRP)}</td>
          <td>${x.Type || '-'}</td>
        </tr>`;
      });
      table += '</tbody></table></div>';
      $("#clientDetailsBody").html(header + table);
      var modal = new bootstrap.Modal(document.getElementById('clientDetailsModal'));
      modal.show();
    },
    error: function(xhr, status, error){
      $("#clientDetailsBody").html(`<div class="text-center text-danger">Error: ${error}</div>`);
      var modal = new bootstrap.Modal(document.getElementById('clientDetailsModal'));
      modal.show();
    }
  });
});
