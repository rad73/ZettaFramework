<?php

/**
 * Настраиваем autoloader
 *
 */
class Zetta_Bootstrap_Resource_Session extends Zend_Application_Resource_Session {

	public function init() {

		if ($this->getBootstrap()->hasPluginResource('multidb')) {
			$this->getBootstrap()->bootstrap('multidb');
		}
		else {
			$this->getBootstrap()->bootstrap('Db');
		}

		$options = $this->getOptions();

		if (isset($options['saveHandler'])) {

			if ($this->getSaveHandler() instanceof Zend_Session_SaveHandler_DbTable) {

				if (!System_Functions::tableExist($options['saveHandler']['options']['name'])) {
					$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
					$_migrationManager->upTo('System_Migrations_CreateTableSession');
					$this->init();
				}

			}

			parent::init();

		}

	}

}