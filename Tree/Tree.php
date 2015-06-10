<?php


namespace Tree;

include 'Tree/TreeItem.php';
use Tree\TreeItem;

class Tree extends TreeItem
{

    /**
     * @var array
     */
    protected $list = array();


    public function __construct()
    {
        $root = array(
            $this->idName => 0,
            $this->parentIdName => 0,
            $this->labelName => 'root'
        );
        parent::__construct($root);
    }

    /**
     * @param array $tree
     * @return array|null
     */
    public function organizeTree(array $tree)
    {
        $menuList = $return = array();
        if (sizeof($tree)) {
            foreach ($tree as $key => $value) {
                if (!array_key_exists($this->idName, $value) ||
                    !array_key_exists($this->parentIdName, $value))
                    continue;
                $id = $value[$this->idName];
                $parentId = $value[$this->parentIdName];

                if (!array_key_exists($id, $menuList)) {
                    $menuList[$id] = $value;
                }

                if ($parentId == 0)
                    $return[$id] = &$menuList[$id];
                else
                    $menuList[$parentId]['children'][$id] = &$menuList[$id];
            }
            $this->list = $return;
            return $this;
        }
        return null;
    }

    /**
     * @throws \Exception
     */
    public function buildTree() {
        if(sizeof($this->list)) {
            foreach($this->list as $id => $listItem) {
                $this->addChild($listItem);
            }
        }
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }


}