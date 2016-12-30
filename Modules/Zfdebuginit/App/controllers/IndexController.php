<?php

class Modules_Zfdebuginit_IndexController extends Zend_Controller_Action {

	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin')) {
			throw new Exception('Access Denied');
		}
		
		
	}
	
	public function phpinfoAction() {
		
		foreach (Zend_Controller_Front::getInstance()->getPlugins() as $plugin) {
			Zend_Controller_Front::getInstance()->unregisterPlugin($plugin);
		}
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		phpinfo();

	}
    
}
