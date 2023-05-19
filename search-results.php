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

<body>
    <h2 align="center">Search for Equipment</h2>
    <div class="" style="display: flex;"><a id="back-to-search-button" href="search.php"><< Back To Search</a></div>
    <hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=type">View/Modify All Types</a></div>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=manu">View/Modify All Manufacturers</a></div>
    <hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=insert">Insert New Device</a></div>
    
    <div class="search-info">
<?php
    include("assets/php/functions.php");

    // start benchmark (line 227 for stop benchmark)
    $time_start = microtime(true);

    $dblink=db_iconnect("inventory");

    $type = urldecode($_GET['type']);
    $manu = urldecode($_GET['manu']);
    $serial = urldecode($_GET['serial']);
    $serial_all = urldecode($_GET['serial-all']);

    // create the inital SELECT query, the conditions will be determined depending on $_GET arguments
    $search_query = "SELECT `auto_id`, `serial_num`";

    // create the inital COUNT query, the conditions will be determined depending on $_GET arguments
    $count_query = "SELECT COUNT(`auto_id`) FROM `equipment`";

    // conditions for both search and count query
    $cond = '';

    // if direct page access is attempted, redirect back to search.php
    if ($_SERVER['REQUEST_URI'] == "/search-results.php")
        redirectToSearch();
    
    // get types list to convert the type num to its corresponding name
    $type_query = "SELECT * FROM `type`";
    $type_results = $dblink->query($type_query) or 
        die("Something went wrong with $type_query <br>".$dblink->error);
    
    $type_names = array();
    $type_ids = array();

    while ($type_array = $type_results->fetch_array(MYSQLI_ASSOC)) {
        array_push($type_names, $type_array['name']);
        array_push($type_ids, $type_array['auto_id']);
    }
    
    $type_list = array_combine($type_names, $type_ids);

    // get manu list to convert the manu num to its corresponding name
    $manu_query = "SELECT * FROM `manufacturer`";
    $manu_results = $dblink->query($manu_query);

    $manu_names = array();
    $manu_ids = array();

    while ($manu_array = $manu_results->fetch_array(MYSQLI_ASSOC)) {
        array_push($manu_names, $manu_array['name']);
        array_push($manu_ids, $manu_array['auto_id']);
    }

    $manu_list = array_combine($manu_names, $manu_ids);

    // display search info
    displaySearchInfo($type, $manu, $serial, $serial_all);

    // determine columns selected
    $search_query = selectColumns($search_query, $type, $manu, $serial_all);

    if (isset($_GET['type']) && isset($_GET['manu'])) {

        if ($type != "select-all") 
            $cond = $cond . "`type` = '$type_list[$type]'";
        
        if ($_GET['manu'] != "select-all") {
            $cond = strlen($cond) > 0 ? $cond . " AND " : '';
            $cond = $cond . "`manufacturer` = '$manu_list[$manu]'";
        }

        if (!empty($_GET['serial'])) {
            $cond = strlen($cond) > 0 ? $cond . " AND " : '';
            $cond = $cond . "`serial_num` LIKE '%$serial%'";
        }
    }
    else if (isset($_GET['serial']) && !empty($_GET['serial'])) {
        $cond = $cond . "`serial_num` LIKE '%$serial%'";
    }

    $resultsPerPage = 1000;
    $pagenum = ($_GET['pagenum'] == '0' || !isset($_GET['pagenum'])) ? 1 : intval($_GET['pagenum']);
    $offset = ($pagenum - 1) * $resultsPerPage;

    // add condition(s) to the query
    $search_query = empty($cond) ? $search_query : $search_query . " WHERE " . $cond;

    // add offset and limit to query
    $search_query = $search_query . " LIMIT $offset, $resultsPerPage";

    // execute search query
    $search_results = $dblink->query($search_query);

    // for table display purposes
    $rowCount = isset($_GET['rowCount']) ? intval($_GET['rowCount']) + 1 : 1;  

    // add condition(s) to count query, and execute query
    $count_query = empty($cond) ? $count_query : $count_query . " WHERE " . $cond;

    // if ALL is selected, change query to get largest auto_id
    if ($type == "select-all" && $manu == "select-all" && $serial_all == "true") {
        $count_query = "SELECT `auto_id` FROM `equipment` ORDER BY `auto_id` DESC LIMIT 1";
    }
    else if (empty($type) && empty($manu) && $serial_all == "true") {
        $count_query = "SELECT `auto_id` FROM `equipment` ORDER BY `auto_id` DESC LIMIT 1";
    }

    $totalRowCount = $dblink->query($count_query)->fetch_row()[0];
    $totalPages = (int)(ceil($totalRowCount / $resultsPerPage));

    // echo "<div><b><u>Search Query</u></b>: $search_query</div>";
    // echo "<div><b><u>Count Query</u></b>: $count_query</div>";
    echo "<div>$totalRowCount results found.</div>";
    echo "<hr>";
?>


</div> <!-- end of div id="search-info" (line 13) -->


<!-- create table + table heading -->
<div class="table-wrapper">
    <table>
    <thead id="table-header">
        <tr>
            <th>#</th>
            <th>Auto ID</th>
            <?=displayTableHeader($type, $manu)?>   <!-- display type and/or manu columns as needed -->
            <th>Serial</th>
            <th>Status</th>
            <th>Modify</th>   <!-- modify button column -->
        </tr>
    </thead>
<?php
    $type_list = array_flip($type_list);
    $manu_list = array_flip($manu_list);
    ?>
    <!-- show results in a table -->
    <?php while ($search_array = $search_results->fetch_array(MYSQLI_ASSOC)): ?>
        <tr>
            <td><?=$rowCount++?></td>
            <td><?=$search_array['auto_id']?></td>
            <?=displayTableColumns($type, $manu, $type_list, $manu_list, $search_array)?>
            <td><?=$search_array['serial_num']?></td>
            <td><?=checkInactive($search_array['auto_id']) ? "inactive" : "active"?></td>
            <td align="center"><a id="modify-button" target="_blank" href="modify.php?modify=device&auto_id=<?=$search_array['auto_id']?>"><i class="bi bi-pencil-square"></i></a></td>
        </tr>
    <?php endwhile; ?>
    </table>
    <?php echo ($totalPages == 0) ? "<p align='center'>No data found.</p>" : ""?>
</div>

<!----------- PAGE NAVIGATION ------------>
<div>
<ul class="pagination">
    <?php if ($pagenum-1 > 1):?>
        <li class="prev"><a href="<?='search-results.php?type=' .$type. '&manu=' .$manu. '&serial-num=' .$serial . '&serial-all=' . $serial_all . '&pagenum=' . 1 . '&rowCount=' . ($resultsPerPage*(1))-$resultsPerPage?>"> First </a></li>
    <?php endif; ?>
    <?php if ($pagenum > 1):?>
        <li class="prev"><a href="<?='search-results.php?type=' .$type. '&manu=' .$manu. '&serial-num=' .$serial . '&serial-all=' . $serial_all . '&pagenum=' . $pagenum-1 . '&rowCount=' . ($resultsPerPage*($pagenum-1))-$resultsPerPage?>"> < </a></li>
    <?php endif; ?>

    <li class="current-page">Page <?=($totalPages > 0) ? $pagenum : 0 ?> / <?=$totalPages?></li>

    <?php if ($pagenum < $totalPages):?>
        <li class="next"><a href="<?='search-results.php?type=' .$type. '&manu=' .$manu. '&serial-num=' .$serial. '&serial-all=' . $serial_all . '&pagenum=' . $pagenum+1 . '&rowCount=' . ($resultsPerPage * ($pagenum+1))-$resultsPerPage?>"> > </a></li>
    <?php endif; ?>
    <?php if ($pagenum+1 < $totalPages):?>
        <li class="next"><a href="<?='search-results.php?type=' .$type. '&manu=' .$manu. '&serial-num=' .$serial. '&serial-all=' . $serial_all . '&pagenum=' . $totalPages . '&rowCount=' . ($resultsPerPage * ($totalPages))-$resultsPerPage?>"> Last </a></li>
    <?php endif; ?>

</ul>

<?php if ($totalPages > 1):?>
<div class="goToPage">
    <label for="goToPageNum">To Page: </label>
    <!-- <input id="goToPageNum" type="text" placeholder="Go to page"> -->
    <select id="goToPageNum">
        <?php for ($x = 1; $x <= $totalPages; $x++): ?>
            <option value="<?=$x?>"><?=$x?></option>
        <?php endfor; ?>
    </select>
    <button id="goToPageButton2">Go</button>
    <script type="text/javascript">
        document.getElementById("goToPageNum").value = <?=$pagenum?>;

        var button = document.getElementById("goToPageButton2");
        button.onclick = function() {
            var page = Number(document.getElementById("goToPageNum").value);
            var perPage = <?=$resultsPerPage?>;
            var row = (page - 1) * perPage;

            <?php if (!empty($type) && !empty($manu)): ?>
                document.location.href = "search-results.php?type=<?=$type?>&manu=<?=$manu?>&serial=<?=$serial?>&serial-all=<?=$serial_all?>&pagenum=" + encodeURIComponent(page) +  "&rowCount=" + encodeURIComponent(row);
            <?php elseif ($serial_all == "true"): ?>
                document.location.href = "search-results.php?serial=<?=$serial?>&serial-all=<?=$serial_all?>&pagenum=" + encodeURIComponent(page) +  "&rowCount=" + encodeURIComponent(row);
            <?php endif; ?>
        }
</script>
</div>
</div>
<?php endif; ?>

<?php
    // stop benchmark
    $time_end = microtime(true);
    $seconds = $time_end - $time_start;
    $execution_time = ($seconds)/60;

?>
    <div><p class="execution"><b><u> Execution time</u></b>: <?=$execution_time?> minutes or <?=$seconds?> seconds. </p></div>
</body>