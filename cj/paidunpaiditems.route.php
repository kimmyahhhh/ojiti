<?php
session_start();
include_once("../../process/inventorymanagement/paidunpaiditems.process.php");

$process = new Process();

if (isset($_POST['action']) && $_POST['action'] === 'SearchPaidUnpaid') {
    $process->SearchPaidUnpaid($_POST);
}
if (isset($_POST['action']) && $_POST['action'] === 'GetItemDetails') {
    $process->GetItemDetails($_POST);
}
if (isset($_POST['action']) && $_POST['action'] === 'GetClientDetails') {
    $process->GetClientDetails($_POST);
}
