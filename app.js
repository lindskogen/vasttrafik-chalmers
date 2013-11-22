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
			var $time = $("<td />", {"html": (value===undefined? "":value), "class": "bus-time" + (value=='Nu'?' bus-now':'')});
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
	});
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