var BranchMigrations = function (container) {

	/**
	 * Расстояние между пузырьками
	 */
	var _distance = 60;

	/**
	 * Данные о мастер-ветке
	 */
	var _masterData = null;

	/**
	 * Данные о текущей
	 */
	var _currentData = null;

	/**
	 * Raphael объект
	 */
	var _raphael;

	/**
	 * Контейнер для графика
	 */
	var _container;

	/**
	 * Отсупы графика
	 */
	var _padding = {
		top: 150, left: 100, right: 200
	}

	var _this = this;
	this._dotsMaster = [];
	this._dotsCurrent = [];



	/**
	 * Устанавливаем значения для графика
	 */
	this.setData = function (data) {

		_masterData = data.master;

		// возможно ветки одинаковы, но миграции сделаны в различной последовательностии
		this._equal = false;
		if (data.current.length == _masterData.length) {

			for(var item in _masterData) {

				if (!this._inBranch(_masterData[item].class_name, data.current)) {
					this._equal = false;
					break;
				}
				else {
					this._equal = true;
				}
				
			}
			
		}

		if (this._equal == false) {
			_currentData = data.current;
		}
		else {
			_currentData = [];
		}

		this._preDraw();
		
	};

	/**
	 * Перед рисованием инициализируем канву
	 * и подготавливаем всё необходимое
	 */
	this._preDraw = function () {

		var length = _masterData.length > _currentData.length ? _masterData.length : _currentData.length;
		var width = length * _distance + _padding.left + _padding.right;
		_raphael = Raphael(container, width < 800 ? 800 : width, 400);

		this._drawBlockInfo();

	};

	/**
	 * Конструктор
	 */
	this.__constructor = function () {
		_container = $('#' + container);
	}.apply(this);


	/**
	 * Рисуем мастер-ветку
	 */
	this.drawMasterBranch = function (settings) {
		this
			._drawMasterLine(settings.line)
			._drawMasterBubble(settings.bubble);
	}

	/**
	 * Рисуем текущую ветку
	 */
	this.drawCurrentBranch = function (settings) {
		this
			._drawCurrentLine(settings.line)
			._drawCurrentBubble(settings.bubble);
	}


	/**
	 * Рисуем линию графика мастер-ветки
	 */
	this._drawMasterLine  = function (options) {

		options['stroke-linejoin'] = 'round';
		var path = _raphael.path().attr(options);

		this._call_draw(_masterData, _distance, 0, function (x, y, i) {
			path[i == 0 ? "moveTo" : "lineTo"](x, y, 0);
		});

		return this;

	}

	/**
	 * Рисуем пузыримастер-ветки
	 */
	this._drawMasterBubble  = function (options) {

		this._call_draw(_masterData, _distance, 0, function (x, y, i) {

			var _options = options.normal;
			var isCross = false;

			if (_this._inBranch(_masterData[i].class_name, _currentData) || _this._equal) {
				_options = options.cross;
				isCross = true;
			}

			var dot = _raphael.circle(x, y, _options.radius*2).attr(_options);
			dot._info = _masterData[i];
			dot._options = _options;
			dot._position = {x: x, y: y};
			dot._master = true;
			dot._cross = isCross;
			
			var text = _raphael.text(x, y, dot._info.class_name).attr({fill: '#777'});
			w = text.getBBox().width;
			var alpha = 30;

			text
				.attr('x', x + (w / 2) * Math.cos(alpha*Math.PI/180))
				.attr('y', y - (w / 2) * Math.sin(alpha*Math.PI/180) - 15)
				.rotate(-alpha);
			
			dot._text = text;

			_this._dotsMaster.push(dot);

		});

	}
	
	/**
	 * Рисуем линию графика текущей ветки
	 */
	this._drawCurrentLine  = function (options) {

		options['stroke-linejoin'] = 'round';
		var path = _raphael.path().attr(options);
		var start = true;

		this._call_draw(_currentData, _distance, _distance, function (x, y, i) {
			
			if (!_masterData[i] || _masterData[i].class_name != _currentData[i].class_name || !start) {
				if (start) {
					path["moveTo"](x - _distance, y - _distance, 0);
					start = false;
				}
				path["lineTo"](x, y + 10);
			}

		});

		return this;

	}

	/**
	 * Рисуем пузыри текущей ветки
	 */
	this._drawCurrentBubble  = function (options) {

		var start = true;

		this._call_draw(_currentData, _distance, _distance, function (x, y, i) {

			if (!_masterData[i] || _masterData[i].class_name != _currentData[i].class_name || !start) {
			
				var _options = options.normal;
				var isCross = false;

				if (_this._inBranch(_currentData[i].class_name, _masterData)) {
					_options = options.cross;
					isCross = true;
				}
				y += 10;
				var dot = _raphael.circle(x, y, _options.radius*2).attr(_options);
				dot._info = _currentData[i];
				dot._position = {x: x, y: y};
				dot._options = _options;
				dot._master = false;
				dot._cross = isCross;

				var text = _raphael.text(x, y, dot._info.class_name).attr({fill: '#777'});
				w = text.getBBox().width;
				var alpha = 30;

				text
					.attr('x', x - (w / 2) * Math.cos(alpha*Math.PI/180))
					.attr('y', y + (w / 2) * Math.sin(alpha*Math.PI/180) + 15)
					.rotate(-alpha);

				dot._text = text;
					
				_this._dotsCurrent.push(dot);
				
				start = false;

			}

		});

	}
	
	this._call_draw = function (_data, distX, distY, callback) {

		for (var i = 0, length = _data.length; i < length; i++) {
			
	        var y = _padding.top + distY,
	            x = Math.round(distX*i) + _padding.left;
            callback(x, y, i);

		}

	}

	this._inBranch = function(className, branch) {

		for (var item in branch) {
			if (branch[item].class_name == className) {
				return true;
			}
		}

		return false;

	}


	/**
	 * Рисуем блок информации по текущей миграции
	 */
	this._drawBlockInfo = function(date, class_name, comment) {

		var text = 
			'<div id="block_info" class="bottom_left">'
				+ '<div class="outer_info">'
					+ '<div>'
						+ '<span class="link_merge">' 
							+ '<a href="' + class_name + '"></a>'
						+ '</span>'
						
						+ '<span class="link_disconnect">' 
							+ '<a href="' + class_name + '"></a>'
						+ '</span>'
						
						+ '<span class="date_commit">' + date + '</span> - '
						+ '<span class="class_commit">' 
							+ '<a href="' + class_name + '"></a>'
						+ '</span>'
					+ '</div>'
					+ '<div class="commit_text">' + comment + '</div>'
				+ '</div>'
			+'</div>';

		$(_container).append(text);
		
		var width = $('#block_info').css('width'),
			height = $('#block_info').css('height');

		this.descrBlock = _raphael.set();
		this.descrBlock.push(_raphael.rect(1, 1, width, height, 5).attr({stroke: '#dbd6d6', 'stroke-width': 2, fill: '#fff'}));
		this.descrBlock.attr('opacity', 0.8);

		this._hideBlockInfo();

	}

	this._changeBlockInfo = function (dot) {
	
		with(dot._info) {

			$('#block_info').css('height', 'auto');

			$('#block_info .date_commit').html(date);
			$('#block_info .class_commit a').html(class_name);
			$('#block_info .commit_text').html(comment);

			$('#block_info .link_merge a').attr('href', uriUp +'/'+ class_name);
			$('#block_info .link_disconnect a').attr('href', uriChainBreak +'/'+ class_name);
			$('#block_info .class_commit a').attr('href', uriInfo +'/'+ class_name);
			
			if (dot._master == true) {
				$('#block_info .link_disconnect').hide();
				
				if (dot._cross == false) {
					$('#block_info .link_merge').show();
				}
				else {
					$('#block_info .link_merge').hide();
				}
			}
			else {
				$('#block_info .link_disconnect').show();
				$('#block_info .link_merge').hide();
			}

		}
		
		this.descrBlock[0].attr('height', $('#block_info').height());
			
	}

	this._moveBlockInfo = function (x, y, className) {
		
		$('#block_info', _container).css({top: y, left: x});
		$('#block_info').attr('class', className)
		this.descrBlock.attr('x', x).attr('y', y);
		this._showBlockInfo();

		this._paintStr(x, y, className);
		
	}

	this._hideBlockInfo = function () {
		$('#block_info').hide();
		this.descrBlock.hide();
	}
	
	this._showBlockInfo = function () {
		
		$('#block_info').show();
		this.descrBlock
			.show()
			.toFront();
	}

	this._paintStr = function (x, y, className) {

		if (this._str) {
			this._str.remove();
		}

		this._str = _raphael.path().attr({stroke: '#dbd6d6', 'stroke-width': 2, fill: '#fff'});
		var widthBlockInfo = parseInt(this.descrBlock[0].attr('width'));
		var heightBlockInfo = parseInt(this.descrBlock[0].attr('height'));

		var strLen = 10;
		var padding = 10;

		switch(className) {
			case 'top_left':
					var x = x + padding, y = y + 1, flag = -1;
				break;
			case 'top_right':
					var x = x + widthBlockInfo - padding - 2 * strLen, y = y + 1, flag = -1;
				break;
			case 'bottom_left':
					var x = x + padding, y = y + heightBlockInfo - 1, flag = 1;
				break;
			case 'bottom_right':
					var x = x + widthBlockInfo - padding - 2 * strLen, y = y + heightBlockInfo - 1, flag = 1;
				break;
		}

		this._str.moveTo(x, y).lineTo(x + strLen, y + strLen * flag).lineTo(x + 2 * strLen, y);
	
	}

	this.setEvents = function () {

		var maxLeft = _container.width() - $('#block_info').width();

		$(this._dotsMaster).each(function (i) {

			$(this.node).hover(function () {

				_dot = _this._dotsMaster[i];
				_dot.attr('r', 7);

				_this._changeBlockInfo(_dot);
				_this._hoverCrossDots(_dot._info.class_name);
				
				if (maxLeft < _dot._position.x - 20) {
					_this._moveBlockInfo(_dot._position.x - $('#block_info').width() + 20, _dot._position.y + 20, 'top_right');
				}
				else {
					_this._moveBlockInfo(_dot._position.x - 20, _dot._position.y + 20, 'top_left');
				}
				
			},
			function () {
				_dot = _this._dotsMaster[i];
				_dot.attr('r', 6);
			}); 
			
		});

		$(this._dotsCurrent).each(function (i) {

			$(this.node).hover(function () {

				_dot = _this._dotsCurrent[i];
				_dot.attr('r', 7);

				_this._changeBlockInfo(_dot);
				_this._hoverCrossDots(_dot._info.class_name);
				
				if (maxLeft < _dot._position.x - 20) {
					_this._moveBlockInfo(_dot._position.x - $('#block_info').width() + 20, _dot._position.y - ($('#block_info').height()+20), 'bottom_right');
				}
				else {
					_this._moveBlockInfo(_dot._position.x - 20, _dot._position.y - ($('#block_info').height()+20), 'bottom_left');
				}
				
			},
			function () {
				_dot = _this._dotsCurrent[i];
				_dot.attr('r', 6);
			}); 
			
		});

	}
	
	this._hoverCrossDots = function (className) {
		$(this._dotsCurrent).each(function (i) {
			if (this._info.class_name == className) {
				this.attr('fill', 'red');
			}
			else {
				this.attr('fill', this._options.fill);
			}
		});
		
		$(this._dotsMaster).each(function (i) {
			if (this._info.class_name == className) {
				this.attr('fill', 'red');
			}
			else {
				this.attr('fill', this._options.fill);
			}
		});
	}

}