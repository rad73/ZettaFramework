<?php

class Modules_Access_Framework_Validator_RePassword extends Zend_Validate_Abstract
{
    const ERROR = 'password not equal';

    protected $_messageTemplates = array(
        self::ERROR => "password not equal",
    );

    public function isValid($value)
    {
        $password = Zend_Controller_Front::getInstance()->getRequest()->getParam('password');

        if ($value != $password) {
            $this->_error(self::ERROR);

            return false;
        } else {
            return true;
        }
    }
}
