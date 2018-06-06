<?php

/**
 * Расширяем стандартный Db
 *
 */
class Zetta_Bootstrap_Resource_Db extends Zend_Application_Resource_Db
{
    protected $_db;

    public function init()
    {
        if ($this->getBootstrap()->hasPluginResource('multidb')) {
            return $this->getBootstrap()->bootstrap('multidb');
        }

        $this->_db = parent::init();

        if (null != $this->_db) {
            $this
                ->_saveInRegistry()
                ->_saveConfigRegistry()
                ->_registerSqliteFunctions();
        }

        return $this->_db;
    }

    /**
     * Сохраняем объект бд в реестре
     * Теперь к нему можно обратиться Zend_Registry::get('db')
     */
    protected function _saveInRegistry()
    {
        Zend_Registry::set('db', $this->_db);

        return $this;
    }

    /**
     * Сохраняем конфиг БД в реестре
     * Теперь к нему можно обратиться Zend_Registry::get('config')->Db
     */
    protected function _saveConfigRegistry()
    {
        Zend_Registry::get('config')->Db = (object)$this->getDbAdapter()->getConfig();

        return $this;
    }

    protected function _registerSqliteFunctions()
    {
        if ($this->_db instanceof Zend_Db_Adapter_Pdo_Sqlite) {
            $this->_db->getConnection()->sqliteCreateFunction('md5', 'md5', 1);
        }
    }
}
