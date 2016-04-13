<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Sorry, you have to log in.';
    exit;
} else {
    $binderUsername = $_SERVER['PHP_AUTH_USER'];
    $binderPassword = $_SERVER['PHP_AUTH_PW'];
    $context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$binderUsername:$binderPassword")
    )
)); 

?>

<!--

Using this tool requires adding two columns to transfers.db:

	ALTER TABLE unit ADD COLUMN dateDeleted text;
	ALTER TABLE unit ADD COLUMN deletedBy text;

todo:
• optimize page load time (currently all API calls happen before page load)
• maybe the best way to do the above is to do the API calls asyncronously via jquery after page load?
	• document ready, with loading animation in SS and Binder columns
	• for each UUID, var = UUID
	• make API calls to SS and Binder
	• modify SS and Binder column accordingly


transfers.db "unit" table columns should be

0|id|INTEGER|1||1
1|uuid|VARCHAR(36)|0||0
2|path|BLOB|0||0
3|unit_type|VARCHAR(10)|0||0
4|status|VARCHAR(20)|0||0
5|microservice|VARCHAR(50)|0||0
6|current|BOOLEAN|0||0
7|dateDeleted|text|0||0
8|deletedBy|text|0||0

Permissions on the DB need to be:
	-rwxrw-r- - 1 www-data root  74K May 28 10:56 transfers.db


-->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Automation-audit</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
	<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>


	<style type="text/css">
		body { padding-top: 70px; }
		.label-as-badge {
    border-radius: 1em;
    font-size: 15px;

}

	.exlposion{
		position: fixed;
		bottom: 0;
		right: 0;
		z-index: 999;
	}
	</style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
<?php 

	if (isset($_GET['pipeline'])){
		$selectedPipeline = $_GET['pipeline'];
	}
	else {
		$selectedPipeline = 'automation-tools';
	};

	if (isset($_GET['db'])){
		$selectedDB = '/usr/lib/archivematica/'.$selectedPipeline.'/transfers/'.$_GET['db'];
	}
	else {
		$selectedDB = '/usr/lib/archivematica/'.$selectedPipeline.'/transfers/transfers.db';
	};

	?>

<img class="exlposion" src="explosion-1.gif">

	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="#">Automation-audit</a>
		    </div>

		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
				<li> </li>

				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">select Pipeline<span class="caret"></span></a>
				  <ul class="dropdown-menu" role="menu">
				    <li><a href='?selectedPipeline=automation-tools'> Pipeline 1 (VNX unbagged) </a></li>
				    <li><a href='?selectedPipeline=automation-tools-2'> Pipeline 2 (VNX bagged) </a></li>
				    <li><a href='?selectedPipeline=automation-tools-3'> Pipeline 1 (Isilon bagged) </a></li>
				  </ul>
				</li>

				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">select database<span class="caret"></span></a>
				  <ul class="dropdown-menu" role="menu">
				  	<?php
						foreach (glob("/usr/lib/archivematica/automation-tools/transfers/transfers.db*") as $filename) {
						    echo "<li><a href='?db=$filename'> $filename </a></li>";
						}
				  	?>
				  </ul>
				</li>
		      </ul>
		      <ul class="nav navbar-nav navbar-right">
		      	<li><a href=""><span class="glyphicon glyphicon-user" aria-hidden="true"></span><span class="user"><?php echo " {$_SERVER['PHP_AUTH_USER']}"; ?></span></a></li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
	</nav>
<h2><?php echo $selectedDB; ?></h2>

<?php
	$db = new SQLite3($selectedDB);
	$query = $db->query('SELECT * FROM unit');
	echo '<table class="table table-striped">
		      <thead>
		        <tr>
		          <th>id</th>
		          <th>Path</th>
		          <th>Stage</th>
		          <th>Status</th>
		          <th>AIP UUID</th>
		          <th>AIP size</th>
		          <th>Storage Service</th>
		          <th>Binder</th>
		          <th>Source deletion status</th>
		          <th></th>
		        </tr>
		      </thead>
		      <tbody>';

	while ($row = $query->fetchArray()) {
		$id = $row[0];
		$uuid = $row[1];
		$path = $row[2];
		$unitType = $row[3];
		$status = $row[4];
		$microservice = $row[5];
		$current = $row[6];
		$dateDeleted = $row[7];
		$deletedBy = $row[8];
		$rowcolor = "";
		$storageservice = "";
		$deletebutton = "";
		$ssgood = False;
		$bindergood = False;
		$binderAIPsize = "n/a";
		$totalSize = "";
		$remainingForDeletion = "";
		$hasBeenDeleted = "";
		$binderstatus = "";
		if ($status == "FAILED" or $status == "REJECTED"){
			$rowcolor = "danger";
		};

		/* if uuid is not empty, ping SS API and Binder API  */
		if (strlen(trim($uuid)) > 0){
			$ssUrl = 'http://archivematica.museum.moma.org:8000/api/v2/file/'.$uuid.'/?format=json';
			$url_header = @get_headers($ssUrl);
			if ($url_header[0] == 'HTTP/1.1 200 OK') {
				$ssgood = True;
				$ssendpoint = file_get_contents($ssUrl);
				$jsonresult = json_decode($ssendpoint, true);
				$storageserviceuuid = $jsonresult['uuid'];
				if ($storageserviceuuid == $uuid){
					$storageservice = '<span class="label label-success label-as-badge"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></span>';
				};
			}
			else  {
				$ssgood = False;
				$storageservice = '<span class="label label-danger label-as-badge"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>';
			}
			$binderURL = 'http://drmc.museum.moma.org/api/aips/'.$uuid;
			$binderEndpoint = @file_get_contents($binderURL, false, $context);
			if ($binderEndpoint === FALSE) {
				$binderstatus = '<span class="label label-danger label-as-badge"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>';
				$bindergood = False;
				$rowcolor = "danger";
			}
			else {
				$binderjson = json_decode($binderEndpoint, true);
				$binderstatus = '<span class="label label-success label-as-badge"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></span>';
				$bindergood = True;
				$binderAIPsize = $binderjson['size'];
				$totalSize = $totalSize + $binderAIPsize;
			}

		};

		if ($ssgood and $bindergood and $status != "FAILED" and strlen($dateDeleted) < 2){
			$deletebutton = '<div class="btn-group" role="group" aria-label="...">
	                        <button type="button" id="'.$uuid.'" class="btn btn-warning btn-xs rm">mark source as deleted <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
	                </div>';
	        $remainingForDeletion = $remainingForDeletion+$binderAIPsize;
		}
		elseif (strlen($dateDeleted) > 2) {
			$deletebutton = 'Deleted by '.$deletedBy." on ".$dateDeleted;
			$hasBeenDeleted = $hasBeenDeleted+$binderAIPsize;
		};

		

	echo '<tr class="'.$rowcolor.'">
		<th>'.$id.'</th>
		<td>'.$path.'</td>
		<td>'.$unitType.'</td>
		<td>'.$status.'</td>
		<td>'.$uuid.'</td>
		<td><span class="converter">'.$binderAIPsize.'</span></td>
		<td>'.$storageservice.'</td>
		<td>'.$binderstatus.'</td>
		<td>'.$deletebutton.'</td>
	';



	};
}

		$db->close();
		unset($db);
		echo "Total automated data: <span class='converter'>".$totalSize."</span>";
		echo "Stuff to delete: <span class='converter'>".$remainingForDeletion."</span>";
		echo "Stuff already deleted: <span class='converter'>".$hasBeenDeleted."</span>"
?>


</body>
<script src="rm.js"></script>
<script src="jquery.filesize.min.js"></script>
<script>
$(function() {
$(".converter").filesize();
});
</script>





