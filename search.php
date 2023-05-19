<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search</title>
<link href="assets/css/my-style.css" rel="stylesheet">
<link href="assets/css/modify.css" rel="stylesheet">

</head>
<body>
    <?php
        include("assets/php/functions.php");

        // start benchmark (line 284 for stop benchmark)
        $time_start = microtime(true);

        $dblink=db_iconnect("inventory");

        if (isset($_GET['type']) && isset($_GET['manu']) &&
            !empty($_GET['type']) && !empty($_GET['manu'])) {

            $type = $_GET['type'];
            $manu = $_GET['manu'];

            if (isset($_GET['serial-num'])) {
                if (!empty($_GET['serial-num'])){

                    $serial = $_GET['serial-num'];

                    redirect("/search-results.php?type=$type&manu=$manu&serial=$serial&serial-all=false");
                }
                else 
                    ?><script>alert("Please enter a serial number.")</script><?php
            }
            else if (isset($_GET['serial-all']) && $_GET['serial-all'] == "true") {
                $serial_all = $_GET['serial-all'];

                redirect("/search-results.php?type=$type&manu=$manu&serial=&serial-all=$serial_all");
            }

        }
        else if (isset($_GET['serial-num'])) {
            if (!empty($_GET['serial-num'])) {
                $serial = $_GET['serial-num'];

                redirect("/search-results.php?serial=$serial&serial-all=false");
            }
            else 
                ?><script>alert("Please enter a serial number.")</script><?php
        }
        else if (isset($_GET['serial-all']) && $_GET['serial-all'] == "true") {

            redirect("/search-results.php?serial=&serial-all=true");
        }
        
    ?>

    <h2>Search for Equipment</h2>
    <hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=type">View/Modify All Types</a></div>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=manu">View/Modify All Manufacturers</a></div>
    <hr>
    <div class="" style="display: flex;"><a id="modify-link" href="modify.php?modify=insert">Insert New Device</a></div>
    <hr>
    <form action="" method="GET">
    <div class="search">
        <div class="row">
        <label for="first-option">Search by:
        <select name="first-option" id="first-option" size="4" onchange="showSubmitButton(this), firstOptionSelected(this)">
            <option value="type">Type</option>
            <option value="manu">Manufacturer</option>
            <option value="serial-num">Serial #</option>
            <option value="all">All</option>
        </select>
        </label>
        <input type="hidden" name="first" id="first" value="<?=isset($_GET['first']) ? $_GET['first'] : ''?>">
        <script type="text/javascript">
            document.getElementById('first-option').value = "<?=$_GET['first']?>";
            document.getElementById('first').value = "<?=$_GET['first']?>";
        </script>
        </div>

        <div class="row">
        <?php
            if (isset($_GET['first'])) {
        ?><script>document.getElementById('first-option').disabled = true;</script> <?php

                if ($_GET['first'] == "type") {
        ?>
                    <div class="column">
                    <label for="type-drop" id="type-drop">Select Type:
                    <select name="type-drop" id="type-drop-select" size="8" onchange="showSubmitButton(this), typeSelected(this)">
                    <option value="select-all" selected>(SELECT ALL)</option>
        <?php
                    $type_query = "SELECT * FROM `type` ORDER BY `name` ";
                    $type_results = $dblink->query($type_query) or 
                        die("Something went wrong with $type_queryl <br>".$dblink->error);
            
                    while ($type_list = $type_results->fetch_array(MYSQLI_ASSOC)) {
        ?>              <option value="<?=$type_list['name']?>"><?=$type_list['name']?><?=$type_list['status'] == "inactive" ? " (inactive)" : ""?></option>    <?php
                    } // end of while loop
        ?>
                    </select>
                    </label>
                    <input type="hidden" name="type" id="type">
                    <script type="text/javascript">
                    document.getElementById('type-drop-select').value = "<?=$_GET['type']?>";
                    document.getElementById('type').value = "<?=$_GET['type']?>";
                    </script>
                    </div> <!-- end of column -->
        <?php

                    if (isset($_GET['type'])) {
        ?>
                        <script>document.getElementById('type-drop-select').disabled = true;</script>
                        <div class="column">
                        <label for="manu-drop" id="manu-drop">Select Manufacturer:
                        <select name="manu-drop" id="manu-drop-select" size="8" onchange="showSubmitButton(this), manuSelected(this)">
                        <option value="select-all" selected>(SELECT ALL)</option>
        <?php
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
                            $type_for_search = $type_list[$_GET['type']];
                        
                        $manu_query = "";
                        if ($_GET['type'] == "select-all")
                            $manu_query = "SELECT * FROM `manufacturer` ORDER BY `name`";
                        else {
                            $manu_query = "SELECT DISTINCT `manufacturer`.`name` FROM `equipment`, `manufacturer`
                            WHERE `manufacturer`.`auto_id` = `equipment`.`manufacturer` AND 
                            `equipment`.`type` = $type_for_search ORDER BY `name`";
                        }
                        $manu_results = $dblink->query($manu_query) or 
                            die("Something went wrong with $manu_query <br>".$dblink->error);
                    
                        while ($manu_list = $manu_results->fetch_array(MYSQLI_ASSOC)) {
        ?>                  <option value="<?=$manu_list['name']?>"><?=$manu_list['name']?><?=$manu_list['status'] == "inactive" ? " (inactive)" : ""?></option>    <?php
                        }   // end of while loop
        ?>
                        </select>
                        </label>
                        <input type="hidden" name="manu" id="manu">
                        <script type="text/javascript">
                        document.getElementById('manu-drop-select').value = "<?=$_GET['manu']?>";
                        document.getElementById('manu').value = "<?=$_GET['manu']?>";
                        </script>
                        </div> <!-- end of column -->
        <?php
                        if (isset($_GET['manu'])) {
        ?>
                            <script>document.getElementById('manu-drop-select').disabled = true;</script>
                            <div class="column">
                            <label for="serial-num" id="serial-num">Serial #: 
                            <input type="text" id="serial-num-field" name="serial-num" onkeypress="showSubmitButton(this)">
                            </label>
                            <input id="serial-all-box" name="serial-all" type="checkbox" onchange="showSubmitButton(this)" value="false">
                            <label for="serial-all" id="serial-all">Select all</label>
                            
                            <script type="text/javascript">
                            document.getElementById('serial-num-field').value = "<?=$_GET['serial-num']?>"
                            </script>
                            </div> <!-- end of column -->
        <?php
                        }
                    } // end of if (second option)
                } // end of if (first option type)


                else if ($_GET['first'] == "manu") {
        ?>
                    <div class="column">
                    <label for="manu-drop" id="manu-drop">Select Manufacturer:
                    <select name="manu-drop" id="manu-drop-select" size="8" onchange="showSubmitButton(this), manuSelected(this)">
                    <option value="select-all" selected>(SELECT ALL)</option>
        <?php
                    $manu_query = "SELECT * FROM `manufacturer` ORDER BY `name`";
                    $manu_results = $dblink->query($manu_query) or 
                        die("Something went wrong with $manu_query <br>".$dblink->error);
                
                    while ($manu_list = $manu_results->fetch_array(MYSQLI_ASSOC)) {
        ?>              <option value="<?=$manu_list['name']?>"><?=$manu_list['name']?><?=$manu_list['status'] == "inactive" ? " (inactive)" : ""?></option>    <?php
                    }   // end of while loop
        ?>
                    </select>
                    </label>
                    <input type="hidden" name="manu" id="manu">
                    <script type="text/javascript">
                    document.getElementById('manu-drop-select').value = "<?=$_GET['manu']?>"
                    document.getElementById('manu').value = "<?=$_GET['manu']?>"
                    </script>
                    </div> <!-- end of column -->
        <?php
                    if (isset($_GET['manu'])) {
                        $manu_chosen = $_GET['manu'];
        ?>
                        <script>document.getElementById('manu-drop-select').disabled = true;</script>
                        <div class="column">
                        <label for="type-drop" id="type-drop">Select Type:
                        <select name="type-drop" id="type-drop-select" size="8" onchange="showSubmitButton(this), typeSelected(this)">
                        <option value="select-all" selected>(SELECT ALL)</option>
        <?php
                            // get manu list to convert the manu num to its corresponding name
                            $manu_query = "SELECT * FROM `manufacturer` WHERE `status` = 'active'";
                            $manu_results = $dblink->query($manu_query);

                            $manu_names = array();
                            $manu_ids = array();

                            while ($manu_array = $manu_results->fetch_array(MYSQLI_ASSOC)) {
                                array_push($manu_names, $manu_array['name']);
                                array_push($manu_ids, $manu_array['auto_id']);
                            }

                            $manu_list = array_combine($manu_names, $manu_ids);
                            $manu_for_search = $manu_list[$_GET['manu']];

                        $type_query = "";
                        if ($manu_chosen == "select-all")
                            $type_query = "SELECT * FROM `type` WHERE `status` = 'active' ORDER BY `name`";
                        else {
                            $type_query = "SELECT DISTINCT `type`.`name` FROM `equipment`, `type` WHERE 
                            `type`.`auto_id` = `equipment`.`type` AND `equipment`.`manufacturer` = 
                            '$manu_for_search' ORDER BY `name`";
                        }
                          

                        $type_results = $dblink->query($type_query) or 
                            die("Something went wrong with $type_query <br>".$dblink->error);

                        while ($type_list = $type_results->fetch_array(MYSQLI_ASSOC)) {
        ?>                  <option value="<?=$type_list['name']?>"><?=$type_list['name']?><?=$type_list['status'] == "inactive" ? " (inactive)" : ""?></option>    <?php
                        } // end of while loop

        ?>
                        </select>
                        </label>
                        <input type="hidden" name="type" id="type">
                        <script type="text/javascript">
                        document.getElementById('type-drop-select').value = "<?=$_GET['type']?>"
                        document.getElementById('type').value = "<?=$_GET['type']?>"
                        </script>
                        </div> <!-- end of column -->
        <?php
                        if (isset($_GET['type'])) {
        ?>
                            <script>document.getElementById('type-drop-select').disabled = true;</script>
                            <div class="column">
                            <label for="serial-num" id="serial-num">Serial #: 
                            <input type="text" id="serial-num-field" name="serial-num" onkeypress="showSubmitButton(this)">
                            </label>
                            <input id="serial-all-box" name="serial-all" type="checkbox" onchange="showSubmitButton(this)" value="false">
                            <label for="serial-all" id="serial-all">Select all</label>
                            
                            <script type="text/javascript">
                            document.getElementById('serial-num-field').value = "<?=$_GET['serial-num']?>"
                            </script>
                            </div> <!-- end of column -->
        <?php
                        }
                    } // end of if (second option)
                } // end of if (first option manu)

                else if ($_GET['first'] == "serial-num") {
        ?>
                    <div class="column">
                    <label for="serial-num" id="serial-num"> Serial #: 
                    <input type="text" id="serial-num-field" name="serial-num" onkeypress="showSubmitButton(this)">
                    </label>
                    <input id="serial-all-box" name="serial-all" type="checkbox" onchange="showSubmitButton(this)" value="false">
                    <label for="serial-all" id="serial-all">Select all</label>
                    </div> <!-- end of column -->
        <?php
                }

                else if ($_GET['first'] == "all") {
                    redirect("search-results.php?type=select-all&manu=select-all&serial=&serial-all=true");
                }
            } // end of if - first option
            
        ?>
        </div> <!-- end of row -->

        <div class="row">
        <input class="column" type="submit" id="submit-button" style="display: none;" onclick="disableSelect()">
        
        <?php
            if (isset($_GET['first'])) {
        ?>
            <a class="column" id="reset-button" href="search.php">Reset</a>
        <?php
            }
        ?>
        </div> <!-- end of row -->
    </div> <!-- end form-wrapped div -->
    </form>
</body>

<?php
    // stop benchmark
    $time_end = microtime(true);
    $seconds = $time_end - $time_start;
    $execution_time = ($seconds)/60;

?>
    <div><p class="execution"><b><u> Execution time</u></b>: <?=$execution_time?> minutes or <?=$seconds?> seconds. </p></div>
<?php
?>

<script src="assets/js/my-script.js" type="text/javascript"></script>