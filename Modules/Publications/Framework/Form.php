<?php

class Modules_Publications_Framework_Form extends Zetta_Form {

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

		return parent::addElement($element, $name, $options);

	}

	public function setElements(array $elements) {

		$this->addElements($elements);

		$this->addElement('button', 'submit', array('type' => 'submit', 'title' => $this->getAttrib('submitTitle') ? $this->getAttrib('submitTitle') : 'Отправить'));

		return $this;

	}

	public function getPostData() {
		
		foreach ($this->_fields as $field) {

			if ($field['type'] == 'date' || $field['type'] == 'datetime') {

				$string_date = $this->getValue($field['name']) . ($field['type'] == 'datetime' ? ' ' . Zend_Controller_Front::getInstance()->getRequest()->getParam($field['name'] . '_time') : '');
				$parse_date = date_parse($string_date);

				$arrayData[ $field['name'] ] = sprintf('%04d-%02d-%02d' .($field['type'] == 'datetime' ? ' %02d:%02d' : '') , $parse_date['year'], $parse_date['month'], $parse_date['day'], $parse_date['hour'], $parse_date['minute']);

			}
			else if ($field['type'] == 'multiCheckbox' && sizeof($this->getValue($field['name']))) {
				$arrayData[ $field['name'] ] = '÷' . implode('÷', $this->getValue($field['name'])) . '÷';
			}
			else if ($field['type'] == 'file') {

				// закачиваем файлик
				if (sizeof($_FILES) && array_key_exists($field['name'], $_FILES)) {

					$filesArray = $_FILES[$field['name']];

					if (false == is_array($filesArray['tmp_name'])) {
						foreach ($filesArray as $index => $value) {
							$filesArray[$index] = array($value);
						}
					}

					$incomingDir = USER_FILES_PATH . DS . 'files/incoming' . DS;
					$incomingDirRelative = str_replace(FILE_PATH, '', $incomingDir);
					if (false == is_dir($incomingDir)) mkdir($incomingDir);
					$arrayData[ $field['name'] ] = array();
					
					foreach ($filesArray['name'] as $index => $name) {
						
						if (!$filesArray['error'][$index] && $filesArray['tmp_name'][$index]) {

							$fName = explode('.', $name);
							$ext = end($fName);
							$fileName = str_replace($ext, '_' . time() . '.' . $ext, $name);
							move_uploaded_file($filesArray['tmp_name'][$index], $incomingDir . $fileName);

							$arrayData[$field['name']][] = $incomingDirRelative . $fileName;
							
						}
	
					}
					
					if (sizeof($arrayData[$field['name']])) {
						$arrayData[$field['name']] =  sizeof($arrayData[$field['name']]) == 1 ? $arrayData[$field['name']][0] : json_encode($arrayData[$field['name']]);
					}
					else {
						$arrayData[$field['name']] = new Zend_Db_Expr('NULL');
					}

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
			if (!ZETTA_FRONT && $field->hidden_admin) continue;

			$arrayFields[$i] = $field->toArray();
			$arrayFields[$i]['options'] = array(
				'data-type'	=> $field->type,
				'data-validator'	=> $field->validator,
				'data-errormsg'	=> $field->errormsg,
				'list_values'	=> $field->list_values,
				'name'	=> $field->name,
				'title'	=> $field->title,
				'value'	=> $field->default,
			);
			
			if ($field->properties) {
				$extraOptions = json_decode($field->properties, true);
				$arrayFields[$i]['options'] = $arrayFields[$i]['options'] + $extraOptions;
			}
			
			if ($field->tooltip) {
				$arrayFields[$i]['options']['description'] = $field->tooltip;
			}

			if ($field['validator']) {

				$classValidate = preg_match('/new (.*)\(.*/iU', $field['validator'], $matches)
					? new $matches[1]($field)
					: new Modules_Publications_Framework_Validator_CustomRegexp($field);

				$arrayFields[$i]['options']['validators'] = array(
					'custom'	=> $classValidate
				);

				$arrayFields[$i]['options']['required'] = 1;

			}

		}

		$this->_fields = $arrayFields;
		$this->setElements($this->_fields);

	}

}
