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

    public function getTreeByRootName($name)
    {
        $struct = $this->modelStructure->getRoots();

        $tree = [];
        foreach ($struct as $root) {

            if ($root->title != $name) {
                continue;
            }

//            Debug::vars($root);

            $tree[] = $root;

            $leaves = $root->children()->as_array();

//            Debug::vars($leaves);

            $tree = array_merge($tree, $leaves);
        }

        Debug::vars($tree);

        Debug::vars($this->modelStructure->prepareStructure($tree));
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

}
