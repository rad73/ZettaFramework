$(function () {

	$('.z_ico_delete').click(function () {
		
		var _this = this;
		
		_confirm('Удалить выбранное задание?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	});
	
	$('.z_ico_pause').click(function () {
		
		var _this = this;
		
		_confirm('Прервать выбранное задание?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	});
	
	$('.z_ico_play').click(function () {
		
		var _this = this;
		
		_confirm('Запустить выбранное задание?', 'Внимание это действие не обратимо. Продолжить?', function () {

			showPreloader();
			$(_this).addClass('hidden');
			$('.z_ico_pause', $(_this).parents('.z_icons')).removeClass('hidden');

			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});

		});
			
		return false;

	});
	
});