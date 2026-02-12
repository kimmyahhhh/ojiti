<?php
include_once("../../process/inventorymanagement/closetransaction.process.php");

$process = new Process();

if(isset($_POST['action']) AND $_POST['action'] == 'Initialize'){
    $process->Initialize();
}

if(isset($_POST['action']) AND $_POST['action'] == 'CloseTransaction'){
    $process->CloseTransaction($_POST);
}
