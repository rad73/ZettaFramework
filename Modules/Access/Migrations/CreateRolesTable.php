<?php


class Modules_Access_Migrations_CreateRolesTable extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание таблицы ролей';


    public function up($params = false)
    {
        $this->createTable('access_roles', array(
            'name' => array(
                'type' => 'varchar',
                'length' => 32,
                'comment' => 'Уникальное название роли',
                'keys' => array(
                    'p_role' => array('primary' => true),
                    'uniq_role' => array('uniq' => true),
                )
            ),
            'description' => array(
                'type' => 'varchar',
                'length' => 255,
                'comment' => 'Описание роли',
            ),
            'role_parent' => array(
                'type' => 'varchar',
                'length' => 32,
                'comment' => 'Родительская роль',
                'null' => 1,
                'references' => array(
                    'access_roles__access_roles' => array(
                        'table' => 'access_roles',
                        'field' => 'name',
                        'ondelete' => 'CASCADE',
                        'onupdate' => 'CASCADE'
                    )
                ),
            ),
            'sort' => array(
                'type' => 'int',
                'comment' => 'Сортировка родительский ролей',
            ),
        ));
        
        /* добавляем роли по умолчанию */
        $model = new Modules_Access_Model_Roles();
        $model->insert(array(
            'name' => 'superadmin',
            'description' => 'Суперпользователь',
            'sort' => 0,
        ));
        $model->insert(array(
            'name' => 'admin',
            'description' => 'Администратор',
            'role_parent' => 'superadmin',
            'sort' => 1,
        ));
        $model->insert(array(
            'name' => 'user',
            'description' => 'Зарегистрированный пользователь',
            'role_parent' => 'admin',
            'sort' => 1,
        ));
        $model->insert(array(
            'name' => 'guest',
            'description' => 'Гость',
            'role_parent' => 'user',
            'sort' => 1,
        ));
    }

    public function down($params = false)
    {
        $this->dropTable('access_roles');
    }
}
