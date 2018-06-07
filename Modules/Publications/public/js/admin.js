$(function () {

	// удаляем тип публикации
	$('.z_publications_list .z_ico_delete').click(function () {

		var _this = this;
		_confirm('Удалить выбранный элемент?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});

		return false;

	});

	{	// сортируем поля

		_zettaUISort($('#z_fields_list'), function (data) {

			showPreloader();

			$.post(_urlPublications.sortFields, {data: data, format: 'json', csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});

		});

	}

	// удаляем поле
	$('.deleteField').click(function () {

		var _this = this;

		_confirm('Удалить выбранный элемент?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});

		return false;

	});

	// поле "Список" показываем прячем
	$('select[name=type]').change(function () {

		if ($(this).val() == 'select' || $(this).val() == 'radio' || $(this).val() == 'multiCheckbox') {
			$('#list_values').parents('.form_row').show();
		}
		else {
			$('#list_values').parents('.form_row').hide();
		}

	});
	$('select[name=type]').change();


	{	// сортируем публикации

		_zettaUISort($('#z_view_publications'), function (data) {

			showPreloader();

			$.post(_urlPublications.sortPublications, {data: data, page: _currentPage, format: 'json', csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});

		}, false, true);

	}

	{

		$('.z_folder_browse').remove();

		$('input[z_image_dialog=1]').each(function () {

			var _isImage = $(this).val().match(/\.(jpg|jpeg|png|gif)$/);

			$(this)
				.after('<span class="z_folder_browse"><!-- --></span><div class="z_temp_image">'
					+ (
						$(this).val()
							? _isImage ? '<img src="' + $(this).val() + '"/>' : '<a href="' + $(this).val() + '">' + $(this).val() + '</a>'
							: ''
					)
					+ '</div>')
				.parents('.form_row').addClass('z_image_browse_row');

			$(this).focus(function () {
				$(this).click();
			});

		});

		var _dialogOpen = false;

		$('input[z_image_dialog=1], .z_folder_browse').click(function () {

			if (_dialogOpen) return false; _dialogOpen = true;

			var _parent = $(this).parents('.form_row:first')
				_input = $('input', _parent),
				_object = $('.z_temp_image', _parent);

			_redactor.image(_object, function (data, object) {

				var _matches = data.match(/(src|href)="(.+?)"/);

				_matches
					? _input.val(_matches[2])
					: _input.val('');

				_object.html(data);

				_dialogOpen = false;

			}, true);

			return false;

		});

	}

	{	// datetime && date input

		$('input[z_date_time=1]').each(function () {

			var _val = $(this).val(),
				_timeValue = '',
				_dateValue = '';

			if (_val) {
				_timeValue = _val.split(' ')[1];
				_dateValue = _val.split(' ')[0];
			}

			$(this).after('<input type="text" class="z_timepicker" name="' + $(this).attr('name') + '_time" value="' + _timeValue + '"/>');
			$(this).val(_dateValue);

		});
		$('.z_timepicker').timepicker();


		$('input[z_date=1], input[z_date_time=1]').datepicker({
			changeMonth: true,
			changeYear: true,
			showOn: "both",
			buttonImage: _icoCalendar,
			buttonImageOnly: true,
			firstDay: 1,
			dateFormat: "dd.mm.yy"
		});

		$('input[z_date=1], input[z_date_time=1]').each(function () {

			var _val = $(this).val(),
				_dateValue = [];

			if (_val) {
				_dateValue = _val.split('-');
				$(this).val(_dateValue[2] + '.' + _dateValue[1] + '.' + _dateValue[0]);
			}

		});

	}

    {	// mapPoint input

		$('input[z_map_point=1]').each(function () {

            var _mapPlaceHolder = $('<div class="map_point"/>'),
                _input = $(this),
                _coord = _input.val() || '53.8750,27.4110';

            _input.after(_mapPlaceHolder);
            _input.attr('readonly', 'readonly');

            var _saveCoords = function(coords) {
                _input.val([coords[0].toPrecision(6), coords[1].toPrecision(6)]);
            }

            var startMapPoint = function () {
                ymaps.ready(function () {
                    myMap = new ymaps.Map(_mapPlaceHolder.get(0), {
                        center: _coord.split(','),
                        zoom: 10,
                        behaviors: ['scrollZoom', 'drag']
                    });

                    var placemark = new ymaps.Placemark(myMap.getCenter(), {}, {draggable: true});
                    myMap.geoObjects.add(placemark);
                    _saveCoords(myMap.getCenter());

                    placemark.events.add("dragend", function (e) {
                        _saveCoords(this.geometry.getCoordinates());
                    }, placemark);

                });
            }

            if (typeof(ymaps) == 'undefined') {
                $.getScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU', function(data, textStatus, jqxhr) {
                    startMapPoint();
                });
            }
            else {
                startMapPoint();
            }

		});

	}

	{	// сортировка пейджингом

		_zettaUIDropPage($('.pagination_control'), function (data, item_droped) {

			$.post(_urlPublications.sortPublications, {data: data, format: 'json', csrf_hash: _csrf_hash}, function () {
				$(item_droped).click();
			});

    	});

	}

})
