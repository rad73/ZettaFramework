<?php

// @todo пересобрать нормально форму

class Zetta_Form extends Zend_Form {

	protected $_formID = null;

	protected $_view;


	protected $elementDecorators = array(
	    'ViewHelper',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element text')),
	    array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('label' => 'Label'), array('class' => 'label')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row clearfix')),
	);

	protected $elementCheckboxDecorators = array(
	     'ViewHelper',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element checkbox')),
		array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('label' => 'Label'), array('class' => 'label checkbox_label', 'placement' => 'append')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row clearfix')),
	);

	protected $elementRadioDecorators = array(
		'ViewHelper',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element radio')),
		array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('label' => 'Label'), array('class' => 'label radio_label', 'placement' => 'prepend')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row_radio clearfix')),
	);

	protected $elementSubmitDecorators = array(
	    'ViewHelper',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element submit')),
		array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row z_buttons_placehoder clearfix')),
	);

	protected $elementCaptchaDecorators = array(
	    'Captcha',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element captcha')),
		array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('label' => 'Label'), array('class' => 'label')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row clearfix')),
	);

	protected $elementFileDecorators = array(
	    'File',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element file')),
		array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('label' => 'Label'), array('class' => 'label')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row clearfix')),
	);


	protected $elementHiddenDecorators = array(
	    'ViewHelper',
	    'Errors',
	    array(array('data'  => 'HtmlTag'), array('tag' => 'div', 'class' => 'element hidden')),
		array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
	    array(array('label' => 'Label'), array('class' => 'label')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'hidden')),
	);


	public function addElement($element, $name = null, $options = null) {

		switch ($element) {
			case 'checkbox':
					$options['decorators'] = $this->elementCheckboxDecorators;
				break;
			case 'multiCheckbox':
			case 'radio':
					$options['decorators'] = $this->elementRadioDecorators;
				break;
			case 'submit':
					$options['decorators'] = $this->elementSubmitDecorators;
				break;
			case 'captcha':
					$options['decorators'] = $this->elementCaptchaDecorators;
					$options['autocomplete'] = "off";

					$wordLen = isset(Zend_Registry::get('config')->app->captcha['wordLen'])
						? Zend_Registry::get('config')->app->captcha['wordLen']
						: 5;

					$lineNoiseLevel = isset(Zend_Registry::get('config')->app->captcha['lineNoiseLevel'])
						? Zend_Registry::get('config')->app->captcha['lineNoiseLevel']
						: 2;

					$dotNoiseLevel = isset(Zend_Registry::get('config')->app->captcha['dotNoiseLevel'])
						? Zend_Registry::get('config')->app->captcha['dotNoiseLevel']
						: 20;

					$provider = isset(Zend_Registry::get('config')->app->captcha['provider'])
						? Zend_Registry::get('config')->app->captcha['provider']
						: 'Image';

					$expiration = isset(Zend_Registry::get('config')->app->captcha['expiration'])
						? Zend_Registry::get('config')->app->captcha['expiration']
						: '600';


					$options['captcha'] = array(
					    'captcha' => $provider,
					    'font' => SYSTEM_PATH . '/public/font/captcha_font.ttf',
					    'imgDir'	=> TEMP_PATH . DS . 'Captcha',
					    'imgUrl'	=> $this->_view->baseUrl() . '/Temp/Captcha/',
					    'wordLen'	=>  $wordLen,
					    'lineNoiseLevel'	=> $lineNoiseLevel,
					    'dotNoiseLevel'	=> $dotNoiseLevel,
					    'expiration'	=> $expiration,
					);

					$options['prefixPath']['captcha'] = array(
						'prefix'	=> 'Zetta_Captcha',
						'path'		=> 'Zetta/Captcha'
					);

				break;
			case 'file':
					$options['decorators'] = $this->elementFileDecorators;
				break;
			case 'hidden':
					$options['decorators'] = $this->elementHiddenDecorators;
				break;
			default:
					$options['decorators'] = $this->elementDecorators;
				break;
		}

		$options['decorators'][sizeof($options['decorators']) - 1][1]['id'] = 'row_' . $name;
		$options['decorators'][sizeof($options['decorators']) - 2][1]['class'] = 'label_' . $element;

		return parent::addElement($element, $name, $options);

	}

	public function __construct($options = null) {

		$this->_view = Zend_Layout::getMvcInstance()->getView();

		$this->setAttrib('class', 'zetta_form');
		$this->addPrefixPath('Zetta_Form_Element', 'Zetta/Form/Element/', 'element');

		parent::__construct($options);

		if ((is_array($options) && isset($options['id'])) || isset($options->id)) {
			$this->_formID = is_array($options) ? $options['id'] : $options->id;
			$this->addElement('hidden', 'form_name', array(
				'value'	=> $this->_formID
			));
		}

	}


	public function isValid($data) {

		if ($this->_formID) {
			if (!isset($data['form_name']) || $this->_formID != $data['form_name']) {
				return false;
			}
		}

		return parent::isValid($data);

	}

}