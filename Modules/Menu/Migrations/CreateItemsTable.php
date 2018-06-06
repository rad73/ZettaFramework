<?php

/**
 * Миграция по созданию таблицы для разделов меню
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Menu_Migrations_CreateItemsTable extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание таблицы разделов меню';

    
    public function up($params = null)
    {
        $this->createTable('menu_items', array(
        
            'item_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'auto_increment' => 1,
            ),
            
            'parent_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'references' => array(
                    'menu_items__menu_items' => array(
                        'table' => 'menu_items',
                        'field' => 'item_id',
                        'ondelete' => 'CASCADE',
                        'onupdate' => 'CASCADE',
                    )
                ),
                'null' => true

            ),
            
            'menu_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'references' => array(
                    'menu_items__menu' => array(
                        'table' => 'menu',
                        'field' => 'menu_id',
                        'ondelete' => 'CASCADE',
                        'onupdate' => 'CASCADE',
                    )
                ),

            ),
        
            'name' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Название меню',
                'null' => true
            ),
            
            'type' => array(
                'type' => 'varchar',
                'length' => 10,
                'comment' => 'Тип раздела (router или external)',
            ),
            
            'route_id' => array(
                'type' => 'int',
                'unsigned' => 1,
                'references' => array(
                    'menu_items__routes' => array(
                        'table' => 'routes',
                        'field' => 'route_id',
                        'ondelete' => 'CASCADE',
                        'onupdate' => 'CASCADE',
                    )
                ),
                'null' => true

            ),
            
            'external_link' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Ссылка на внешний источник',
                'null' => true
            ),
            
            'disable' => array(
                'type' => 'char',
                'length' => 1,
                'comment' => 'Раздел выключен',
                'null' => true,
            ),
            
            'sort' => array(
                'type' => 'int',
                'unsigned' => 1,
                'default' => '0',
            ),
            
        ));
    }

    public function down($params = null)
    {
        $this->dropTable('menu_items');
    }
}
