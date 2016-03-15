$(function() {

	$('input[name=z_block_switch]').change(function () {

		if ('on' == $('input[name=z_block_switch]:checked').val()) {
			_initBlocks();
			$.cookie('z_blocks_enabled', 1, {path: _baseUrl + '/'});
		}
		else {
			_destroyBlocks();
			$.removeCookie('z_blocks_enabled', {path: _baseUrl + '/'});
		}

	});

	if ($.cookie('z_blocks_enabled')) {
		$('input[name=z_block_switch]').each(function () {
			if ($(this).val() == 'on') {
				$(this).click().change();
			}
		});
	}
	else {
		$('input[name=z_block_switch]').each(function () {
			if ($(this).val() == 'off') {
				$(this).click().change();
			}
		})
	}


});

var _initBlocks = function () {

	var in_edit = false;

	_destroyBlocks();

	$('#z_blocks_wrapper').removeClass('z_blocks_disabled');

	$('.icon-pencil, .icon-lock, .icon-unlock').click(function () {

		var _parent = $(this).parents('.z_admin_wrapper:first');
			_text = $('.z_redactor', _parent);

		if (_redactor._object && _redactor._object.get(0) == _text.get(0)) {
			in_edit = true;
		}
		else {
			in_edit = false;
		}

	})

	/* Иконка редактирования блока */
	$('.icon-pencil').click(function () {

		if (in_edit) return false;

		var _parent = $(this).parents('.z_admin_wrapper:first');
		_createEditor(_parent, _currentRouteId);

		return false;

	});

	/* иконка блока содержимое которое наследуется */
	$('.icon-lock').click(function () {

		if (in_edit) return false;

		var _parent = $(this).parents('.z_admin_wrapper:first');

		_confirm(
			'Внимание!',
			'Содержимое блока наследуется от главной страницы, заменить текст блока для этой страницы?',
			function () {
				_createEditor(_parent, _currentRouteId);
				_parent
					.addClass('z_override_block')
					.removeClass('z_inherit_block');
			},
			'Заменить',
			'Редактировать',
			function () {
				_createEditor(_parent, 1);
				_parent
					.addClass('z_inherit_block')
					.removeClass('z_override_block');
			}
		);

		return false;
	});

	/* иконка блока содержимое которое не наследуется */
	$('.icon-unlock').click(function (e) {

		if (in_edit) return false;

		var _parent = $(this).parents('.z_admin_wrapper:first');

		if (e.pageX) {		// физически кликнули по кнопке - выставляем контент родителя

			_confirm(
				'Внимание!',
				'Наследовать содержимое с главной страницы?',
				function () {

					$.post(_urlBlockInfo, {format: 'json', block_name: _parent.data('block-name'), csrf_hash: _csrf_hash}, function (data) {

						$('.z_redactor', _parent).html(data.block.content);

						_parent
							.addClass('z_inherit_block')
							.removeClass('z_override_block');

						$.post(_urlBlockDelete, {format: 'json', block_name: _parent.data('block-name'), route_id: _currentRouteId, csrf_hash: _csrf_hash}, function (data) {

						});

					}, 'json');

				}

			);

		}
		else {
			_createEditor(_parent, _currentRouteId);
		}
		return false;
	});


	/* Клакаем по тексту для редактирования*/
	$('.z_redactor').click(function () {

		var _parent = $(this).parents('.z_admin_wrapper:first');
		$('.z_icons_editable VAR:visible', _parent).click();

		return false;

	});


	var _createEditor = function(_parent, _route_id) {

		var _this = $('.z_redactor', _parent),
			_callback = function (html, object) {

				var _block_name = _parent.data('block-name');

				$.post(_urlBlockSave, {format: 'json', block_name: _block_name, content: html, route_id: _route_id, csrf_hash: _csrf_hash}, function () {

				}, 'json');

			}

		_redactor.destroy();

		switch (_parent.data('block-type')) {
			case 'html':
				_redactor.html(_this, _callback);
				_redactor._view._bodyClick({pageX: _parent.offset().left, pageY: _parent.offset().top});
			break;
			case 'text':
				_redactor.html(_this, _callback, {
					toolbarExternal: '-1',
					allowedTags: ['br']
				});
				_redactor._view._bodyClick({pageX: _parent.offset().left, pageY: _parent.offset().top});
			break;
			case 'image':
				_redactor.image(_this, _callback);
				break;
		}

	}

}

var _destroyBlocks = function () {
	$('.z_admin_block .icon-pencil, .z_admin_block .icon-lock, .z_admin_block .icon-unlock, .z_redactor').unbind('click');
	$('#z_blocks_wrapper').addClass('z_blocks_disabled');
}