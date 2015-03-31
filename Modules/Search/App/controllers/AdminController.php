<?php

class Modules_Search_AdminController extends Zend_Controller_Action {

	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_search')) {
			throw new Exception('Access Denied');
		}
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
            ->initContext();
            
	}
	
	public function indexAction() {
		
		if (is_file(TEMP_PATH . '/Search/write.lock.file')) {
			$_indexHandle = Zend_Search_Lucene::open(TEMP_PATH . '/Search');
		}
		else {
			$_indexHandle = Zend_Search_Lucene::create(TEMP_PATH . '/Search');
		}
		
		$this->view->count = intval($_indexHandle->maxDoc());

	}

}