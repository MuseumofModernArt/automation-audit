<?php 

$uuid = $_POST['uuid'];
$user = $_POST['user'];
$database = $_POST['database'];

if (!isset($uuid) || !isset($user) || !isset($database)) {
	exit;
}

date_default_timezone_set('America/New_York');
$datedel = date('m/d/Y h:i:s a', time());
$db = new SQLite3($database);
$db->exec('UPDATE unit SET dateDeleted="'.$datedel.'" WHERE uuid="'.$uuid.'"');
$db->exec('UPDATE unit SET deletedBy="'.$user.'" WHERE uuid="'.$uuid.'"');
print 'Deleted by '.$user." on ".$datedel;

?>
