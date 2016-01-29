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
		body { padding-top: 70px; 
			margin: 50px;}
		.label-as-badge {
    border-radius: 1em;
    font-size: 15px;

}


path { 
  stroke: black;
  stroke-width: 2;
  fill: none;
}

.pre_ingest{
  stroke: orange;
  stroke-width: 2;
  fill: none;
 }

 .readyForIngest{
  stroke: green;
  stroke-width: 2;
  fill: none;
 }

  .readyForIngest2{
  stroke: rgb(45, 255, 0);
  stroke-width: 2;
  fill: none;
 }

 .artworkBacklog{
  stroke: red;
  stroke-width: 2;
  fill: none;
 }

  .mpaBacklog{
  stroke: blue;
  stroke-width: 2;
  fill: none;
 }
 
.axis path,
.axis line {
	fill: none;
	stroke: grey;
	stroke-width: 1;
	shape-rendering: crispEdges;
}

.legend rect {
  fill:white;
  stroke:black;
  opacity:0.8;}

	</style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
<script src="d3.legend.js"></script>


<?php 
	$command = escapeshellcmd('/home/archivesuser/moma-utils/pre-ingest-metrics/metrics.py');
	$output = shell_exec($command);
	// $selectedDB = '/home/archivesuser/moma-utils/pre-ingest-metrics/metrics.db'
	$selectedDB = 'metrics.db'
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
		      <a class="navbar-brand" href="#">Media cons pre-ingest stats</a>
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

<?php
	$db = new SQLite3($selectedDB);
	$query = $db->query('SELECT * FROM counting');
	$pre_ingest_data = array();
	$readyForIngest_data = array();
	$artworkBacklog_data = array();
	$mpaBacklog_data = array();

	while ($row = $query->fetchArray()) {
		$date = $row[0];
		$pre_ingest = $row[1];
		$readyForIngest = $row[3];
		$artworkBacklog = $row[4];
		$mpaBacklog = $row[5];
		$pre_ingest_isilon = $row[6];
		$readyForIngest2 = $row[7];

		$simple_pre_ingest_staging_data[] = array("date" => $date, "close" => $pre_ingest+$pre_ingest_isilon);
		$simple_backlog_data[] = array("date" => $date, "close" => $artworkBacklog+$mpaBacklog);
		$simple_ready_data[] = array("date" => $date, "close" => $readyForIngest+$readyForIngest2);

		};

	$simple_pre_ingest_staging_data = json_encode($simple_pre_ingest_staging_data);
	$simple_backlog_data = json_encode($simple_backlog_data);
	$simple_ready_data = json_encode($simple_ready_data);

		// echo $date.$pre_ingest.$run_component.$readyForIngest.$artworkBacklog;
		// add these to the JSON for the D3 chart
	// uncoment for deubugging
	// echo $output
?>

</body>

<script type="text/javascript">

// set dimensions of the graph

var margin = { top: 30, right: 20, bottom: 30, left: 50 },
    width = 1200 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

// parse the date format
var	parseDate = d3.time.format("%Y-%m-%d").parse;

//colors (this is new)
// Our color bands
var color = d3.scale.ordinal()
    .range(["#308fef", "#5fa9f3", "#1176db"]);


// set the ranges
var x = d3.time.scale().range([0, width]);
var y = d3.scale.linear().range([height, 0]);

// define the axis
var xAxis = d3.svg.axis().scale(x)
	.orient("bottom").ticks(5);
var yAxis = d3.svg.axis().scale(y)
	.orient("left").ticks(5);
  
// Define the line
var	valueline = d3.svg.line()
	.x(function(d) { return x(d.date); })
	.y(function(d) { return y(d.close); });
    
// Adds the svg canvas
var	svg = d3.select("body")
	.append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
	.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
 
// Get the data
     var simple_pre_ingest_staging_data = <?php echo $simple_pre_ingest_staging_data; ?>;
     var simple_backlog_data = <?php echo $simple_backlog_data; ?>;
     var simple_ready_data = <?php echo $simple_ready_data; ?>;
 
 	//get data for pre-ingest
	simple_pre_ingest_staging_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});



	//get data for readyForIngest_data
	simple_ready_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});


	//get data for artworkBacklog_data
	simple_backlog_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});



	// Scale the range of the data
	x.domain(d3.extent(simple_pre_ingest_staging_data, function(d) { return d.date; }));
	y.domain([0, 800]);
 


	// draw pre-ingest
	svg.append("path")	
		.attr("class", "pre_ingest")
		.attr("d", valueline(simple_pre_ingest_staging_data))
		.attr("data-legend",function(d) { return "Pre-ingest Staging"});
 
 	// draw simple_ready_data
	svg.append("path")
		.attr("class", "readyForIngest")
		.attr("d", valueline(simple_ready_data))
		.attr("data-legend",function(d) { return "Ready for ingest"});

 	// draw artworkBacklog_data
	svg.append("path")
		.attr("class", "artworkBacklog")
		.attr("d", valueline(simple_backlog_data))
		.attr("data-legend",function(d) { return "Artwork level backlog"});



	// Add the X Axis
	svg.append("g")		
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);
 
	// Add the Y Axis
	svg.append("g")		
		.attr("class", "y axis")
		.call(yAxis);
  legend = svg.append("g")
    .attr("class","legend")
    .attr("transform","translate(850,30)")
    .style("font-size","12px")
    .call(d3.legend)

  setTimeout(function() { 
    legend
      .style("font-size","20px")
      .attr("data-style-padding",10)
      .call(d3.legend)
  },1000)

svg.append("text")
    .attr("class", "x label")
    .attr("text-anchor", "end")
    .attr("x", width)
    .attr("y", height - 6)
    .text("date");
    svg.append("text")
    .attr("class", "y label")
    .attr("text-anchor", "end")
    .attr("y", 6)
    .attr("dy", ".75em")
    .attr("transform", "rotate(-90)")
    .text("number of artworks");

svg.append("text")
        .attr("x", (width / 2))             
        .attr("y", 0 - (margin.top / 2))
        .attr("text-anchor", "middle")  
        .style("font-size", "16px") 
        .style("text-decoration", "underline")  
        .text("Number of Artworks in ingest queue");

</script>

