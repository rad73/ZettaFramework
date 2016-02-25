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
        	$return = $router->assemble($urlOptions, $name, $reset, $encode);
    	}
    	else {

    		$menu_routes = Modules_Router_Model_Router::getInstance();

    		if (array_key_exists('route_id', $urlOptions)) {

    			$current = $menu_routes->getItem($urlOptions['route_id']);

    		}
    		else {

    			$module = array_key_exists('module', $urlOptions) ? $urlOptions['module'] : $front->getRequest()->getModuleName();
		    	$controller = array_key_exists('controller', $urlOptions) ? $urlOptions['controller'] : $front->getRequest()->getControllerName();
		    	$action = array_key_exists('action', $urlOptions) ? $urlOptions['action'] : $front->getRequest()->getActionName();

	    		$current = $menu_routes->getRoute($module, $controller, $action);

    		}

	    	$currentUrl = $front->getBaseUrl() . $current['url'];
	    	$options = array();
	    	foreach ($urlOptions as $key=>$val) {
	    		if ($key == 'route_id' || $key == 'controller' || $key == 'module' || ($key == 'action' && $action != $front->getDefaultAction())) continue;
				$options[] = $key . '=' . ($encode ? urlencode($val) : $val);
	    	}

	    	$return = $currentUrl . (sizeof($options) ? ('?' . implode('&amp;', $options)) : '');

    	}

    	return $return;

    }

}