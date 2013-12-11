<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * модель структуры
 */
class Model_ORM_Structure extends ORM_MPTT {

    protected $_table_name  = 'structure';
    protected $_primary_key = 'id';

    /**
     * связь с статьями
     * @var type
     */
    protected $_has_one = array(
        'article' => array(
            'model'       => 'ORM_Articles',
            'foreign_key' => 'parent_id',
        ),
    );

    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Переобъявили, так как по другому перемещение внутри узла я сделать не смог
     */
    protected function lock()
    {
        $q = 'LOCK TABLE ' . $this->_db->quote_table($this->_table_name) . ' WRITE';

        if ($this->_object_name) {
            $q.= ', ' . $this->_db->quote_table($this->_table_name);
            $q.= ' AS ' . $this->_db->quote_column($this->_object_name) . ' WRITE';
        }

        $this->_db->query(NULL, $q, TRUE);
    }

    /**
     * получить нужный формат
     * @param type $dataSet
     * @return int
     */
    public static function getStruct($dataSet)
    {
        //создали элемент для цикла
        $elements = $dataSet;

        $result = array();
        foreach ($elements as $id => $value) {
            if ($value['parent_id'] > 0 && isset($elements[$value['parent_id']])) {

                $elements[$id]['parent']                     = & $elements[$value['parent_id']];
                $elements[$value['parent_id']]['children'][] = & $elements[$id];

                if (isset($elements[$id]['countChildren'])) {
                    $elements[$id]['countChildren'] ++;
                }
                else {
                    $elements[$id]['countChildren'] = 0;
                }
            }
            else {
                $result[$id] = & $elements[$id];
            }
        }

        return $result;
    }

    /**
     * получить с базы дерево
     * @return type
     */
    public function getTreeAsArray()
    {
        $this->clear();

        $struct = $this->fulltree()->as_array();

        $dataSet = array();

        //Пробежались по дереву
        foreach ($struct as $cat) {
            $categ                             = $cat->as_array();
            $categ['visible']                  = $cat->article->visible;
            $dataSet[$categ['id']]             = $categ;
            $dataSet[$categ['id']]['children'] = array();
        }

        return self::getStruct($dataSet);
    }

    /**
     * найти по ид
     * @param type $id
     * @return \Model_ORM_Structure
     */
    public function findById($id)
    {
        return $this->where("id", "=", $id)->find();
    }

    /**
     * добавить элемент
     * @param type $parent_id
     * @return type
     */
    public function addNewElement($parent_id = NULL)
    {
        $this->clear();
        $this->title = "Новое поле";

        if (!$parent_id) {
            $this->make_root();
        }
        else {
            $this->insert_as_last_child($parent_id);
        }

        return $this->id;
    }

}
