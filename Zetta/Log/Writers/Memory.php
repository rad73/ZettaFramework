<?php

class Zetta_Log_Writers_Memory extends Zend_Log_Writer_Mock {
   
    protected static $Events = array();

    public function _write($event) {
        self::$Events[] = $event;
    }

    static public function getEvents() {
        return self::$Events;
    }

}