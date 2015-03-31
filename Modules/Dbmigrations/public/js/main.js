$(function () {

	$('#show_graph').click(function () {
		$('#graph').show();
		return false;
	});

	$('#current_to_master').click(function () {
		if (!confirm($(this).attr('rel'))) {
			return false;
		}
	});

	$.getJSON(_migrationsDataJson ,{format:'json'}, function (data) {

		var g = new BranchMigrations('graph');
		g.setData(data.Dbmigrations);
		
		g.drawCurrentBranch({
			line: {stroke:'#47b7e0', 'stroke-width': 6},
			bubble: {
				cross : {fill: '#88e047', stroke: '#FFF', 'stroke-width': 2, radius: 3},
				normal: {fill: '#47b7e0', stroke: '#FFF', 'stroke-width': 2, radius: 3}
			}
		});

		g.drawMasterBranch({
			line: {stroke:'#aeaeae', 'stroke-width': 6},
			bubble: {
				cross : {fill: '#88e047', stroke: '#FFF', 'stroke-width': 2, radius: 3},
				normal: {fill: '#aeaeae', stroke: '#FFF', 'stroke-width': 2, radius: 3}
			}
		});
	
		g.setEvents();
		
	});
	

});