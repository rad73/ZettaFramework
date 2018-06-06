<?php
use morphos\Russian\NounPluralization;

class Zetta_View_Helper_Declension extends \Zend_View_Helper_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function declension(int $number, string $word, $animateness = false)
    {
        return NounPluralization::pluralize($number, $word, $animateness);
    }
}
