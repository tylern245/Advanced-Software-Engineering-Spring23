<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Results</title>
<link href="assets/css/my-style.css" rel="stylesheet">
<link href="assets/css/modify.css" rel="stylesheet">
<meta charset="utf-8">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
</head>

<?php    
    // for table display purposes
    $rowCount = isset($_GET['rowCount']) ? intval($_GET['rowCount']) + 1 : 1;  
?>

<body>
    <h2 align="center">Search for Equipment</h2>
    <div class="" style="display: flex;">
        <a id="back-to-search-button" href="search_api.php"><< Back To Search (API)</a>
    </div>
    <div style="display: flex;">
        <a id="back-to-search-button" href="insert_api.php">Insert New Device (API)</a>
    </div>

<?php     
    // display search info
    displaySearchInfo($type, $manu, "", "false");
?>

    <!-- create table + table heading -->
<div class="table-wrapper">
    <table>
    <thead id="table-header">
        <tr>
            <th>Auto ID</th>
            <th>Type</th>
            <th>Manufacturer</th>
            <th>Serial</th>
            <th>Status</th>
            <th>Modify</th>   <!-- modify button column -->
        </tr>
    </thead>
    <?php foreach ($data as $row): ?>
    <?php $col = explode(",", $row);?>
        <tr>
            <td><?=$col[0]?></td>
            <td><?=$col[1]?></td>
            <td><?=$col[2]?></td>
            <td><?=$col[3]?></td>
            <td><?=checkInactive($col[0]) ? "inactive" : "active"?></td>
            <td align="center"><a id="modify-button" target="_blank" href="modify.php?modify=device&auto_id=<?=$col[0]?>"><i class="bi bi-pencil-square"></i></a></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php echo ($status == "FAILURE") ? "<p align='center'>No data found.</p>" : ""?>
</div>
    <div><p class="execution"><b><u> Execution time</u></b>: <?=($execution_time)/60?> minutes or <?=$execution_time?> seconds. </p></div>
</body>