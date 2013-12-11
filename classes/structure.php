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
        return $this->modelStructure->getTreeAsArray();
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
