<?php
// Disable error reporting to output for JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
session_start();

include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (is_array($data)) {
    try {
        $conn->begin_transaction();

        // Columns in tbl_purchasereturned:
        // Batchno, SIno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, VatSales, Vat, AmountDue, DateAdded, DatePurchase, User, AsOf, ProdPend, Stock, Branch, Warranty, imgname, TransactionNo, Reason
        
        // Get max Batchno for manual increment since it's not AUTO_INCREMENT
        $res = $conn->query("SELECT MAX(Batchno) as m FROM tbl_purchasereturned");
        $maxRow = $res->fetch_assoc();
        $nextBatchNo = ($maxRow['m'] ?? 0) + 1;

        $stmt = $conn->prepare("INSERT INTO tbl_purchasereturned (Batchno, TransactionNo, Product, SIno, Serialno, Quantity, Type, Category, Supplier, DealerPrice, TotalPrice, SRP, TotalSRP, Branch, User, AsOf, Reason, DateAdded) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $user = $_SESSION['USERNAME'] ?? 'System';
        $asOf = date('m/d/Y');

        foreach ($data as $row) {
            $TransactionNo = $row['TransactionNo'] ?? '';
            $Product = $row['Product'] ?? '';
            $SIno = $row['SIno'] ?? '';
            $SerialNo = $row['SerialNo'] ?? '';
            $Quantity = $row['Quantity'] ?? 0;
            // TransactionType is ignored as it's not in the table
            $Type = $row['Type'] ?? '';
            $Category = $row['Category'] ?? '';
            $Supplier = $row['Supplier'] ?? '';
            $DealerPrice = $row['DealerPrice'] ?? 0;
            $TotalPrice = $row['TotalPrice'] ?? 0;
            $SRP = $row['SRP'] ?? 0;
            $TotalSRP = $row['TotalSRP'] ?? 0;
            $Branch = $row['Branch'] ?? '';
            $Reason = $row['Reason'] ?? '';

            $stmt->bind_param("issssssssssssssss", $nextBatchNo, $TransactionNo, $Product, $SIno, $SerialNo, $Quantity, $Type, $Category, $Supplier, $DealerPrice, $TotalPrice, $SRP, $TotalSRP, $Branch, $user, $asOf, $Reason);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $nextBatchNo++;
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        error_log("Error in submit-purchase-return.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
}
?>
