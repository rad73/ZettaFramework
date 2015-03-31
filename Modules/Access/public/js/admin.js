$(function () {
	
	$('.z_ico_delete').click(function () {
		
		var _this = this;
		
		_confirm('Удалить выбранный элемент?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});
			
		return false;

	});
	
	/* при каждом клике на радио перевыставляем права */
	$('.z_rb input').click(function () {
		
		var _parent = $(this).parents('LI:first'),
			_childrens = $('LI', _parent);
			
		_parent
			.removeClass('z_allow')
			.removeClass('z_deny');

		if (false == $(this).is(':checked')) return;
		
		if ($(this).val() == 'deny') {
			
			_childrens
				.removeClass('z_allow')
				.addClass('z_deny');
				
			_parent
				.removeClass('z_allow')
				.addClass('z_deny');

			$('input', _childrens)
				.attr('disabled', 'disabled')
				.removeAttr('checked');

			$('input[value=inherit]', _childrens)
				.removeAttr('disabled')
				.prop("checked", true);

		}
		
		if ($(this).val() == 'allow') {
			
			_childrens
				.removeClass('z_deny')
				.addClass('z_allow');
				
			_parent
				.removeClass('z_deny')
				.addClass('z_allow');

			$('input', _childrens)
				.removeAttr('disabled')
				.removeAttr('checked');

			$('input[value=inherit]', _childrens)
				.prop("checked", true);
			
		}
		
		if ($(this).val() == 'inherit') {
			
			// получаем доступ у родителя
			var _praParent = _parent.parents('LI:first');
			
			if (_praParent.length) {
				
			}
			else {	// мы находимся в корне поэтому доступ есть у всех
				
			}
			
			
		}
			
		
	});
	
	
	// переключалка типов привелегии
	$('input[name=type]').click(function () {
		
		$('#fieldset-base .form_row').hide();
		
		if ($(this).val() == 'free') {
			$('#resource_name').parents('.form_row:first').show();
			$('#description').parents('.form_row:first').show();
		}
		else {
			$('#route_id').parents('.form_row:first').show();
		}
		
	});
	$('input[name=type]:checked').click();
	
	/* сортировка ролей */
	{
		_zettaUISort($('.sortable'), function (data) {
			
			showPreloader();

			$.post(_url.sortRoles, {tree: data, format: 'json', csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});
			
		},  true);
		
	}
	
})