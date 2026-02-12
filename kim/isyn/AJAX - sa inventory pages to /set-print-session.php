<?php
session_start();
header('Content-Type: application/json');

// Error handling
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($_SESSION['EMPNO']) || !isset($_SESSION['USERNAME']) || !isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['ids']) && is_array($input['ids']) && !empty($input['ids'])) {
    $_SESSION['print_ids'] = $input['ids'];
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No IDs provided']);
}
?>
