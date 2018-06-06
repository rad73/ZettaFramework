<?php

class Modules_Access_Model_Rules extends Zetta_Db_Table
{
    protected $name = 'access_rules';
    
    public function addRule($resource_name, $role_name, $type)
    {
        $is_allowed = $type == 'allow' ? 1 : 0;
        
        $exist = $this->fetchRow(
        
            $this->select()
            ->where('resource_name = ?', $resource_name)
            ->where('role_name = ?', $role_name)
        );
        
        if (sizeof($exist)) {
            $this->update(array(
                'is_allowed' => $is_allowed
            ), $this->getAdapter()->quoteInto('rule_id = ?', $exist->rule_id));
        } else {
            $this->insert(array(
                'is_allowed' => $is_allowed,
                'resource_name' => $resource_name,
                'role_name' => $role_name,
            ));
        }
    }
    
    public function removeRule($resource_name, $role_name)
    {
        return $this->delete(array(
            $this->getAdapter()->quoteInto('resource_name = ?', $resource_name),
            $this->getAdapter()->quoteInto('role_name = ?', $role_name)
        ));
    }
    
    public function removeRoleRules($role_name)
    {
        return $this->delete(array(
            $this->getAdapter()->quoteInto('role_name = ?', $role_name)
        ));
    }
}
