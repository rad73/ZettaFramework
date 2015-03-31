$(function () {

	$('#z_settings_list .z_ico_delete').click(function () {
		
		var _this = this;
		
		_confirm('Удалить выбранный элемент?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	});
	
});