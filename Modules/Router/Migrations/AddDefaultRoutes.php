<?php

/**
 * Миграция по добавлению роутов по умолчанию
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Router_Migrations_AddDefaultRoutes extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Добавляем роуты по умолчанию';

    
    public function up($params = null)
    {
        $model = new Modules_Router_Model_Router();
        
        $model->insert(array(
            'route_id' => 1,
            'name' => 'Главная страница',
            'uri' => '',
            'module' => 'default',
            'controller' => 'index',
        ));
        
        $model->insert(array(
            'route_id' => 2,
            'parent_route_id' => 1,
            'name' => 'Вход',
            'uri' => 'login',
            'module' => 'access',
            'controller' => 'login',
        ));
    }

    public function down($params = null)
    {
    }
}
