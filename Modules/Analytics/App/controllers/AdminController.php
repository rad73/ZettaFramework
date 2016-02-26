<?php

class Modules_Analytics_AdminController extends Zend_Controller_Action {

	protected $_googleID;
	protected $_googleEmail;

	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_analytics')) {
			throw new Exception('Access Denied');
		}

		$this->_model = Modules_Settings_Model_Settings::getInstance();

		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
            ->initContext();

   		$this->_googleID = Zend_Registry::get('SiteConfig')->google_analytics_id;

   		$this->_googleEmail = Zend_Registry::get('SiteConfig')->google_email
   			? Zend_Registry::get('SiteConfig')->google_email
   			: false;

		$this->_googleAuthKey = is_readable(Zend_Registry::get('SiteConfig')->google_auth_key)
   			? Zend_Registry::get('SiteConfig')->google_auth_key
   			: MODULES_PATH . DS . 'Analytics' . DS . 'zetta.p12';

	}

	public function indexAction() {
		$this->view->googleID = $this->_googleID;
	}

	public function getfileAction() {

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$file = $this->getParam('file');
		if ($file && (stristr($file, '.xml') || stristr($file, '.csv'))) {

			$pathFile = TEMP_PATH . DS . 'Analytics' . DS . $file;

			if (!is_file($pathFile) || filemtime($pathFile) + 3600 < time()) {
				$this->cronAction();
			}

			echo  file_get_contents(TEMP_PATH . DS . 'Analytics' . DS . $file);
		}

	}

	public function cronAction() {

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		require_once 'StatGa/config.php';

		$u = $this->_googleEmail;
		$id = $this->_googleID;
		$key = $this->_googleAuthKey;

		$datestart = date('Y-m-d', time() - 180 * 24 * 3600);

		$path = TEMP_PATH . DS . 'Analytics' . DS;

		require_once 'StatGa/stat.php';

	}

}