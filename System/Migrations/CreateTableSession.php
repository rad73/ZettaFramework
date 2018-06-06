<?php

class System_Migrations_CreateTableSession extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание таблицы сессий';
    protected $_tableName = 'sessions';

    public function up($params = null)
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'char',
                'length' => '32',
                'comment' => 'Уникальный идентификатор сессии',
                'keys' => array(
                    'session_uniq' => array('uniq' => 1),
                ),
            ),
            'modified' => array(
                'type' => 'int',
                'length' => '11',
                'comment' => 'Дата когда сессия была создана',
            ),
            'lifetime' => array(
                'type' => 'int',
                'length' => '11',
                'comment' => 'Время жизни сессии',
            ),
            'data' => array(
                'type' => 'text',
                'comment' => 'Сессионные данные',
            )
        ));
    }

    public function down($params = null)
    {
        $this->dropTable($this->_tableName);
    }
}
