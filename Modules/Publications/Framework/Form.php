<?php

class Publications_Framework_Form extends Zetta_Form {

	/**
	 * Модель типов публикаций
	 *
	 * @var Modules_Publications_Model_List
	 */
	protected $_modelList;

	/**
	 * Модель полей публикаций
	 *
	 * @var Modules_Publications_Model_Fields
	 */
	protected $_modelFields;

	protected $_rubric;

	protected $_fields;


	public function __construct($model_name, $options = array()) {

		$options['id'] = $model_name;

		parent::__construct($options);

		$this->_modelList = new Modules_Publications_Model_List();
		$this->_modelFields = new Modules_Publications_Model_Fields();

		$this->_rubric = $this->_modelList->getRubricInfo($model_name);

		$this->_addElements();

		$route_id = Zend_Registry::isRegistered('RouteCurrentId') && Zend_Registry::get('RouteCurrentId')
			? Zend_Registry::get('RouteCurrentId')
			: Zend_Controller_Front::getInstance()->getRequest()->getParam('route_id');

		if ($route_id) {
			$this->setAction(Zend_Registry::get('view')->url(array('route_id' => $route_id)));
		}

	}

	public function addElement($element, $name = null, $options = null) {

		switch($element) {
			case 'html':
					$element = 'textarea';
				break;
			case 'file_dialog':
					$element = 'text';
					$options['z_image_dialog'] = 1;
				break;
			case 'date':
					$element = 'text';
					$options['z_date'] = 1;
				break;
			case 'datetime':
					$element = 'text';
					$options['z_date_time'] = 1;
				break;
			case 'captcha':
					// throw new Exception('Мы ещё не подключили этот тип поля');
					$options['captcha'] = array(
					    'captcha' => 'Image',
					    'font' => SYSTEM_PATH . '/public/font/captcha_font.ttf',
					    'imgDir'	=> TEMP_PATH . DS . 'Captcha',
					    'imgUrl'	=> HTTP_HOST . '/Temp/Captcha/',
					    'wordLen'	=> 5,
					    'lineNoiseLevel'	=> 2,
					    'dotNoiseLevel'	=> 50,

					);
				break;
			case 'route':
					$element = 'select';
					$options['list_values'] = 'routes';
				break;
			case 'file':
					$element = 'ZettaFile';
				break;
		}

		if (array_key_exists('default', $options) && $options['default']) {
			$options['value'] = $options['default'];
		}
		if (array_key_exists('title', $options) && $options['title']) {
			$options['label'] = $options['title'];
		}

		$elementObject = parent::addElement($element, $name, $options);

		if (array_key_exists('list_values', $options) && $options['list_values']) {

			switch ($element) {
				case 'multiCheckbox':
				case 'radio':
				case 'select':

					if ('routes' == $options['list_values']) {
						$this->getElement($name)->addMultiOptions(Modules_Router_Model_Router::getInstance()->getRoutesTreeHash());
					}
					else {
						$model = new Modules_Publications_Model_Table($options['list_values']);
						$options = $model->getAssocArray('publication_id', 'name');

						$this->getElement($name)->addMultiOptions($options);
					}

					break;
			}

		}

		return $elementObject;

	}

	public function setElements(array $elements) {

		$this->addElements($elements);

		$this->addElement('submit', 'submit', array('title' => $this->getAttrib('submitTitle') ? $this->getAttrib('submitTitle') : 'Отправить'));

		return $this;

	}

	public function getPostData() {

		foreach ($this->_fields as $field) {

			if ($field['type'] == 'date' || $field['type'] == 'datetime') {

				$string_date = $this->getValue($field['name']) . ($field['type'] == 'datetime' ? ' ' . Zend_Controller_Front::getRequest()->getParam($field['name'] . '_time') : '');
				$parse_date = date_parse($string_date);

				$arrayData[ $field['name'] ] = sprintf('%04d-%02d-%02d' .($field['type'] == 'datetime' ? ' %02d:%02d' : '') , $parse_date['year'], $parse_date['month'], $parse_date['day'], $parse_date['hour'], $parse_date['minute']);

			}
			else if ($field['type'] == 'multiCheckbox') {
				$arrayData[ $field['name'] ] = '÷' . implode('÷', $this->getValue($field['name'])) . '÷';
			}
			else if ($field['type'] == 'file') {

				// закачиваем файлик
				if (sizeof($_FILES) && array_key_exists($field['name'], $_FILES) && !$_FILES[$field['name']]['error']) {

					$incomingDir = USER_FILES_PATH . DS . 'files/incoming';
					if (false == is_dir($incomingDir)) mkdir($incomingDir);

					$fName = explode('.', $_FILES[$field['name']]['name']);
					$ext = end($fName);
					$fileName = str_replace($ext, '_' . time() . '.' . $ext, $_FILES[$field['name']]['name']);
					move_uploaded_file($_FILES[$field['name']]['tmp_name'], $incomingDir . DS . $fileName);

					$arrayData[ $field['name'] ] = '/UserFiles/files/incoming/' . $fileName;

				}

			}
			else {
				$arrayData[ $field['name'] ] = $this->getValue( $field['name']);
			}

		}

		unset($arrayData['captcha']);

		return $arrayData;

	}

	public function getFields() {
		return $this->_fields;
	}

	protected function _addElements() {

		if (!$this->_rubric) throw new Exception('rubric_id не определён');

		$fields = $this->_modelFields->getFieldsByRubric($this->_rubric->rubric_id);

		$arrayFields = array();
		foreach ($fields as $i=>$field) {

			if (ZETTA_FRONT && $field->hidden_front) continue;

			$arrayFields[$i] = $field->toArray();
			$arrayFields[$i]['options'] = $field->toArray();

			if ($field['validator']) {

				$arrayFields[$i]['options']['validators'] = array(
					'custom'	=> new Publications_Framework_Validator_CustomRegexp($field)
				);

				$arrayFields[$i]['options']['required'] = 1;

			}

		}

		$this->_fields = $arrayFields;
		$this->setElements($this->_fields);

	}

}