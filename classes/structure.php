<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Description of structure
 *
 * @author seyfer
 */
class Structure {

    /**
     *
     * @var \Model_ORM_Structure
     */
    private $modelStructure;

    public function __construct()
    {
        $this->modelStructure = new Model_ORM_Structure();
    }

    public function addRoot()
    {
        return $this->modelStructure->make_root();
    }

    public function getTreeAsArray()
    {
        return $this->modelStructure->getFullTreeAsArray();
    }

    public function getRootsAsArray()
    {
        return $this->modelStructure->getRootsAsArray();
    }

    public function getRootsByNameAsArray($name)
    {
        $roots = $this->getRootsByName($name);

        return $this->modelStructure->prepareStructure($roots);
    }

    public function getRootsByName($name)
    {
        $struct = $this->modelStructure->getRoots();

        $filtered = [];
        foreach ($struct as $each) {
            if ($each->title == $name) {
                $filtered[] = $each;
            }
        }

        return $filtered;
    }

    /**
     * получить дерево по корневому узлу
     * @param type $name
     */
    public function getTreeByRootName($name)
    {
        $struct = $this->getRootsByName($name);

        $tree = [];
        foreach ($struct as $root) {
            $tree = $this->modelStructure->getTreeByNode($root);
        }

//        $this->modelStructure->treeDebugTitle($tree);
//        Debug::vars($this->modelStructure->prepareStructure($tree));

        return $tree;
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function getTreeByRootNameAsArray($name)
    {
        $tree = $this->getTreeByRootName($name);

        return $this->modelStructure->prepareStructure($tree);
    }

    /**
     *
     * @param type $id
     */
    public function delete($id)
    {
        $this->modelStructure->clear();

        $link = $this->modelStructure
                ->findById($id);

        if ($link->loaded()) {
            return $link->delete();
        }
    }

    /**
     *
     * @param type $structure
     * @param string $tplPath
     * @return type
     */
    public function renderWithTpl($structure, $tplPath)
    {

        if (!$tplPath) {
            $tplPath = 'structure/index/list.tpl';
        }

        $structureList = View::factory($tplPath)
                ->set('structure', $structure)
                ->render();

//        Debug::vars(__METHOD__, $structure);

        return $structureList;
    }

}
