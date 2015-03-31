<?php

class Modules_Dbmigrations_TestCase_Manager extends PHPUnit_Framework_TestCase {

	protected $_object;


	public function setUp() {
		$this->_object = new Modules_Dbmigrations_Framework_Manager();
	}

	public function tearDown() {
		unset($this->_object);
	}

	public function testGetMigrationClasses() {

		$classes = $this->_object->getMigrationClasses();
		
		$this->assertTrue(is_array($classes));
		$this->assertContains('Dbmigrations_Migrations_CreateTableHistory', $classes);

	}

	public function testGetCurrentBranch() {

		$current = $this->_object->getCurrentBranch();
		
		$this->assertTrue(is_array($current));

	}


	public function testGetMasterBranch() {

		$master = $this->_object->getMasterBranch();
		
		$this->assertTrue(is_array($master));

	}

	public function testUpTo() {
		$this->_object->upTo('Temp_Migration');
	}
	
	public function testDownTo() {
		$this->_object->downTo('Temp_Migration');
	}

}

class Temp_Migration extends Dbmigrations_Framework_Abstract {

	public function up($params = array()) {
		$this->createTable('temp_migration', array(
			'id'	=> array(
				'type'	=> 'int',
				'comment'	=> 'Test field'
			)
		));
		
		
		$this->addColumn('temp_migration', 'ref_id', array(
			'type'	=> 'int',
			'null'	=> true
		));

		$this->createKey('temp_migration', 'id', 'p_key', array('uniq' => true));

		$this->createForeignKey('temp_migration', 'ref_id', 'ref_id_fk', array(
			'table'	=> 'temp_migration',
			'field'	=> 'id',
			'ondelete'	=> 'SET NULL',
			'onupdate'	=> 'CASCADE'
		));
		
	}

	public function down($params = array()) {

		$this->renameTable('temp_migration', 'temp_rename_migration');
		$this->dropForeignKey('temp_rename_migration', 'ref_id_fk');
		$this->dropKey('temp_rename_migration', 'p_key');
		$this->dropColumn('temp_rename_migration', 'ref_id');
		$this->dropTable('temp_rename_migration');

	}
}