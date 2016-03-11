<?php

/**
 * Исправляем ошибку, при которой SaveHandler::write возвращает false
 * и php7 выдает notice
 */
class Zetta_Session_SaveHandler_DbTable extends Zend_Session_SaveHandler_DbTable implements Zend_Session_SaveHandler_Interface {

    public function write($id, $data) {

        parent::write($id, $data);
        return true;

    }

}
