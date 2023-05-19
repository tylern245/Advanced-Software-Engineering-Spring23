<?php
	$files=array('equip-partaa','equip-partab','equip-partac','equip-partad','equip-partae', 
				 'equip-partaf','equip-partag','equip-partah','equip-partai','equip-partaj',
				 'equip-partak');
	// foreach($files as $key=>$value)
	// {
	// 		shell_exec("/usr/bin/php /var/www/html/import-args.php $key $value > /var/www/html/equipment/log/$value.log 2>/var/www/html/equipment/log/$value.log &");
	// }
	
    foreach ($files as $file) {
        echo "$file\n";
    }
	
?>
