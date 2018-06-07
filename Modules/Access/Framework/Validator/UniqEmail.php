<?php

class Modules_Access_Framework_Validator_UniqEmail extends Zend_Validate_Abstract
{
    const ERROR = "'%value%' must be unique E-mail";

    protected $_messageTemplates = array(
        self::ERROR => "'%value%' must be unique E-mail",
    );

    public function isValid($value)
    {
        $isValid = true;
        $model = new Modules_Access_Model_Users();

        $sql = $model->select()->where('email = ?', $value);

        if ($login = Zend_Controller_Front::getInstance()->getRequest()->getParam('login')) {
            $sql = $sql->where('username != ?', $login);
        }

        if (sizeof($model->fetchAll($sql))) {
            $this->_error(self::ERROR, $value);
            $isValid = false;
        }

        return $isValid;
    }
}
