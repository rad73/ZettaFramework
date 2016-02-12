<?php

/**
 * Вывод субменю
 *
 */
class Zetta_View_Helper_Submenu extends Zend_View_Helper_Action {

    public function submenu($level = 1) {

    	$menuModel = new Modules_Menu_Model_Menu();
    	$submenuItems = $menuModel->getSubmenu($level);

    	$view = $this->view;

    	$view
			->addBasePath(MODULES_PATH . DS . 'Menu/App/views')
			->addBasePath(HEAP_PATH . DS . 'Menu/App/views')
		;


    	$menuModel = new Modules_Menu_Model_Menu();
    	$view->tree = $submenuItems;

    	$return = '';
    	if ($submenuItems) {
			$return = $view->render('submenu/index.phtml');
    	}

    	return $return;

    }

}