/* orderconfirmation.js
 * Mirrors the structure used in transmittalreceipt.js.
 * Handles initialization, dropdown wiring, product selection,
 * table management, and submission for the Order Confirmation page.
 */

let orderItemsTbl;
let selectedRow = null;
let orderItemData = [];
let cachedTypesByBranch = {};
let cachedCategoriesByType = {};
let cachedItemsBySelection = {};
let cachedSINoByItem = {};
let currentSelectMode = "";
let currentOrderNo = "";
let productSummary = {};
let quantityInput = 0;

$(document).ready(function () {
    initializeOrderConfirmation();
    bindEventHandlers();
});

/* -------------------------------------------------------------------------- */
/* Initialization                                                             */
/* -------------------------------------------------------------------------- */
function initializeOrderConfirmation() {
    $.ajax({
        url: "../../routes/inventorymanagement/orderconfirmation.route.php",
        type: "POST",
        data: { action: "Initialize" },
        dataType: "JSON",
        beforeSend() {
            disableFormControls();
        },
        success(response) {
            currentOrderNo = response.order_no || "";
            $("#order_no").val(currentOrderNo);

            populateSelect("#isynBranch", response.branches);
            cachedTypesByBranch = response.types || {};
            cachedCategoriesByType = response.categories || {};

            setupDataTable();
            enableFormControls();
        },
        error(err) {
            console.error("Initialize error", err);
            Swal.fire({ icon: "error", title: "Initialization failed" });
        }
    });
}

function disableFormControls() {
    $("#addButton, #submit-btn, #cancel-btn").prop("disabled", true);
    $("form input, form select, form button").prop("disabled", true);
}

function enableFormControls() {
    $("#addButton").prop("disabled", false);
    $("form input, form select, form button").prop("disabled", false);
    $("#order_no").prop("disabled", true);
    $("#sender").prop("readonly", true);
    $("#submit-btn").prop("disabled", true);
    $("#cancel-btn").prop("disabled", true);
}

/* -------------------------------------------------------------------------- */
/* Event Binding                                                              */
/* -------------------------------------------------------------------------- */
function bindEventHandlers() {
    $("#isynBranch").on("change", handleBranchChange);
    $("#type").on("change", handleTypeChange);
    $("#category").on("change", handleCategoryChange);
    $("input[name='inlineRadioOptions']").on("change", handleSelectModeChange);
    $("#itemSelect").on("change", handleItemChange);
    $("#SIno").on("change", handleSINoChange);
    $("#quantityInput").on("input", handleQuantityInput);
    $("#editSRPtoggle").on("click", toggleEditSRP);

    $("#addButton").on("click", addItemToTable);
    $("#cancelProduct").on("click", cancelSingleProduct);
    $("#submit-btn").on("click", submitOrderConfirmation);
    $("#cancel-btn").on("click", resetForms);

    $("#searchTransmittalBtn").on("click", openTransmittalModal);
}

/* -------------------------------------------------------------------------- */
/* Dropdown Flow                                                              */
/* -------------------------------------------------------------------------- */
function populateSelect(selector, options, placeholder = "Select") {
    const $el = $(selector);
    $el.empty().append(`<option value="" disabled selected>${placeholder}</option>`);
    if (!options) return;
    options.forEach(opt => {
        $el.append(`<option value="${opt.value || opt}">${opt.label || opt}</option>`);
    });
}

function handleBranchChange() {
    const branch = $(this).val();
    const types = cachedTypesByBranch[branch] || [];
    populateSelect("#type", types, "Select Type");
    resetSelects(["#category", "#itemSelect", "#SIno"]);
    resetProductSummary();
}

function handleTypeChange() {
    const type = $(this).val();
    const categories = cachedCategoriesByType[type] || [];
    populateSelect("#category", categories, "Select Category");
    resetSelects(["#itemSelect", "#SIno"]);
    resetProductSummary();
}

function handleCategoryChange() {
    const category = $(this).val();
    currentSelectMode = $("input[name='inlineRadioOptions']:checked").val();
    if (!currentSelectMode) {
        $("#itemSelect").empty().append(`<option value="" disabled selected>Select mode first</option>`);
        return;
    }
    fetchItemOptions(category, currentSelectMode);
}

function handleSelectModeChange() {
    currentSelectMode = $(this).val();
    $("#itemSelect").empty().append(`<option value="" disabled selected>Select</option>`);
    $("#SIno").empty().append(`<option value="" disabled selected>Select</option>`);
    resetProductSummary();
    if ($("#category").val()) {
        fetchItemOptions($("#category").val(), currentSelectMode);
    }
}

function handleItemChange() {
    const selected = $(this).val();
    if (!selected) return;
    const siList = cachedSINoByItem[selected] || [];
    populateSelect("#SIno", siList, "Select SI No");
    resetProductSummary();
}

function handleSINoChange() {
    const siNo = $(this).val();
    if (!siNo) return;
    fetchProductSummary(siNo);
}

function resetSelects(selectors) {
    selectors.forEach(sel => {
        $(sel).empty().append(`<option value="" disabled selected>Select</option>`);
    });
}

function fetchItemOptions(category, mode) {
    const branch = $("#isynBranch").val();
    const type = $("#type").val();
    if (!branch || !type || !category || !mode) return;

    $.ajax({
        url: "../../routes/inventorymanagement/orderconfirmation.route.php",
        type: "POST",
        data: {
            action: "FetchItems",
            branch,
            type,
            category,
            mode
        },
        dataType: "JSON",
        success(response) {
            const { items = [], siNoMap = {} } = response;
            cachedItemsBySelection = items;
            cachedSINoByItem = siNoMap;
            populateSelect("#itemSelect", items, "Select");
            $("#SIno").empty().append(`<option value="" disabled selected>Select</option>`);
        }
    });
}

/* -------------------------------------------------------------------------- */
/* Product Summary                                                            */
/* -------------------------------------------------------------------------- */
function fetchProductSummary(siNo) {
    $.ajax({
        url: "../../routes/inventorymanagement/orderconfirmation.route.php",
        type: "POST",
        data: { action: "ProductSummary", siNo },
        dataType: "JSON",
        success(response) {
            productSummary = response || {};
            populateProductSummary(productSummary);
        }
    });
}

function populateProductSummary(summary) {
    $("#supplierSIdisplay").val(summary.SIno || "");
    $("#serialNodisplay").val(summary.Serialno || "");
    $("#productDisplay").val(summary.product || "");
    $("#supplierDisplay").val(summary.Supplier || "");
    $("#srpDisplay").val(summary.SRP || "");
    $("#quantityDisplay").val(summary.Quantity || "");
    $("#delearsPriceDisplay").val(summary.DealerPrice || "");
    $("#totalPriceDisplay").val(summary.TotalPrice || "");
    $("#warranty").val(summary.Warranty || "");
    $("#vat").val(summary.Vat || "");
    $("#vatsales").val(summary.VatSales || "");
    $("#editSRP").val("");
    $("#quantityInput").val("");
    $("#submit-btn").prop("disabled", true);
}

function resetProductSummary() {
    populateProductSummary({});
}

/* -------------------------------------------------------------------------- */
/* Quantity / SRP                                                             */
/* -------------------------------------------------------------------------- */
function handleQuantityInput() {
    quantityInput = parseFloat($(this).val()) || 0;
    if (!productSummary.SRP) {
        $("#editSRP").val("");
        return;
    }
    const total = quantityInput * parseFloat(productSummary.SRP);
    $("#editSRP").val(total ? total.toFixed(2) : "");
}

function toggleEditSRP() {
    const enabled = $(this).is(":checked");
    $("#editSRP").prop("disabled", !enabled);
    if (!enabled) {
        $("#editSRP").val("");
    }
}

/* -------------------------------------------------------------------------- */
/* DataTable                                                                  */
/* -------------------------------------------------------------------------- */
function setupDataTable() {
    orderItemsTbl = $("#table1").DataTable({
        searching: false,
        ordering: false,
        paging: false,
        lengthChange: false,
        info: false,
        scrollY: "300px",
        scrollX: true,
        scrollCollapse: true,
        responsive: false
    });

    $("#table1 tbody").on("click", "tr", function () {
        if (!orderItemsTbl) return;
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            selectedRow = null;
            $("#cancel-btn").prop("disabled", true);
        } else {
            orderItemsTbl.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
            selectedRow = this;
            $("#cancel-btn").prop("disabled", false);
        }
    });
}

/* -------------------------------------------------------------------------- */
/* Table / Item Handling                                                      */
/* -------------------------------------------------------------------------- */
function addItemToTable(e) {
    e.preventDefault();

    const product = $("#productDisplay").val();
    const quantity = $("#quantityInput").val();
    const srp = $("#editSRP").val() || $("#srpDisplay").val();
    const siNo = $("#SIno").val();
    const serial = $("#serialNodisplay").val();
    const category = $("#category").val();
    const type = $("#type").val();
    const branch = $("#isynBranch").val();
    const supplier = $("#supplierDisplay").val();
    const vat = $("#vat").val();
    const vatSales = $("#vatsales").val();
    const warranty = $("#warranty").val();
    const datePrepared = new Date().toLocaleDateString();

    if (!product || !quantity || !srp || !siNo) {
        Swal.fire({ icon: "warning", title: "Please complete product info" });
        return;
    }

    orderItemsTbl.row.add([
        product,
        quantity,
        parseFloat(srp).toFixed(2),
        siNo,
        serial,
        product,
        supplier,
        category,
        type,
        branch,
        vat,
        vatSales,
        warranty,
        datePrepared
    ]).draw(false);

    $("#submit-btn").prop("disabled", false);
    $("#cancel-btn").prop("disabled", false);
    resetForms(false);
}

function cancelSingleProduct() {
    if (!selectedRow) {
        Swal.fire({ icon: "warning", title: "Select an item to remove" });
        return;
    }
    orderItemsTbl.row(selectedRow).remove().draw(false);
    selectedRow = null;
    $("#cancel-btn").prop("disabled", true);
    if (orderItemsTbl.rows().count() === 0) {
        $("#submit-btn").prop("disabled", true);
    }
}

function resetForms(hard = true) {
    if (hard) {
        $("#myForm")[0].reset();
        $("#orderform")[0].reset();
        $("#summary")[0].reset();
        $("#compute")[0].reset();
        $("#tableBody").empty();
        orderItemsTbl.clear().draw();
        $("#submit-btn").prop("disabled", true);
        $("#cancel-btn").prop("disabled", true);
    } else {
        $("#orderform")[0].reset();
        $("#summary")[0].reset();
        $("#compute")[0].reset();
    }
}

/* -------------------------------------------------------------------------- */
/* Submission                                                                 */
/* -------------------------------------------------------------------------- */
function submitOrderConfirmation() {
    const rows = orderItemsTbl.rows().data().toArray();
    if (!rows.length) {
        Swal.fire({ icon: "warning", title: "No items to submit" });
        return;
    }

    const payload = rows.map(row => ({
        product: row[0],
        quantity: row[1],
        srp: row[2],
        SINo: row[3],
        serialNo: row[4],
        category: row[8],
        type: row[9],
        branch: row[10],
        vat: row[11],
        vatSales: row[12],
        warranty: row[13],
        datePrepared: row[14],
        order_no: currentOrderNo,
        recipient: $("#recipient").val(),
        sender: $("#sender").val()
    }));

    $.ajax({
        url: "./ajax-inventory/submit-btn-order-confirmation.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success() {
            Swal.fire({ icon: "success", title: "Order saved" }).then(() => location.reload());
        },
        error(err) {
            console.error("Submit error", err);
            Swal.fire({ icon: "error", title: "Submission failed" });
        }
    });
}

/* -------------------------------------------------------------------------- */
/* Transmittal Modal (just hook point here)                                   */
/* -------------------------------------------------------------------------- */
function openTransmittalModal() {
    // If you need to hook a modal like transmittal receipt, call it here.
    // $("#exampleModal").modal("show");
}
