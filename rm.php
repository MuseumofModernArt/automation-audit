<?php 

if (isset($_POST['delete'])){
	transfer_rm($_POST['delete']);
	$uuid = $_POST['delete'];
}

date_default_timezone_set('America/New_York');
$datedel = date('m/d/Y h:i:s a', time());

function transfer_rm($id){
	$db = new SQLite3('transfers.db');
	$db->exec('UPDATE unit SET dateDeletedgood=$datedel WHERE uuid=$uuid');
}


?>