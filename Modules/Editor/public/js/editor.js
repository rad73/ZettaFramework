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
	image: function (object, callback) {

		if (this._objectList[object]) return false;

		this._dispatch({target: object});

		this._object = $(object);
		this._objectList[this._object.get(0)] = 1;

		this._callbackComplete = function (html, _object) {

			// чистим блок от посторонних тегов
			html = html.replace(/([\s\S]*(<a.*?><img.*><\/a>)[\s\S]*)/gi, '$2');	//  разрешаем теги A > IMG

			if (false == html.match(/^<a.*?><img.*><\/a>$/)) {
				html = html.replace(/([\s\S]*(<img.*?>)[\s\S]*)/gi, '$2');		// разрешаем тег IMG
			}

			_object.html(html);

			callback(html, _object);

		}

		this._create({toolbarExternal: 'null'});

		this._object
			.unbind('paste')
			.bind('paste', function () { return false; });

		if (this._object.html().indexOf('img') == -1) {
			($.proxy(this._object.redactor('core.getObject').filemanager.makeFilemanager, this._object.redactor('core.getObject')))();
		}
		else {
            this._object.redactor('core.getObject').image.showEdit($('img', this._object))
		}

		// отслеживаем успешное завершение работы с картинками
		var _this = this;
		var _timer = setInterval(function () {
			if (_this._object) {
				_this._object.blur();	// постоянно убираем фокус, чтобы кроме картинки нельзя было ничего вставить
			}
		}, 100);


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
			$('.redactor_box').parent().addClass('in_edit');

			$('.redactor_editor')
				.unbind('click', this._bodyClick)
				.bind('click', this._bodyClick);

			$('#zetta_editor_toolbar')
				.show()
				.draggable();

		},
		onDestroy: function () {

			$('#zetta_editor_toolbar').empty();
			$('.in_edit').removeClass('in_edit');

			$('.redactor_editor').unbind('click', this._bodyClick);

			$('#zetta_editor_toolbar').hide();
			$('.zetta_edit_toolbar_fixed').removeClass('zetta_edit_toolbar_fixed');

		},
		_bodyClick: function (e) {

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
			_this: true,
			lang: 'ru',
			toolbarExternal: '#zetta_editor_toolbar',
			imageUpload: _baseUrl + '/mvc/editor/index/imageupload/?csrf_hash=' + _csrf_hash,
			fileUpload: _baseUrl + '/mvc/editor/index/fileupload/?csrf_hash=' + _csrf_hash,
			imageGetJson: _baseUrl + '/mvc/editor/index/images/',
			focus: false,
			emptyHtml: '',
            plugins: ['clearformatting', 'undoredo', 'filemanager', 'video', 'table', 'fontfamily', 'fontsize', 'fontcolor', 'pin'],
			deniedTags: ['html', 'head', 'link', 'body', 'meta', 'style', 'applet'],
			paragraphy: false,
			initCallback: function () {
    			var button = this.button.addFirst ('save', this.lang.get('save'));

                this.button.addCallback(button, function () {
		    		_this._object.blur();
			    	_this.destroy()
				});
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

$(document).click(function (e) {
	_redactor._dispatch(e);
});

$('body').unload(function () {
	_redactor.destroy();
});