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
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>


	<style type="text/css">
		body { padding-top: 70px; }
		.label-as-badge {
    border-radius: 1em;
    font-size: 15px;

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
	$selectedDB = '/home/archivesuser/moma-utils/pre-ingest-metrics/metrics.db'
?>

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
				
		      </ul>
		      <ul class="nav navbar-nav navbar-right">
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
	</nav>
<h2><?php echo $selectedDB; ?></h2>

<?php
	$db = new SQLite3($selectedDB);
	$query = $db->query('SELECT * FROM counting');
	while ($row = $query->fetchArray()) {
		$date = $row[0];
		$pre_ingest = $row[1];
		$run_component = $row[2];
		$readyForIngest = $row[3];
		$artworkBacklog = $row[4];
		echo var_dump($row);
		// add these to the JSON for the D3 chart

	};


	
	
?>

<svg id="visualisation" width="1000" height="500"></svg>

</body>

<script type="text/javascript">
var vis = d3.select("#visualisation"),
    WIDTH = 1000,
    HEIGHT = 500,
    MARGINS = {
        top: 20,
        right: 20,
        bottom: 20,
        left: 50
    },

    xScale = d3.scale.linear().range([MARGINS.left, WIDTH - MARGINS.right]).domain([2000,2010]),
    yScale = d3.scale.linear().range([HEIGHT - MARGINS.top, MARGINS.bottom]).domain([134,215]),
    xAxis = d3.svg.axis()
    .scale(xScale),
  
yAxis = d3.svg.axis()
    .scale(yScale)
    .orient("left");
    vis.append("svg:g")
    .call(xAxis);

    vis.append("svg:g")
    .attr("transform", "translate(0," + (HEIGHT - MARGINS.bottom) + ")")
    .call(xAxis);

	vis.append("svg:g")
    .attr("transform", "translate(" + (MARGINS.left) + ",0)")
    .call(yAxis);
</script>

