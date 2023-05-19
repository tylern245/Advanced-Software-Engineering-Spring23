<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Insert New Device</title>
<link href="assets/css/my-style.css" rel="stylesheet">
<link href="assets/css/modify.css" rel="stylesheet">
<meta charset="utf-8">
</head>

<?php
    include("assets/php/functions.php");
    $dblink = db_iconnect("inventory");

    // start benchmark
    $time_start = microtime(true);
    $execution_time = 0;

    if (isset($_POST['submit'])) {
        $type=urlencode($_POST['type']);
        $manu=urlencode($_POST['manufacturer']);
        $serial=urlencode($_POST['serial-num']);
        $status=urlencode($_POST['status']);

        // initiate cURL command
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ec2-3-16-113-76.us-east-2.compute.amazonaws.com/api/insert?type=$type&manufacturer=$manu&serial-num=$serial&status=$status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false
        ));

        // execute cURL command
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            echo "<h3> Error Search API #: $err";
            die();
        }
        else {
            $results = json_decode($response, true);
            $tmp = explode(":", $results[0]);
            $status = trim($tmp[1]);

            if ($status == "SUCCESS") {
                $tmp = explode(":", $results[1]);
                $msg = trim($tmp[1]);
                $content = explode(",", $msg);
?>
                <div class="alert alert-success">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                New device has been added. <br>
                <b><u>Type:</u></b> <?=$content[0]?><br>
                <b><u>Manufacturer:</u></b> <?=$content[1]?><br>
                <b><u>Serial #:</u></b> <?=$content[2]?><br>
                <b><u>Status:</u></b> <?=$content[3]?><br>
                </div>
<?php
                $tmp = explode(":", $results[2]);
                $execution_time = trim($tmp[1]);
            } // end of SUCCESS

            else if ($status == "WARNING") {
                $tmp = explode(":", $results[1]);
                $msg = trim($tmp[1]);

                $tmp = explode(":", $results[2]);
                $execution_time = trim($tmp[1]);
?>
                <div class="alert alert-warning">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?=$msg?>
                </div>
<?php       } // end of WARNING
        }
    } // end of isset(submit)
?>

<?php
    $type_query = "SELECT * FROM `type` ORDER BY `name` ";
    $type_results = $dblink->query($type_query) or 
        die("Something went wrong with $type_query <br>".$dblink->error);

    $manu_query = "SELECT * FROM `manufacturer` ORDER BY `name`";
    $manu_results = $dblink->query($manu_query) or 
        die("Something went wrong with $manu_query <br>".$dblink->error);
?>


<body>
<h2>Insert New Device (API)</h2>
<div class="" style="display: flex;">
<a id="back-to-search-button" href="search_api.php">Search All Equipment (API)</a>
</div>

<div class="device-container">
<form method="POST">
    <label for="type-drop">Type: </label>
    <select name="type" id="type-drop" required>
    <?php while ($row = $type_results->fetch_array(MYSQLI_ASSOC)): ?>
        <option value="<?=$row['name']?>"><?=$row['name']?><?=$row['status'] == "inactive" ? " (inactive)" : ""?></option>
    <?php endwhile; ?>
    </select>

    <label for="manu-drop">Manufacturer: </label>
    <select name="manufacturer" id="manu-drop" required>    
    <?php while ($row = $manu_results->fetch_array(MYSQLI_ASSOC)): ?>            
        <option value="<?=$row['name']?>"><?=$row['name']?><?=$row['status'] == "inactive" ? " (inactive)" : ""?></option>
    <?php endwhile; ?>
    </select>

    <label for="serial-num-field">Serial #: </label>
    <input name="serial-num" id="serial-num-field" type="text" required>

    <label for="status-drop">Status: </label>
    <select name="status" id="status-drop" required>
        <option value="active">active</option>
        <option value="inactive">inactive</option>
    </select>

    <input type="submit" name="submit" id="submit-button">
</form>
</div>

<?php
    // stop benchmark
    $time_end = microtime(true);
    if ($execution_time == 0) 
        $execution_time = $time_end - $time_start;
    
?>

<div><p class="execution"><b><u> Execution time</u></b>: <?=($execution_time)/60?> minutes or <?=$execution_time?> seconds. </p></div>
</body>
<script src="assets/js/my-script.js" type="text/javascript"></script>