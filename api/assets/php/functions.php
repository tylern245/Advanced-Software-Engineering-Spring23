<?php
	function db_iconnect($dbname) {
        $user="webuser";
        $pw="7MWml!6HKUliFj2k";
        $hostname="localhost";
        $dblink=new mysqli($hostname, $user, $pw, $dbname);
        return $dblink;
    }

    function redirect ($uri) { 
    ?>
	<script type="text/javascript">
	    document.location.href="<?php echo $uri; ?>";
	</script>
    <?php die;
    }

    function redirectToSearch ( ) {
        $uri = 'search.php';
        ?>
        <script type="text/javascript">
            alert("Please perform a proper search.");
            document.location.href="<?php echo $uri; ?>";
        </script>
        <?php die;
    }

    function displaySearchInfo($type, $manu, $serial, $serial_all) {
        echo "<hr>";

        if ($type == "select-all") {
            echo <<<EOT
            <b><u>Type</u></b>: ALL
            <br>
            EOT;
        }
        else if (!empty($type)) {
            echo <<<EOT
            <b><u>Type</u></b>: $type
            <br>
            EOT;
        }

        if ($manu == "select-all") {
            echo <<<EOT
            <b><u>Manufacturer</u></b>: ALL
            <br>
            EOT;
        }
        else if (!empty($manu)) {
            echo <<<EOT
            <b><u>Manufacturer</u></b>: $manu
            <br>
            EOT;
        }

        if (!empty($serial)) {
            echo <<<EOT
            <b><u>Serial #</u></b>: $serial
            EOT;
        }
        else if ($serial_all == "true") {
            echo <<<EOT
            <b><u>Serial #</u></b>: ALL
            EOT;
        }

        echo "<hr>";
    }

    function selectColumns($cond, $type, $manu, $serial_all) {
        if ($type == "select-all" || empty($type))
            $cond = $cond . ", `type`";

        if ($manu == "select-all" || empty($manu))
            $cond = $cond . ", `manufacturer`";

        $cond = $cond . " FROM `equipment` ";
        return $cond;
    }

    function displayTableHeader($type, $manu) {
        if ($type == "select-all" || empty($type)) {
            echo <<<EOT
            <th>Type</th>
            EOT;
        }

        if ($manu == "select-all" || empty($manu)) {
            echo <<<EOT
            <th>Manufacturer</th>
            EOT;
        }
    }

    function displayTableColumns($type, $manu, $type_list, $manu_list, $search_array) {
        if ($type == "select-all" || empty($type)) {
            $temp = $type_list[$search_array['type']];
            echo <<<EOT
            <td>$temp</td>
            EOT;
        }

        if ($manu == "select-all" || empty($manu)) {
            $temp = $manu_list[$search_array['manufacturer']];
            echo <<<EOT
            <td>$temp</td>
            EOT;
        }
    }

    function addAnd($query, $len) {
        if (strlen($query) > $len) {
            $query = $query . " AND ";
        }

        return $query;
    }

    function getAllTypesIntoArray() {
        $dblink = db_iconnect("inventory");

        // create type array (to convert the type num with the corresponding type name)
        $type_query = "SELECT * FROM `type`";
        $type_results = $dblink->query($type_query);
        $type_ids = array();
        $type_names = array();
        
        while ($type_array = $type_results->fetch_array(MYSQLI_ASSOC)) {
            array_push($type_ids, $type_array['auto_id']);
            array_push($type_names, $type_array['name']);
        }
        
        $type_list = array_combine($type_names, $type_ids);

        return $type_list;
    }

    function getAllManufacturersIntoArray() {
        $dblink = db_iconnect("inventory");

        // create manu array (to convert the manu num to its corresponding manu name)
        $manu_query = "SELECT * FROM `manufacturer`";
        $manu_results = $dblink->query($manu_query);
        $manu_names = array();
        $manu_ids = array();

        while ($manu_array = $manu_results->fetch_array(MYSQLI_ASSOC)) {
            array_push($manu_names, $manu_array['name']);
            array_push($manu_ids, $manu_array['auto_id']);
        }

        $manu_list = array_combine($manu_names, $manu_ids);

        return $manu_list;
    }

    function updateDevice($id, $type, $manu, $serial) {
        $dblink = db_iconnect("inventory");

        $update_type = "UPDATE `equipment` SET `type` = '$type' WHERE `auto_id` = $id";
        $update_manu = "UPDATE `equipment` SET `manufacturer` = '$manu' WHERE `auto_id` = $id";
        $update_serial = "UPDATE `equipment` SET `serial_num` = '$serial' WHERE `auto_id` = $id";

        if ($dblink->query($update_type) && $dblink->query($update_manu) && $dblink->query($update_serial)) {
            echo <<<EOT
            <div class="alert alert-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Successfully updated device $id!
            </div>
            EOT;
        }
        else {
            echo <<<EOT
            <div class="alert alert-failure">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Error updating device $id.
            </div>
            EOT;
        }
    }

    function insertDevice($type, $manu, $serial) {
        $dblink = db_iconnect("inventory");
        
        $insert = "INSERT INTO `equipment` ( `type`, `manufacturer`, `serial_num`)
        VALUES ('$type', '$manu', '$serial')";
        $dblink->query($insert);        
    }

    function insertDeviceToInactive($id, $type, $manu) {
        $dblink = db_iconnect("inventory");
        
        $insert = "INSERT INTO `equipment_inactive` (`auto_id`, `type`, `manufacturer`) VALUES ('$id', '$type', '$manu')";

        $dblink->query($insert);      
    }

    function deleteDevice($table, $id) {
        $dblink = db_iconnect("inventory");

        $delete = "DELETE FROM `$table` WHERE `auto_id` = $id";
        $dblink->query($delete);
    }

    function deleteDeviceMultiple($table, $identifier, $value) {
        $dblink = db_iconnect("inventory");

        $delete = "DELETE FROM `$table` WHERE `$identifier` = '$value'";
        $dblink->query($delete);
    }

    function updateType($id, $old_type, $new_type, $old_status, $new_status) {
        $dblink = db_iconnect("inventory");

        // check if type exists
        $sql = "SELECT `name` FROM `type` WHERE `name` = '$new_type'";
        $count = $dblink->query($sql)->num_rows;

        $update_type = "UPDATE `type` SET `name` = '$new_type' WHERE `auto_id` = '$id'";

        if ($count > 0 && $old_type != $new_type) {
            echo <<<EOT
            <div class="alert alert-warning">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            $new_type already exists.
            </div>
            EOT;

            return;
        }

        if ($dblink->query($update_type) && $old_type != $new_type) {
            echo <<<EOT
            <div class="alert alert-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Successfully updated type from '$old_type' to '$new_type'!
            </div>
            EOT;
        }
        else if (!$dblink->query($update_type)){
            echo <<<EOT
            <div class="alert alert-failure">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Error updating type to '$new_type'.
            </div>
            EOT;
        }
        if ($old_status != $new_status) {
            $type_list = getAllTypesIntoArray();
            moveEquipment($id, "type", $type_list[$new_type], $new_type, $new_status);

            echo <<<EOT
            <div class="alert alert-warning" id="alert-div">
            <div class="loader"></div>
            Changing status of '$new_type' equipment to '$new_status'!
            <br>
            (AJAX call in the background. Calculating execution time.)
            </div>
            EOT;
        }
    }

    function insertType($type, $status) {
        $dblink = db_iconnect("inventory");

        // check if type exists
        $sql = "SELECT `name` FROM `type` WHERE `name` = '$type'";
        $count = $dblink->query($sql)->num_rows;

        if ($count > 0) {
            echo <<<EOT
            <div class="alert alert-warning">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            $type already exists.
            </div>
            EOT;
        }
        else {
            $sql = "INSERT INTO `type` (`name`, `status`) VALUES ('$type', '$status')";
            if ($dblink->query($sql)) {
                
                echo <<<EOT
                <div class="alert alert-success">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Successfully added '$type' with '$status' status!
                </div>
                EOT;
            }
            else {
                echo <<<EOT
                <div class="alert alert-failure">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Error adding '$type'.
                </div>
                EOT;
            }
        }
    }

    function updateManu($id, $old_manu, $new_manu, $old_status, $new_status) {
        $dblink = db_iconnect("inventory");

        // check if manu exists
        $sql = "SELECT `name` FROM `manufacturer` WHERE `name` = '$new_manu'";
        $count = $dblink->query($sql)->num_rows;

        $update_manu = "UPDATE `manufacturer` SET `name` = '$new_manu' WHERE `auto_id` = '$id'";

        if ($count > 0 && $old_manu != $new_manu) {
            echo <<<EOT
            <div class="alert alert-warning">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            $new_manu already exists.
            </div>
            EOT;

            return;
        }
        if ($dblink->query($update_manu) && $old_manu != $new_manu) {
            echo <<<EOT
            <div class="alert alert-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Successfully updated manufacturer from '$old_manu' to '$new_manu'!
            </div>
            EOT;
        }
        else if (!$dblink->query($update_manu)){
            echo <<<EOT
            <div class="alert alert-failure">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Error updating manufacturer to '$new_manu'.
            </div>
            EOT;
        }
        if ($old_status != $new_status) {
            $manu_list = getAllManufacturersIntoArray();
            moveEquipment($id, "manufacturer", $manu_list[$new_manu], $new_manu, $new_status);

            echo <<<EOT
            <div class="alert alert-warning" id="alert-div">
            <div class="loader"></div>
            Changing status of '$new_manu' equipment to '$new_status'...
            <br>
            (AJAX call in the background. Calculating execution time.)
            </div>
            EOT;

            // <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        }
    }

    function insertManu($manu, $status) {
        $dblink = db_iconnect("inventory");

        // check if manu exists
        $sql = "SELECT `name` FROM `manufacturer` WHERE `name` = '$manu'";
        $count = $dblink->query($sql)->num_rows;
        
        if ($count > 0) {
            echo <<<EOT
            <div class="alert alert-warning">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            '$manu' already exists.
            </div>
            EOT;
        }
        else {
            $sql = "INSERT INTO `manufacturer` (`name`, `status`) VALUES ('$manu', '$status')";
            if ($dblink->query($sql)) {
                echo <<<EOT
                <div class="alert alert-success">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Successfully added '$manu' with '$status' status!
                </div>
                EOT;
            }
            else {
                echo <<<EOT
                <div class="alert alert-failure">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Error adding '$manu'.
                </div>
                EOT;
            }
        }
    }

    function checkInactive($id) {
        $dblink = db_iconnect("inventory");

        $sql = "SELECT * FROM `equipment_inactive` WHERE `auto_id` = $id";
        $count = $dblink->query($sql)->num_rows;

        return ($count > 0);
    }

    function moveEquipment($id, $identifier, $num_value, $value, $new_status) {
        $dblink = db_iconnect("inventory");
        $update_status = "UPDATE `$identifier` SET `status` = '$new_status' WHERE `auto_id` = '$id'";
        $dblink->query($update_status);
    ?>
        <script>
            $.ajax({
                type: "POST",
                url: "modify/move-equipment.php",
                dataType: "text",
                data: "&identifier=<?=$identifier?>&num_value=<?=$num_value?>&value=<?=$value?>" + 
                "&new=<?=$new_status?>",
                success: function(data) {
                    $('#alert-div').html("<div class=loader></div>Finalizing...");
                    setTimeout(function() {
                        $('#alert-div').removeClass('alert-warning');
                        $('.loader').remove();
                        $('#alert-div').addClass('alert-success');
                        $('#alert-div').add('i').addClass('bi bi-check-square');
                        $('#alert-div').html(data);
                    }, 3000);
                    

                },               
                error: function() {
                    alert("UPDATE EQUIPMENT REQUEST FAILED.");
                    $('#alert-div').removeClass('alert-warning');
                    $('#alert-div').addClass('alert-failure');
                    $('#alert-div').add('i').addClass('bi bi-x-square');
                    $('#alert-div').html("Failed to change '<?=$value?>' equipment to '<?=$new_status?>'!");                            
                }
            });
        </script>
    <?php

        // POSSIBLE ADD AN OFFSET AND LIMIT, maybe run php script in the background
        // with AJAX

        // select from OLD table
        // $sql = "SELECT * FROM `$old` WHERE `$identifier` = '$value'";
        // echo "query: " . $sql;
        // $results = $dblink->query($sql);

        // $sql="SET AUTOCOMMIT=0";
        // $dblink->query($sql);

        // // add to NEW table
        // while ($selected = $results->fetch_array(MYSQLI_ASSOC)) {
        //     $id = $selected['auto_id'];
        //     $type = $selected['type'];
        //     $manu = $selected['manufacturer'];
        //     $serial = $selected['serial_num'];

        //     $sql = "INSERT INTO `$new` (`auto_id`, `type`, `manufacturer`, `serial_num`)
        //     VALUES ('$id', '$type', '$manu', '$serial')";

        //     $dblink->query($sql);
        // }

        // $sql="COMMIT";
        // $dblink->query($sql);

        // // delete from OLD table
        // $sql = "DELETE FROM `$old` WHERE `$identifier` = '$value'";
        // echo "query: " . $sql;
        // $dblink->query($sql);
    }
?>