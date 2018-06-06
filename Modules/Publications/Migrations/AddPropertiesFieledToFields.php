<?php

/**
 * Создание поля "property" в таблице полей публикаций
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Migrations_AddPropertiesFieledToFields extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание поля "property" в таблице полей публикаций';

    /**
     * Модель полей
     *
     * @var Modules_Publications_Model_Fields
     */
    protected $_model;

    protected $_nameColumn = 'properties';


    public function __construct()
    {
        parent::__construct();
        $this->_model = new Modules_Publications_Model_Fields();
    }

    public function up($params = null)
    {
        $this->addColumn($this->_model->info('name'), $this->_nameColumn, array(
            'type' => 'text',
            'comment' => 'JSON строка для доп. свойств поля',
            'null' => true,
        ));
    }

    public function down($params = null)
    {
        $this->dropColumn($this->_model->info('name'), $this->_nameColumn);
    }
}
