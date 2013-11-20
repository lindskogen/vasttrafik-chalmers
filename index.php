<!doctype html>
<html>
<head>
	<title>Västtrafik test</title>
	<meta charset="utf-8" />
	<link href="//fonts.googleapis.com/css?family=Roboto:900,400,300" rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="linje.js"></script>
	<script type="text/javascript" src="moment-with-langs.min.js"></script>
	<script type="text/javascript">
	var oldLinjer = new LinjeList();
	var linjer;
	var currentValue;
	var ids = {
		"chalmers": "9021014001960000",
		"tvargata": "9021014001970000",
		"gbgc": "9021014008000000"
	};
	function fetchData(id) {
		$.getJSON("vasttrafik.php?", {
			"id": (id?id:ids.chalmers)
		}, function(data) {
			var depBoard = data.DepartureBoard;
			Linje.now = toDate(depBoard.serverdate, depBoard.servertime);
			$("#stationName").html(depBoard.Departure[0].stop);
			Linje.maxLength = 0;
			linjer = new LinjeList();
			$.each(depBoard.Departure, function(key, value) {
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
	}
	$(document).ready(function() {
		$.each(ids, function(key) {
			$("#stations").append($("<option />", {"value": this, "html": key}));
		})
		loopFetchData();
		$("#stations").on("change", function() {
			currentValue = this.value;
			fetchData(currentValue);
		});
	});
	function loopFetchData() {
		fetchData(currentValue);
		setTimeout(loopFetchData, 60000);
	}
	</script>
	<style type="text/css">
	@-webkit-keyframes rotateIn {
		from {
			-webkit-transform: rotateX(90deg);
		} to {
			-webkit-transform: rotateX(0deg);
		}
	}
	@-moz-keyframes rotateIn {
		from {
			-moz-transform: rotateX(90deg);
		} to {
			-moz-transform: rotateX(0deg);
		}
	}
	@-o-keyframes rotateIn {
		from {
			-o-transform: rotateX(90deg);
		} to {
			-o-transform: rotateX(0deg);
		}
	}
	@keyframes rotateIn {
		from {
			transform: rotateX(90deg);
		} to {
			transform: rotateX(0deg);
		}
	}
	body {
		font-family: 'Roboto', sans-serif;
		font-size: 1.5em;
		margin: 0;
		font-weight: lighter;
	}
	#entries tr {
		-webkit-animation: rotateIn 200ms ease-in;
		-moz-animation: rotateIn 200ms ease-in;
		-o-animation: rotateIn 200ms ease-in;
		animation: rotateIn 200ms ease-in;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #fff;
	}
	#entries {
		width: 700px;
		border-collapse: collapse;
	}
	#entries td {
		padding: 0;
	}
	#entries tr:nth-child(even) {
		background-color: #dedede;
	}
	#entries tr:nth-child(odd) {
		background-color: #c8c8c8;
	}
	#entries td.dest-title {
		font-weight: normal;
		padding-left: 10px;
		min-width: 240px;
		border-right: 1px solid #fff;
	}
	#entries td.bus-title {
		font-weight: bolder;
		font-size: 1.5em;
		text-align: center;
		padding: 5px;
	}
	#entries td.bus-time {
		padding: 5px 10px;
		text-align: right;
		font-size: 1.5em;
	}
	</style>
</head>
<body>
	<!--<select id="stations"></select>-->
	<div>
		<!--<h2 id="stationName"></h2>-->
		<table id="entries"></table>
	</div>
</body>
</html>
