<?php
    include_once("../process/maintenance.process.php");

    $process = new Maintenance();
    
    if(!empty($_POST['maintenance_action']) AND $_POST['maintenance_action'] == 'get_All'){
        $process->get_All();
    }
    if(!empty($_POST['maintenance_action']) AND $_POST['maintenance_action'] == 'get_citytown'){
        $province_selected = $_POST['province_selected'];
        $process->get_citytown($province_selected);
    }
    if(!empty($_POST['maintenance_action']) AND $_POST['maintenance_action'] == 'get_brgy'){
        $citytown_selected = $_POST['citytown_selected'];
        $process->get_brgy($citytown_selected);
    }
    if(!empty($_POST['maintenance_action']) AND $_POST['maintenance_action'] == 'get_province'){
        $region_selected = $_POST['region_selected'];
        $process->get_province($region_selected);
    }
   
    if(!empty($_POST['maintenance_action']) AND $_POST['maintenance_action'] == 'getRegion'){
        $process->getRegion();
    }
    if(!empty($_POST['maintenance_action']) AND $_POST['maintenance_action'] == 'get_street'){
        $barangay_selected = $_POST['barangay_selected'];
        $process->get_street($barangay_selected);
    }
