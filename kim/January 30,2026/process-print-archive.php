<?php
session_start();
include('../../../database/connection.php');

header('Content-Type: application/json');

// Error handling to prevent HTML output on fatal errors
ini_set('display_errors', 0);
error_reporting(E_ALL);

function send_json_error($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

if (!isset($_SESSION['EMPNO']) || !isset($_SESSION['USERNAME']) || !isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] !== true) {
    send_json_error('Unauthorized');
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['ids']) || !is_array($input['ids']) || empty($input['ids'])) {
    send_json_error('No items selected');
}

$ids = $input['ids'];
$db = new Database();
$conn = $db->conn;

// 0. Check schema of tbl_purchasereturnedtransaction and add TotalAmount if missing
// We are using the existing table to store the returned items history.
// We also need to store the TotalAmount for this transaction.
$checkCol = $conn->query("SHOW COLUMNS FROM tbl_purchasereturnedtransaction LIKE 'TotalAmount'");
if ($checkCol->num_rows == 0) {
    // Add the column
    $conn->query("ALTER TABLE tbl_purchasereturnedtransaction ADD COLUMN TotalAmount DECIMAL(15, 2) DEFAULT 0.00 AFTER TransactionNo");
}

// 1. Calculate Total Amount for these items
$count = count($ids);
$types = str_repeat('s', $count);
$placeholders = implode(',', array_fill(0, $count, '?'));

// Handle TotalPrice being VARCHAR with commas
$sumSql = "SELECT SUM(CAST(REPLACE(TotalPrice, ',', '') AS DECIMAL(15, 2))) as total FROM tbl_purchasereturned WHERE Batchno IN ($placeholders)";
$stmtSum = $conn->prepare($sumSql);
if ($stmtSum) {
    $stmtSum->bind_param($types, ...$ids);
    $stmtSum->execute();
    $resultSum = $stmtSum->get_result();
    $rowSum = $resultSum->fetch_assoc();
    $totalAmount = $rowSum['total'] ?? 0;
    $stmtSum->close();
    
    // Generate a Transaction Number
    $transactionNo = 'RTN-' . date('YmdHis');
    $user = $_SESSION['USERNAME'] ?? 'System';
    
    // 2. Insert items into tbl_purchasereturnedtransaction
    // We copy the items from tbl_purchasereturned to tbl_purchasereturnedtransaction
    // And set the TransactionNo and TotalAmount
    
    // Fetch the items details
    $fetchSql = "SELECT * FROM tbl_purchasereturned WHERE Batchno IN ($placeholders)";
    $stmtFetch = $conn->prepare($fetchSql);
    if ($stmtFetch) {
        $stmtFetch->bind_param($types, ...$ids);
        $stmtFetch->execute();
        $resultFetch = $stmtFetch->get_result();
        
        while ($row = $resultFetch->fetch_assoc()) {
            // Prepare insert
            // We need to map columns. Assuming tbl_purchasereturnedtransaction has similar columns to tbl_purchasereturned
            // based on the backup inspection, they share many columns.
            // We will explicitly map the common important ones and the new ones.
            
            // Columns in tbl_purchasereturnedtransaction (from inspection):
            // Batchno, SIno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice...
            // TransactionNo, TransactionType
            // + TotalAmount (added above)
            
            $insertSql = "INSERT INTO tbl_purchasereturnedtransaction (
                SIno, Serialno, Product, Supplier, Category, Type, Quantity, 
                DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, 
                VatSales, Vat, AmountDue, DateAdded, DatePurchase, User, 
                AsOf, ProdPend, Stock, Branch, Warranty, imgname, 
                TransactionNo, TransactionType, TotalAmount
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 'RETURNED', ?)";
            
            $stmtInsert = $conn->prepare($insertSql);
            if ($stmtInsert) {
                $stmtInsert->bind_param(
                    'sssssssssssssssssssssssssd',
                    $row['SIno'], $row['Serialno'], $row['Product'], $row['Supplier'], $row['Category'], $row['Type'], $row['Quantity'],
                    $row['DealerPrice'], $row['TotalPrice'], $row['SRP'], $row['TotalSRP'], $row['Markup'], $row['TotalMarkup'],
                    $row['VatSales'], $row['Vat'], $row['AmountDue'], 
                    // DateAdded is NOW()
                    $row['DatePurchase'], $user,
                    $row['AsOf'], $row['ProdPend'], $row['Stock'], $row['Branch'], $row['Warranty'], $row['imgname'],
                    $transactionNo, $totalAmount
                );
                if (!$stmtInsert->execute()) {
                    // Log error but continue or handle it
                    // error_log("Insert failed: " . $stmtInsert->error);
                }
                $stmtInsert->close();
            }
        }
        $stmtFetch->close();
    }
}

// 3. Update Status to 'Archived' for these items
// Use Batchno as it is the confirmed column name in archive-single-return-item.php
$sql = "UPDATE tbl_purchasereturned SET Status = 'Archived' WHERE Batchno IN ($placeholders)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    send_json_error('Database prepare error: ' . $conn->error);
}

$stmt->bind_param($types, ...$ids);

if ($stmt->execute()) {
    // 2. Store IDs in session for the print page
    $_SESSION['print_ids'] = $ids;
    echo json_encode(['status' => 'success']);
} else {
    send_json_error('Database execute error: ' . $stmt->error);
}
?>