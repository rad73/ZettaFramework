<?php

class Modules_Service_BackupController extends Zend_Controller_Action {
	
	public function init() {

		if ($this->getParam('secret_key') != Zend_Registry::get('config')->Db->staticSalt) {
			throw new Exception('Access Deny BackupController via http');
		}
				
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
	}
	
	
	public function indexAction() {
		Modules_Service_Model_Backup::getInstance()->backup($this->getParam('skip_zetta'));
	}

}