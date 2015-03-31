<?php

class Modules_Blocks_AdminController extends Zend_Controller_Action {

	/**
	 * Модель Modules_Blocks_Model_Blocks
	 *
	 * @var Modules_Blocks_Model_Blocks
	 */
	protected $_modelBlocks;
	
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_blocks')) {
			throw new Exception('Access Denied');
		}
		
		$this->_modelBlocks = new Modules_Blocks_Model_Blocks();

		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('save', 'json')
        	->addActionContext('blockinfo', 'json')
        	->addActionContext('blockdelete', 'json')
            ->initContext();

	}

	public function saveAction() {
		if ($this->getRequest()->isPost()) {
			$this->_modelBlocks->save($this->getParam('block_name'), $this->getParam('content'), $this->getParam('route_id'));
		}
	}
	
	public function blockinfoAction() {
		
		$block = $this->_modelBlocks->getBlock($this->getParam('block_name'));
		
		if ($block) {
			$this->view->block = $block->toArray();
		}
		else {
			$this->view->block = array(
				'content' => ''
			);
		}

	}
	
	public function blockdeleteAction() {
		if ($this->getRequest()->isPost()) {
			$this->_modelBlocks->deleteBlock($this->getParam('block_name'), $this->getParam('route_id'));
		}
	}

}