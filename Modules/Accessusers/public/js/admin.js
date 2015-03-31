$(function () {

	$('.z_ico_delete').click(function () {
		
		var _this = this;
		
		_confirm('Удалить пользователя?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	});
	
});