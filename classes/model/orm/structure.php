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
     * фикс для к3.2 +
     */
    protected function lock()
    {
        $query = 'LOCK TABLE ' . $this->_db->quote_table($this->_table_name) . ' WRITE';

        if ($this->_object_name) {
            $query.= ', ' . $this->_db->quote_table($this->_table_name);
            $query.= ' AS ' . $this->_db->quote_column($this->_object_name) . ' WRITE';
        }

        $this->_db->query(NULL, $query, TRUE);
    }

    /**
     * получить нужный формат
     * @param type $dataSet
     * @return int
     */
    protected function formStructure($dataSet)
    {
        //создали элемент для цикла
        $elements = $dataSet;

        $result = array();
        foreach ($elements as $id => $value) {
            if ($value['parent_id'] > 0 &&
                    isset($elements[$value['parent_id']])) {

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
    public function getFullTreeAsArray()
    {
        $this->clear();

        $struct = $this->getFullTree();

        return $this->prepareStructure($struct);
    }

    /**
     * полное дерево ORM_MPTT
     * @return type
     */
    public function getFullTree()
    {
        return $this->fulltree()->as_array();
    }

    /**
     * главные узлы ORM_MPTT
     * @return type
     */
    public function getRoots()
    {
        $this->clear();

        return $this->roots()->as_array();
    }

    /**
     * получить детей
     * @return type
     */
    public function getChildrens()
    {
        return $this->children()->as_array();
    }

    /**
     * получить по узлу выборку
     * дерева
     * @param type $root
     * @return type
     */
    public function getTreeByNode($root)
    {
//        Debug::vars(__METHOD__);

        $tree   = [];
        $tree[] = $root;

        $fullRootTree = $this->getTreeByNodeRecursive($root, $tree);

        return $fullRootTree;
    }

    /**
     * вывести заголовки для дебага
     * @param type $tree
     */
    public function treeDebugTitle($tree)
    {
        foreach ($tree as $each) {
            Debug::vars($each->title);
        }
    }

    /**
     * выбирает дерево для узла
     * @param type $node
     * @param type $tree
     * @return type
     */
    public function getTreeByNodeRecursive($node, $tree = array())
    {
        if ($node->has_children()) {

            $childrens = $node->getChildrens();
            $tree      = array_merge($tree, $childrens);

            foreach ($childrens as $child) {
                $tree = $this->getTreeByNodeRecursive($child, $tree);
            }
        }

        return $tree;
    }

    /**
     * получить корневые узлы
     * @return type
     */
    public function getRootsAsArray()
    {
        $struct = $this->getRoots();

        return $this->prepareStructure($struct);
    }

    /**
     * подготовить формат массива
     * @param type $struct
     * @return type
     */
    public function prepareStructure($struct)
    {
        $dataSet = $this->prepareDataSet($struct);

        return $this->formStructure($dataSet);
    }

    /**
     * подготовка данных с настройками
     * @param type $struct
     * @return array
     */
    protected function prepareDataSet($struct)
    {
        foreach ($struct as $cat) {
            $categ                             = $cat->as_array();
            $categ['visible']                  = $cat->article->visible;
            $categ['link']                     = $cat->article->link;
            $dataSet[$categ['id']]             = $categ;
            $dataSet[$categ['id']]['children'] = array();
        }

        return $dataSet;
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
