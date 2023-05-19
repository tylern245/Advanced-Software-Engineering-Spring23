<?php
    include("../assets/php/functions.php");
    $dblink = db_iconnect("inventory");

    $old = $_POST['old'];
    $identifier = $_POST['identifier'];
    $num_value = $_POST['num_value'];
    $value = $_POST['value'];
    $status = $_POST['status'];
    $time_start = $_POST['time'];
    $count = $_POST['count'];

    // delete from OLD table
    $sql = "DELETE FROM `$old` WHERE `$identifier` = '$num_value'";
    $dblink->query($sql);

    // stop benchmark
    $time_end = microtime(true);
    $seconds = $time_end - $time_start;
    $execution_time = ($seconds)/60;
    $records_per_sec = $count / $seconds;
    echo "Successfully changed the status of all '$value' equipment to '$status'!";
    echo "<br>";
    echo "<b><u>AJAX Call execution time:</u></b> " . $execution_time . " minutes or " . $seconds . " seconds.";
    echo "<br>";
    echo "$count total devices. $records_per_sec devices per second.";
?>