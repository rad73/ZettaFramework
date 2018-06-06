<?php

/**
 * Класс для авторизации пользователя, он переопределяет синглтон Zend_Auth в дальнейшем следует его интерфейсу
 *
 * @example
 *
 * Zend_Auth::getInstance()->getIdentity()
 *
 */
class Modules_Access_Framework_Auth extends Zend_Auth
{
    protected static $_bootstraped = false;


    /**
     * Порядок проверки запросов авторизации
     *
     * @var array
     */
    protected $_plugins = array(
        Modules_Access_Framework_Auth_Plugin_Internet::class,
        Modules_Access_Framework_Auth_Plugin_RequestRsa::class,
        Modules_Access_Framework_Auth_Plugin_Request::class,
        Modules_Access_Framework_Auth_Plugin_Session::class,
        Modules_Access_Framework_Auth_Plugin_Cookie::class,
    );

    protected $_userInfo;

    /**
     * Паттерн синглтон
     *
     * @return Modules_Access_Framework_Auth
     */
    public static function getInstance($bootstrap = true)
    {
        if (null === self::$_instance || !self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        if (true === $bootstrap && false === self::$_bootstraped) {
            self::$_instance->bootstrap();
            self::$_bootstraped = true;
        }

        return self::$_instance;
    }

    /**
     * Возвращает объект-хранилище данных по авторизации
     *
     * @return Zend_Auth_Storage_Interface
     */
    public function getStorage()
    {
        if (
            true == Zend_Registry::get('config')->Access->cookie
            && Zend_Controller_Front::getInstance()->getRequest()->getParam('use_cookie')
        ) {
            $this->setStorage(new Modules_Access_Framework_Auth_Storage_Cookie());
        }

        return parent::getStorage();
    }

    /**
     * Попытка авторизации
     *
     * @return bool		возвращает true в случае успеза авторизации
     */
    public function bootstrap()
    {
        foreach ($this->_plugins as $plugin) {
            if (is_object($plugin)) {
                $object = $plugin;
            } elseif (is_string($plugin) && class_exists($plugin)) {
                $object = new $plugin();
            }

            if (
                $object instanceof Modules_Access_Framework_Auth_Plugin_Abstract
                && $object->authenticate()
            ) {
                $this->_saveAuth($object->getResultObject());

                return true;
            }
        }

        return false;
    }

    /**
     * Добавление плагина авторизации
     *
     * @param Modules_Access_Framework_Auth_Plugin_Abstract $name
     */
    public function addPlugin(Modules_Access_Framework_Auth_Plugin_Abstract $name)
    {
        array_push($this->_plugins, $name);

        return $this;
    }

    /**
     * Выход из системы авторизации
     *
     */
    public function logOut()
    {
        $this->clearIdentity();
    }

    public function getUserInfo()
    {
        if ($this->getIdentity()) {
            $model = new Modules_Access_Model_Users();

            return $model->getUser($this->getIdentity()->username);
        }
    }

    /**
     * Безопасное сохранение данных об авторизации
     *
     * @param stdClass $result
     */
    protected function _saveAuth($result)
    {
        Zend_Auth::getInstance()->getStorage()->write($result);
        $this->_userInfo = $result;
    }
}
