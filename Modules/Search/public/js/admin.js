$(function () {
	
	var _inProgress = false;
	
	$('#z_reindexate').click(function () {
		
		if (_inProgress) return false;
		
		var _this = this;
		
		_confirm('Переиндексировать сайт?', false, function () {

			_inProgress = true;
			showPreloader();
			
			$.post($(_this).attr('href'), {csrf_hash: _csrf_hash}, function () {
				$.History.go($.History.getState());
				hidePreloader();
				_inProgress = false;
				_alert('Внимание!', 'Индексирование успешно завершено');
			});
				
		});
		
		return false;
		
	});
	
})
