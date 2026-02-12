<?php
// Disable error reporting to output for JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

try {
    // Get all products from inventory list
    $stmt = $conn->prepare("SELECT Product, SIno, Serialno, Quantity FROM tbl_invlist ORDER BY Product ASC");
    
    if (!$stmt) {
         throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'Product' => $row['Product'],
            'SIno' => $row['SIno'],
            'Serialno' => $row['Serialno'],
            'Quantity' => $row['Quantity']
        ];
    }
    
    echo json_encode($data);
    $stmt->close();
} catch (Exception $e) {
    error_log("Error in load-all-products.php: " . $e->getMessage());
    echo json_encode([]);
}
?>
