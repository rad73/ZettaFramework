<?php

class Zetta_View_Helper_FormSelect extends Zend_View_Helper_FormSelect
{
    public function formSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n")
    {
        if ($value) {
            if (is_null($attribs)) {
                $attribs = array();
            }

            $attribs['data-init_value'] = $value;
        }

        return parent::formSelect($name, $value, $attribs, $options, $listsep);
    }
}
