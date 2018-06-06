<?php

/**
 * Миграция по созданию поля в таблицах хранения публикаций
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Migrations_CreatePublicationAbstractFieled extends Modules_Dbmigrations_Framework_Abstract
{
    protected $_comment = 'Создание поля в таблицах хранения публикаций';

    
    public function up($params = false)
    {
        list($table_name, $field_name, $options) = $params;
        $this->addColumn(Modules_Publications_Model_Table::PREFIX_TABLE . $table_name, $field_name, $options);
    }

    public function down($params = false)
    {
        list($table_name, $field_name) = $params;
        $this->dropColumn(Modules_Publications_Model_Table::PREFIX_TABLE . $table_name, $field_name);
    }
}
