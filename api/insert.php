<?php
    // start benchmark 
    $time_start = microtime(true);

    if (!isset($_REQUEST['type'])) {
        $output[]="Status: ERROR";
        $output[]="MSG: Type data NULL";
        $output[]="Action: Resend type data";
        $responseData = json_encode($output);
        echo $responseData;
        die();
    }
    if (!isset($_REQUEST['manufacturer'])) {
        $output[]="Status: ERROR";
        $output[]="MSG: Manufacturer data NULL";
        $output[]="Action: Resend manufacturer data";
        $responseData = json_encode($output);
        echo $responseData;
        die();
    }
    if (!isset($_REQUEST['serial-num'])) {
        $output[]="Status: ERROR";
        $output[]="MSG: Serial # data NULL";
        $output[]="Action: Resend serial # data";
        $responseData = json_encode($output);
        echo $responseData;
        die();
    }
    if (!isset($_REQUEST['status'])) {
        $output[]="Status: ERROR";
        $output[]="MSG: Status data NULL";
        $output[]="Action: Resend status data";
        $responseData = json_encode($output);
        echo $responseData;
        die();
    }

    if (isset($_REQUEST['type']) && isset($_REQUEST['manufacturer']) && isset($_REQUEST['serial-num']) && isset($_REQUEST['status'])) {
        $type = $_REQUEST['type'];
        $manu = $_REQUEST['manufacturer'];
        $serial_num = $_REQUEST['serial-num'];
        $status = $_REQUEST['status'];

        // assume true until error occurs
        $confirmInsert = true;

        $type_list = getAllTypesIntoArray();
        $manu_list = getAllManufacturersIntoArray();

        // create insert query
        $insert_query = "INSERT INTO `equipment` (`type`, `manufacturer`, `serial_num`) VALUES ('$type_list[$type]', '$manu_list[$manu]', '$serial_num')";

        // check if serial num exists
        $sql = "SELECT `auto_id` FROM `equipment` WHERE `serial_num` = '$serial_num'";
        $count = $dblink->query($sql)->num_rows;
        if ($count > 0) 
            $confirmInsert = false;
        
        // stop benchmark
        $time_end = microtime(true);
        $seconds = $time_end - $time_start;
        // $execution_time = ($seconds)/60;

        // if true, proceed with inserting device
        if ($confirmInsert) {
            if ($dblink->query($insert_query)) {
                if ($status == "inactive") {
                    $sql = "SELECT `auto_id` FROM `equipment` ORDER BY `auto_id` DESC LIMIT 1";
                    $id = $dblink->query($sql)->fetch_array(MYSQLI_NUM)[0];
                    insertDeviceToInactive($id, $type_list[$type], $manu_list[$manu]);
                }
 
                $output[]="Status: SUCCESS";
                $output[]="MSG: $type,$manu,$serial_num,$status";
                $output[]="Action: ".$seconds;
                $responseData = json_encode($output);
                echo $responseData;
            }
            else {
                $output[]="Status: ERROR";
                $output[]="MSG: Error inserting device.";
                $output[]="Action: ".$seconds;
                $responseData = json_encode($output);
                echo $responseData;
            }
        }
        else {
            $output[]="Status: WARNING";
            $output[]="MSG: Serial num '$serial_num' already exists.";
            $output[]="Action: ".$seconds;
            $responseData = json_encode($output);
            echo $responseData;
        }
    }
?>