<?php

/**
 * Вывод меню
 *
 */
class Zetta_View_Helper_Menu extends Zend_View_Helper_Abstract {

	protected $_request;

    public function menu($menu_id) {

    	$_modelMenu = new Modules_Menu_Model_Menu();
    	
    	$this->view
    		->addBasePath(HEAP_PATH . DS . 'Menu/App/views')
    		->addBasePath(MODULES_PATH . DS . 'Menu/App/views');

    	$this->view->menu_id = $menu_id;
    	$this->view->tree = $_modelMenu->getMenuTree($menu_id);
    	
    	try {
    		$return = $this->view->render('menu_' . $menu_id . '/index.phtml');
    	}
    	catch (Exception $e) {
    		$return = $this->view->render('menu/index.phtml');
    	}
    	
    	
    	$_user = Zend_auth::getInstance()->getIdentity();
		if ($_user && strstr($_user->role_name, 'admin')) {
			$this->view->content = $return;
			$return = $this->view->render('menu/adminWrapper.phtml');
		}
    	
    	return $return;

    }

}