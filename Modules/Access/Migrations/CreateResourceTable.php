<?php

/**
 * Миграция по созданию таблицы ресурсов
 *
 */
class Modules_Access_Migrations_CreateResourceTable extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание таблицы ресурсов';


    public function up($params = false)
    {
        $this->createTable('access_resources', array(
            'resource_name' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Идентификатор ресурса',
                'keys' => array(
                    'p_name' => array('primary' => true),
                    'uniq_name' => array('uniq' => true),
                )
            ),
            'description' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Описание правила',
            ),
        ));
    }

    public function down($params = false)
    {
        $this->dropTable('access_resources');
    }
}
