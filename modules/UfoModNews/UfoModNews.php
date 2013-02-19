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
        return array('');
    }
    
    public function getItems()
    {
        return array(array(''), array(''));
    }
    
    protected function getItemId()
    {
        return 0;
    }
}
