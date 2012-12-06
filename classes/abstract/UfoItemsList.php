<?php
abstract class UfoItemsList implements Iterator
{
    private $position = 0;
    private $items = array();
    
    public function __construct(array $items)
    {
        $this->position = 0;
        $this->items = $items;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }
    
    public function add(IItem $item)
    {
        return 0 != array_push($item);
    }
    
    public function remove($position)
    {
        if (0 > $position) {
            return false;
        }
        if (0 == $position) {
            return !is_null(array_shift($this->items));
        } else if ((count($this->items) - 1) == $position) {
            return !is_null(array_pop($this->items));
        } else {
            $this->items = array_merge(array_slice($this->items, 0, $position), 
                                       array_slice($this->items, $position + 1));
            return true;
        }
    }
}
