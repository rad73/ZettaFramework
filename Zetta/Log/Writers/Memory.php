<?php

class Zetta_Log_Writers_Memory extends Zend_Log_Writer_Mock {
   
    protected static $Events = array();

    public function _write($event) {
        self::$Events[] = is_string($event) ? $event : print_r($event, 1);
    }

    static public function getEvents() {
        return self::$Events;
    }

}