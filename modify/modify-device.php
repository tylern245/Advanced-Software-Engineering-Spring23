<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modify Device</title>
<link href="../assets/css/my-style.css" rel="stylesheet">
<link href="../assets/css/modify.css" rel="stylesheet">
<meta charset="utf-8">
</head>
<h3>Modify Device</h3>

<?php
    // start benchmark
    $time_start = microtime(true);
    
    // assume false until correct condition is met
    $confirmModify = false;

    if (!isset($_GET['auto_id']) || empty($_GET['auto_id']))
        redirectToSearch();

    $auto_id = $_GET['auto_id'];

    // if auto id does not exist, display error and php die()
    $sql = "SELECT `auto_id` FROM `equipment` WHERE `auto_id` = $auto_id";
    $num_rows = $dblink->query($sql)->num_rows;
?>
    <?php if ($num_rows == 0): ?>
        <div class="alert alert-warning">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        Device ID:<?=$auto_id?> does not exist.
        </div>
        <?php die(); ?>
    <?php endif; ?>
<?php
    if (isset($_POST['type']) && isset($_POST['manu']) && isset($_POST['serial-num']) && isset($_POST['status'])) {
        $type_list = getAllTypesIntoArray();
        $manu_list = getAllManufacturersIntoArray();
        
        $new_type = '';
        $new_type_status = '';
        $new_manu = '';
        $new_manu_status = '';
        $new_serial = $_POST['serial-num'];
        $new_status = $_POST['status'];

        if ($_POST['type'] == "add-new") {
            $new_type = $_POST['new-type'];
            $new_type_status = $_POST['new-type-status'];
            $search_type = "SELECT * FROM `type` WHERE `name` = '$new_type'";
            $num_rows = $dblink->query($search_type)->num_rows;
?>
            <?php if ($num_rows > 0): ?>
                <div class="alert alert-warning">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?=$new_type?> already exists.
                </div>
            
            <?php else: 
                $insert_query = "INSERT INTO `type` (`name`, `status`) VALUES ('$new_type', '$new_type_status')";
                $dblink->query($insert_query);
                $confirmModify = true;
            ?>
                <div class="alert alert-success">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                '<?=$new_type?>' has been added.
                </div>
            <?php endif; ?>           
<?php
        }
        else if (!empty($_POST['type'])){            
            $new_type = $_POST['type'];
            $confirmModify = true;
        }

        if ($_POST['manu'] == "add-new") {
            $new_manu = $_POST['new-manu'];
            $new_manu_status = $_POST['new-manu-status'];
            $search_manu = "SELECT * FROM `manufacturer` WHERE `name` = '$new_manu'";
            $num_rows = $dblink->query($search_manu)->num_rows;
?>

            <?php if ($num_rows > 0): ?>
                <div class="alert alert-warning">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?=$new_manu?> already exists.
                </div>
            
            <?php else: 
                $insert_query = "INSERT INTO `manufacturer` (`name`, `status`) VALUES ('$new_manu', '$new_manu_status')";
                $dblink->query($insert_query);
                $confirmModify = true;
            ?>
                <div class="alert alert-success">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?=$new_manu?> has been added.
                </div>
            <?php endif; ?>   
<?php 
        }
        else if (!empty($_POST['manu'])) {
            $new_manu = $_POST['manu'];
            $confirmModify = true;
        }
            

        // if old status is active and new status is inactive
        if (!checkInactive($auto_id) && $new_status == "inactive") {
            $type_num = $type_list[$new_type];
            $manu_num = $manu_list[$new_manu];

            // insert device into inactive table
            insertDeviceToInactive($auto_id, $type_num, $manu_num);
?>
            <div class="alert alert-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Updated status to <?=$new_status?>.
            </div>
<?php
        }
        // if old status is inactive and new status is active
        else if (checkInactive($auto_id) && $new_status == "active") {
            // delete device from inactive table
            deleteDevice("equipment_inactive", $auto_id);
?>
            <div class="alert alert-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            Updated status to <?=$new_status?>.
            </div>
<?php
        }

        // if true, proceed with modifying device
        if ($confirmModify) {
            // current device from active equipment query
            $device_query = "SELECT * FROM `equipment` WHERE `auto_id` = '$auto_id'";
            $device_active_result = $dblink->query($device_query);

            $type_num = $type_list[$new_type];
            $manu_num = $manu_list[$new_manu];

            updateDevice($auto_id, $type_num, $manu_num, $new_serial);
        }
    }
?>

<?php
    // TYPE QUERY
    $type_query = "SELECT * FROM `type` ORDER BY `name`;";
    $type_results = $dblink->query($type_query);

    // MANUFACTURER QUERY
    $manu_query = "SELECT * FROM `manufacturer` ORDER BY `name`;";
    $manu_results = $dblink->query($manu_query);
?>

<body>
    <div class="" style="display: flex;"><a id="back-to-search-button" href="search.php"><< Back To Search</a></div>

    <hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=type">View/Modify All Types</a></div>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=manu">View/Modify All Manufacturers</a></div>
    <hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=insert">Insert New Device</a></div>
    <hr>

    <div class="device-container">
    <form method="POST">
        <label>Auto ID: <?=$auto_id?></label>
        <input name="auto_id" hidden value="<?=$auto_id?>">
        
        <label for="type-drop">Type: </label>
        <select name="type" id="type-drop" required onchange="showInsert(this)">
            <option value="add-new">(ADD NEW)</option>
        <?php while ($row = $type_results->fetch_array(MYSQLI_ASSOC)): ?>
            <option value="<?=$row['name']?>"><?=$row['name']?><?=$row['status'] == "inactive" ? " (inactive)" : ""?></option>
        <?php endwhile; ?>
        </select>

            <div id="insert-new-type" style="display: none">
                <label for="new-type-field">Enter New Type: </label>
                <input name="new-type" id="new-type-field" disabled required>
                <select name="new-type-status" id="new-type-status-drop" disabled required>
                    <option value="active" selected>active</option>
                    <option value="inactive">inactive</option>
                </select>
            </div>
        
        <label for="manu-drop">Manufacturer: </label>
        <select name="manu" id="manu-drop" required onchange="showInsert(this)">
            <option value="add-new">(ADD NEW)</option>        
        <?php while ($row = $manu_results->fetch_array(MYSQLI_ASSOC)): ?>            
            <option value="<?=$row['name']?>"><?=$row['name']?><?=$row['status'] == "inactive" ? " (inactive)" : ""?></option>
        <?php endwhile; ?>
        </select>

            <div id="insert-new-manu" style="display: none">
                <label for="new-manu-field">Enter New Manufacturer: </label>
                <input name="new-manu" id="new-manu-field" disabled required>
                <select name="new-manu-status" id="new-manu-status-drop" disabled required>
                    <option value="active" selected>active</option>
                    <option value="inactive">inactive</option>
                </select>
            </div>

        <label for="serial-num-field">Serial #: </label>
        <input name="serial-num" id="serial-num-field" type="text" required>

        <label for="status-drop">Status: </label>
        <select name="status" id="status-drop" required>
            <option value="active">active</option>
            <option value="inactive">inactive</option>
        </select>

        <input id="submit-button" type="submit">
    </form>
    </div>

    <script>
    <?php
        $type_list = getAllTypesIntoArray();
        $manu_list = getAllManufacturersIntoArray(); 

        $type_list = array_flip($type_list); 
        $manu_list = array_flip($manu_list);
        
        // current device query
        $device_query = "SELECT * FROM `equipment` WHERE `auto_id` = '$auto_id'";
        $device_active_result = $dblink->query($device_query);

        $row = $device_active_result->fetch_array(MYSQLI_ASSOC);
        $displayType = $type_list[$row['type']];
        $displayManu = $manu_list[$row['manufacturer']];
        $displaySerial = $row['serial_num'];
        $displayStatus = checkInactive($auto_id) ? "inactive" : "active";

    ?>
        document.getElementById('type-drop').value = "<?=$displayType?>";
        document.getElementById('manu-drop').value = "<?=$displayManu?>";
        document.getElementById('serial-num-field').value = "<?=$displaySerial?>";
        document.getElementById('status-drop').value = "<?=$displayStatus?>"
    </script>
</body>
<?php
    // stop benchmark
    $time_end = microtime(true);
    $seconds = $time_end - $time_start;
    $execution_time = ($seconds)/60;
?>
    <div><p class="execution"><b><u> Execution time</u></b>: <?=$execution_time?> minutes or <?=$seconds?> seconds. </p></div>

<script src="assets/js/my-script.js" type="text/javascript"></script>