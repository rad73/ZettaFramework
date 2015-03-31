<?php

class Modules_Seo_AdminController extends Zend_Controller_Action {

	/**
	 * Модель
	 *
	 * @var Modules_Seo_Model_Seo
	 */
	protected $_model;


	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_seo')) {
			throw new Exception('Access Denied');
		}
		
		$this->view->currentUrl = $this->getParam('currentUrl');

		$this->_model = new Modules_Seo_Model_Seo();
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
            ->initContext();
		
	}

	public function indexAction() {

		$form = new Zetta_Form(Zend_Registry::get('config')->Seo->form);

		$data = $this->_model->findByUrl($this->view->currentUrl);

		if (sizeof($data)) {
			$form->setDefaults($data->toArray());
		}

		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$arrayData = array(
				'title'	=> ($title = $form->getValue('title')) ? $title : new Zend_Db_Expr('NULL'),
				'keywords'	=> ($keywords = $form->getValue('keywords')) ? $keywords : new Zend_Db_Expr('NULL'),
				'description'	=> ($description = $form->getValue('description')) ? $description : new Zend_Db_Expr('NULL'),
			);

			$this->_model->save($arrayData, $this->view->currentUrl);

			$this->renderScript('admin/addComplete.ajax.phtml');

		}

	}

}