<?php
date_default_timezone_set('Europe/Stockholm');
$AUTH_KEY = "42a193cf-a9b9-400f-8a15-c3db3a206251";
$id_chalmers = "9021014001960000";
$id_tvargata = "9021014001970000";
$id_gbgC = "9021014008000000";

$requestData = array(
	"authKey" => $AUTH_KEY,
	"id" => $id_gbgC,
	"date" => date("Y-m-d"), 
	"format" => "json"
);
$locationRequest = array(
	"input" => "Göteborg",
	"authKey" => $AUTH_KEY, 
	"format" => "json"
);
$locationID = "http://api.vasttrafik.se/bin/rest.exe/location.name?" . http_build_query($locationRequest);
$base_url = "http://api.vasttrafik.se/bin/rest.exe/departureBoard?" . http_build_query($requestData);
//echo $base_url;

//$jsonData = file_get_contents($base_url);
?>
<html>
<head>
	<title>Västtrafik test</title>
	<meta charset="utf-8" />
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="linje.js"></script>
	<script type="text/javascript">
	console.info("<?php echo $base_url; ?>");
	//var json = <?php echo $jsonData; ?>;
	var linjer;
	var ids = {
		"chalmers": "9021014001960000",
		"tvargata": "9021014001970000",
		"gbgc": "9021014008000000"
	};
	function fetchData(id) {
		$.getJSON("http://api.vasttrafik.se/bin/rest.exe/departureBoard?jsonpCallback=?", {
			"authKey": "<?php echo $AUTH_KEY;?>",
			"id": (id?id:ids.chalmers),
			"date": "<?php echo date("Y-m-d");?>",
			"format": "json"
		}, function(data) {
			console.log(data);
			$("#stationName").html(data.DepartureBoard.Departure[0].stop);
			Linje.maxLength = 0;
			linjer = new LinjeList();
			$.each(data.DepartureBoard.Departure, function(key, value) {
				linjer.push(value);
				if (linjer.changed) {
					// TODO: If a bus changes, only that bus should be moved/removed
				}
			});

			linjer.list.sort(linjeListSort);
			showInTable(linjer.list);
		});
	}
	function showInTable(linjer) {
		var trs = [];
		$.each(linjer, function(key, entry) {
			var $tr = $("<tr />");
			var $busName = $("<td />", {"class": "bus-title", "html": entry.name}).css({"background-color": entry.color.bg, "color": entry.color.fg});
			var $destName = $("<td />", {"class": "dest-title", "html": entry.direction});
			$tr.append($busName, $destName);
			var timeArray = entry.getTimes();
			for (var i = 0; i < Linje.maxLength; i++) {
				var value = timeArray[i];
				var $time = $("<td />", {"html": (value===undefined? "":value), "class": "bus-time"});
				$tr.append($time);
			}
			console.log($tr[0], entry.times)
			trs.push($tr);
		});
		var $table = $("#entries");
		$table.html("");
		$.each(trs, function() {
			$table.append(this);
		})
	}
	$(document).ready(function() {
		$.each(ids, function(key) {
			$("#stations").append($("<option />", {"value": this, "html": key}));
		})
		fetchData(ids.chalmers);
		//loopFetchData();
		$("#entries").on("mouseover", "tr", function(elem){

		});
		$("#stations").on("change", function() {
			fetchData(this.value);
		});
	});
	function loopFetchData() {
		fetchData();
		//setTimeout(loopFetchData, 1000);
	}
	</script>
	<style type="text/css">
	body {
		font-family: sans-serif;
	}
	#entries tr td:first-child {
		text-align: center;
	}
	#entries tr {
		-webkit-transition: -webkit-transform 100ms ease-in;
		-moz-transition: -moz-transform 100ms ease-in;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #fff;
	}
	#entries {
		border-collapse: collapse;
	}
	/*#entries tr:hover {
		-webkit-transform: rotateX(90deg);
		-moz-transform: rotateX(90deg);
	}*/
	#entries td {
		padding: 5px;
	}
	#entries tr:nth-child(even) {
		background-color: #b2acab;
	}
	#entries tr:nth-child(odd) {
		background-color: #c8c8c8;
	}
	.bus-title {
		font-size: 1.5em;
		text-align: center;
		padding: 0 5px;
	}
	.bus-time {
		text-align: right;
		width: 70px;
	}
	.removed {
		-webkit-transform: rotateX(90deg);
		-moz-transform: rotateX(90deg);
	}

	</style>
</head>
<body>
	<select id="stations"></select>
	<div>
		<h2 id="stationName"></h2>
		<table id="entries"></table>
	</div>
</body>
</html>