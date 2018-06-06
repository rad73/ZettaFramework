<?php

namespace Zetta;

class Hook extends \Zend_Registry
{
    public static function call($name, $params = false)
    {
        if (self::isRegistered($name)) {
            $funcNames = self::get($name);
            foreach ($funcNames as $func) {
                if (is_callable($func)) {
                    $func($params);
                }
            }
        }
    }

    public static function add($name, $function)
    {
        $funcNames = self::isRegistered($name) ? self::get($name) : [];
        $funcNames[] = $function;
        self::set($name, $funcNames);
    }
}
