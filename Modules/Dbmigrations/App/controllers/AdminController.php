<?php

class Modules_Dbmigrations_AdminController extends Zend_Controller_Action {

	protected $_manager;


	public function init() {
		
		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_dbmigrations')) {
			throw new Exception('Access Denied');
		}
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('info', 'html')
            ->initContext();
		
		$this->_manager = new Modules_Dbmigrations_Framework_Manager();

	}

    public function indexAction() {

    	$this->view->migrations = $this->_manager->getMigrationClasses();

    	try {
    		$currentBranch = $this->_manager->getCurrentBranch();
    	}
    	catch (Exception $e) {
    		$this->_manager->upTo('Dbmigrations_Migrations_CreateTableHistory');
    		$currentBranch = $this->_manager->getCurrentBranch();
    	}

    	$array = array();
    	foreach ($this->_manager->getCurrentBranch() as $item) {
    		array_push($array, $item['class_name']);
    	}
    	
    	$this->view->currentClassNames = $array;
    	
    }

    public function dataAction() {

    	$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch
			->addActionContext('data', 'json')
			->initContext();

		$this->view->Dbmigrations = array(
			'master'	=> $this->_manager->getMasterBranch(),
			'current'	=> $this->_manager->getCurrentBranch()
		);
	
	}

	public function infoAction() {
		
		$className = $this->_getParam('class');
		
		$migration = new $className;
		
		$this->view->class_name = $className;
		$reflector = new ReflectionClass($className);
		$file = $reflector->getFileName();
		
		$this->view->file = $file;
		$this->view->source = highlight_file($file, true);
		$this->view->comment = $migration->getComment();
		
		
	}

	public function upAction() {
		
		$className = $this->_getParam('class');
		$this->_manager->upTo($className);

		$this->_redirect(Zend_Registry::get('http_referer'), array('prependBase' => false));
		
	}

	public function downAction() {
		
		$className = $this->_getParam('class');
		$this->_manager->downTo($className);

		$this->_redirect(Zend_Registry::get('http_referer'), array('prependBase' => false));
		
	}

	public function tomasterAction() {
		$this->_manager->setCurrentToMaster();
		
		$this->_redirect(Zend_Registry::get('http_referer'), array('prependBase' => false));

	}

	public function chainbreakAction() {
		$className = $this->_getParam('class');
		$this->_manager->chainBreak($className);
		
		$this->_redirect(Zend_Registry::get('http_referer'), array('prependBase' => false));

	}

}