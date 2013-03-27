<?php
require_once 'UfoTemplateGlobal.php';

class UfoTplNews extends UfoTemplateGlobal
{
    /**
     * Ссылка на объект модуля текущего раздела.
     * Переопределена здесь чтобы получить тип текущего класса,
     * а не абстрактного родительского класса (для IDE).
     * @var UfoModNews
     */
    protected $module = null;
    
    protected function drawEmpty()
    {
        echo '<div>Данные по запросу отсутствуют.</div>';
    }
    
    /**
     * Вывод данных отдельного элемента.
     * @param array|false $item
     */
    protected function drawItem($item)
    {
        if (!is_array($item)) {
            //$this->drawEmpty();
            $this->core->registerError(404, 'Запрашиваемая страница не найдена');
            return;
        }
        echo '<h2>' . $item['Title'] . '</h2>' . "\r\n";
        echo '<div>' . $item['DateCreate'] . '</div>' . "\r\n";
        echo '<div>' . $item['Body'] . '</div>' . "\r\n";
        if ('' != $item['Author']) {
            echo '<div>' . $item['Author'] . '</div>' . "\r\n";
        }
        echo '<div class="newstape"><a href="' . $this->section->getField('path') . '">Все новости</a></div>';
    }
    
    protected function drawItems($items)
    {
        if (!is_array($items)) {
            $this->drawEmpty();
            return;
        }
        foreach ($items as $item) {
            echo '<div class="newstapetitle"><a href="' . $this->section->getField('path') . $item['Id'] . '/">' . 
                 $item['Title'] . '</a></div>' . "\r\n";
            echo '<div class="newstapetext">' . $item['Announce'] . '</div>' . "\r\n";
            echo '<div class="newstapedate">' . $item['DateCreate'] . '</div>' . "\r\n";
            echo '<div class="newstapedivider"></div>' . "\r\n";
        }
    }
    
    public function drawBodyContent()
    {
        if (0 != $this->module->getItemId()) {
            $this->drawItem($this->module->getItem());
        } else {
            $this->drawItems($this->module->getItems());
        }
        echo '<hr />' . "\r\n";
    }
}
