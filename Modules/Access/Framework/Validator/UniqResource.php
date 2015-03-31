<?php

class Access_Framework_Validator_UniqResource extends Zend_Validate_Abstract {
	
	const ERROR = "'%value%' must be unique resource";
 
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique resource",
    );
	
	public function isValid($value) {

		$isValid = true;
		$request = Zend_Controller_Front::getInstance()->getRequest();

		if (
			$request->getParam('resource')
			|| (is_numeric($value) && Zend_Controller_Front::getInstance()->getRequest()->getParam('type') == 'free')
			|| (false == is_numeric($value) && $request->getParam('type') == 'router')
		) {
			return true;
		}

		$resource_name =  (is_numeric($value) ? 'route_' . $value : $value);
		$model = new Modules_Access_Model_Resources();

		if (sizeof($model->getResource($resource_name))) {
			$this->_error(self::ERROR, $resource_name);
			$isValid = false;
		}

		return $isValid;

	}

}