<?php

/**
 * Миграция по созданию таблицы для маршрутизатора
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Router_Migrations_CreateRoutesTable extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание таблицы роутинга';

    
    public function up($params = null)
    {
        $this->createTable('routes', array(
        
            'route_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'auto_increment' => 1,
            ),
        
            'name' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Название маршрута',
            ),
            
            'parent_route_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'comment' => 'ID родителя',
                'null' => true,
                'references' => array(
                    'routes__routes' => array(
                        'table' => 'routes',
                        'field' => 'route_id',
                        'ondelete' => 'CASCADE',
                        'onupdate' => 'CASCADE'
                    )
                ),
            ),
            
            'uri' => array(
                'type' => 'varchar',
                'length' => 50,
                'comment' => 'URI раздела',
            ),
            
            'disable' => array(
                'type' => 'char',
                'length' => 1,
                'comment' => 'Раздел выключен',
                'null' => true,
            ),
            
            'module' => array(
                'type' => 'varchar',
                'length' => 25,
                'comment' => 'Имя модуля (module)',
            ),
            
            'controller' => array(
                'type' => 'varchar',
                'length' => 50,
                'comment' => 'Имя контроллера (controller)',
            ),
            
            'action' => array(
                'type' => 'varchar',
                'length' => 50,
                'comment' => 'Имя действия (action)',
                'null' => 1,
            ),
            
            'parms' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Параметры для выполнения (params)',
                'null' => 1,
            ),
            
            'sort' => array(
                'type' => 'int',
                'unsigned' => 1,
                'null' => 1,
            )
            
        ));
    }

    public function down($params = null)
    {
        $this->dropTable('routes');
    }
}
