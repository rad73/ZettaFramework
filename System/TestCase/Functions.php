<?php

class System_TestCase_Functions extends PHPUnit_Framework_TestCase {
	
	public function testFile2Class() {
		
		$this->assertEquals('System_TestCase_Functions', System_Functions::File2Class(__FILE__));
		$this->assertFalse(System_Functions::File2Class(FILE_PATH . 'const.php'));
		
	}
	
	public function testClass2File() {
		
		$this->assertEquals('System/TestCase/Functions.php', System_Functions::Class2File('System_TestCase_Functions'));
		
	}
	
}