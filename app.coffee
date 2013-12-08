@toDate = (date, time) -> new Date Date.parse([date, time, 'GMT+0100'].join ' ')
@depID = (dep) -> [dep.sname, dep.direction.split(' ')[0]].join '|'

class App
	constructor: (@id) ->
		@initLoop()
	initLoop: =>
		@reloadData()
		setTimeout @initLoop, 20000
	reloadData: (id) ->
		id = id || @id
		$.getJSON 'vasttrafik.php?', 'id': id, (data) =>
			@linjer = {}
			Linje.maxLength = 0
			board = data.DepartureBoard
			@now = toDate board.serverdate, board.servertime
			for dep in board.Departure
				id = depID dep
				unless @linjer[id]?
					@linjer[id] = new Linje dep
				else
					@linjer[id].addTime dep.rtDate, dep.rtTime
			@drawTable()
			return
	drawTable: ->
		trs = []
		linjer = $.map @linjer, (v,k) ->
			[v]
		linjer.sort (l1, l2) -> 
			l1.times[0] - l2.times[0]
		for id, lin of @linjer
			tr = $ '<tr />'
			busTitle = $('<td />', { 'class': 'bus-title', 'html': lin.sname })
				.css { 'background-color': lin.color.bg, 'color': lin.color.fg }
			destName = $ '<td />', { 'class': 'dest-title', 'html': lin.direction.split('via')[0].trim() }

			tr.append busTitle, destName

			i = 0
			strings = lin.getTimeStrings()

			while i < Math.min(Linje.maxLength, 2)
				time = strings[i++]
				tr.append $ '<td />', { 'html': time || '', 'class': 'bus-time' + `(time=='Nu'?' bus-now':'')`}
			trs.push tr
		$('#entries').html('')
			.append trs
		return

class Linje
	maxLength: 0
	constructor: (dep) ->
		@times = []
		{@sname, @direction} = dep
		@id = depID dep
		@addTime dep.rtDate, dep.rtTime
		@color = 'fg': dep.bgColor, 'bg': dep.fgColor
	addTime: (date, time) ->
		@times.push toDate date, time
		Linje.maxLength = @times.length if Linje.maxLength < @times.length
		@times.sort() if @times.length > 1

	getTimeStrings: -> @getTimeString t for t in @times
	getTimeString: (time) ->
		timeLeft = moment(time).diff moment(), 'minutes'

		return 'Nu' if timeLeft < 1
		return time.toLocaleTimeString().substr 0, 5 if timeLeft > 10
		return timeLeft + 'm'
	
window.app = new App('9021014001960000')
