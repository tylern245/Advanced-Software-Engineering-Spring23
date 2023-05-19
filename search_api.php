<?php
    include("assets/php/functions.php");
    $dblink = db_iconnect("inventory");

    if (isset($_POST['submit'])) {
        $type=urlencode($_POST['type']);
        $manu=urlencode($_POST['manufacturer']);
        
        // initiate cURL command
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ec2-3-16-113-76.us-east-2.compute.amazonaws.com/api/search?type=$type&manufacturer=$manu",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false
        ));

        // execute cURL command
        $response = curl_exec($curl);
        $err = curl_error($curl);

        $type=$_POST['type'];
        $manu=$_POST['manufacturer'];

        if ($err) {
            echo "<h3> Error Search API #: $err";
            die();
        }
        else {
            $results = json_decode($response, true);
            $tmp = explode(":", $results[0]);
            $status = trim($tmp[1]);

            if ($status == "SUCCESS") {
                $tmp=explode(":", $results[1]);
                $data = json_decode($tmp[1], true);
                $tmp=explode(":", $results[2]);
                $execution_time = $tmp[1];
 
                include("results_api.php");
            }
            else if ($status == "FAILURE") {
                $tmp=explode(":", $results[1]);
                $msg = trim($tmp[1]);
                $tmp=explode(":", $results[2]);
                $execution_time = $tmp[1];

                include("results_api.php");
            }
        }
    }
?>

<?php if (!isset($_POST['submit'])): ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search</title>
<link href="assets/css/my-style.css" rel="stylesheet">
<link href="assets/css/modify.css" rel="stylesheet">
</head>
<body>
<h2>Search for Equipment (API)</h2>

<div style="display: flex;">
<a id="back-to-search-button" href="insert_api.php">Insert New Device (API)</a>
</div>

<form method="POST">
    <div class="row">
    <div class="column">
        <label for="type-drop-select" id="type-drop">Select Type:</label>
        <select name="type" id="type-drop-select" size="8" onchange="">
        <option value="select-all" selected>(SELECT ALL)</option>
<?php
        $type_query = "SELECT * FROM `type` ORDER BY `name` ";
        $type_results = $dblink->query($type_query) or 
            die("Something went wrong with $type_queryl <br>".$dblink->error);

        while ($type_list = $type_results->fetch_array(MYSQLI_ASSOC)) {
?>              
            <option value="<?=$type_list['name']?>"><?=$type_list['name']?><?=$type_list['status'] == "inactive" ? " (inactive)" : ""?></option>    <?php
        } // end of while loop
?>
        </select>
    </div> <!-- end of column -->

    <div class="column">
        <label for="manu-drop-select" id="manu-drop">Select Manufacturer:</label>
        <select name="manufacturer" id="manu-drop-select" size="8" onchange="">
        <option value="select-all" selected>(SELECT ALL)</option>
<?php
        $manu_query = "SELECT * FROM `manufacturer` ORDER BY `name`";
        $manu_results = $dblink->query($manu_query) or 
        die("Something went wrong with $manu_query <br>".$dblink->error);

        while ($manu_list = $manu_results->fetch_array(MYSQLI_ASSOC)) {
?>                  
            <option value="<?=$manu_list['name']?>"><?=$manu_list['name']?><?=$manu_list['status'] == "inactive" ? " (inactive)" : ""?></option>    <?php
        }   // end of while loop
        ?>
        </select>
    </div> <!-- end of column -->
    </div> <!-- end of row -->

    <div class="row">
        <input class="column" type="submit" name="submit" id="submit-button">
    </div>
</form>

</body>
<script src="assets/js/my-script.js" type="text/javascript"></script>
<?php endif; ?>