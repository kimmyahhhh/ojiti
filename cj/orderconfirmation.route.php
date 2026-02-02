<?php
session_start();
include_once("../../process/inventorymanagement/orderconfirmation.process.php");
include_once("../../reports/inventorymanagement/orderconfirmation.reports.php");

$process = new OrderConfirmationProcess();
$report = new OrderConfirmationReports();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
    register_shutdown_function(function() {
        $e = error_get_last();
        if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            echo json_encode(["STATUS"=>"ERROR","MESSAGE"=>"Server error: ".$e['message']]);
        }
    });
}

if(isset($_POST['action']) AND $_POST['action'] == 'Initialize'){
    $process->Initialize();
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadBranch'){
    $process->LoadBranch($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadCategory'){
    $process->LoadCategory($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadSerialProduct'){
    $process->LoadSerialProduct($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadProductSummary'){
    $process->LoadProductSummary($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'SubmitOC'){
    $process->SubmitOC($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'OCSearch'){
    $process->OCSearch($_POST);
}

if(isset($_GET["type"]) && $_GET["type"] == 'PrintOC'){
    if (isset($_GET['no'])) {
        $report->PrintOCByNo($_GET['no']);
    } else if (isset($_SESSION['SelectedOCNo'])) {
        $report->PrintOCByNo($_SESSION['SelectedOCNo']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || empty($_POST['action']))) {
    echo json_encode(["STATUS"=>"ERROR","MESSAGE"=>"Missing action"]);
}


