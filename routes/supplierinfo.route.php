<?php
include_once("../../process/profiling/supplierinfo.process.php");

$process = new Process();

if(isset($_POST['action']) AND $_POST['action'] == 'LoadSupplierList'){
    $process->LoadSupplierList();
}

if(isset($_POST['action']) AND $_POST['action'] == 'GetSupplierInfo'){
    $process->GetSupplierInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'SaveInfo'){
    $process->SaveInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'UpdateInfo'){
    $process->UpdateInfo($_POST);
}
if(isset($_POST['action']) AND $_POST['action'] == 'GenerateSupplierNo'){
    $process->GenerateSupplierNo();
}
