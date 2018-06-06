<?php

class Access_Framework_Validator_UniqLogin extends Zend_Validate_Abstract
{
    const ERROR = "'%value%' must be unique username";
 
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique username",
    );
    
    public function isValid($value)
    {
        $isValid = true;
        $model = new Modules_Access_Model_Users();
        
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('login')) {
            return $isValid;
        }
            
        if (sizeof($model->getUser($value))) {
            $this->_error(self::ERROR, $value);
            $isValid = false;
        }

        return $isValid;
    }
}
