<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Description of structure
 *
 * @author seyfer
 */
class Structure
{

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

    public function getTreeAsArray($scope = null, $fromLevel = null)
    {
        return $this->modelStructure->getFullTreeAsArray($scope, $fromLevel);
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

    public function getTreeFromLevel($level)
    {
        return $this->modelStructure->getNodesFromLevel($level);
    }

    public function getTreeFromLevelAsArray($level)
    {
        $tree = $this->getTreeFromLevel($level);

        return $this->modelStructure->prepareStructure($tree);
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function getTreeByNodeNameAsArray($name)
    {
        $tree = $this->getTreeByNodeName($name);

        return $this->modelStructure->prepareStructure($tree);
    }

    /**
     * получить дерево по корневому узлу
     * @param type $name
     */
    public function getTreeByNodeName($name)
    {
        $node = $this->getNodeByName($name);

        $tree = $this->modelStructure->getTreeByNode($node);

        return $tree;
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function getNodeByName($name)
    {
        return $this->modelStructure->getNodeByName($name);
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

        $routeMedia        = Route::get('structure/media');
        $boostrapFixJsPath = $routeMedia->uri(array('file' => 'js/bootstrap-fix.js'));

        $structureList = View::factory($tplPath)
                ->set('structure', $structure)
                ->set("boostrapFixJsPath", $boostrapFixJsPath)
                ->render();

        return $structureList;
    }

}
