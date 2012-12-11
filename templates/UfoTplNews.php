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
    
    public function getMetaTags()
    {
        
    }
    
    public function getHeadTitle()
    {
        return '<title>' . $this->fields->title . '</title>' . "\r\n";
    }
    
    public function getBodyTitle()
    {
        return '<h1>' . $this->fields->title . '</h1>' . "\r\n";
    }
    
    public function getBodyContent()
    {
        ob_start();
        return ob_get_clean();
    }
    
    public function getDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        return '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->';
    }
}
