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
	var oldLinjer = new LinjeList();
	var linjer;
	var currentValue;
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
			console.log("Reloaded");
			Linje.now = new Date();
			var dirtyRows = [];
			$("#stationName").html(data.DepartureBoard.Departure[0].stop);
			Linje.maxLength = 0;
			linjer = new LinjeList();
			$.each(data.DepartureBoard.Departure, function(key, value) {
				linjer.push(value);
			});
			linjer.list.sort(linjeListSort);
			listOrder(linjer.list, oldLinjer.list);
			oldLinjer = linjer;
			showInTable(linjer.list);
		});
	}
	function listOrder(list, ref) {
		for (var i = 0, len = list.length; i < len; i++) {
			list[i].dirty = (ref[i] === undefined || list[i].id != ref[i].id);
		}
	}

	function showInTable(linjer) {
		var trs = [];
		$.each(linjer, function(key, entry) {
			var $tr = $("<tr />");
			if (entry.dirty) {
				$tr.addClass("removed");
				entry.dirty = false;
			}
			var $busName = $("<td />", {"class": "bus-title", "html": entry.name}).css({"background-color": entry.color.bg, "color": entry.color.fg});
			var $destName = $("<td />", {"class": "dest-title", "html": entry.direction});
			$tr.append($busName, $destName);
			var timeArray = entry.getTimes();
			var min = Math.min(Linje.maxLength, 2);
			for (var i = 0; i < min; i++) {
				var value = timeArray[i];
				var $time = $("<td />", {"html": (value===undefined? "":value), "class": "bus-time"});
				$tr.append($time);
			}
			trs.push($tr);
		});
		var $table = $("#entries");
		$table.html("");
		$.each(trs, function() {
			$table.append(this);
		});
		setTimeout(function() {
			$(".removed").removeClass("removed");
		}, 100);
	}
	$(document).ready(function() {
		$.each(ids, function(key) {
			$("#stations").append($("<option />", {"value": this, "html": key}));
		})
		loopFetchData();
		$("#entries").on("mouseover", "tr", function(elem){

		});
		$("#stations").on("change", function() {
			fetchData(this.value);
			currentValue = this.value;
		});
	});
	function loopFetchData() {
		fetchData(currentValue);
		setTimeout(loopFetchData, 60000);
	}
	</script>
	<style type="text/css">
	body {
		font-family: sans-serif;
	}
	#entries tr {
		-webkit-transition: -webkit-transform 200ms ease-in;
		-moz-transition: -moz-transform 200ms ease-in;
		-o-transition: -o-transform 200ms ease-in;
		-ms-transition: -ms-transform 200ms ease-in;
		transition: transform 200ms ease-in;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #fff;
	}
	#entries {
		border-collapse: collapse;
	}
	#entries td {
		padding: 5px;
	}
	#entries tr:nth-child(even) {
		background-color: #dedede;
	}
	#entries tr:nth-child(odd) {
		background-color: #c8c8c8;
	}
	#entries td.dest-title {
		padding-left: 10px;
		min-width: 240px;
		border-right: 1px solid #fff;
	}
	.bus-title {
		font-size: 1.5em;
		text-align: center;
		padding: 0 5px;
	}
	.bus-time {
		text-align: right;
	}
	.removed {
		-webkit-transform: rotateX(90deg);
		-moz-transform: rotateX(90deg);
		-o-transform: rotateX(90deg);
		-ms-transform: rotateX(90deg);
		transform: rotateX(90deg);
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