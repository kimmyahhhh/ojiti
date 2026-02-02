<?php
include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

$data = json_decode(file_get_contents("php://input"), true);
$transactionNo = $data['transactionNo'] ?? '';

$response = ['status' => 'error', 'message' => 'Invalid transaction number'];

if ($transactionNo) {
    // Delete items for this transaction
    $stmt = $conn->prepare("DELETE FROM tbl_purchasereturned WHERE TransactionNo = ?");
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
