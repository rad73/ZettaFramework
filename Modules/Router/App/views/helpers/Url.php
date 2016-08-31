<?php

/**
 * Переопределяем стандартный помошник URL, для совместимости с Modules_Router
 *
 */
class Zetta_View_Helper_Url extends Zend_View_Helper_Abstract {

	protected $_request;

    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true) {

    	$front = Zend_Controller_Front::getInstance();

    	if ($name == null) {
    		$name = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
    	}

    	if ($name == 'mvc') {
    		$router = $front->getRouter();
        	$return = $router->assemble($urlOptions, $name, $reset, $encode) . Zend_Controller_Router_Abstract::URI_DELIMITER;
    	}
    	else {

            $module = array_key_exists('module', $urlOptions) ? $urlOptions['module'] : $front->getRequest()->getModuleName();
            $controller = array_key_exists('controller', $urlOptions) ? $urlOptions['controller'] : $front->getRequest()->getControllerName();
            $action = array_key_exists('action', $urlOptions) ? $urlOptions['action'] : $front->getRequest()->getActionName();

            $menu_routes = Modules_Router_Model_Router::getInstance();
            if (array_key_exists('route_id', $urlOptions)) {
                $current = $menu_routes->getItem($urlOptions['route_id']);
            }
            else if (!array_key_exists('action', $urlOptions)) {
                $current = $menu_routes->current();
            }
            else {
                $current = $menu_routes->getRoute($module, $controller, $action);
            }

            if (false == $reset) {
                $urlOptions = array_merge($_GET, $urlOptions);
            }

	    	$currentUrl = $front->getBaseUrl() . $current['url'];
	    	$options = array();
	    	foreach ($urlOptions as $key=>$val) {

	    		if ($key == 'route_id' || $key == 'controller' || $key == 'module' || ($key == 'action' && $action != $front->getDefaultAction())) continue;

                if (!is_array($val)) {
                    $val = array($val);
                }

                foreach ($val as $row) {
                    $options[] = urlencode($key) . '=' . ($encode ? urlencode($row) : $row);
                }

	    	}

	    	$return = $currentUrl . (sizeof($options) ? ('?' . implode('&', $options)) : '');

    	}

    	return $return;

    }

}