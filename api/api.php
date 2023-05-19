<?php
    include("assets/php/functions.php");
    header('Content-Type: application.json');
    header('HTTP/1.1 200 OK');
    $dblink = db_iconnect("inventory");

    $url = $_SERVER['REQUEST_URI'];

    $path=parse_url($url, PHP_URL_PATH);
    $pathComp = explode("/", trim($path, "/"));
    $endpoint = $pathComp[1];

    switch($endpoint) {
        case "search":
            include("search.php");
            break;
        case "insert":
            include("insert.php");
            break;
        default:
            $output[]="Status: ERROR";
            $output[]="MSG: " . $endpoint .  " Endpoint not found";
            $output[]="Action: None";
            break;
    }


?>