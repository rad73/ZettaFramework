<?php

/**
 * Готовим магию Di
 *
 */
class Zetta_Bootstrap_Resource_Di extends Zend_Application_Resource_ResourceAbstract
{
    protected $container;

    public function init()
    {
        $builder = new \DI\ContainerBuilder();
        $builder->useAnnotations(true);
        $this->container = $builder->build();

        $this
            ->saveInRegistry()
        ;

        return $this->container;
    }

    /**
     * Сохраняем объект container в реестре
     * Теперь к нему можно обратиться Zend_Registry::get('container')
     * @return self
     */
    protected function saveInRegistry()
    {
        Zend_Registry::set('container', $this->container);
        return $this;
    }
}
