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

    if (isset($_REQUEST['type']) && isset($_REQUEST['manufacturer'])) {
        $type = $_REQUEST['type'];
        $manu = $_REQUEST['manufacturer'];
        $limit = 1000;

        // create array to store result
        $info=array();

        // create initial select query
        $search_query = "SELECT * FROM `equipment`";
        
        // conditions to add to select query
        $cond = "";

        $type_list = getAllTypesIntoArray();
        $manu_list = getAllManufacturersIntoArray();

        // conditions based on search
        if ($type != "select-all") 
            $cond .= "`type` = '$type_list[$type]'";
    
        if ($manu != "select-all") {
            $cond = strlen($cond) > 0 ? $cond . " AND " : '';
            $cond = $cond . "`manufacturer` = '$manu_list[$manu]'";
        }

        // add condition(s) to query
        $search_query .= strlen($cond) > 0 ? " WHERE " . $cond : "" ;

        
        // add limit to query
        // $offset = (120 - 1) * $limit;
        $search_query .= " LIMIT $limit";
        
        // execute search query
        $search_results = $dblink->query($search_query);
        $num_rows = $dblink->query($search_query)->num_rows;



        $type_list = array_flip($type_list);
        $manu_list = array_flip($manu_list);

        while ($data = $search_results->fetch_array(MYSQLI_ASSOC)) {
            $id = $data['auto_id'];
            $type = $type_list[$data['type']];
            $manu = $manu_list[$data['manufacturer']];
            $serial_num = $data['serial_num'];

            $info[] = "$id,$type,$manu,$serial_num";
        }
        
        $infoJSON = json_encode($info);
        
        // stop benchmark
        $time_end = microtime(true);
        $seconds = $time_end - $time_start;
        // $execution_time = ($seconds)/60;

        if ($num_rows == 0) {
            $output[]="Status: FAILURE";
            $output[]="MSG: No data found";
            $output[]="Action: ".$seconds;
            $responseData = json_encode($output);
            echo $responseData;
            die();
        }

        $output[]="Status: SUCCESS";
        $output[]="MSG: ".$infoJSON;
        $output[]="Action: ".$seconds;
        $responseData = json_encode($output);
        echo $responseData;
    }
?>