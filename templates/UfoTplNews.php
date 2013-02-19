<?php
require_once 'classes/abstract/UfoTemplate.php';

class UfoTplNews extends UfoTemplate
{
    /**
     * —сылка на объект модул€ текущего раздела.
     * ѕереопределена здесь чтобы получить тип текущего класса,
     * а не абстрактного родительского класса (дл€ IDE).
     * @var UfoModNews
     */
    protected $module = null;
    
    public function drawItem(&$item)
    {
        echo '<p>One item from news feed</p>' . "\r\n";
    }
    
    public function drawItems(&$items)
    {
        foreach ($items as $item) {
            echo '<p>News feed item</p>' . "\r\n";
        }
    }
    
    public function drawBodyContent()
    {
        if (0 != $this->id = $this->getItemId()) {
            $this->drawItem($this->module->getItem());
        } else {
            $this->drawItems($this->module->getItems());
        }
    }
}
