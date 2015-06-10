<?php


namespace Tree;


class TreeItem implements \ArrayAccess, \Countable, \IteratorAggregate
{

    protected
        $idName = 'id',
        $parentIdName = 'parent_id',
        $childrenName = 'children',
        $labelName = 'name',
        $id = 0,
        $parentId = 0,
        $name = '',
        $children = array(),
        $parent = null,
        $level = 0,
        $delimetr = '<br>';


    /**
     * @param array $item
     */
    public function __construct(array $item)
    {
        return $this->fromArray($item);
    }

    /**
     * @param \TreeTreeItem $child
     * @return \Tree\TreeItem
     * @throws Exception
     */
    public function addChild($child)
    {
        if (!$child instanceof \TreeItem) {
            $child = $this->createChild($child);
        } elseif ($child->getParent()) {
            throw new \Exception('Cannot add menu item as child, it already has a parent.');
        }

        $child->setParent($this);
        $child->setParentId($this->getId());
        $this->children[$child->getId()] = $child;


        return $child;
    }

    public function getChild($id)
    {
        return isset($this->children[$id]) ? $this->children[$id] : null;
    }

    /**
     * @param array $item
     * @return \Tree\TreeItem
     */
    protected function createChild(array $item)
    {
        return new \Tree\TreeItem($item);
    }

    /**
     *
     * @param mixed $id
     */
    public function removeChild($id)
    {
        $id = ($id instanceof TreeItem) ? $id->getId() : $id;

        if (isset($this->children[$id])) {
            $this->children[$id]->setParent(null);
            unset($this->children[$id]);
        }
    }

    /**
     * @return TreeItem
     */
    public function getFirstChild()
    {
        return reset($this->children);
    }

    /**
     * @return TreeItem
     */
    public function getLastChild()
    {
        return end($this->children);
    }

    /**
     *
     * @param boolean $withChildren Whether to
     * @return array
     */
    public function toArray($withChildren = true)
    {
        $array = array();
        $fields = array(
            $this->idName => 'id',
            $this->parentIdName => 'parentId',
            $this->labelName => 'name'
        );

        foreach ($fields as $field) {
            $array[$field] = $this->$field;
        }
        $array['level'] = $this->getLevel();

        if ($withChildren) {
            $array[$this->childrenName] = array();
            foreach ($this->children as $child) {
                $array[$this->childrenName][] = $child->toArray();
            }
        }

        return $array;
    }

    /**
     *
     * @return boolean;
     */
    public function hasChildren()
    {
        foreach ($this->children as $child) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param  array $array The menu item array
     * @return TreeItem
     */
    public function fromArray($array)
    {
        if (isset($array[$this->idName])) {
            $this->setId($array[$this->idName]);
        }
        if (isset($array[$this->parentIdName])) {
            $this->setParentId($array[$this->parentIdName]);
        }
        if (isset($array['name'])) {
            $this->setName($array['name']);
        }

        if (isset($array[$this->childrenName])) {
            foreach ($array[$this->childrenName] as $id => $child) {
                $this->addChild($child);
            }
        }

        return $this;
    }

    /**
     *
     * @return TreeItem
     */
    public function getRoot()
    {
        $obj = $this;
        do {
            $found = $obj;
        } while ($obj = $obj->getParent());

        return $found;
    }

    /**
     *
     * @return bool
     */
    public function isRoot()
    {
        return (bool)!$this->getParent();
    }

    /**
     * @return \Tree\TreeItem|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @param \Tree\TreeItem $parent
     * @return \Tree\TreeItem
     */
    public function setParent(\Tree\TreeItem $parent = null)
    {
        return $this->parent = $parent;
    }

    /**
     * @return array of TreeItem objects
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param  array $children An array of TreeItem objects
     * @return TreeItem
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Implements Countable
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayObject($this->children);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($id)
    {
        return isset($this->children[$id]);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($id)
    {
        return $this->getChild($id, false);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($id, $value)
    {
        return $this->addChild($id)->setName($value);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($id)
    {
        $this->removeChild($id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        $count = -1;
        $obj = $this;

        do {
            $count++;
        } while ($obj = $obj->getParent());

        return $count;
    }

    /**
     * @param $id
     */
    public function getItemById($id)
    {
        if ($this) {
            foreach ($this as $item) {
                if ($item->getId() == $id) {
                    return $item;
                } else {
                    foreach ($this->children as $child) {
                        return $child->getItemById($id);
                    }
                }
            }
        }

        return null;
    }

    public function getPath()
    {
        $obj = $this;
        $arrStr = array();
        $str = '';
        do {
            if (!$obj->isRoot()) {
                $arrStr[] = str_repeat('-', $obj->getLevel()) . " " . $obj->getName();
            }
        } while ($obj = $obj->getParent());

        $arrStr = array_reverse($arrStr);
        $str = implode($this->delimetr,$arrStr);
        return $str;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = '';

        if (!$this->isRoot()) {
            $str .= str_repeat('-', $this->getLevel()) . " " . $this->getName() . $this->delimetr;
        }
        foreach ($this->children as $child) {
            $str .= (string) $child;
        }

        return $str;
    }

    /**
     * @return string
     */
    public function getDelimetr()
    {
        return $this->delimetr;
    }

    /**
     * @param string $delimetr
     */
    public function setDelimetr($delimetr)
    {
        $this->delimetr = $delimetr;
    }

}