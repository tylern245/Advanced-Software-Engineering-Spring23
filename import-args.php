<?php
function db_connect($dbname) {
	$user="webuser";
	$pw="7MWml!6HKUliFj2k";
	$hostname="localhost";
	$dblink=new mysqli($hsotname, $user, $pw, $dbname);
	return $dblink;
}

$dblink=db_connect("inventory");
echo "Hello from php process $arg[1] about to process file: $arg[2]\n";
$fp=fopen("/home/ubuntu/$arg[2]", "r");
$count=0;
$time_start=microtime(true);
echo "<p>Start time is: $time_start</p>\n";

while(($row=fgetcsv($fp)) !== FALSE) {
	$sql = "INSERT INTO `equipment` (`type`, `manufacturer`, `serial_num`) VALUES ('$row[0]', '$row[1]', '$row[2]')";
	$dblink->query($sql) or 
		die("Something went wrong with $sql<br>".$dblink->error);
	$count++;
}

$time_end=microtime(true);
$seconds = $time_end - $time_start;
$execution_time = ($seconds)/60;
echo "<p>Execution time: $execution_time minutes or $seconds seconds</p>\n";
$rowsPerSecond = $count/$second;
echo "<p>Insert rate: $rowsPerSecond per second</p>\n";
fclose($fp);
?>