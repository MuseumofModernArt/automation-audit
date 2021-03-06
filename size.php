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

 .run_component{
  stroke: blue;
  stroke-width: 2;
  fill: none;
 }

 .readyForIngest{
  stroke: green;
  stroke-width: 2;
  fill: none;
 }

 .artworkBacklog{
  stroke: red;
  stroke-width: 2;
  fill: none;
 }

 .preIngestIsilon{
 	  stroke: rgb(0, 238, 117);
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
	$selectedDB = 'metrics.db'
?>


<?php

	$db = new SQLite3($selectedDB);
	$query = $db->query('SELECT * FROM size');
	$pre_ingest_data = array();
	$readyForIngest_data = array();
	$artworkBacklog_data = array();
	$preIngestIsilon_data = array();
	$readyForIngestIsilon_data = array();

	while ($row = $query->fetchArray()) {
		$date = $row[0];
		$pre_ingest = (((($row[1] / 1000) / 1000) / 1000) / 1000);
		$readyForIngest = (((($row[3] / 1000) / 1000) / 1000) / 1000);
		$artworkBacklog = (((($row[4] / 1000) / 1000) / 1000) / 1000);
		$preIngestIsilon = (((($row[6] / 1000) / 1000) / 1000) / 1000);
		$readyForIngestIsilon = (((($row[7] / 1000) / 1000) / 1000) / 1000);

		$pre_ingest_data[] = array("date" => $date, "close" => $pre_ingest);
		$readyForIngest_data[] = array("date" => $date, "close" => $readyForIngest);
		$artworkBacklog_data[] = array("date" => $date, "close" => $artworkBacklog);
		$preIngestIsilon_data[] = array("date" => $date, "close" => $preIngestIsilon);
		$readyForIngestIsilon_data[] = array("date" => $date, "close" => $readyForIngestIsilon);

		};

	$pre_ingest_data = json_encode($pre_ingest_data);
	$readyForIngest_data = json_encode($readyForIngest_data);
	$artworkBacklog_data = json_encode($artworkBacklog_data);
	$preIngestIsilon_data = json_encode($preIngestIsilon_data);
	$readyForIngestIsilon_data = json_encode($readyForIngestIsilon_data);

		// echo $date.$pre_ingest.$run_component.$readyForIngest.$artworkBacklog;
		// add these to the JSON for the D3 chart
	// uncoment for deubugging
	// echo $output
?>

</body>

<script type="text/javascript">

// set dimensions of the graph

var margin = { top: 30, right: 20, bottom: 30, left: 50 },
    width = 800 - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

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
     var pre_ingest_data = <?php echo $pre_ingest_data; ?>;
     var readyForIngest_data = <?php echo $readyForIngest_data; ?>;
     var artworkBacklog_data = <?php echo $artworkBacklog_data; ?>;
     var preIngestIsilon_data = <?php echo $preIngestIsilon_data; ?>;
     var readyForIngestIsilon_data = <?php echo $readyForIngestIsilon_data; ?>;
 
 	//get data for pre-ingest
	pre_ingest_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});

	//get data for readyForIngest_data
	readyForIngest_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});

	//get data for artworkBacklog_data
	artworkBacklog_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});

	//get data for preIngestIsilon_data
	preIngestIsilon_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});

	//get data for readyForIngestIsilon_data
	readyForIngestIsilon_data.forEach(function(d) {
		d.date = parseDate(d.date);
		d.close = +d.close;
	});

	// Scale the range of the data
	x.domain(d3.extent(pre_ingest_data, function(d) { return d.date; }));
	y.domain([0, 60]);
 


	// draw pre-ingest
	svg.append("path")	
		.attr("class", "pre_ingest")
		.attr("d", valueline(pre_ingest_data))
		.attr("data-legend",function(d) { return "VNX staging"});
 
 	// draw readyForIngest_data
	svg.append("path")
		.attr("class", "readyForIngest")
		.attr("d", valueline(readyForIngest_data))
		.attr("data-legend",function(d) { return "VNX ready for ingest"});
 	// draw artworkBacklog_data
	svg.append("path")
		.attr("class", "artworkBacklog")
		.attr("d", valueline(artworkBacklog_data))
		.attr("data-legend",function(d) { return "VNX backlog"});
		// draw preIngestIsilon
	svg.append("path")
		.attr("class", "preIngestIsilon")
		.attr("d", valueline(preIngestIsilon_data))
		.attr("data-legend",function(d) { return "Isilon staging"});
		// draw readyForIngestIsilon
	svg.append("path")
		.attr("class", "readyForIngestIsilon")
		.attr("d", valueline(readyForIngestIsilon_data))
		.attr("data-legend",function(d) { return "Isilon ready for ingest"});

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
    .attr("transform","translate(50,30)")
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
    .text("Size in Terabytes");

svg.append("text")
        .attr("x", (width / 2))             
        .attr("y", 0 - (margin.top / 2))
        .attr("text-anchor", "middle")  
        .style("font-size", "16px") 
        .style("text-decoration", "underline")  
        .text("DRMC Pre-ingest storage usage");

</script>