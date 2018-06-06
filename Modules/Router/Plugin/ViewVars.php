<?php

/**
 * Плагин для установки переменных во view
 *
 *
 */
class Modules_Router_Plugin_ViewVars extends Zend_Controller_Plugin_Abstract
{
    protected $_securitySession = null;
    protected $_csrf_hash = null;
    protected $_view = null;

    public function __construct()
    {
        $this->_view = Zend_Registry::get('view');
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->_view->route_current_id = Zend_Registry::get('RouteCurrentId');
        $this->_view->route_current = Zend_Registry::get('RouteCurrent');
    }
}
