<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modify Manufacturers</title>
<link href="../assets/css/my-style-2.css" rel="stylesheet">
<link href="../assets/css/modify.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<meta charset="utf-8">
</head>
<h2>View/Modify Manufacturers</h2>
<?php
    // start benchmark
    $time_start = microtime(true);

    if (isset($_GET['result']) && $_GET['result'] == "edit" &&
        isset($_POST['id']) && isset($_POST['manu']) && isset($_POST['status'])) {
        $id = $_POST['id'];
        $sql = "SELECT `name`, `status` FROM `manufacturer` WHERE `auto_id` = $id";
        $results = $dblink->query($sql);
        $row = $results->fetch_array(MYSQLI_ASSOC);
        $old_manu = $row['name'];
        $old_status = $row['status'];

        $new_manu = $_POST['manu'];
        $new_status = $_POST['status'];
        
        // UPDATE name and/or status. AJAX call is invoked if the status has been changed
        updateManu($id, $old_manu, $new_manu, $old_status, $new_status);
    }
    else if 
        (isset($_GET['result']) && $_GET['result'] == "add" &&
         isset($_POST['id']) && isset($_POST['manu']) && isset($_POST['status'])) {

        $id = $_POST['id'];
        $new_manu = $_POST['manu'];
        $new_status = $_POST['status'];

        insertManu($new_manu, $new_status);
    }
?>
<?php
    $manu_query = "SELECT * FROM `manufacturer` ORDER BY `name`";
    $manu_results = $dblink->query($manu_query);

    $manu_list = getAllManufacturersIntoArray();
    $manu_list = array_flip($manu_list);

    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $current_manu = !empty($id) ? $manu_list[$id] : "";
?>

<body>
    <div class="" style="display: flex;"><a id="back-to-search-button" href="search.php"><< Back To Search</a></div>
<hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=type">View/Modify All Types</a></div>
<hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=insert">Insert New Device</a></div>
<hr>

    <div class="row container">
    <div class="column table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Manufacturer</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <?php while ($manu_list = $manu_results->fetch_array(MYSQLI_ASSOC)): ?>
                <tr>
                    <td><?=$manu_list['name']?></td>
                    <td><?=$manu_list['status']?></td>
                    <td>
                        <button onclick="editManu(<?=$manu_list['auto_id']?>)">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <button id="add-button" onclick="addManu()"><i class="bi bi-plus-square-fill"></i> Add Manufacturer</button>
    </div>

    <?php if (isset($_GET['action'])): ?>
        <?php if ($_GET['action'] == "edit"): ?>
        <div class="column edit-container">
            <h3><?php echo "Edit Manufacturer: $current_manu";?></h3>
            <form method="POST" action="modify.php?modify=manu&result=edit">
                <div>
                    <input name="id" value="<?=$id?>" hidden>
                    <label for="manu-field">Manufacturer:</label>
                    <input name="manu" id="manu-field" required value="">
                    
                    <label for="status-drop">Status: </label>
                    <select name="status" id="status-drop" required>
                        <option value="active" selected>active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <input type="submit" name="save" id="save-button" value="save" onclick="">
            </form>
                <button name="cancel" id="cancel-button" onclick="cancelManu()">cancel</button>
        </div>
        <?php
            $id = $_GET['id'];
            $manu_query = "SELECT * FROM `manufacturer` WHERE `auto_id` = $id";
            $results = $dblink->query($manu_query);
            $row = $results->fetch_array(MYSQLI_ASSOC);
            $old_manu = $row['name'];
            $old_status = $row['status'];
        ?>
        <script>
            document.getElementById('manu-field').value = "<?=$old_manu?>";
            document.getElementById('status-drop').value = "<?=$old_status?>";
        </script>
        <?php endif; ?> <!-- end of action == edit -->

        <?php if ($_GET['action'] == "add"): ?>
        <div class="column edit-container">
            <h3><?php echo "Add New Manufacturer";?></h3>
            <form method="POST" action="modify.php?modify=manu&result=add">
                <div>
                    <input name="id" value="<?=$id?>" hidden>
                    <label for="manu-field">Manufacturer:</label>
                    <input name="manu" id="manu-field" required value="">
                    
                    <label for="status-drop">Status: </label>
                    <select name="status" id="status-drop" required>
                        <option value="active" selected>active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <input type="submit" name="save" id="save-button" value="save">
            </form>
                <button name="cancel" id="cancel-button" onclick="cancelManu()">cancel</button>
        </div>
        <?php
            $id = $_GET['id'];
            $manu_query = "SELECT * FROM `manufacturer` WHERE `auto_id` = $id";
            $results = $dblink->query($manu_query);
            $row = $results->fetch_array(MYSQLI_ASSOC);
            $old_manu = $row['name'];
            $old_status = $row['status'];
        ?>
        <script>
            document.getElementById('manu-field').value = "<?=$old_manu?>";
            document.getElementById('status-drop').value = "<?=$old_status?>"
        </script>
        <?php endif; ?> <!-- end of action == edit -->
    <?php endif; ?> <!-- end of isset action -->

    </div>
</body>

<?php
    // stop benchmark
    $time_end = microtime(true);
    $seconds = $time_end - $time_start;
    $execution_time = ($seconds)/60;
?>
<div><p class="execution"><b><u> Execution time</u></b>: <?=$execution_time?> minutes or <?=$seconds?> seconds. </p></div>
<script src="../assets/js/modify.js" type="text/javascript"></script>
