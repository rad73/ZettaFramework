<?php

class Modules_Settings_AdminController extends Zend_Controller_Action {

	protected $_model;
		
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_settings')) {
			throw new Exception('Access Denied');
		}
		
		$this->_model = Modules_Settings_Model_Settings::getInstance();
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('add', 'html')
        	->addActionContext('delete', 'json')
            ->initContext();
		
	}

	public function indexAction() {

		$this->view->settings = (Zend_Registry::get('SiteConfig')->count() > 0) 
			? Zend_Registry::get('SiteConfig')->toArray()
			: array();
			
			
	}
	
	public function addAction() {

		$form = new Zetta_Form(Zend_Registry::get('config')->Settings->form);

		if ($key = $this->getParam('key_id')) {
			$form->setDefaults(Zend_Registry::get('SiteConfig')->get($key)->toArray());
			$form->removeElement('key');
			$this->view->key = $key;
		}

		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			if (false == isset($key)) {
				$key = $form->getValue('key');
			}
			
			$arrayData = array(
				'value'	=> $form->getValue('value'),
				'description'	=> $form->getValue('description'),
			);

			$this->_model->save($arrayData, $key);

			$this->renderScript('admin/addComplete.ajax.phtml');

		}
		
	}
	
	public function deleteAction() {
		$this->_model->delete($this->getParam('key'));
	}

}