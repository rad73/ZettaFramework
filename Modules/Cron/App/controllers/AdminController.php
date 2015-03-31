<?php

class Modules_Cron_AdminController extends Zend_Controller_Action {

	/**
	 * Модель планировщика
	 *
	 * @var Modules_Cron_Model_Cron
	 */
	protected $_model;
	
	public function init() {

		if (false == Zetta_Acl::getInstance()->isAllowed('admin_module_cron')) {
			throw new Exception('Access Denied');
		}
		
		$this->_model = new Modules_Cron_Model_Cron();
		
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('index', 'html')
        	->addActionContext('add', 'html')
        	->addActionContext('delete', 'json')
        	->addActionContext('run', 'json')
        	->addActionContext('stop', 'json')
            ->initContext();
            
	}
	
	public function indexAction() {
		
		$this->view->tasks = $this->_model->fetchAll();

	}
	
	public function deleteAction() {
		
		if ($this->getRequest()->isPost()) {
			$this->_model->delete($this->_model->getAdapter()->quoteInto('cron_id = ?', $this->getParam('cron_id')));
			$this->view->clearVars();
		}
		
	}
	
	public function stopAction() {
		
		if ($this->getRequest()->isPost()) {
			
			$this->_model->update(array(
				'in_progress'	=> new Zend_Db_Expr('NULL'),
				'last_run_finish'	=> new Zend_Db_Expr('NULL'),
			),$this->_model->getAdapter()->quoteInto('cron_id = ?', $this->getParam('cron_id')));
			
			$this->view->clearVars();

		}
		
	}
	
	public function runAction() {
		
		if ($this->getRequest()->isPost()) {
			
			$task = $this->_model->fetchRow(
				$sql = $this->_model->select()
					->where('cron_id = ?', $this->getParam('cron_id'))
			);
			
			require_once 'CronController.php';
			Modules_Cron_CronController::RunTasks(array($task));
			
			$this->view->clearVars();
			
		}
		
	}

	public function addAction() {
		
		$form = new Zetta_Form(Zend_Registry::get('config')->Cron->form->task);
		
		/* заполняем выпадающие списки данными */
		$minute = $form->getElement('minute');
		for ($i = -1; $i <= 59; $i++) {
			($i == -1)
				? $minute->addMultiOption('*', '*')
				: $minute->addMultiOption($i, $i);
		}
		
		$hour = $form->getElement('hour');
		for ($i = -1; $i <= 23; $i++) {
			($i == -1)
				? $hour->addMultiOption('*', '*')
				: $hour->addMultiOption($i, sprintf('%02d', $i));
		}
		
		$day = $form->getElement('day');
		for ($i = 0; $i <= 31; $i++) {
			($i == 0)
				? $day->addMultiOption('*', '*')
				: $day->addMultiOption($i,  sprintf('%02d', $i));
		}
		
		$month = $form->getElement('month');
		for ($i = 0; $i <= 12; $i++) {
					
			if ($i == 0) {
				$month->addMultiOption('*', '*');
			}
			else {
				$month_str = Zend_Locale_Data::getContent(new Zend_Locale(), 'month', array('gregorian', 'stand-alone', 'wide', intval($i)));
				$month->addMultiOption($i, $month_str);
			}

		}
		
		$week = $form->getElement('week_day');
		$array_weekDay = array('', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
		for ($i = 0; $i <= 7; $i++) {
					
			if ($i == 0) {
				$week->addMultiOption('*', '*');
			}
			else {
				$week_str = Zend_Locale_Data::getContent(new Zend_Locale(), 'day', array('gregorian', 'format', 'wide', $array_weekDay[$i]));
				$week->addMultiOption($i, $week_str);
			}

		}
		
		
		if ($cron_id = $this->getParam('cron_id')) {
			$this->view->cron_id = $cron_id;
			$editData = $this->_model->fetchRow($this->_model->select()->where('cron_id = ?', $cron_id))->toArray();
			$form->setDefaults($editData);
		}
		
		if (!sizeof($_POST) || !$form->isValid($_POST)) {
		    $this->view->form = $form;
		}
		else {

			$arrayData = array(
				'minute'	=> $form->getValue('minute'),
				'hour'		=> $form->getValue('hour'),
				'day'		=> $form->getValue('day'),
				'month'		=> $form->getValue('month'),
				'week_day'		=> $form->getValue('week_day'),
				'task'		=> $form->getValue('task'),
				'active'	=> (bool)$form->getValue('active') == true ? '1' : new Zend_Db_Expr('NULL'),
			);
			
			if ($cron_id) {
				$this->_model->update($arrayData, $this->_model->getAdapter()->quoteInto('cron_id = ?', $cron_id));
			}
			else {
				$this->_model->insert($arrayData);
			}
			
			$this->renderScript('admin/addComplete.ajax.phtml');

		}
		
	}
	
}