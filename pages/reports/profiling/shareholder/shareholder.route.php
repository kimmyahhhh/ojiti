shroute

<?php
// Ensure no whitespace before this tag
ob_start();
ini_set('display_errors', 0); // Force disable display errors
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && $error['type'] === E_ERROR) {
        ob_clean();
        echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "Fatal Error: " . $error['message']]);
    }
});

include_once("../../process/profiling/shareholderinfo.process.php");
include_once("../../reports/profiling/shareholdercert.reports.php");

// DEBUG: Check if request reaches here
if(isset($_POST['action']) && ($_POST['action'] == 'UpdateInfo' || $_POST['action'] == 'SaveInfo')) {
    // file_put_contents('route_debug.txt', "Route reached for " . $_POST['action'] . "\n", FILE_APPEND);
    // echo json_encode(["STATUS" => "DEBUG", "MESSAGE" => "Route Reached!"]);
    // exit;
}

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
