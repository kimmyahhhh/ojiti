<?php
include_once("../../process/profiling/customerinfo.process.php");

$process = new Process();

if(isset($_POST['action']) AND $_POST['action'] == 'LoadCustomerList'){
    $process->LoadCustomerList();
}

if(isset($_POST['action']) AND $_POST['action'] == 'GetCustomerInfo'){
    $process->GetCustomerInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'SaveInfo'){
    $process->SaveInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'UpdateInfo'){
    $process->UpdateInfo($_POST);
}
