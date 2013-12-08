class Linje
	constructor: (@dep) ->
		@id = @dep.name + "|" + dep.direction
		if (@dep.name.match /\w+ \d+/)
			@name = dep.name.split(" ")[1]
		else
			@name = dep.name.substr 0, 3
		@name = @name.trim()
		@origName = dep.name
		@color = 'fg': dep.bgColor, 'bg': dep.fgColor
		@direction = dep.direction
		@times = []
		@addTime @dep
	toDate: (date, time) ->
		dateParts = date.split '-'
		timeParts = time.split ':'
		# new Date Date.UTC(parseInt(dateParts[0], 10), parseInt(dateParts[1]))

	addTime: (dep) ->
		date = toDate(dep.rtDate, dep.rtTime) if dep.rtTime? && dep.rtDate?
	getTimes: ->
		list = []
	getTimeLeft: (date) ->

	getTimeString: (time) ->
		timeLeft = moment(time).diff moment(), 'minutes'

		return 'Nu' if timeLeft < 1
		return time.toLocaleTimeString().substr 0, 5 if timeLeft > 10
		return timeLeft + 'm'


class LinjeList
	constructor: ->
		@list = []
	push: (dep) ->

	
