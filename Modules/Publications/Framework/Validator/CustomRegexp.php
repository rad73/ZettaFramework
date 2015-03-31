<?php

/**
 * Валидатор для custom полей
 * 
 * Доступные значения - регулярка | email | url
 *
 */
class Publications_Framework_Validator_CustomRegexp extends Zend_Validate_Abstract {
	
    protected $_field;
    
    const ERROR = "'%value%' is not corrected";
    
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' is not corrected",
    );
    
    public function __construct($field) {
    	$this->_field = $field;
    }
	
	public function isValid($value) {
		
		if ($this->_field->type == 'captcha') return true;
		
		switch ($this->_field->validator) {
			case 'email':
					$pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
				break;
			case 'url':
					$pattern = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
				break;
			default:
					$pattern = '/^' . $this->_field->validator . '$/';
				break;
		}
		
		$isValid = true;
		
		if (!preg_match($pattern, $value)) {
			
			$this->_field->errormsg 
				? $this->setMessage($this->_field->errormsg, self::ERROR)
				: false;

			$this->_error(self::ERROR, $value);
			return false;
		}
		
		return $isValid;
		
	}

}