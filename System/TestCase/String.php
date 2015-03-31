<?php

class System_TestCase_String extends PHPUnit_Framework_TestCase {
	
	public function testStrToLower() {
		
		$this->assertEquals(System_String::StrToLower('Проверка'), 'проверка');
		$this->assertEquals(System_String::StrToLower('ПРОВЕРКА'), 'проверка');
		$this->assertEquals(System_String::StrToLower('проверка'), 'проверка');
		
	}
	
	public function testStrToUpper() {

		$this->assertEquals(System_String::StrToUpper('Проверка'), 'ПРОВЕРКА');
		$this->assertEquals(System_String::StrToUpper('ПРОВЕРКА'), 'ПРОВЕРКА');
		$this->assertEquals(System_String::StrToUpper('проверка'), 'ПРОВЕРКА');
		
	}
	
}