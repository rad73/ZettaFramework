<?php

class Modules_Blocks_Model_Blocks extends Zetta_Db_Table
{

    /**
     * Имя таблицы в которое содержаться блоки
     *
     * @var string
     */
    protected $_name = 'blocks';

    /**
     * Массив блоков на текущей странице
     *
     * @var Zend_Db_Table_Rowset
     */
    protected static $_fullData = null;

    /**
     * ID текущего маршрута
     *
     * @var int
     */
    protected $_currentRouteId = 1;


    public function __construct($config = array(), $definition = null)
    {
        parent::__construct($config, $definition);

        $currentRoute = Modules_Router_Model_Router::getInstance()->current();
        $this->_currentRouteId = $currentRoute['route_id'];
    }

    /**
     * Получаем информацию по блоку на текущей странице
     *
     * @param string $blockName		имя блока
     * @return Zend_Db_Row
     */
    public function getBlock($blockName, $inherit = true)
    {
        foreach ($this->fetchFull() as $i => $row) {
            if ($row->block_name == $blockName) {
                if (true == $inherit) {
                    return $row;
                } elseif ($this->_currentRouteId == $row->route_id) {
                    return $row;
                }
            }
        }
    }

    /**
     * Удаляем содержимое блока
     *
     * @param string $blockName		имя блока
     * @param int $route_id			ID маршрута к которому прикреплён блок
     */
    public function deleteBlock($blockName, $route_id = 1)
    {
        $block = $this->fetchRow(
            $this->select()
            ->where('block_name = ?', $blockName)
            ->where('route_id = ?', $route_id)
        );

        if ($block) {
            $this->delete($this->getAdapter()->quoteInto('block_id = ?', $block->block_id));
        }
    }

    /**
     * Сохраняем содержимое блока
     *
     * @param string $blockName		Имя блока
     * @param string $content		Сожержимое
     * @param int $route_id			ID маршрута к которому прикреплён блок
     */
    public function save($blockName, $content, $route_id = 1)
    {
        $array = array(
            'block_name' => $blockName,
            'content' => $content,
            'route_id' => $route_id
        );

        $inDb = $this->fetchRow(
            $this->select()
            ->where('block_name = ?', $blockName)
            ->where('route_id = ?', $route_id)
        );

        if ($inDb) {
            $this->update($array, $this->getAdapter()->quoteInto('block_id = ?', $inDb->block_id));
        } else {
            $this->insert($array);
        }
    }

    public function fetchFull()
    {
        if (null === self::$_fullData) {
            $select = $this->select()
                ->where('route_id = 1')
                ->order('route_id DESC');

            if ($this->_currentRouteId && $this->_currentRouteId != 1) {
                $select = $select
                    ->orWhere('route_id = ?', $this->_currentRouteId);
            }

            self::$_fullData = $this->fetchAll($select);
        }

        return self::$_fullData;
    }
}
