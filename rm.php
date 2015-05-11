<?php 

if (isset($_POST)){
	transfer_rm($_POST);
	$uuid = $_POST;
	date_default_timezone_set('America/New_York');
	$datedel = date('m/d/Y h:i:s a', time());
	$db = new SQLite3('transfers.db');
	$db->exec('UPDATE unit SET dateDeletedgood=$datedel WHERE uuid="$uuid"');
}


?>