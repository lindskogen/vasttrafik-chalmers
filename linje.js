function Linje(dep) {
	this.dep = dep;
	this.id = dep.name + "|" + dep.direction;
	if (dep.name.match(/w+ \d+/))
		this.name = dep.name.split(" ")[1];
	else
		this.name = dep.name.substr(0,3);
	this.name = this.name.trim();
	this.origName = dep.name;
	this.color = {"fg" : dep.bgColor, "bg": dep.fgColor};
	this.direction = dep.direction;
	this.times = [];
	this.addTime(dep);
}
function toDate(date, time) {
	var dateParts = date.split("-");
	var timeParts = time.split(":");
	return new Date(Date.UTC(parseInt(dateParts[0],10), parseInt(dateParts[1],10) -1, parseInt(dateParts[2],10), parseInt(timeParts[0],10) -1, parseInt(timeParts[1],10)));
}
Linje.prototype.addTime = function(dep) {
	var date;
	if (dep.rtTime && dep.rtDate) {
		date = toDate(dep.rtDate, dep.rtTime);
	} else {
		date = toDate(dep.date, dep.time);
	}
	for (var i = 0; i < this.times.length; i++) {
		if (date.valueOf() == this.times[i].valueOf())
			return false;
	}
	this.times.push(date);
	if (this.times.length > 1) {
		this.times = this.times.sort();
	}
	if (!Linje.maxLength || this.times.length > Linje.maxLength)
		Linje.maxLength = this.times.length;
	return true;
}
Linje.prototype.getTimes = function() {
	var list = [];
	for (var key in this.times) {
		var value = this.times[key];
		if (value < Linje.now) {
			delete this.times[key];
		}
		list.push(this.getTimeString(value));
	}
	return list;
}
Linje.prototype.getTimeLeft = function(date) {
	if (date === undefined)
		date = this.times[0];
	var timeLeft = (date - Linje.now)/1000;
	return parseInt(timeLeft/60);
};
Linje.prototype.getTimeString = function(time) {
	time = this.getTimeLeft(time);
	if (time < 1) {
		return "Nu";
	} else if (time > 60) {
		var hours = parseInt(time/60);
		return hours + "h " + time%60 + "m";
	}
	return time + "m";
}

function LinjeList() {
	this.list = [];
}
LinjeList.prototype.push = function(dep) {
	for (var i = 0; i < this.list.length; i++) {
		var entry = this.list[i];
		if (entry.origName === dep.name && entry.direction === dep.direction) {
			if (entry.addTime(dep))
				entry.dirty = true;
			return entry;
		}
	}
	var lin = new Linje(dep);
	this.list.push(lin);
	return lin;
};
function linjeListSort(l1, l2) {
	return l1.times[0] - l2.times[0];
}
