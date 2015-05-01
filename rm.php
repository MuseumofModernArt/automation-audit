<?php 

if (isset($_POST['delete'])){
	transfer_rm($_POST['delete']);
}


function transfer_rm($id){
	$db = new SQLite3('transfers.db');
	$db->exec('DELETE FROM unit WHERE uuid="manager"');
}


?>