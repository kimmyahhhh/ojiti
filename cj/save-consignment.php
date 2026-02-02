<?php
include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$successCount = 0;
$errorCount = 0;

$conn->begin_transaction();

$logFile = dirname(__FILE__) . '/consignment_log.txt';
$colsRes = $conn->query("SHOW COLUMNS FROM tbl_invlistconsign");
$actualCols = [];
while($c = $colsRes->fetch_assoc()) { $actualCols[] = $c['Field']; }
file_put_contents($logFile, "Starting consignment process at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents($logFile, "Database: " . DB . " on " . HOST . "\n", FILE_APPEND);
file_put_contents($logFile, "Table columns: " . implode(", ", $actualCols) . "\n", FILE_APPEND);

try {
    foreach ($data as $item) {
        file_put_contents($logFile, "Processing item: " . $item['product'] . " (SI: " . $item['SIno'] . ")\n", FILE_APPEND);
        
        // Fetch original item details from tbl_invlist to ensure consistency
        $stmt = $conn->prepare("SELECT * FROM tbl_invlist WHERE SIno = ? AND Product = ? AND Category = ? AND Branch = ? LIMIT 1");
        $stmt->bind_param("ssss", $item['SIno'], $item['product'], $item['category'], $item['branch']);
        $stmt->execute();
        $original = $stmt->get_result()->fetch_assoc();
        
        if (!$original) {
            file_put_contents($logFile, "Exact match failed, trying fallback search...\n", FILE_APPEND);
            // Fallback: try without category/branch if exact match fails
            $stmt = $conn->prepare("SELECT * FROM tbl_invlist WHERE SIno = ? AND Product = ? LIMIT 1");
            $stmt->bind_param("ss", $item['SIno'], $item['product']);
            $stmt->execute();
            $original = $stmt->get_result()->fetch_assoc();
        }
        
        if (!$original) {
            throw new Exception("Original item not found in tbl_invlist: " . $item['product'] . " (SI: " . $item['SIno'] . ")");
        }

        // Deduct from original inventory
        $qtyToDeduct = intval($item['quantity']);
        $currentQty = intval($original['Quantity']);
        $newQty = $currentQty - $qtyToDeduct;
        
        if ($newQty < 0) {
            throw new Exception("Insufficient stock for: " . $item['product'] . " (Current: $currentQty, Requested: $qtyToDeduct)");
        }

        // Update original inventory
        $updateStmt = $conn->prepare("UPDATE tbl_invlist SET Quantity = ? WHERE SIno = ? AND Serialno = ? AND Product = ? AND Category = ? AND Branch = ?");
        $updateStmt->bind_param("isssss", $newQty, $original['SIno'], $original['Serialno'], $original['Product'], $original['Category'], $original['Branch']);
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update inventory: " . $updateStmt->error);
        }
        file_put_contents($logFile, "Inventory updated. Affected rows: " . $updateStmt->affected_rows . "\n", FILE_APPEND);

        // Calculate unit markup
        $totalMarkup = floatval($item['markup']);
        $unitMarkup = $qtyToDeduct > 0 ? $totalMarkup / $qtyToDeduct : 0;

        // Insert into consignment table
        file_put_contents($logFile, "Preparing insert statement...\n", FILE_APPEND);
        $insertSql = "INSERT INTO tbl_invlistconsign (Batchno, SIno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, VatSales, Vat, AmountDue, DateAdded, DatePurchase, User, AsOf, ProdPend, Stock, Branch, Warranty, imgname) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insertStmt = $conn->prepare($insertSql);
        if (!$insertStmt) {
            file_put_contents($logFile, "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $user = $_SESSION['USERNAME'] ?? 'SYSTEM';
        $batchNo = (string)($original['Batchno'] ?? '0');
        $siNo = (string)($item['SIno'] ?? '');
        $serialNo = (string)($item['supplierSI'] ?? ''); 
        $productName = (string)($item['product'] ?? '');
        $supplierName = (string)($item['supplier'] ?? '');
        $categoryName = (string)($item['category'] ?? '');
        $typeName = (string)($item['type'] ?? '');
        $q = (string)$qtyToDeduct;
        $dp = (string)($item['dealersPrice'] ?? '0');
        $tp = (string)($item['totalPrice'] ?? '0');
        $s = (string)($item['srp'] ?? '0');
        $ts = (string)($item['totalSRP'] ?? '0');
        $m = (string)$unitMarkup;
        $tm = (string)$totalMarkup;
        $vs = (string)($item['vatsale'] ?? '0');
        $v = (string)($item['vat'] ?? '0');
        $ad = (string)($item['amountDue'] ?? '0');
        $dA = (string)($item['dateAdded'] ?? '');
        $dP = (string)($original['DatePurchase'] ?? $dA);
        $aO = (string)($original['AsOf'] ?? date('m/d/Y'));
        $pP = (string)($original['ProdPend'] ?? 'NO');
        $stk = (string)($item['stock'] ?? '');
        $brn = (string)($item['branch'] ?? '');
        $wrn = (string)($original['Warranty'] ?? '0');
        $img = (string)($original['imgname'] ?? '');

        file_put_contents($logFile, "Binding parameters...\n", FILE_APPEND);
        $insertStmt->bind_param("ssssssssssssssssssssssssss", 
            $batchNo, $siNo, $serialNo, $productName, $supplierName, $categoryName, $typeName, 
            $q, $dp, $tp, $s, $ts, 
            $m, $tm, $vs, $v, $ad, 
            $dA, $dP, $user, $aO, $pP, $stk, $brn, $wrn, $img
        );
        
        file_put_contents($logFile, "Executing insert...\n", FILE_APPEND);
        if (!$insertStmt->execute()) {
            file_put_contents($logFile, "Insert failed: " . $insertStmt->error . "\n", FILE_APPEND);
            throw new Exception("Insert failed: " . $insertStmt->error);
        }
        file_put_contents($logFile, "Insert successful. ID: " . $conn->insert_id . "\n", FILE_APPEND);
        $successCount++;
    }

    $conn->commit();
    file_put_contents($logFile, "Transaction committed successfully.\n", FILE_APPEND);
    
    // VERIFICATION: Check if data is actually there now
    $check = $conn->query("SELECT * FROM tbl_invlistconsign WHERE SIno = '" . $item['SIno'] . "' AND Product = '" . $item['product'] . "' ORDER BY DateAdded DESC LIMIT 1");
    if ($row = $check->fetch_assoc()) {
        file_put_contents($logFile, "VERIFIED: Item exists in tbl_invlistconsign. SI: " . $row['SIno'] . "\n", FILE_APPEND);
    } else {
        file_put_contents($logFile, "VERIFICATION FAILED: Item NOT found in tbl_invlistconsign after commit!\n", FILE_APPEND);
    }
    
    echo json_encode(['success' => true, 'message' => "$successCount items successfully consigned."]);

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    file_put_contents($logFile, "CRITICAL ERROR: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
