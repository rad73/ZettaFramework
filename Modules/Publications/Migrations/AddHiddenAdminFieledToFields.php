<?php

/**
 * Создание поля "hidden_admin" в таблице полей публикаций
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Migrations_AddHiddenAdminFieledToFields extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание поля "hidden_admin" в таблице полей публикаций';

    /**
     * Модель полей
     *
     * @var Modules_Publications_Model_Fields
     */
    protected $_model;

    protected $_nameColumn = 'hidden_admin';


    public function __construct()
    {
        parent::__construct();
        $this->_model = new Modules_Publications_Model_Fields();
    }

    public function up($params = null)
    {
        $this->addColumn($this->_model->info('name'), $this->nameColumn, array(
            'type' => 'char',
            'length' => 1,
            'comment' => 'Не выводить поле администратору',
            'null' => true,
        ));
    }

    public function down($params = null)
    {
        $this->dropColumn($this->_model->info('name'), $this->nameColumn);
    }
}
