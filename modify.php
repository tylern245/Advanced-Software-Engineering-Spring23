<?php
    include("assets/php/functions.php");
    $dblink=db_iconnect("inventory");

    $modify = $_GET['modify'];

    switch($modify){
        case "device":
            include("modify/modify-device.php");
            break;

        case "type":
            include("modify/modify-type.php");
            break;
        
        case "manu":
            include("modify/modify-manu.php");
            break;
        case "insert":
            include("modify/insert-device.php");
            break;

        default:
            redirect("search.php");
            break;
    }
?>