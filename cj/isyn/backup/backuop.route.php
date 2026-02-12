<?php
include_once("../../process/inventorymanagement/backup.process.php");

$process = new BackupProcess();

if(isset($_POST['action'])) {
    if ($_POST['action'] == 'StartBackup') {
        $process->StartBackup();
    } else if ($_POST['action'] == 'PerformBackup') {
        $process->PerformBackup($_POST['backup_id']);
    } else if ($_POST['action'] == 'GetProgress') {
        $process->GetProgress($_POST['backup_id']);
    }
} else if (isset($_GET['action']) && $_GET['action'] == 'DownloadFile') {
    $process->DownloadFile($_GET['filename']);
}
?>
