<?php

/**
 * Миграция по созданию таблицы для редактируемых блоков
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Blocks_Migrations_CreateBlocksTable extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание таблицы для редактируемых блоков';
    protected $_tableName = 'blocks';

    public function up($params = null)
    {
        $this->createTable($this->_tableName, array(
            'block_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'auto_increment' => 1,
            ),
            'block_name' => array(
                'type' => 'varchar',
                'length' => 100,
                'comment' => 'Уникальное имя блока',
            ),
            'content' => array(
                'type' => 'text',
                'comment' => 'Содержимое блока',
            ),
            'route_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'comment' => 'ID маршрута в которой блок переопределён',
                'references' => array(
                    'blocks__routes' => array(
                        'table' => 'routes',
                        'field' => 'route_id',
                        'ondelete' => 'CASCADE',
                        'onupdate' => 'CASCADE'
                    )
                ),
            ),
        ));
        
        $this->createKey($this->_tableName, array('block_name', 'route_id'), 'uniq_block', array(
            'uniq' => true
        ));
    }

    public function down($params = null)
    {
        $this->dropTable($this->_tableName);
    }
}
