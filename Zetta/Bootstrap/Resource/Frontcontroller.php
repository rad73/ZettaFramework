<?php

/**
 * Расширяем стандартный Frontcontroller
 *
 */
class Zetta_Bootstrap_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    protected $_frontController;

    public function init()
    {
        $this->getBootstrap()->bootstrap('Di');

        $this->_frontController = $this->getFrontController();
        $this->_setRequest();
        $this->_frontController = parent::init();

        $this->setDispatcherContainer();

        return $this->_frontController;
    }

    /**
     * Устанавливаем свой Request
     */
    protected function _setRequest()
    {
        $options = $this->getOptions();
        if (array_key_exists('request', $options)) {
            $this->_frontController->setRequest(new $options['request']);
        }

        return $this;
    }

    /**
     * Устанавливаем DI контейнер в Dispatcher
     */
    protected function setDispatcherContainer()
    {
        $dispatcher = $this->_frontController->getDispatcher();
        $dispatcher->setContainer(Zend_Registry::get('container'));

        return $this;
    }
}
