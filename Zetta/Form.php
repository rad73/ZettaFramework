<?php

// @todo пересобрать нормально форму

class Zetta_Form extends Zend_Form
{
    protected $_formID = null;

    protected $_view;

    protected $_formElementsDecorator = array(
        'FormElements',
        'Form',
        array(array('tag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'wrap-zetta-form'))
    );

    protected $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-text')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row form-text')),
    );

    protected $elementSelectDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-select')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row form-select')),
    );

    protected $elementCheckboxDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-checkbox')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label checkbox_label', 'placement' => 'append')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-check')),
    );

    protected $elementRadioDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-radio')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label radio_label', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-radio')),
    );

    protected $elementSubmitDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-submit')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-submit')),
    );

    protected $elementCaptchaDecorators = array(
        'Captcha',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-captcha')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-captcha')),
    );

    protected $elementFileDecorators = array(
        'File',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-file')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-file')),
    );

    protected $elementHiddenDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element element-hidden')),
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-hidden')),
    );

    protected $elementNoteDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('description' => 'Description'), array('tag' => 'i', 'class' => 'element-description')),
        array(array('label' => 'Label'), array('class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row f-note')),
    );

    public function addElement($element, $name = null, $options = null)
    {
        switch ($element) {
            case 'select':
                    $options['decorators'] = $this->elementSelectDecorators;
                break;
            case 'note':
                    $options['decorators'] = $this->elementNoteDecorators;
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

                    $width = isset(Zend_Registry::get('config')->app->captcha['width'])
                        ? Zend_Registry::get('config')->app->captcha['width']
                        : '200';

                    $height = isset(Zend_Registry::get('config')->app->captcha['height'])
                        ? Zend_Registry::get('config')->app->captcha['height']
                        : '50';


                    $options['captcha'] = array(
                        'captcha' => $provider,
                        'font' => SYSTEM_PATH . '/public/font/captcha_font.ttf',
                        'imgDir' => TEMP_PATH . DS . 'Captcha',
                        'imgUrl' => $this->_view->baseUrl() . '/Temp/Captcha/',
                        'wordLen' => $wordLen,
                        'lineNoiseLevel' => $lineNoiseLevel,
                        'dotNoiseLevel' => $dotNoiseLevel,
                        'expiration' => $expiration,
                        'width' => $width,
                        'height' => $height,
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

        $options['decorators'][sizeof($options['decorators']) - 1][1]['id'] = 'form-' . $name;
        $options['decorators'][sizeof($options['decorators']) - 2][1]['class'] = 'label label-' . $element;

        if (isset($options['class'])) {
            $options['decorators'][sizeof($options['decorators']) - 1][1]['class'] .= ' ' . $options['class'];
        }

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

    /**
     * Преобразуем форму к массиву
     * @return array
     */
    public function toArray()
    {
        $result = [
            'elements'  => [],
            'errors'    => $this->getMessages()
        ];

        foreach ($this->getElements() as $name => $element) {
            $result['elements'][$name] = [
                'attr'         => $element->getAttribs(),
                'description'  => $element->getDescription(),
                'errors'       => array_values($element->getMessages()),
                'id'           => $element->getId(),
                'label'        => $element->getLabel(),
                'name'         => $element->getName(),
                'type'         => $element->getType(),
                'value'        => $element->getValue(),
                'isRequired'   => $element->isRequired(),
            ];

            if ($element instanceof Zend_Form_Element_Captcha) {
                $captchaObject = $element->getCaptcha();
                $captchaObject->generate();

                $result['elements'][$name]['captchaId'] = $captchaObject->getId();
                $result['elements'][$name]['captchaUrl'] = $captchaObject->getImgUrl()
                    . $captchaObject->getId()
                    . $captchaObject->getSuffix()
                ;
            }
        }

        return $result;
    }
}
