$(document).ready(function () {
  $("#category").select2({ width: "100%" });
  $("#itemSelect").select2({ width: "100%" });
  $("#SIno").select2({ width: "100%" });

  $("#quantityInput").on("input", function () {
    var quantity = parseFloat($("#quantityInput").val()) || 0;
    var srp = parseFloat($("#srpDisplay").val()) || 0;
    var editSRP = quantity * srp;
    $("#editSRP").val(editSRP.toFixed(2));
  });

  $("#editSRPtoggle").on("change", function () {
    if (!this.checked) {
      $("#editSRP").prop("disabled", true);
    } else {
      $("#editSRP").prop("disabled", false);
    }
  });

  $("input[name='inlineRadioOptions']").on("change", function () {
    var type = $(this).val();
    var category = $("#category").val();
    var selectElement = $("#itemSelect");
    if (type && category) {
      $.ajax({
        type: "POST",
        url: "./ajax-inventory/fetch_items.php",
        data: { type: type, category: category },
        success: function (response) {
          var data = JSON.parse(response);
          selectElement.empty();
          selectElement.append('<option selected disabled>Select</option>');
          $.each(data.options, function (index, option) {
            selectElement.append('<option value="' + option + '">' + option + "</option>");
          });
        },
        error: function () {
          console.error("Error fetching items");
        },
      });
    }
  });

  $("#itemSelect").on("change", function () {
    var selectedOption = $(this).val();
    var category = $("#category").val();
    var type = $("input[name='inlineRadioOptions']:checked").val();
    if (selectedOption && category && type) {
      $.ajax({
        type: "POST",
        url: "./ajax-inventory/fetch_items.php",
        data: { selectedOption: selectedOption, category: category, type: type },
        success: function (response) {
          var data = JSON.parse(response);
          var siSelectElement = $("#SIno");
          siSelectElement.empty();
          siSelectElement.append('<option selected disabled>Select</option>');
          $.each(data.SIno, function (index, option) {
            siSelectElement.append('<option value="' + option + '">' + option + "</option>");
          });
        },
        error: function () {
          console.error("Error fetching SI numbers");
        },
      });
    }
  });

  $("#SIno").on("change", function () {
    var selectedSIno = $(this).val();
    $.ajax({
      type: "POST",
      url: "./ajax-inventory/product-summary.php",
      data: { SIno: selectedSIno },
      dataType: "json",
      success: function (productSummary) {
        if (productSummary.error) {
          Swal.fire({ icon: "error", title: productSummary.error });
        } else {
          $("#serialNodisplay").val(productSummary.Serialno);
          $("#supplierSIdisplay").val(productSummary.SIno);
          $("#productDisplay").val(productSummary.product);
          $("#supplierDisplay").val(productSummary.Supplier);
          $("#srpDisplay").val(productSummary.SRP);
          $("#quantityDisplay").val(productSummary.Quantity);
          $("#delearsPriceDisplay").val(productSummary.DealerPrice);
          $("#totalPriceDisplay").val(productSummary.TotalPrice);
          $("#warranty").val(productSummary.Warranty);
          $("#vat").val(productSummary.Vat);
          $("#vatsales").val(productSummary.VatSales);
        }
      },
      error: function () {
        Swal.fire({ icon: "error", title: "Error fetching product summary" });
      },
    });
  });

  $("#addButton").on("click", function () {
    var quantity = $("#quantityInput").val();
    var srp = $("#editSRP").val();
    var datePrepared = new Date().toLocaleDateString();
    var SIno = $("#SIno").val();
    var serialno = $("#serialNodisplay").val();
    var product = $("#productDisplay").val();
    var category = $("#category").val();
    var type = $("#type").val();
    var branch = $("#isynBranch").val();
    var supplier = $("#supplierDisplay").val();
    var warranty = $("#warranty").val();
    var vat = $("#vat").val();
    var vatsales = $("#vatsales").val();

    if (!branch || !type || !category || !product || !SIno) {
      Swal.fire({ icon: "warning", text: "Please complete Product Information." });
      return;
    }
    if (!quantity || quantity === "0") {
      Swal.fire({ icon: "warning", text: "Please enter desired quantity amount." });
      return;
    }

    var newRow = document.createElement("tr");
    newRow.innerHTML =
      "<td>" +
      product +
      "</td><td>" +
      quantity +
      "</td><td>" +
      srp +
      "</td><td>" +
      SIno +
      "</td><td>" +
      vat +
      "</td><td>" +
      vatsales +
      "</td><td>" +
      warranty +
      "</td><td>" +
      datePrepared +
      "</td><td>" +
      serialno +
      "</td><td>" +
      category +
      "</td><td>" +
      type +
      "</td><td>" +
      branch +
      "</td><td>" +
      supplier +
      "</td>";

    var tableBody = document.getElementById("table1").querySelector("tbody");
    tableBody.appendChild(newRow);
    document.getElementById("orderform").reset();

    $("#submit-btn").prop("disabled", false);
    $("#cancel-btn").prop("disabled", false);
  });

  $("#cancel-btn").on("click", function () {
    $("#myForm")[0].reset();
    $("#summary")[0].reset();
    $("#orderform")[0].reset();
    $("#compute")[0].reset();
    $("#tableBody").empty();
  });

  $("#submit-btn").on("click", function () {
    var tableBody = document.getElementById("table1").querySelector("tbody");
    var dataArray = [];
    tableBody.querySelectorAll("tr").forEach(function (row) {
      var cells = row.querySelectorAll("td");
      var data = {
        product: cells[0] ? cells[0].innerText || "" : "",
        quantity: cells[1] ? cells[1].innerText || "" : "",
        srp: cells[2] ? cells[2].innerText || "" : "",
        SINo: cells[3] ? cells[3].innerText || "" : "",
        vat: cells[4] ? cells[4].innerText || "" : "",
        vatSales: cells[5] ? cells[5].innerText || "" : "",
        warranty: cells[6] ? cells[6].innerText || "" : "",
        datePrepared: cells[7] ? cells[7].innerText || "" : "",
        serialno: cells[8] ? cells[8].innerText || "" : "",
        category: cells[9] ? cells[9].innerText || "" : "",
        type: cells[10] ? cells[10].innerText || "" : "",
        branch: cells[11] ? cells[11].innerText || "" : "",
        supplier: cells[12] ? cells[12].innerText || "" : "",
        order_no: $("#order_no").val(),
        recipient: $("#recipient").val(),
        sender: $("#sender").val(),
      };
      dataArray.push(data);
    });

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "./ajax-inventory/submit-btn-order-confirmation.php", true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          setTimeout(location.reload.bind(location), 3000);
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: function (toast) {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            },
          });
          Toast.fire({ icon: "success", title: "Added successfully" });
        } else {
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: function (toast) {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            },
          });
          Toast.fire({ icon: "error", title: "Error inserting data" });
        }
      }
    };
    $("#myForm")[0].reset();
    $("#summary")[0].reset();
    $("#orderform")[0].reset();
    $("#compute")[0].reset();
    $("#tableBody").empty();
    var jsonData = JSON.stringify(dataArray);
    xhr.send(jsonData);
  });
});
