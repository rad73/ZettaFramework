<?php

/**
 * Проверка на уникальность типа публикации
 *
 */
class Modules_Publications_Framework_Validator_UniqPublicationType extends Zend_Validate_Abstract {
	
	const ERROR = "'%value%' must be unique type of publications";
 
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique type of publications",
    );
	
	public function isValid($value) {

		if (Zend_Controller_Front::getInstance()->getRequest()->getParam('rubric_id')) return true;	// изменение публикации - валидатор пропускаем
		
		$isValid = true;
		
		if (System_Functions::tableExist(Modules_Publications_Model_Table::PREFIX_TABLE . $value)) {
			$this->_error(self::ERROR, $value);
			$isValid = false;
		}

		return $isValid;
		
	}

}
