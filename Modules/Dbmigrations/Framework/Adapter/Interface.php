<?php

interface Modules_Dbmigrations_Framework_Adapter_Interface {

	public function createTable($name, $columns);
	public function dropTable($name);
	public function renameTable($tableName, $newName);
	
	public function createKey($table, $columnName, $keyName, $options = array('uniq'=>false, 'primary'=>false));
	public function	dropKey($table, $key);
	
	public function createForeignKey($table, $columnName, $keyName, $options = array('table'=>null, 'field'=>null, 'ondelete'=>null, 'onupdate'=>null));
	public function dropForeignKey($table, $keyName);

	public function	addColumn($table, $name, $options = array(
			'type'	=> null, 
			'length'	=> null, 
			'unsigned'	=> false, 
			'auto_increment'	=> false,
			'comment'	=> '',
			'default'	=> '',
			'null'	=> false,
	));
	
	public function	dropColumn($table, $name);
	public function	alterColumn($table, $name, $newName, $options = array(
			'type'	=> null, 
			'length'	=> null, 
			'unsigned'	=> false, 
			'auto_increment'	=> false,
			'comment'	=> '',
			'default'	=> '',
			'null'	=> false,
	));
}
