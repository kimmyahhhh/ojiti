<?php
include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

$data = json_decode(file_get_contents("php://input"), true);
$transactionNo = $data['transactionNo'] ?? '';

$response = ['status' => 'error', 'message' => 'Invalid transaction number'];

if ($transactionNo) {
    // Archive items for this transaction
    $stmt = $conn->prepare("UPDATE tbl_purchasereturned SET Status = 'Archived' WHERE TransactionNo = ?");
    $stmt->bind_param("s", $transactionNo);
    
    if ($stmt->execute()) {
        $response = ['status' => 'success'];
    } else {
        $response = ['status' => 'error', 'message' => $conn->error];
    }
    $stmt->close();
}

echo json_encode($response);
?>
