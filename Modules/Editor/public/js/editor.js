/**
 * WYSIWYG редактор, прослойка между редактором и кодом
 *
 * _redactor.html(object, callback);	- редактируем область текста
 * _redactor.image(object, callback);	- редактируем картинку
 * _redactor.destroy();					- уничтажаем редактор
 *
 */
var _redactor = {

	_object: null,
	_objectList: [],
	_callbackComplete: null,
	_init_options: null,

	/**
	 * Редактор текста
	 *
	 * @param object object							Объект редактирования
	 * @param function callback(html, object)		Функция при завершении редактирования
	 */
	html: function (object, callback, options) {

		if (this._objectList[object]) return false;

		this._init_options = options;

		this._dispatch({target: object});

		this._object = $(object);
		this._objectList[this._object.get(0)] = 1;

		this._callbackComplete = callback;

		this._create(options);

	},

	/**
	 * Редактор картинки
	 *
	 * @param object object							Объект редактирования
	 * @param function callback(html, object)		Функция при завершении редактирования
	 */
	image: function (object, callback, disableEdit) {

		if (this._objectList[object]) return false;

		this._dispatch({target: object});

		this._object = $(object);
		this._objectList[this._object.get(0)] = 1;

		this._callbackComplete = function (html, _object) {
			
			// чистим блок от посторонних тегов
			html = html.replace(/(.*(<a.*?><img.*><\/a>).*)/gi, '$2');	//  разрешаем теги A > IMG

			if (!html.match(/^<a.*?><img.*><\/a>$/)) {
				html = html.replace(/(.*(<img.*?>).*)/gi, '$2');		// разрешаем тег IMG
			}

			_object.html(html);
			callback(html, _object);

		}

		this._create({
			toolbarExternal: 'null', 
			saveWhenFileSelected: true, 
			replaceFull: true,
			paragraphize: false,
			plugins: ['filemanager'],
			callbacks: {
			   change: function() {
				   	_redactor.destroy();
			   }
		   }  
		});

		this._object
			.unbind('paste')
			.bind('paste', function () { return false; });

		if (this._object.html().indexOf('img') == -1 || disableEdit) {
			($.proxy(this._object.redactor('core.object').filemanager.makeFilemanager, this._object.redactor('core.object')))();
		}
		else {
            this._object.redactor('core.object').image.showEdit($('img', this._object))
		}

	},

	/**
	 * Уничтажаем созданный редактор
	 *
	 * @param object object							Объект редактирования
	 * @param function callback(html, object)		Функция при завершении редактирования
	 */
	destroy: function (object) {

		if (object) {
			this._objectList[object] = false;
		}
		else if (this._object) {
			
			var _codeMirror = this._object.redactor('core.object').codemirror;
			if (_codeMirror.$textarea && _codeMirror.$textarea.hasClass('open')) {
				_codeMirror.toggle();
			}
			this._object.redactor('core.editor').show();
			this._object.redactor('core.destroy');

			this._save();

			this._objectList[this._object] = false;
			this._object = null;

			this._view.onDestroy();
		}

	},

	_dispatch: function (e) {

		if (!this._object) return;
		if (this._init_options && this._init_options['no_dispatch']) return;

		var _destroy = true,
			_this = this;
			_parents = $(e.target).parents();

		_parents.push(e.target);
		
		_parents.each(function () {

			if ($(this).get(0) == _this._object.get(0)) {
				_destroy = false;
			}

			var _class = this.className,
				_id = this.id;

			if (
				(_class && _class.indexOf('redactor') >= 0)
				|| (_class && _class.indexOf('CodeMirror') >= 0)
				|| (_id && _id.indexOf('redactor') >= 0)
				|| $(this).is('.ui-dialog')
			) {
				_destroy = false;
			}

		});

		if (_destroy == true) {
			this.destroy();
		}

	},

	_view: {
		onCreate: function () {
			
			$('.redactor-box').parent().addClass('in_edit');

			$('#zetta_editor_toolbar')
				.show()
				.draggable();

		},
		onDestroy: function () {

			$('#zetta_editor_toolbar').empty();
			$('.in_edit').removeClass('in_edit');

			$('#zetta_editor_toolbar').hide();
			$('.zetta_edit_toolbar_fixed').removeClass('zetta_edit_toolbar_fixed');

		},
		_bodyClick: function (e) {

			if ($('.zetta_edit_toolbar_fixed').length) return false;

			var _width = $('#zetta_editor_toolbar').width(),
				_height = $('#zetta_editor_toolbar').height(),
				_maxWidth = $('html').width(),

				_left = (_left = $(_redactor._object).offset().left) <= 0 ? 10 : _left,
				_left = _left + _width >= _maxWidth ? _maxWidth - _width - 20 : _left,

				_top = (_top = e.pageY - _height) < 80 ? e.pageY + 100 : _top;

			$('#zetta_editor_toolbar').stop(false, false).animate({
				top: _top,
				left: _left
			});

		}
	},

	_create: function (options) {

		var _this = this;

		var _options = $.extend({}, {
			lang: 'ru',
			toolbarExternal: '#zetta_editor_toolbar',
			toolbarFixed: false,
			imageUpload: _baseUrl + '/mvc/editor/index/imageupload/?csrf_hash=' + _csrf_hash,
			fileUpload: _baseUrl + '/mvc/editor/index/fileupload/?csrf_hash=' + _csrf_hash,
			imageGetJson: _baseUrl + '/mvc/editor/index/images/',
			emptyHtml: '',
            plugins: ['codemirror', 'clearformatting', 'undoredo', 'alignment', 'filemanager', 'video', 'table', 'fontfamily', 'fontsize', 'fontcolor', 'pin'],
			deniedTags: ['html', 'head', 'link', 'body', 'meta', 'style', 'applet'],
			paragraphize: true,
			cleanSpaces: false,
			imageEditable: true,
			imagePosition: true,
            imageResizable: true,
			codemirror: {
                lineNumbers: true,
                mode: 'htmlmixed',
                indentUnit: 4,
				theme: 'material'
            },
			callbacks: {
				init: function () {
					
					this.observe.images();
					this.observe.links();
					
					var button = this.button.addFirst ('save', this.lang.get('save'));

	                this.button.addCallback(button, function () {
			    		_this._object.blur();
				    	_this.destroy()
					});
					
				},
				click: function (e) {
					_this._view._bodyClick(e);
				},
				fileSelected: function (e) {
					
					if (options && typeof options.saveWhenFileSelected  != 'undefined' && options.saveWhenFileSelected == true) {
						_redactor._dispatch(e);
					}

				}
			}

		}, options);

		this._object.redactor(_options);

		this._view.onCreate();

	},

	_save: function () {
		if (typeof(this._callbackComplete) == 'function') {
			this._callbackComplete(this._object.html(), this._object);
		}

	}

}

$('body').on('click', function (e) {
	_redactor._dispatch(e);
});

$('body').unload(function () {
	_redactor.destroy();
});
