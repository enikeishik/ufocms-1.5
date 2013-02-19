<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModNews extends UfoModule
{
    /**
     * Идентификатор выбранного элемента.
     * @var int
     */
    protected $id = 0;
    
    /**
     * Формирование массива данных одного элемента.
     * @return array
     */
    public function getItem()
    {
        return array('');
    }
    
    /**
     * Формирование массива массивов данных элементов.
     * @return array:array
     */
    public function getItems()
    {
        return array(array(''), array(''));
    }
    
    /**
     * Получение идентификатора текущего элемента, возвращает 0 если запрошен не элемент, а список.
     * @return int
     */
    protected function getItemId()
    {
        return 0;
    }
}
