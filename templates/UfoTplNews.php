<?php
require_once 'classes/abstract/UfoTemplate.php';

class UfoTplNews extends UfoTemplate
{
    /**
     * ������ �� ������ ������ �������� �������.
     * �������������� ����� ����� �������� ��� �������� ������,
     * � �� ������������ ������������� ������ (��� IDE).
     * @var UfoModNews
     */
    protected $module = null;
    
    public function drawMetaTags()
    {
        
    }
    
    public function drawHeadTitle()
    {
        echo '<title>' . $this->sectionFields->title . '</title>' . "\r\n";
    }

    public function drawHeadCode()
    {
    
    }
    
    public function drawBodyTitle()
    {
        return '<h1>' . $this->sectionFields->title . '</h1>' . "\r\n";
    }
    
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
