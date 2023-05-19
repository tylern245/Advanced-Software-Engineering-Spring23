<?php
	function db_iconnect($dbname) {
	$user="webuser";
	$pw="7MWml!6HKUliFj2k";
	$hostname="localhost";
	$dblink=new mysqli($hostname, $user, $pw, $dbname);
	return $dblink;
}

$dblink=db_iconnect("inventory");
$type = $argv[1];
$time_start = microtime(true);

$sql = "SET AUTOCOMMIT=0";
$dblink->query($sql) or 
	die("Something went wrong with: $sql <br>".$dblink->error);

$sql = "SELECT * FROM `type` WHERE `name` = '$type'";
$result_1=$dblink->query($sql)
	or die("Something went wrong with: $sql <br>".$dblink->error);
$type_array = $result_1->fetch_array(MYSQLI_ASSOC);

$sql = "SELECT * FROM `equipment` WHERE `type` = '$type_array[name]'";
$result_2 = $dblink->query($sql) or 
	die("Something went wrong with: $sql <br>".$dblink->error);
//$count = $result_2->num_rows;
$count = 0;
while ($equip_array = $result_2->fetch_array(MYSQLI_ASSOC)) {
	echo "<p>Row ". ++$count. ". About to update $equip_array[auto_id] with new type $type_array[auto_id] from $equip_array[type].</p>\n";
	$sql = "UPDATE `equipment` SET `type`='$type_array[auto_id]' WHERE `auto_id` = '$equip_array[auto_id]'";
	$result_3 = $dblink->query($sql) or 
		die("Something went wrong with: $sql <br>".$dblink->error);
}

$sql="COMMIT";
$dblink->query($sql) or 
	die("Something went wrong with: $sql <br>".$dblink->error);

$time_end = microtime(true);
$seconds = $time_end - $time_start;
$execution_time = ($seconds)/60;
$avg_update_per_sec = $count / $seconds;

echo "<p> Execution time: $execution_time minutes or $seconds seconds. </p>\n";
echo "<p> Average of $avg_update_per_sec records updated per second. </p>\n";
echo "\n<p> Done </p>\n";
?>