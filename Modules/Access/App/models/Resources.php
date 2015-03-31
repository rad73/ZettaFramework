<?php

class Modules_Access_Model_Resources extends Zend_Db_Table  {

	protected $_name = 'access_resources';
	
	public function getResources($role_name) {

		if ($role_name == 'superadmin' || (is_array($role_name) && in_array('superadmin', $role_name))) {
			return $this->fetchAll();
		}

		$modelRules = new Modules_Access_Model_Rules();
		
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('resources' => $this->info('name')))
			->join(array('rules' => $modelRules->info('name')), 'resources.resource_name = rules.resource_name', false)
			->where('is_allowed = 1')
			->group('resources.resource_name');
			
		$select = is_string($role_name)
			? $select->where('rules.role_name = ?', $role_name)
			: $select->where('rules.role_name IN (?)', $role_name);
			
		return $this->fetchAll($select);
		
	}

	public function getResource($resource_id) {
		
		return $this->fetchRow($this->select()
			->where('resource_name = ?', $resource_id)
		);
		
	}
	
}