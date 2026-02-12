<?php
session_start();
include_once("../../process/inventorymanagement/cancelconsignment.process.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
}
$process = new CancelConsignmentProcess();
if (isset($_POST['action']) && $_POST['action'] === 'Initialize') { $process->Initialize(); }
if (isset($_POST['action']) && $_POST['action'] === 'LoadTypes') { $process->LoadTypes($_POST); }
if (isset($_POST['action']) && $_POST['action'] === 'LoadCategories') { $process->LoadCategories($_POST); }
if (isset($_POST['action']) && $_POST['action'] === 'SearchProducts') { $process->SearchProducts($_POST); }
if (isset($_POST['action']) && $_POST['action'] === 'CancelConsignment') { $process->CancelConsignment($_POST); }
