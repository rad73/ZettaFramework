<?php

class Modules_Guitestcase_AdminController extends Zend_Controller_Action {

	/**
	 * Модель Testcase
	 *
	 * @var Testcase
	 */
	protected $model = null;

	protected $_testCases = array();

	public function init() {
		
		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_guitestcase')) {
			throw new Exception('Access Denied');
		}
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('run', 'html')
        	->addActionContext('runall', 'html')
            ->initContext();

		$this->_testCases = Modules_Guitestcase_Framework_Manager::getInstance()->getTestCaseClasses();

	}

    public function indexAction() {
    	$this->view->testCases = $this->_testCases;
    }
    
    
    public function runAction() {
    	
    	if (
    		($testCase = $this->_getParam('testcase')) && 
    		true == array_key_exists($testCase, $this->_testCases)
    	) {

    		$className = $this->_testCases[$testCase];
			$object = new $className();

			if ($object instanceof PHPUnit_Framework_TestCase) {

				$result = Modules_GuiTestcase_Framework_Manager::getInstance()->run($object);
				$this->view->result = $result;
				$this->view->no_header = $this->hasParam('no_header');

			}
			else {
				throw new System_Exception($testCase . ' is not instance of PHPUnit_Framework_TestCase');
			}
			
		}
    	else {
    		$this->_forward('index');
    	}

    }

    public function runallAction() {
    	$this->indexAction();
    }

}