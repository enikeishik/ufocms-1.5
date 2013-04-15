<?php
require_once 'UfoTemplateGlobal.php';

class UfoStpSearch extends UfoTemplateGlobal
{
    /**
     * Ссылка на объект модуля текущего раздела.
     * Переопределена здесь чтобы получить тип текущего класса,
     * а не абстрактного родительского класса (для IDE).
     * @var UfoSysSearch
     */
    protected $module = null;
    
    public function drawBodyContent()
    {
        $items = $this->module->getContent();
        if (false === $items) {
            echo '<p>Некоректный запрос</p>' . "\r\n";
            return;
        }
        foreach ($items as $item) {
            echo '<p>' . $item['Title'] . '</p>' . "\r\n";
        }
    }
}
