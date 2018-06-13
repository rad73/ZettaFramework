$(function () {

	$('.sortable>LI>UL').nestedSortable({
		forcePlaceholderSize: true,
		handle: '.move',
		helper:	'clone',
		listType: 'UL',
		items: "li:not(.disabled)",
		opacity: .8,
		placeholder: 'sortable_placeholder',
		revert: 250,
		tabSize: 25,

		isTree: true,
		stop: function () {

			showPreloader();

			var _tree = _dumpTree();

			$.post(_urlSaveTree, {format: 'json', tree: _tree, csrf_hash: _csrf_hash}, function () {
				hidePreloader();
				$.History.trigger();
			});

		}
	});

	$('.sortable .z_ico_delete').click(function () {

		var _this = this;

		_confirm('Удалить выбранный элемент?', 'Внимание это действие не обратимо. Продолжить?', function () {
			$.post(_this.href, {format: 'json', csrf_hash: _csrf_hash}, function () {
				$.History.trigger();
			});
		});

		return false;

	});

	$('#add_route_form input[name=type]').click(function () {

		$('#fieldset-extend_0, #fieldset-extend_1, #fieldset-extend_2').hide();

		switch($(this).val()) {
			case 'default':
					($('#fieldset-extend_0').show() && $('#add_route_form #default_actions').change());
				break;
			case 'free':
					$('#fieldset-extend_1').show();
				break;
			default:
					$('#fieldset-extend_2').show();
				break;
		}

	});

	$('#add_route_form #default_modules').change(function () {

		if ($(this).val()) {

			var _array = $(this).val().split('~');

			$.post(_urlGetActions, {format: 'json', csrf_hash: _csrf_hash, getActions: 1, _module: _array[0], _controller: _array[1]}, function (data) {

				$('#add_route_form #default_actions').empty();

				for(var index in data.actions) {
					$('#add_route_form #default_actions').append('<option value="' + index + '">' + data.actions[index] + '</option>');
				}

				if ($('#action_value').length) {
					$('#add_route_form #default_actions').val($('#action_value').val());
				}

				$('#add_route_form #default_actions').change();

			}, 'json');

		}

	});

	$('#add_route_form #default_actions').change(function () {

		if ($(this).val()) {

			var _array = $(this).val().split('~');

			$('#add_route_form #module').val(_array[0]);
			$('#add_route_form #controller').val(_array[1]);
			$('#add_route_form #action').val(_array[2]);
			$('#add_route_form #parms').val('');


		}

	});

	$('#add_route_form input[name=type]:checked').length
		? $('#add_route_form input[name=type]:checked').click()
		: $('#add_route_form input[name=type]:first').click();

	if ($('#type-default').is(':checked')) {
		$('#add_route_form #default_modules').change();
	}

});

var _dumpTree = function () {

	var _dump = [];

	$('.sortable LI').each(function (i) {

		var _id = $(this).data('id'),
			_parent_id = $(this).parents('li:first').data('id');

		if (_id == 1) return;

		_dump.push({id: _id, parent_id: _parent_id, sort: i});

	});

	return _dump;

}
