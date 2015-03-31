/** 
 * Переменная-флаг означающий, что сортировку переместили 
 * на jquery UIdroppable
 */
var _dropPage = false;

/**
 * Объект который подлежит сортировке
 */
var _sortObject = null;

/**
 * Объекты могут сортироваться
 *
 * @param object object
 * @param complete callback
 * @html
		<ul> 
			<li data-id="1">
					<div class="move">drag_me</div>
					Sample Text
				</div>
			</li>
		</ul>
 */
var _zettaUISort = function(object, complete, nested, quickDump) {
	
	var _fn = nested ? 'nestedSortable' : 'sortable';
	
	_sortObject = $(object);
	_sortObject[_fn]({
		forcePlaceholderSize: true,
		handle: '.move',
		helper:	'clone',
		listType: 'UL',
		items: "li:not(.not_moved)",
		opacity: .8,
		placeholder: 'sortable_placeholder',
		revert: 250,
		tabSize: 25,
		stop: function (e, ui) {

			_sortObject = null;

			if (typeof(complete) == 'function' && false == _dropPage) {
				if (quickDump) {
					complete(_dumpFieldsQuick(ui.item));
				}
				else {
					complete(_dumpFields());
				}
			}

		}
	});
	
	var _dumpFields = function () {
	
		var _dump = [];
		
		$('LI', object).each(function (i) {
			
			var _id = $(this).data('id'),
				_parent_id = $(this).parents('li:first').data('id');
			
			_dump.push({id: _id, parent_id: _parent_id, sort: (i+1)});
			
		});
		
		return _dump;
		
	}
	
	var _dumpFieldsQuick = function (element) {
	
		var _dump = {
			prev: $(element).prev('LI').data('id'),
			current: $(element).data('id'),
			next: $(element).next('LI').data('id')
		};
		
		return _dump;
		
	}
	
}

/**
 * Сортировка пейджингом
 *
 */
var _zettaUIDropPage = function (object, complete) {
	
	$('A', object).droppable({
        tolerance: 'pointer',
        hoverClass: 'z_drop_pagination',
        drop:function(event, ui) {

        	var _this_drop = this,
        		_currentPage = $('.pagination_current', object).text();
        		
        	_sortObject.on( "sortstop", function( event, ui ) {
        		
	        	$(ui.item).hide();

	        	$(_this_drop).effect('pulsate', false, 300);
	        	
	       		showPreloader();

	        	$.get($(_this_drop).attr('href'), {format: 'html'}, function (data) {

	        		var _newPage = $('.pagination_current', data).text(),
	        			_data = {
		        			prev: _newPage >= _currentPage ? $('li:first', data).data('id') : null,
							current: $(ui.item).data('id'),
							next: _newPage < _currentPage ? $('li:first', data).data('id'): null,
		        		};
		        		
	        		if (typeof(complete) == 'function') {
						complete(_data, _this_drop);
					}
					
					_dropPage = false;
				
	        	});
        		
	        	return false;
	        	
        	});
        	
        },
        over: function () {
        	_dropPage = true;
        	_sortObject.sortable( "option", "revert", false);
        	$('#z_window .ui-sortable-helper').addClass('ui-sortable-paging');
        	$('#z_window .sortable_placeholder').addClass('z_hide');
        },
        out: function () {
        	_dropPage = false;
        	_sortObject.sortable( "option", "revert", true);
        	$('#z_window .ui-sortable-helper').removeClass('ui-sortable-paging');
        	$('#z_window .sortable_placeholder').removeClass('z_hide');
        }

    });

}
	
