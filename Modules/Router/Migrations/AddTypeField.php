<?php

/**
 * Миграция по добавлению поля type
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Router_Migrations_AddTypeField extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Добавляем поле type';

    /**
     * Модель маршрутизатора
     *
     * @var Modules_Router_Model_Router
     */
    protected $_model;

    protected $_nameColumn = 'type';


    public function __construct()
    {
        parent::__construct();
        $this->_model = new Modules_Router_Model_Router();
    }

    public function up($params = null)
    {
        $this->addColumn($this->_model->info('name'), $this->nameColumn, array(
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'Тип маршрута',
            'null' => true,
            'after' => 'disable',
        ));
    }

    public function down($params = null)
    {
        $this->dropColumn($this->_model->info('name'), $this->nameColumn);
    }
}
