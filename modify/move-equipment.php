<?php
    include("../assets/php/functions.php");

    // start benchmark
    $time_start = microtime(true);

    $dblink = db_iconnect("inventory");

    $identifier = $_POST['identifier'];
    $num_value = $_POST['num_value'];
    $value = $_POST['value'];
    $new_status = $_POST['new'];

    $table = $new_status == "inactive" ? "equipment" : "equipment_inactive" ;

    $count_query = "SELECT COUNT(`auto_id`) FROM `$table` WHERE `$identifier` = '$num_value'";
    $total_count = $dblink->query($count_query)->fetch_row()[0];

    switch($new_status) {
        case "active":
            deleteDeviceMultiple("equipment_inactive", $identifier, $num_value);
            break;
        case "inactive":
            $limit = 100000;
            $interval_count = 1;
            $total_queries = (int)(ceil($total_count / $limit));

            while ($interval_count <= $total_queries) {
                $sql="SET AUTOCOMMIT=0";
                $dblink->query($sql);

                $offset = ($interval_count - 1) * $limit;

                $sql = "SELECT `auto_id`, `type`, `manufacturer` FROM `equipment` WHERE `$identifier` = '$num_value' LIMIT $offset, $limit";
                // $sql = "SELECT `auto_id`, `type`, `manufacturer` FROM `equipment` WHERE `$identifier` = '$num_value'";
                $results = $dblink->query($sql);

                while ($selected = $results->fetch_array(MYSQLI_ASSOC)) {
                    $id = $selected['auto_id'];
                    $type = $selected['type'];
                    $manu = $selected['manufacturer'];
        
                    $insert = "INSERT IGNORE INTO `equipment_inactive` (`auto_id`, `type`, `manufacturer`) VALUES ('$id', '$type', '$manu')";
                    $dblink->query($insert);     
                }

                $sql="COMMIT";
                $dblink->query($sql);

                $interval_count += 1;
            }
            break;
    }
    // $output = $time_start . "," . $total_count;
    // echo $output;

    // stop benchmark
    $time_end = microtime(true);
    $seconds = ($time_end - $time_start);
    $execution_time = ($seconds)/60;
    $records_per_sec = $total_count / $seconds;

    echo "Successfully changed the status of all '$value' equipment to '$new_status'!";
    echo "<br>";
    echo "<b><u>AJAX Call execution time:</u></b> " . $execution_time . " minutes or " . $seconds . " seconds.";
    echo "<br>";
    echo "$total_count total devices updated. Average of $records_per_sec devices per second.";
?>