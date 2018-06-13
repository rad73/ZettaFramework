$(function () {
	
	{	// переключаем тип меню 

		$('input[name=type]').click(function () {
			
			if ('router' == $(this).val() && $(this).is(':checked')) {
				$('#fieldset-by_router').show();
			}
			else {
				$('#fieldset-by_router').hide();
			}
			
		});
		
		
		$('input[name=type]:checked').click();

	}
	
	{	// переключаем тип раздела 

		$('input[name=type_section]').click(function () {
			
			if ('router' == $(this).val() && $(this).is(':checked')) {
				$('#fieldset-by_router').show();
				$('#fieldset-by_external').hide();
			}
			else {
				$('#fieldset-by_router').hide();
				$('#fieldset-by_external').show();
			}
			
		});
		
		
		$('input[name=type_section]:checked').click();

	}


	_zettaUISort($('.sortable'), function (data) {
		
		showPreloader();
			
		$.post(_urlSaveTree, {format: 'json', tree: data, csrf_hash: _csrf_hash}, function () {
			hidePreloader();
		});
		
	}, true);
	
	$('.z_ico_delete').click(function () {
		
		var _this = this;
		
		_confirm('Удалить выбранный элемент?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	})
	
	
});
