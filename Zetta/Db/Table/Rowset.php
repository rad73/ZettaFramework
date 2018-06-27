<?php

class Zetta_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    /**
     * Преобразуем массив данных в массив объектов
     * @return array
     */
    public function toArrayObjects()
    {
        $rowArray = array();
        foreach ($this as $row) {
            $rowArray[] = $row;
        }
        return $rowArray;
    }
}
