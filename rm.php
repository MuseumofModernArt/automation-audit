<?php 

if (isset($_POST['uuid'])){
	$uuid = $_POST['uuid'];
	date_default_timezone_set('America/New_York');
	$datedel = date('m/d/Y h:i:s a', time());
	$db = new SQLite3('transfers.db');
	$db->exec('UPDATE unit SET dateDeleted="'.$datedel.'" WHERE uuid="'.$uuid.'"');
	print $uuid;
}


?>