{exec} = require 'child_process'

task 'watch', 'watch and compile with source maps', ->
	exec 'coffee -o js -mwc app.coffee', (err, stdout, stderr) ->
		throw err if err
		console.log stdout + stderr

task 'build', 'compile to production', ->
	exec 'coffee -o js -c app.coffee', (err, stdout, stderr) ->
		throw err if err
		console.log stdout + stderr
