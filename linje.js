var now = new Date();
function Linje(dep) {
	this.dep = dep;
	if (dep.name.indexOf(" ") !== -1)
		this.name = dep.name.split(" ")[1];
	else
		this.name = dep.name.substr(0,3);
	this.origName = dep.name;
	this.color = {"fg" : dep.bgColor, "bg": dep.fgColor};
	this.direction = dep.direction;
	this.times = [];
	this.addTime(dep);
}
function toDate(date, time) {
	console.log(date, time);
	var dateParts = date.split("-");
	var timeParts = time.split(":");
	return new Date(Date.UTC(parseInt(dateParts[0],10), parseInt(dateParts[1],10) -1, parseInt(dateParts[2],10), parseInt(timeParts[0],10) -1, parseInt(timeParts[1],10)));
}
Linje.prototype.addTime = function(dep) {
	if (dep.rtTime && dep.rtDate) {
		console.log(dep.rtDate, dep.rtTime, toDate(dep.rtDate, dep.rtTime));
		this.times.push(toDate(dep.rtDate, dep.rtTime));
	} else {
		this.times.push(toDate(dep.date, dep.time));
	}
	if (this.times.length > 1) {
		this.times = this.times.sort();
	}
	if (!Linje.maxLength || this.times.length > Linje.maxLength)
		Linje.maxLength = this.times.length;
}
Linje.prototype.getTimes = function() {
	var list = [];
	for (var key in this.times) {
		var value = this.times[key];
		list.push(this.getTimeString(value));
	}
	return list;
}
Linje.prototype.getTimeLeft = function(date) {
	if (date === undefined)
		date = this.times[0];
	var timeLeft = (date - now)/1000;
	return parseInt(timeLeft/60);
};
Linje.prototype.getTimeString = function(time) {
	time = this.getTimeLeft(time);
	return (time < 1 ? "Nu":time);
}

function LinjeList() {
	this.list = [];
}
LinjeList.prototype.push = function(dep) {
	var wasSet = false;
	for (var i = 0; i < this.list.length; i++) {
		var entry = this.list[i];
		if (entry.origName === dep.name && entry.direction === dep.direction) {
			entry.addTime(dep);
			wasSet = true;
		}
	}
	if (!wasSet) {
		this.list.push(new Linje(dep));
	}
};
function linjeListSort(l1, l2) {
	return l1.times[0] - l2.times[0];
}
