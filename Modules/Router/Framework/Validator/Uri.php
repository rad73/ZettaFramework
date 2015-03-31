<?php

class Router_Framework_Validator_Uri extends Zend_Validate_Abstract {
	
	const ERROR = "'%value%' must be unique subsection";
 
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique subsection",
    );
	
	public function isValid($value) {

		if (Zend_Controller_Front::getInstance()->getRequest()->getParam('route_id')) return true;
		
		$this->_setValue($value);
		$isValid = true;
		
		$model = Modules_Router_Model_Router::getInstance();
		
		$sql = $model->select()
			->where('uri = ?', $value)
			->where('parent_route_id = ?', $_REQUEST['parent_route_id']);

		
		$data = $model->fetchRow($sql);
		
		if (sizeof($data)) {
			$this->_error(self::ERROR);
			 $isValid = false;
		}
		
		return $isValid;
		
	}

}