<?php

/**
 * Плагин для поиска текущего route_id
 *
 *
 */
class Modules_Router_Plugin_FindRouteCurrent extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getParam('front')) {
            defined('ZETTA_FRONT') || define('ZETTA_FRONT', $request->getParam('front') ? true : false);
        }

        $currentRoute = Modules_Router_Model_Router::getInstance()->current();
        Zend_Registry::set('RouteCurrent', $currentRoute);
        Zend_Registry::set('RouteCurrentId', $currentRoute['route_id']);
        defined('ZETTA_FRONT') || define('ZETTA_FRONT', $currentRoute['route_id'] ? true : false);
    }
}
