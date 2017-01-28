<?php

class Modules_Admin_PanelController extends Zend_Controller_Action {

	protected $_user;
	protected $_model;
	
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin')) {
			throw new Exception('Access Denied');
		}
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('managepanel', 'html')
        	->addActionContext('favoriteslist', 'html')
            ->initContext();
		
		$this->_user = Zend_auth::getInstance()->getIdentity();
		$this->_model = new Modules_Admin_Model_Panel();
		
	}
	
	public function indexAction() {
		$this->view->user = $this->_user;
	}
	
	public function managepanelAction() {
		$this->view->modules = $this->_model->findModules();
		$this->view->modulesDeveloper = $this->_model->findModulesDeveloper();
	}
	
	public function favoriteslistAction() {
		$this->view->favorites = $this->_model->getFavorites($this->_user->username);
	}
	
	public function tofavoriteAction() {
		
		if ($this->getRequest()->isPost()) {
			
			$data = $this->_model->fetchRow(
				$this->_model->select()
					->where('username = ?', $this->_user->username)
					->where('module = ?', $this->getParam('name'))
			);
			
			if (!sizeof($data) && $this->getParam('name')) {
				$this->_model->insert(array(
					'username'	=> $this->_user->username,
					'module'	=> $this->getParam('name'),
				));
			}
			
		}
		
		$this->_forward('favoriteslist');
		
	}
	
	public function deletefavoritesAction() {
		
		if ($this->getRequest()->isPost()) {

			$this->_model->delete(
				$this->_model->getAdapter()->quoteInto('id = ?', $this->getParam('id'))
			);
			
		}

		$this->_forward('favoriteslist');
		
	}
    
}
