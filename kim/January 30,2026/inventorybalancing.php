<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
?>

<!doctype html>
<html lang="en" dir="ltr">
    <?php
        include('../../includes/pages.header.php');
    ?>
      <link rel="stylesheet" href="../../assets/datetimepicker/jquery.datetimepicker.css">
      <link rel="stylesheet" href="../../assets/select2/css/select2.min.css">

    <body class="  ">
        <!-- loader Start -->
        <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
        </div>
        <!-- loader END -->

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <style>
                label {
                    color: #090909;
                }
                .Negative {
                    color:rgb(189, 15, 15) !important;
                }
                .form-control {
                    color: #090909;
                    background-color:rgba(206, 209, 157, 0.34);
                    border:1px solid #000000;
                }
            </style>

            <div class="container mt-1">
                <div class="shadow p-3 rounded-2" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Inventory Balancing</p>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row">
                                <div class="col-2">
                                    <div></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <div><label>LAST MONTH INVENTORY END</label></div>
                                        </div>
                                        <div class="col-4">
                                            <div><label>INVENTORY INCOMING</label></div>
                                        </div>
                                        <div class="col-4">
                                            <div><label>TRANSFER PRODUCT (RECEIVED)</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-2">
                                    <div class="mt-2"><label>Total Qty:</label></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" id="lastInvEndTotalQty" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="InvInTotalQty" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="transferProductReceivedTotalQty" class="form-control text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-1">
                                <div class="col-2">
                                    <div class="mt-2"><label>Total DP:</label></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" id="lastInvEndTotalDP" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="InvInTotalDP" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="transferProductReceivedTotalDP" class="form-control text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row">
                                <div class="col-2">
                                    <div></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <div><label>INVENTORY OUTGOING</label></div>
                                        </div>
                                        <div class="col-4">
                                            <div><label>TRANSFER PRODUCT (TRANSFER)</label></div>
                                        </div>
                                        <div class="col-4">
                                            <div><label>PURCHASE RETURNED</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-2">
                                    <div class="mt-2"><label>Total Qty:</label></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" id="InvOutTotalQty" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="transferProductTransferTotalQty" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="PurchaseReturnedTotalQty" class="form-control text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-1">
                                <div class="col-2">
                                    <div class="mt-2"><label>Total DP:</label></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" id="InvOutTotalDP" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="transferProductTransferTotalDP" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="PurchaseReturnedTotalDP" class="form-control text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <div class="shadow p-3 rounded-3" style="background-color: white;">
                            <div class="row">
                                <div class="col-2">
                                    <div></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <div><label>END OF THE MONTH</label></div>
                                        </div>
                                        <div class="col-4">
                                            <div><label>CURRENT INVENTORY</label></div>
                                        </div>
                                        <div class="col-4">
                                            <div><label id="diffLbl">DIFFERENCE</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-2">
                                    <div class="mt-2"><label>Total Qty:</label></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" id="EndOfMonthTotalQty" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="CurrentInvTotalQty" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="DifferenceTotalQty" class="form-control text-end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-1">
                                <div class="col-2">
                                    <div class="mt-2"><label>Total DP:</label></div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="text" id="EndOfMonthTotalDP" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="CurrentInvTotalDP" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" id="DifferenceTotalDP" class="form-control text-end" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mt-3">
                                            <div><label>CHANGE DEALER PRICE</label></div>
                                            <input type="text" id="ChangeDealerPrice" class="form-control text-end" readonly>
                                        </div>
                                        <div class="col-8 mt-3 pt-4">
                                            <button type="button" id="recomputeButton" class="btn btn-primary" onclick="Initialize();"><i class="fa-solid fa-arrows-rotate"></i> Re-compute</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                  
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/inventorymanagement/inventorybalancing.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>