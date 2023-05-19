<?php
    // header('Content-Type: application.json');
    // header('HTTP/1.1 200 OK');

    // $output[] = 'Status: API Main';
    // $output[] = 'MSG: Primary Endpoint reached';
    // $output[] = 'Action: None';
    // $responseData = json_encode($output);
    // echo $responseData;

    $time_start = microtime(true);
    $output = $time_start . "," . 57423;
    $data = json_encode($output);
    $a = explode($data, ",");

    echo $data;
?>