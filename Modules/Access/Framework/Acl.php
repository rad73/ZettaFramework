<?php

/**
 * Класс надстройка над стандартным Zend_Acl
 * @author Александр Хрищанович
 * @example
 *
 * Modules_Access_Framework_Acl::getInstance()->isAllowed($resource_id, (allow|deny))	// проверка доступа, если ресурс не найден возвращается второй параметр
 *
 */
class Modules_Access_Framework_Acl extends Zend_Acl
{
    const GUEST_GROUP = 'guest';

    protected static $_instance;
    protected static $_bootstraped = false;

    protected $_modelRoles;
    protected $_modelRules;
    protected $_modelResources;

    /**
     * Синглтон
     *
     * @return Modules_Access_Framework_Acl
     */
    public static function getInstance($bootstrap = true)
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        if (true === $bootstrap && false === self::$_bootstraped) {
            self::$_bootstraped = self::$_instance->bootstrap();
        }

        return self::$_instance;
    }

    public static function resetInstance()
    {
        self::$_instance == null;
        self::getInstance()->removeAll();
        self::getInstance()->removeRoleAll();
        self::getInstance()->bootstrap();
    }

    protected function __construct()
    {
        $this->_modelRoles = new Modules_Access_Model_Roles();
        $this->_modelRules = new Modules_Access_Model_Rules();
        $this->_modelResources = new Modules_Access_Model_Resources();
    }

    public function bootstrap()
    {
        $this
            ->_initRoles()
            ->_initRules();
            
        return true;
    }

    /**
     * Адаптируем isAllowed под "разрешение по умолчанию"
     *
     * @param string $resource
     * @param string $allowedIfNotFound	(allow|deny)	- если ресурс не найден и параметр allow, то доступ к ресурсу есть
     * @return bool
     */
    public function isAllowed($resource = false, $allowedIfNotFound = 'deny', $false = false)
    {
        $userGroup = $this->getMyGroup();

        if ($userGroup == 'superadmin') {
            return true;
        }

        if ($this->has($resource)) {
            return parent::isAllowed($userGroup, $resource);
        }

        if ($allowedIfNotFound == 'allow') {
            return true;
        }

        return false;
    }

    public function isAllowedGroup($role = null, $resource = null, $privilege = null)
    {
        return parent::isAllowed($role, $resource, $privilege);
    }

    public function isInheritRule($role, $resourse)
    {
        $row = $this->_modelRules->fetchRow(
            $this->_modelRules->select()
            ->where('resource_name = ?', $resourse)
            ->where('role_name = ?', $role)
        );

        return sizeof($row) ? false : true;
    }

    /**
     * Инициализируем роли
     *
     * @param array $roles
     * @return self
     */
    protected function _initRoles($roles = false)
    {
        if (!$roles) {
            $roles = $this->_modelRoles->fetchAll()->toArray();
            $roles = System_Functions::toForest($roles, 'name', 'role_parent');
        }

        foreach ($roles as $role) {
            if (false == $this->hasRole($role['name'])) {
                $this->addRole(new Zend_Acl_Role($role['name']), $role['role_parent'] ? $role['role_parent'] : null);
            }

            if (sizeof($role['childs'])) {
                $this->_initRoles($role['childs']);
            }
        }

        return $this;
    }

    /**
     * Инициализируем правила
     *
     * @return self
     */
    protected function _initRules()
    {
        $resources = $this->_modelResources->fetchAll();
        $rules = $this->_modelRules->fetchAll();

        foreach ($resources as $resource) {
            if (false == $this->has($resource->resource_name)) {
                $this->add(new Zend_Acl_Resource($resource->resource_name));
            }
        }

        if (sizeof($rules)) {
            foreach ($rules as $rule) {
                $rule->is_allowed
                    ? $this->allow($rule->role_name, $rule->resource_name)
                    : $this->deny($rule->role_name, $rule->resource_name);
            }
        }

        return $this;
    }

    public function getMyGroup()
    {
        $auth = Zend_Auth::getInstance()->getIdentity();

        if ($auth) {
            return $auth->role_name;
        } else {
            return self::GUEST_GROUP;
        }
    }

    /**
     * Выборка доступных ролей у пользователя в виде дерева
     *
     * @return array()
     */
    public function getAccepdedRolesTree($roles = false)
    {
        $currentRoles = $this->getMyGroup();
        $return = array();

        if (!$roles) {
            $roles = $this->_modelRoles->fetchAll()->toArray();
            $roles = System_Functions::toForest($roles, 'name', 'role_parent');
        }

        foreach ($roles as $key => $tree) {
            if ($key == $currentRoles) {
                return $tree['childs'];
            } elseif (sizeof($tree['childs'])) {
                return $this->getAccepdedRolesTree($tree['childs']);
            }
        }

        return $return;
    }

    /**
     * Выборка доступных ролей у пользователя
     *
     * @return array()
     */
    public function getAccepdedRoles($roles = false)
    {
        if (!$roles) {
            $roles = $this->getAccepdedRolesTree();
        }

        $return = array();
        foreach ($roles as $role_name => $role) {
            array_push($return, $role_name);

            if (sizeof($role['childs'])) {
                $return = array_merge($return, $this->getAccepdedRoles($role['childs']));
            }
        }

        return $return;
    }

    /**
     * Выборка доступных ролей у пользователя в виде хеша (для select)
     *
     * @return array()
     */
    public function getAccepdedRolesHash($roles = false, $level = 0)
    {
        if (!$roles) {
            $roles = $this->getAccepdedRolesTree();
        }

        $return = array();
        foreach ($roles as $role_name => $role) {
            $return[$role_name] = str_repeat('-', $level) . ' ' . $role_name;

            if (sizeof($role['childs'])) {
                $return = $return + $this->getAccepdedRolesHash($role['childs'], $level + 1);
            }
        }

        return $return;
    }

    public function getParentRole($role)
    {
        $return = $this->_getRoleRegistry()->getParents($role);
        list($role_name) = array_keys($return);

        return $role_name;
    }
}
