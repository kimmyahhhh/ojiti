<?php
session_start();
include_once("../../process/dashboard/dashboard.process.php");

$process = new Process();

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'GetMainChartData':
            $process->GetMainChartData($_POST);
            break;
        case 'GetInventoryChartData':
            $process->GetInventoryChartData($_POST);
            break;
        case 'GetDashboardStats':
            $process->GetDashboardStats($_POST);
            break;
    }
}
?>
