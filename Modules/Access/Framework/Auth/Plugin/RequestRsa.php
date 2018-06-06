<?php

/**
 * Попытка авторизации с запроса
 *
 * @author Александр Хрищанович
 *
 * @example
 * $_POST['username'] = 'sample'
 * $_POST['auth_hash'] = RSA.encrypt(md5.hex('password'), RSA_public, RSA_module);
 *
 */
class Modules_Access_Framework_Auth_Plugin_RequestRsa extends Modules_Access_Framework_Auth_Plugin_Abstract
{
    const SESSION_RSA_KEYS = 'Zetta_RSA_LOGIN';
    
    protected static $_rsaKeys = null;

    
    public function authenticate()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        if (
            !$request->isGet()
            && ($hash = $request->getParam('auth_hash'))
            && ($username = $request->getParam('username'))
        ) {
            $this
                ->setUserName($username)
                ->setHashPassword($this->_decodePassword($hash));

            $authenticate = parent::authenticate();
            if ($authenticate) {
                $this->_clearSession();

                return $authenticate;
            }
        }

        return false;
    }

    /**
     * Получаем RSA ключи
     *
     * @return object
     */
    public static function getKeys()
    {
        if (null === self::$_rsaKeys) {
            self::$_rsaKeys = (object)System_Rsa_Keygen::Generate();
            
            $self = new self();
            $self->_saveKeysToSession(self::$_rsaKeys);
        }

        return self::$_rsaKeys;
    }

    /**
     * Сохраняем ключи в сессии
     *
     * @param object $keys
     */
    protected function _saveKeysToSession($keys)
    {
        $session = new Zend_Session_Namespace(self::SESSION_RSA_KEYS);
        $session->keys = (object)$keys;
    }

    /**
     * Получаем ключи от сессии
     *
     * @return object
     */
    protected function _getSessionKeys()
    {
        $session = new Zend_Session_Namespace(self::SESSION_RSA_KEYS);
        if (null == $session->keys) {
            throw new Exception('RSA ключи не найдены в сессии');
        }

        return $session->keys;
    }

    /**
     * Чистим сессию от ключей
     *
     */
    protected function _clearSession()
    {
        Zend_Session::namespaceUnset(self::SESSION_RSA_KEYS);
    }

    /**
     * Дешифруем пароль
     *
     * @param string $password
     * @return string
     */
    protected function _decodePassword($password)
    {
        $keys = $this->_getSessionKeys();
        $rsa = new System_Rsa_Rsalib($keys->module, $keys->public, $keys->private);

        return $rsa->decrypt($password);
    }
}
