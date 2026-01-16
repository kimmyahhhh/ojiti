<?php
include_once("../../process/profiling/shareholderinfo.process.php");
include_once("../../reports/profiling/shareholdercert.reports.php");

$process = new Process();
$report = new Reports();

if(isset($_POST['action']) AND $_POST['action'] == 'LoadShareHolderNames'){
    $process->LoadShareHolderNames();
}

if(isset($_POST['action']) AND $_POST['action'] == 'LoadShareHolderList'){
    $process->LoadShareHolderList($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'getShareholderInfo'){
    $process->getShareholderInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'getShareholderConfig'){
    $process->getShareholderConfig($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'searchNames'){
    $process->searchNames($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'gnrtCertID'){
    $process->gnrtCertID($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'gnrtSID'){
    $process->gnrtSID($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'SaveInfo'){
    $process->SaveInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'UpdateInfo'){
    $process->UpdateInfo($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'UpdateConfig'){
    $process->UpdateConfig($_POST);
}

if(isset($_POST['action']) AND $_POST['action'] == 'ToSession'){
    $process->ToSession($_POST);
}

if(isset($_GET["type"]) && $_GET["type"] == 'PrintCertificate'){
    $report->PrintCertificate($_SESSION["SHNO"],$_SESSION["FORMAT"]);
}
