<?php

/**
 * Попытка авторизации из $_POST
 * Авторизуем через незашифрованную передачу пароля
 *
 * @author Александр Хрищанович
 *
 * @example
 * $_POST['username'] = 'sample'
 * $_POST['password'] = 'password';
 *
 */
class Modules_Access_Framework_Auth_Plugin_Request extends Modules_Access_Framework_Auth_Plugin_Abstract
{
    public function authenticate()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        if (
            !$request->isGet()
            && ($password = $request->getParam('auth_password'))
            && @($username = $request->getParam('username'))
        ) {
            $this
                ->setUserName($username)
                ->setHashPassword(md5($password));

            return parent::authenticate();
        }

        return false;
    }
}
