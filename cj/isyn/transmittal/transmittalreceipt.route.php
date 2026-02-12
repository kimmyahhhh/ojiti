<?php
session_start();
include_once("../../process/inventorymanagement/transmittalreceipt.process.php");
include_once("../../reports/inventorymanagement/transmittalreceipt.reports.php");

$process = new Process();
$report = new Reports();

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

if(isset($_POST['action']) AND $_POST['action'] == 'LoadCustomerName'){
    $process->LoadCustomerName($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadCustomerNameInfo'){
    $process->LoadCustomerNameInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'AddToItems'){
    $process->AddToItems($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'UseQtyFromBranchConsign'){
    $process->UseQtyFromBranchConsign($_POST);
}

// ======================================================================
if(isset($_POST['action']) AND $_POST['action'] == 'LoadTransaction'){
    $process->LoadTransaction();
}

if(isset($_POST['action']) AND $_POST['action'] == 'DeleteFromItems'){
    $process->DeleteFromItems($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'TransmittalSearch'){
    $process->TransmittalSearch($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'fetchProducts'){
    $process->fetchProducts($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadtoList'){
    $process->LoadtoList($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'SubmitInvOut'){
    $process->SubmitInvOut($_POST);
}

if(isset($_GET["type"]) && $_GET["type"] == 'PrintSalesInvoice'){
    if (isset($_SESSION['SelectedTransNo'])) {
        $report->PrintTransmittalByNo($_SESSION['SelectedTransNo']);
    } else {
        // Fallback for older sessions
        $report->PrintOutgoingSalesInvoice($_SESSION['tableData'],$_SESSION['SalesNoVAT'],$_SESSION['SalesWithVAT'], $_SESSION['SIRef'],$_SESSION['DateAdded']);
    }
}
if(isset($_GET["type"]) && $_GET["type"] == 'PrintTransmittal' && isset($_GET['no'])){
    $report->PrintTransmittalByNo($_GET['no']);
}
