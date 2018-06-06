<?php

class Modules_Router_IndexController extends Zend_Controller_Action
{

    /**
     * Модель Modules_Router_Model_Router
     *
     * @var Modules_Router_Model_Router
     */
    protected $_modelRoutes;

    /**
     * Текущий раздел
     *
     * @var Zend_Db_Row
     */
    protected $_currentSection;

    public function init()
    {
        $this->_modelRoutes = Modules_Router_Model_Router::getInstance();

        $this->_currentSection = $this->_modelRoutes->current();
        $this->_helper->viewRenderer->setNoRender();
        $this->view->headTitle($this->_currentSection['name']);

        if (
            !$this->_currentSection
            || (1 == $this->_currentSection['disable'])	// @todo сделать проверку - если администратор то показать раздел
            || !(Zetta_Acl::getInstance()->isAllowed('route_' . $this->_currentSection['route_id'], 'allow'))
        ) {
            return $this->_forwardTo404();
        }

        if (false == empty($this->_currentSection['redirect_url'])) {
            $this->_redirectToURL();
        }

        $this->_forwardToMVC();
    }

    /**
     * Раздел не найден отправляем на 404 страницу
     *
     */
    protected function _forwardTo404()
    {
        $this->_helper->actionStack('error404', 'error', 'default');
    }

    /**
     * Переадресация по URL
     *
     */
    protected function _redirectToURL()
    {
        $this->_redirect($this->_currentSection['redirect_url'], array('code' => 301));
    }

    /**
     * Запускаем подклченный модуль
     *
     */
    protected function _forwardToMVC()
    {
        $module = $this->_currentSection['module'];
        $controller = $this->_currentSection['controller'];
        switch (true) {
            case $action = $this->_currentSection['action']:					// action явно указан в роутинге
                break;
            case $action = $this->getRequest()->getParam('action'):				// action указан в URL (?action=:action)
                break;
            case $action = $this->getFrontController()->getDefaultAction():		// action по умолчанию
                break;
        }
        
        $params = array_merge_recursive($this->getRequest()->getParams(), ($this->_currentSection['parms'] ? json_decode($this->_currentSection['parms'], true) : array()));
        
        // в URL можно передавать один параметро вида /param_value.html, он ложится в переменную $this->getRequest()->getParam('main_param')
        $url = Zend_Controller_Front::getInstance()->getRequest()->getPathInfo();
        if (preg_match('|.*/(.*)\.html|u', $url, $matches)) {
            $params['main_param'] = $matches[1];
        }
        Zend_Registry::set('main_param', isset($params['main_param']) ? $params['main_param'] : false);

        $this->_helper->actionStack(System_String::StrToLower($action), System_String::StrToLower($controller), System_String::StrToLower($module), $params);
    }

    public function __call($function, $args)
    {
    }
}
