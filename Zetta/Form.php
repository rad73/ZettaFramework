<?php

// @todo пересобрать нормально форму

class Zetta_Form extends Zend_Form
{
    protected $_formID = null;

    protected $_view;

    protected $_formElementsDecorator = array(
        'FormElements',
        'Form',
        array(array('tag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'wrap_zetta_form'))
    );

    protected $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element text')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__text clearfix')),
    );

    protected $elementSelectDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element select')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__select clearfix')),
    );

    protected $elementCheckboxDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element checkbox')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label checkbox_label', 'placement' => 'append')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__checkbox clearfix')),
    );

    protected $elementRadioDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element radio')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label radio_label', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__radio clearfix')),
    );

    protected $elementSubmitDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element submit')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__button z_buttons_placehoder clearfix')),
    );

    protected $elementCaptchaDecorators = array(
        'Captcha',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element captcha')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__captcha clearfix')),
    );

    protected $elementFileDecorators = array(
        'File',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element file')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__file clearfix')),
    );


    protected $elementHiddenDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element hidden')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element_description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form_row form_row__hidden hidden')),
    );


    public function addElement($element, $name = null, $options = null)
    {
        switch ($element) {
            case 'select':
                    $options['decorators'] = $this->elementSelectDecorators;

                break;
            case 'checkbox':
                    $options['decorators'] = $this->elementCheckboxDecorators;

                break;
            case 'multiCheckbox':
            case 'radio':
                    $options['decorators'] = $this->elementRadioDecorators;

                break;
            case 'submit':
            case 'button':
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
                        'imgDir' => TEMP_PATH . DS . 'Captcha',
                        'imgUrl' => $this->_view->baseUrl() . '/Temp/Captcha/',
                        'wordLen' => $wordLen,
                        'lineNoiseLevel' => $lineNoiseLevel,
                        'dotNoiseLevel' => $dotNoiseLevel,
                        'expiration' => $expiration,
                    );

                    $options['prefixPath']['captcha'] = array(
                        'prefix' => 'Zetta_Captcha',
                        'path' => 'Zetta/Captcha'
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

        $elementObject = parent::addElement($element, $name, $options);

        if (array_key_exists('list_values', $options) && $options['list_values']) {
            switch ($element) {
                case 'multiCheckbox':
                case 'radio':
                case 'select':

                    if ('routes' == $options['list_values']) {
                        $this->getElement($name)->addMultiOptions(Modules_Router_Model_Router::getInstance()->getRoutesTreeHash());
                    } elseif ($list_values = json_decode($options['list_values'])) {
                        $this->getElement($name)->addMultiOptions((array)$list_values);
                    } elseif (is_callable($options['list_values'])) {
                        $options = $options['list_values']();
                        $this->getElement($name)->addMultiOptions($options);
                    } else {
                        $model = new Modules_Publications_Model_Table($options['list_values']);
                        $options = $model->getAssocArray('publication_id', 'name');

                        $this->getElement($name)->addMultiOptions($options);
                    }

                    break;
            }
        }

        return $elementObject;
    }

    public function __construct($options = null)
    {
        $this->setDecorators($this->_formElementsDecorator);

        $this->_view = Zend_Layout::getMvcInstance()->getView();

        $this->setAttrib('class', 'zetta_form');
        $this->addPrefixPath('Zetta_Form_Element', 'Zetta/Form/Element/', 'element');
        $this->addPrefixPath('Zetta_Form_Decorator', 'Zetta/Form/Decorator/', 'decorator');

        parent::__construct($options);

        if ((is_array($options) && isset($options['id'])) || isset($options->id)) {
            $this->_formID = is_array($options) ? $options['id'] : $options->id;
            $this->addElement('hidden', 'form_name', array(
                'value' => $this->_formID
            ));
        }
    }


    public function isValid($data)
    {
        if ($this->_formID) {
            if (!isset($data['form_name']) || $this->_formID != $data['form_name']) {
                return false;
            }
        }

        return parent::isValid($data);
    }
}
