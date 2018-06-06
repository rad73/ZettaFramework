<?php

class Zetta_View_Helper_WrapLink extends Zend_View_Helper_Abstract
{
    protected $_request;

    /**
     * Выделяем ссылки в тексте
     *
     * @param string $text
     * @return string
     */
    public function wrapLink($text)
    {
        $array = array(
            '/((ht|f)tps?:\S*)($|<| )/iU' => '<noindex><a href="$1" rel="nofollow" target="_blank">$1</a></noindex>$3'
        );
        
        return preg_replace(array_keys($array), array_values($array), $text);
    }
}
