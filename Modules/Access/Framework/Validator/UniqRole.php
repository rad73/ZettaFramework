<?php

class Access_Framework_Validator_UniqRole extends Zend_Validate_Abstract
{
    const ERROR = "'%value%' must be unique role";
 
    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique role",
    );
    
    public function isValid($value)
    {
        $isValid = true;
        $model = new Modules_Access_Model_Roles();
        
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('role_id')) {
            return $isValid;
        }
            
        if (sizeof($model->getRole($value))) {
            $this->_error(self::ERROR, $value);
            $isValid = false;
        }

        return $isValid;
    }
}
