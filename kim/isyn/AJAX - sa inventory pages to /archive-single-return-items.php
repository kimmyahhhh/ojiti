<?php
include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

$data = json_decode(file_get_contents("php://input"), true);
$batchNo = $data['batchNo'] ?? '';

$response = ['status' => 'error', 'message' => 'Invalid ID'];

if ($batchNo) {
    // Archive single item
    $stmt = $conn->prepare("UPDATE tbl_purchasereturned SET Status = 'Archived' WHERE Batchno = ?");
    $stmt->bind_param("s", $batchNo);
    
    if ($stmt->execute()) {
        $response = ['status' => 'success'];
    } else {
        $response = ['status' => 'error', 'message' => $conn->error];
    }
    $stmt->close();
}

echo json_encode($response);
?>
