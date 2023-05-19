<?php
	function db_iconnect($dbname) {
	$user="webuser";
	$pw="7MWml!6HKUliFj2k";
	$hostname="localhost";
	$dblink=new mysqli($hostname, $user, $pw, $dbname);
	return $dblink;
}

$dblink=db_iconnect("inventory");

// start benchmark
$time_start = microtime(true);

$sql = "SELECT * FROM `type`";
$result=$dblink->query($sql)
	or die("Something went wrong with: $sql <br>".$dblink->error);

while ($item = $result->fetch_array(MYSQLI_ASSOC)) {
	$sql="SET AUTOCOMMIT=0";
	$dblink->query($sql);

	$sql = "SELECT * FROM `equipment` WHERE `type` = '$item[name]'";
	$rst = $dblink->query($sql) or 
		die("Something went wrong with: $sql <br>".$dblink->error);
	while ($data = $rst->fetch_array(MYSQLI_ASSOC)) {
		echo "<p>About to update $data[auto_id] with new type: $item[name] from $data[type]</p>\n";
		$sql = "UPDATE `equipment` SET `type`='$item[auto_id]' WHERE `auto_id` = '$data[auto_id]'";
		$dblink->query($sql) or 
			die("Something went wrong with: $sql <br>".$dblink->error);
	}

	$sql="COMMIT";
	$dblink->query($sql);

}

// end benchmark
$time_end=microtime(true);
$seconds = $time_end - $time_start;
$execution_time = ($seconds)/60;
$avg_update_per_sec = $count / $seconds;

echo "<p> Execution time: $execution_time minutes or $seconds seconds. </p>\n";
echo "<p> Average of $avg_update_per_sec records updated per second. </p>\n";

echo "\n<p>Done</p>";
?>