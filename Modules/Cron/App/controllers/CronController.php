<?php

use \Jenner\Http;

class Modules_Cron_CronController extends Zend_Controller_Action
{

    /**
     * Модель планировщика
     *
     * @var Modules_Cron_Model_Cron
     */
    protected $_model;

    public function init()
    {
        if (!array_key_exists('SHELL', $_SERVER)) {
            throw new Exception('Access CronController via http');
        }

        $this->_model = new Modules_Cron_Model_Cron();

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        $tasks = $this->_model->fetchAll(
            $sql = $this->_model->select()
                ->where('in_progress IS NULL OR last_run_start < ?', date('Y-m-d H:i:s', strtotime("-10 minute")))
                ->where('active = 1')
        );

        $taskToRun = array();

        foreach ($tasks as $task) {
            if ($task->week_day != '*' && intval($task->week_day) != date('N')) {
                continue;
            }
            if ($task->month != '*' && intval($task->month) != date('n')) {
                continue;
            }
            if ($task->day != '*' && intval($task->day) != date('j')) {
                continue;
            }
            if ($task->hour != '*' && intval($task->hour) != date('G')) {
                continue;
            }
            if ($task->minute != '*' && intval($task->minute) != intval(date('i'))) {
                continue;
            }

            $taskToRun[] = $task;
        }

        self::RunTasks($taskToRun);
    }

    public static function RunTasks(array $tasks)
    {
        ini_set('max_execution_time', 600);

        if (sizeof($tasks)) {
            $model = new Modules_Cron_Model_Cron();
            $async = new Http\Async();
            
            foreach ($tasks as $task) {
                if ($task instanceof Zend_Db_Table_Row) {

                    // Стартуем
                    $time_start = microtime(true);
                    $cron_id = $task->cron_id;

                    $model->update(array(
                        'in_progress' => 1,
                        'last_run_start' => new Zend_Db_Expr('NOW()'),
                        'last_run_finish' => new Zend_Db_Expr('NULL')
                    ), $model->getAdapter()->quoteInto('cron_id = ?', $cron_id));

                    $taskUrl = (stripos($task->task, 'http') === false ? HTTP_HOST : '')  . $task->task . '?secret_key=' . Zend_Registry::get('config')->Db->staticSalt;
                    $taskAsync = Http\Task::createGet($taskUrl);
                    $promise = $async->attach($taskAsync, $cron_id);
                    
                    $promise->then(
                        function ($data) use ($task, $cron_id, $model, $time_start) {
                            $time = microtime(true) - $time_start;
                            
                            if (!$data['error']) {
                                Zend_Registry::get('Logger')->info($str = 'Cron: ' . $task->task  . ' завершено за ' . round($time, 3) . ' сек.');
                            } else {
                                Zend_Registry::get('Logger')->info($str = 'Cron: ' . $task->task  . ' завершено с ошибкой "' . $data['error'] . '"! Завершено за ' . round($time, 3) . ' сек.');
                            }
                            
                            // записываем успешное выполнение
                            $model->update(array(
                                'in_progress' => new Zend_Db_Expr('NULL'),
                                'last_run_finish' => new Zend_Db_Expr('NOW()')
                            ), $model->getAdapter()->quoteInto('cron_id = ?', $cron_id));
                        }
                    );
                }
            }
            
            $async->execute();
        }
    }
}
