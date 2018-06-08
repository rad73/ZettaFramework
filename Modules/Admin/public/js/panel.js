$(function () {

	$('body').addClass('zetta_front');

	if ($.fn.button.noConflict) {
		$.fn.btn = $.fn.button.noConflict();
	}

	$(document).ajaxError(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {

		if (thrownError && thrownError != 'abort') {
			_alert('Ошибка: ' + thrownError, xhr.responseText, null, null, {width: 700});
		}
	});

	var _showManageWindow = function () {

		var _panel = $('.z_window_wrapper')
            _panel_manage = $('#z_window');

        _panel.addClass('z_visible');
		_panel_manage.trigger('z_window_show');

        $('body').addClass('z_window_show');

	}
	var _hideManageWindow = function () {

		$('.z_window_wrapper').removeClass('z_visible');
        $('body').removeClass('z_window_show');
		$.History.go('');

	}

	var _afterLoadWindow = function () {

		//$('body, html').animate({
		//	scrollTop: 0
		//}, 100);
		//},

		hidePreloader();
		_showManageWindow();

		$('#z_window .zetta_placeholder A:not(.no_ajax)').unbind('click').click(function () {
			$.History.go($(this).attr('href'));
			return false;
		});

		$('#z_window .zetta_placeholder form:not(.no_ajax)').unbind('submit').submit(function () {

			showPreloader();

			$('*[disabled=disabled]', this).removeAttr('disabled');
			$('*[type=submit]', this).attr('disabled', 'disabled');
			// $('input[type=file]:enabled', this).filter(function() { return $(this).val() == ''; }).attr('disabled', 'disabled');

			var _url = ($(this).attr('action') || $.History.getState()) + '?format=html&currentUrl=' + encodeURIComponent(_currentUrl),
				_method = $(this).attr('method') || 'get';

			$(this).ajaxSubmit({
				cache: false,
				type: _method,
				url: _url,
				success: function(data) {
					$('#z_window .zetta_placeholder').html(data);
					_afterLoadWindow();
				}
			});

			return false;

		});

		$('#z_window *[type=submit]').addClass('ui-button');
		$('#z_window .ui-button').length
			? $('#z_window .ui-button').button()
			: {};


		$('.z_to_favorite')
			.unbind('click')
			.click(function () {

				$.post($(this).attr('href'), {format: 'html', csrf_hash: _csrf_hash}, function (data) {
					$('#z_favorites_placeholder').html(data);
					_reinitFavorites();
				});

				return false;

			});


		$('textarea[data-type=html]').each(function () {

			$(this).removeAttr('data-type');

			_redactor.destroy(this);

			_redactor.html(this, false, {
				toolbarExternal: false,
				focus: false,
				no_dispatch: true
			});

		})

	}

	$('#z_close').click(function () {
		_hideManageWindow();
		document.location.reload();
		return false;
	});

	$('#z_manage_link, #z_panel_placeholder A:not(.no_ajax)').click(function () {
		$.History.go($(this).attr('href'));
		return false;
	});

	$.History.bind(function(url){

		if (!url || url.indexOf('/mvc') == -1) return;

		showPreloader();

		$.get(url, {format: 'html', currentUrl: _currentUrl}, function (data) {
			$('#z_window .zetta_placeholder').html(data);
			_afterLoadWindow();
		});

    });

    _reinitFavorites();


    $('.z_icons_editable A:not(.no_ajax), .z_admin_link')
    	.unbind('click')
    	.click(function () {
    		$.History.go($(this).attr('href'));
    		return false;
    	});


    $('.z_icons_editable .icon-remove')
    	.unbind('click')
    	.click(function () {

    		var _this = this;

    		if (confirm('Удалить?')) {
    			$.get($(this).attr('href'), {format: 'json'}, function (data) {
    				$(_this).parents('.z_admin_wrapper:first').remove();
    			});
    		}

    		return false;

    	});


});

var showPreloader = function () {
	$('#z_ajax_loader').show();
}

var hidePreloader = function () {
	$('#z_ajax_loader').hide();
}

var _reinitFavorites = function () {

	$('#z_favorites_placeholder A:not(.no_ajax)')
		.unbind('click')
		.click(function () {
			$.History.go($(this).attr('href'));
			return false;
		});

	$('#z_favorites_placeholder .z_remove_favorite')
		.unbind('click')
		.click(function () {
			$.post($(this).attr('href'), {format: 'html', csrf_hash: _csrf_hash}, function (data) {
				$('#z_favorites_placeholder').html(data);
				_reinitFavorites();
			});
			return false;
		});

}

/**
 * Показываем confirm диалоговое окно
 *
 * @param title string		Заголовок окна
 * @param message string	Содержимое окошка
 * @param callback string	callback функция при нажатии на Ok
 * @param btnOkTitle string			Надпись на кнопке "Ok"
 * @param btnCancelTitle string		Надпись на кнопке "Cancel"
 */
var _confirm = function(title, message, callback, btnOkTitle, btnCancelTitle, callbackCancel) {

	btnOkTitle = btnOkTitle || 'Ok';
	btnCancelTitle = btnCancelTitle || 'Отмена';

	message = message || 'Внимание это действие не обратимо. Продолжить?';

	$('#zetta_dialog_msg').html(message);

	$('#zetta_confirm')
		.removeClass('hidden')
		.dialog({
			resizable: false,
			modal: true,
			title: title,
			buttons: [
				{
					text: btnOkTitle,
					click: function() {
						$(this).dialog('close');
						callback();
					}

				},
				{
					text: btnCancelTitle,
					click: function() {
						$(this).dialog('close');
						if (typeof(callbackCancel) == 'function') callbackCancel();
					}

				}
			]
	    });

}


/**
 * Показываем alert диалоговое окно
 *
 * @param title string		Заголовок окна
 * @param message string	Содержимое окошка
 * @param callback string	callback функция при нажатии на Ok
 * @param btnOkTitle string			Надпись на кнопке "Ok"
 * @param btnCancelTitle string		Надпись на кнопке "Cancel"
 */
var _alert = function(title, message, callback, btnOkTitle, options) {

	btnOkTitle = btnOkTitle || 'Ok';

	$('#zetta_dialog_alert_msg').html(message);

	$('#zetta_alert')
		.removeClass('hidden')
		.dialog($.fn.extend({}, {
			resizable: false,
			modal: true,
			title: title,
			buttons: [
				{
					text: btnOkTitle,
					click: function() {
						$(this).dialog('close');
						callback();
					}

				}
			]
	    }, options));

}
