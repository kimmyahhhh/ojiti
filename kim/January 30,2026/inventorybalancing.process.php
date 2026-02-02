<?php
include_once("../../database/connection.php");

class Process extends Database
{
    public function Initialize(){
        date_default_timezone_set('Asia/Manila');
        // $AsOf = date("m/d/Y", strtotime("now"));

        // $DateFrom = date("m")."/01/".date("Y");
        // $DateTo = date("m")."/31/".date("Y");
        $DateFrom = "02/01/2025";
        $DateTo = "02/31/2025";
        
        // $PrevInvDate = date("m/Y", strtotime("now"));
        // $PrevInvDate = date("m/Y", strtotime("-1 month"));
        // $PrevInvDateMonth = date("m", strtotime($PrevInvDate));
        // $PrevInvDateYear = date("Y", strtotime($PrevInvDate));
        // $PrevInvDateMonth = date("m", strtotime("-1 month"));
        // $PrevInvDateYear = date("Y", strtotime("-1 month"));
        $PrevInvDateMonth = date("m", strtotime("01/01/2025"));
        $PrevInvDateYear = date("Y", strtotime("01/31/2025"));

        $AsOf = "";
        $qtyInvEnd = "";
        $dpInvEnd = "";

        $qtyInvCurrent = 0.00;
        $dpInvCurrent = 0.00;
        $qtyInvOut = 0.00;
        $dpInvOut = 0.00;
        $qtyInvIn = 0.00;
        $dpInvIn = 0.00;
        $qtyTransfer = 0.00;
        $dpTransfer = 0.00;
        $qtyReceived = 0.00;
        $dpReceived = 0.00;
        $qtyReturned = 0.00;
        $dpReturned = 0.00;
        $myTotalQty = 0;
        $myTotalDP = 0;

        $ChangeDPTotals = 0;

        // Previous Month Inventory
        $stmt = $this->conn->prepare("SELECT DISTINCT AsOf FROM tbl_inventoryend WHERE SUBSTRING(AsOf,1,2) = ? AND SUBSTRING(AsOf,7,10) = ? ORDER BY AsOf DESC LIMIT 1");
        $stmt->bind_param('ss', $PrevInvDateMonth,$PrevInvDateYear);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $AsOf = $row['AsOf'];
        }

        $stmt = $this->conn->prepare("SELECT SUM(Quantity) AS Quantity, SUM(TotalPrice) AS TotalPrice FROM tbl_inventoryend WHERE STR_TO_DATE(AsOf, '%m/%d/%Y') = STR_TO_DATE(?, '%m/%d/%Y')");
        $stmt->bind_param('s', $AsOf);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $qtyInvEnd = ($row['Quantity'] == null) ? 0 : $row['Quantity'];
            $dpInvEnd = ($row['TotalPrice'] == null) ? 0 : $row['TotalPrice'];
        }

        // As Of Current Inventory
        $stmt = $this->conn->prepare("SELECT * FROM tbl_invlist");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $stmt1 = $this->conn->prepare("SELECT SUM(Quantity) AS Quantity, ROUND(SUM(TotalPrice), 2) AS TotalPrice FROM tbl_invlist");
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $stmt1->close();
            $row1 = $result1->fetch_assoc();
            $qtyInvCurrent = $row1['Quantity'];
            $dpInvCurrent = $row1['TotalPrice'];
        } else {
            $qtyInvCurrent = 0.00;
            $dpInvCurrent = 0.00;
        }

        // Inventory Out
        $stmt = $this->conn->prepare("SELECT * FROM tbl_inventoryout WHERE STR_TO_DATE(DateAdded, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateAdded, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
        $stmt->bind_param('ss', $DateFrom,$DateTo);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $stmt1 = $this->conn->prepare("SELECT SUM(Quantity) AS Quantity, SUM(TotalPrice) AS TotalPrice FROM tbl_inventoryout WHERE STR_TO_DATE(DateAdded, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateAdded, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
            $stmt1->bind_param('ss', $DateFrom,$DateTo);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $stmt1->close();
            $row1 = $result1->fetch_assoc();
            $qtyInvOut = $row1['Quantity'];
            $dpInvOut = $row1['TotalPrice'];
        } else {
            $qtyInvOut = 0.00;
            $dpInvOut = 0.00;
        }

        // Inventory In
        $stmt = $this->conn->prepare("SELECT * FROM tbl_inventoryin WHERE STR_TO_DATE(DateAdded, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateAdded, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
        $stmt->bind_param('ss', $DateFrom,$DateTo);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $stmt1 = $this->conn->prepare("SELECT SUM(Quantity) AS Quantity, SUM(TotalPrice) AS TotalPrice FROM tbl_inventoryin WHERE STR_TO_DATE(DateAdded, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateAdded, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
            $stmt1->bind_param('ss', $DateFrom,$DateTo);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $stmt1->close();
            $row1 = $result1->fetch_assoc();
            $qtyInvIn = $row1['Quantity'];
            $dpInvIn = $row1['TotalPrice'];
        } else {
            $qtyInvIn = 0.00;
            $dpInvIn = 0.00;
        }

        // Transfer Product - Transfer
        $stmt = $this->conn->prepare("SELECT * FROM tbl_transferproducttotals WHERE TransferType = 'TRANSFER' AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
        $stmt->bind_param('ss', $DateFrom,$DateTo);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $stmt1 = $this->conn->prepare("SELECT SUM(TotalQuantity) AS TotalQuantity, SUM(TotalDP) AS TotalDP FROM tbl_transferproducttotals WHERE TransferType = 'TRANSFER' AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
            $stmt1->bind_param('ss', $DateFrom,$DateTo);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $stmt1->close();
            $row1 = $result1->fetch_assoc();
            $qtyTransfer = $row1['TotalQuantity'];
            $dpTransfer = $row1['TotalDP'];
        } else {
            $qtyTransfer = 0.00;
            $dpTransfer = 0.00;
        }

        // Transfer Product - Received
        $stmt = $this->conn->prepare("SELECT * FROM tbl_transferproducttotals WHERE TransferType = 'RECEIVED' AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
        $stmt->bind_param('ss', $DateFrom,$DateTo);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $stmt1 = $this->conn->prepare("SELECT SUM(TotalQuantity) AS TotalQuantity, SUM(TotalDP) AS TotalDP FROM tbl_transferproducttotals WHERE TransferType = 'RECEIVED' AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateTransfer, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
            $stmt1->bind_param('ss', $DateFrom,$DateTo);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $stmt1->close();
            $row1 = $result1->fetch_assoc();
            $qtyReceived = $row1['TotalQuantity'];
            $dpReceived = $row1['TotalDP'];
        } else {
            $qtyReceived = 0.00;
            $dpReceived = 0.00;
        }

            // Purchase Returned
            $stmt = $this->conn->prepare("SELECT * FROM tbl_purchasereturned WHERE STR_TO_DATE(AsOf, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(AsOf, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
            $stmt->bind_param('ss', $DateFrom,$DateTo);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $stmt1 = $this->conn->prepare("SELECT SUM(TotalQuantity) AS TotalQuantity, SUM(TotalPrice) AS TotalPrice FROM tbl_purchasereturned WHERE STR_TO_DATE(AsOf, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(AsOf, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
                $stmt1->bind_param('ss', $DateFrom,$DateTo);
                $stmt1->execute();
                $result1 = $stmt1->get_result();
                $stmt1->close();
                $row1 = $result1->fetch_assoc();
                $qtyReturned = $row1['TotalQuantity'];
                $dpReturned = $row1['TotalPrice'];
            } else {
                $qtyReturned = 0.00;
                $dpReturned = 0.00;
            }

            $myTotalQty = $myTotalQty + $qtyInvEnd;
            $myTotalQty = $myTotalQty + $qtyInvIn;
            $myTotalQty = $myTotalQty + $qtyReceived;
            $myTotalQty = $myTotalQty - $qtyInvOut;
            $myTotalQty = $myTotalQty - $qtyTransfer;
            $myTotalQty = $myTotalQty - $qtyReturned;

            $myTotalDP = $myTotalDP + $dpInvEnd;
            $myTotalDP = $myTotalDP + $dpInvIn;
            $myTotalDP = $myTotalDP + $dpReceived;
            $myTotalDP = $myTotalDP - $dpInvOut;
            $myTotalDP = $myTotalDP - $dpTransfer;
            $myTotalDP = $myTotalDP - $dpReturned;
            $myTotalDP = round($myTotalDP, 2);

            // Change Product Dealer Price For the Month
            $stmt = $this->conn->prepare("SELECT * FROM tbl_srp_dp_producthistory WHERE ChangeType = 'DP' AND STR_TO_DATE(DateChange, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateChange, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
            $stmt->bind_param('ss', $DateFrom,$DateTo);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $stmt1 = $this->conn->prepare("SELECT * FROM tbl_srp_dp_producthistory WHERE ChangeType = 'DP' AND STR_TO_DATE(DateChange, '%m/%d/%Y') >= STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(DateChange, '%m/%d/%Y') <= STR_TO_DATE(?, '%m/%d/%Y')");
                $stmt1->bind_param('ss', $DateFrom,$DateTo);
                $stmt1->execute();
                $result1 = $stmt1->get_result();
                $stmt1->close();
                while ($row1 = $result1->fetch_assoc()) {
                    $NewT = 0;
                    $OldT = 0;
                    $NewT = $row1['NewTotalPrice'];
                    $OldT = $row1['OldTotalPrice'];

                    $ChangeDPTotals = $ChangeDPTotals + $NewT + $OldT;

                    $myTotalDP += $NewT - $OldT;

                }
                $ChangeDP = $ChangeDPTotals;
            } else {
                $ChangeDP = 0.00;
            }

            $qtyInvDif = "";
            $qtyInvDifStat = "";
            $qty1 = $myTotalQty;
            $qty2 = $qtyInvCurrent;

            $difQty = ($qty1 - $qty2);
            if ($difQty <> 0){
                if ($difQty < 0){
                    $qtyInvDif = floatval($qty1 - $qty2) * -1;
                } else {
                    $qtyInvDif = floatval($qty1 - $qty2);
                }
                $qtyInvDifStat = "Negative";
            } else {
                $qtyInvDif = floatval($qty1 - $qty2);
                $qtyInvDifStat = "Positive";
            }

            $dpInvDif = "";
            $dpInvDifStat = "";
            $dp1 = $myTotalDP;
            $dp2 = $dpInvCurrent;

            $difDP = ($dp1 - $dp2);
            if ($difDP <> 0){
                if ($difDP < 0){
                    $dpInvDif = floatval($dp1 - $dp2) * -1;
                } else {
                    $dpInvDif = floatval($dp1 - $dp2);
                }
                $dpInvDifStat = "Negative";
            } else {
                $dpInvDif = floatval($dp1 - $dp2);
                $dpInvDifStat = "Positive";
            }

            if ($difQty <> 0 || $difDP <> 0){
                $stmt = $this->conn->prepare("UPDATE tbl_configuration SET Value = 'NO' WHERE ConfigName = 'BALANCE' and ConfigOwner = 'INVENTORYSTATUS'");
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $this->conn->prepare("UPDATE tbl_configuration SET Value = 'YES' WHERE ConfigName = 'BALANCE' and ConfigOwner = 'INVENTORYSTATUS'");
                $stmt->execute();
                $stmt->close();
            }
            

        echo json_encode(array( 
            "QTYINVEND" => $qtyInvEnd,
            "DPINVEND" => $dpInvEnd,
            "QTYINVCURR" => $qtyInvCurrent,
            "DPINVCURR" => $dpInvCurrent,
            "QTYINVOUT" => $qtyInvOut,
            "DPINVOUT" => $dpInvOut,
            "QTYINVIN" => $qtyInvIn,
            "DPINVIN" => $dpInvIn,
            "QTYTRANSFER" => $qtyTransfer,
            "DPTRANSFER" => $dpTransfer,
            "QTYRECEIVED" => $qtyReceived,
            "DPRECEIVED" => $dpReceived,
            "QTYRETURNED" => $qtyReturned,
            "DPRETURNED" => $dpReturned,
            "TOTALQTY" => $myTotalQty,
            "TOTALDP" => $myTotalDP,
            "CHANGEDP" => $ChangeDP,
            "QTYINVDIF" => $qtyInvDif,
            "DPINVDIF" => $dpInvDif,
            "QTYINVDIFSTAT" => $qtyInvDifStat,
            "DPINVDIFSTAT" => $dpInvDifStat,
            "BRUH" => $difDP,
            
            "DATEFROM" => $DateFrom,
            "DATETO" => $DateTo,
            "PREVINVMONTH" => $PrevInvDateMonth,
            "PREVINVYEAR" => $PrevInvDateYear,
        ));
    }

    public function SelectQuery($string){
        $data = [];
        $stmt = $this->conn->prepare($string);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
