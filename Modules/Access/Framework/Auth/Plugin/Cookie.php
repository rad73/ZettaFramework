<?php

/**
 * Попытка авторизации с кук
 *
 * @author Александр Хрищанович
 */
class Modules_Access_Framework_Auth_Plugin_Cookie extends Modules_Access_Framework_Auth_Plugin_Session
{
    const NAMESPACE_DEFAULT = 'Zend_Auth';


    public function authenticate()
    {
        if (isset($_COOKIE[self::NAMESPACE_DEFAULT])) {
            $this->getAuth()->setStorage(new Modules_Access_Framework_Auth_Storage_Cookie());
            $authRequest = $this->getAuth()->getStorage()->read();
            
            if (isset($authRequest->username) && isset($authRequest->auth_hash)) {
                $this->setUserName($authRequest->username);
                $this->setHashPassword($authRequest->auth_hash);

                return parent::authenticate();
            }
        }

        return false;
    }
}
