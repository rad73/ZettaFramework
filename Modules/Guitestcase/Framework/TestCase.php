<?php

class Modules_Guitestcase_Framework_TestCase extends Modules_Guitestcase_Framework_TestCase_Abstract
{
    public function runTestCase(PHPUnit_Framework_TestCase $object)
    {
        $xml = parent::runTestCase($object)->getXML();
        $simpleXmlObject = simplexml_load_string($xml);

        return $simpleXmlObject->testsuite->testsuite;
    }
}
