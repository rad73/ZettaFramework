<?php

class Zetta_Form_Element_ZettaFile extends Zend_Form_Element_File
{

    /**
     * Allow setting the value
     *
     * @param  mixed $value
     * @return Zend_Form_Element_File
     */
    public function setValue($value)
    {
        $this->_value = $value;

        return $this;
    }
    
    public function isValid($value, $context = null)
    {
        if (!$this->isRequired() && !sizeof($_FILES)) {
            return true;
        }
        
        return parent::isValid($value, $context);
    }
}
