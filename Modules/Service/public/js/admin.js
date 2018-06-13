$(function () {

	$('#z_disable_updates').click(function () {
		
		var _this = this;
		
		_confirm('Отключить обновления?', 'Внимание это является потенциальной угрозой взлома вашего сайта?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	});
	
	$('#z_enable_updates').click(function () {

		$.post(this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
			$.History.trigger();
		});

		return false;

	});
	
	$('.z_ico_restore').click(function () {

		var _this = this;
		
		_confirm('Восстановить сайт из выбранного архива?', 'Внимание это действие удалит все изменения на сайте сделанные после даты создания резервной копии!', function () {
			showPreloader();
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});
		});
			
		return false;

	});
	
	$('#z_update').click(function () {

		var _this = this;
		
		_confirm('Обновить сайт?', false, function () {
			showPreloader();
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});
		});
			
		return false;

	});
	
});
