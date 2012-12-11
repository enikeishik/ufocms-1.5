<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModNews extends UfoModule
{
    /**
     * Идентификатор выбранного элемента.
     * @var int
     */
    protected $id = 0;
    
    public function getItem()
    {
        
    }
    
    public function getItems()
    {
        
    }
    
    public function getPage()
    {
        if (0 != $this->id = $this->getItemId()) {
            return $this->getItem();
        } else {
            return $this->getItems();
        }
    }
    
    protected function getItemId()
    {
        return 0;
    }
}
