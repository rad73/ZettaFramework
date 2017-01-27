<?php

/**
 * Проверка на уникальность поля
 *
 */
class Modules_Publications_Framework_Validator_UniqField extends Zend_Validate_Abstract {
	
	const ERROR = "'%value%' must be unique of type publications";
 
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique of type publications",
    );
	
	public function isValid($value) {

		if (Zend_Controller_Front::getInstance()->getRequest()->getParam('field_id')) return true;	// изменение публикации - валидатор пропускаем
		
		$isValid = true;
		
		$modelFields = new Modules_Publications_Model_Fields();
		$rubric_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('rubric_id');
		
		$field = $modelFields->findFiled($value, $rubric_id);
		
		if (sizeof($field)) {
			$this->_error(self::ERROR, $value);
			$isValid = false;
		}

		return $isValid;
		
	}

}
