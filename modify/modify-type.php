<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modify Types</title>
<link href="../assets/css/my-style-2.css" rel="stylesheet">
<link href="../assets/css/modify.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<meta charset="utf-8">
</head>
<h2>View/Modify Types</h2>
<?php
    // start benchmark
    $time_start = microtime(true);

    if (isset($_GET['result']) && $_GET['result'] == "edit" &&
        isset($_POST['id']) && isset($_POST['type']) && isset($_POST['status'])) {
        $id = $_POST['id'];
        $sql = "SELECT `name`, `status` FROM `type` WHERE `auto_id` = $id";
        $results = $dblink->query($sql);
        $row = $results->fetch_array(MYSQLI_ASSOC);
        $old_type = $row['name'];
        $old_status = $row['status'];

        $new_type = $_POST['type'];
        $new_status = $_POST['status'];

        updateType($id, $old_type, $new_type, $old_status, $new_status);
    }
    else if 
        (isset($_GET['result']) && $_GET['result'] == "add" &&
         isset($_POST['id']) && isset($_POST['type']) && isset($_POST['status'])) {

        $id = $_POST['id'];
        $new_type = $_POST['type'];
        $new_status = $_POST['status'];

        insertType($new_type, $new_status);
    }
?>
<?php
    $type_query = "SELECT * FROM `type` ORDER BY `name`";
    $type_results = $dblink->query($type_query);

    $type_list = getAllTypesIntoArray();
    $type_list = array_flip($type_list);

    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $current_type = !empty($id) ? $type_list[$id] : "";
?>

<body>
    <div class="" style="display: flex;"><a id="back-to-search-button" href="search.php"><< Back To Search</a></div>
<hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=manu">View/Modify All Manufacturers</a></div>
<hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=insert">Insert New Device</a></div>
<hr>
    <div class="row container">
    <div class="column table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <?php while ($type_list = $type_results->fetch_array(MYSQLI_ASSOC)): ?>
                <tr>
                    <td><?=$type_list['name']?></td>
                    <td><?=$type_list['status']?></td>
                    <td>
                        <button onclick="editType(<?=$type_list['auto_id']?>)">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <button id="add-button" onclick="addType()"><i class="bi bi-plus-square-fill"></i> Add Type</button>
    </div>

    <?php if (isset($_GET['action'])): ?>
        <?php if ($_GET['action'] == "edit"): ?>
        <div class="column edit-container">
            <h3><?php echo "Edit Type: $current_type";?></h3>
            <form method="POST" action="modify.php?modify=type&result=edit">
                <div>
                    <input name="id" value="<?=$id?>" hidden>
                    <label for="type-field">Type:</label>
                    <input name="type" id="type-field" required value="">
                    
                    <label for="status-drop">Status: </label>
                    <select name="status" id="status-drop" required>
                        <option value="active" selected>active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <input type="submit" name="save" id="save-button" value="save">
            </form>
                <button name="cancel" id="cancel-button" onclick="cancelType()">cancel</button>
        </div>
        <?php
            $id = $_GET['id'];
            $type_query = "SELECT * FROM `type` WHERE `auto_id` = $id";
            $results = $dblink->query($type_query);
            $row = $results->fetch_array(MYSQLI_ASSOC);
            $old_type = $row['name'];
            $old_status = $row['status'];
        ?>
        <script>
            document.getElementById('type-field').value = "<?=$old_type?>";
            document.getElementById('status-drop').value = "<?=$old_status?>"
        </script>
        <?php endif; ?> <!-- end of action == edit -->

        <?php if ($_GET['action'] == "add"): ?>
        <div class="column edit-container">
            <h3><?php echo "Add New Type";?></h3>
            <form method="POST" action="modify.php?modify=type&result=add">
                <div>
                    <input name="id" value="<?=$id?>" hidden>
                    <label for="type-field">Type:</label>
                    <input name="type" id="type-field" required value="">
                    
                    <label for="status-drop">Status: </label>
                    <select name="status" id="status-drop" required>
                        <option value="active" selected>active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <input type="submit" name="save" id="save-button" value="save">
            </form>
                <button name="cancel" id="cancel-button" onclick="cancelType()">cancel</button>
        </div>
        <?php
            $id = $_GET['id'];
            $type_query = "SELECT * FROM `type` WHERE `auto_id` = $id";
            $results = $dblink->query($type_query);
            $row = $results->fetch_array(MYSQLI_ASSOC);
            $old_type = $row['name'];
            $old_status = $row['status'];
        ?>
        <script>
            document.getElementById('type-field').value = "<?=$old_type?>";
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
