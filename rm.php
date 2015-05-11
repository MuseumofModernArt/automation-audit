<?php 

if (isset($_POST['uuid'])){
	$uuid = $_POST['uuid'];
	date_default_timezone_set('America/New_York');
	$datedel = date('m/d/Y h:i:s a', time());
	$db = new SQLite3('transfers.db');
	$query = 'UPDATE unit SET dateDeletedgood='.$datedel.' WHERE uuid='.$uuid.'';
	$db->exec($query);
	print $uuid;
	print $query;
}


?>