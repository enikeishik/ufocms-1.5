<?php
require_once 'classes/abstract/UfoStruct.php';

/**
 * Класс-структура для хранения данных модуля.
 * @author enikeishik
 */
class UfoModNewsParams extends UfoStruct
{
    /**
     * Идентификатор конкретного элемента.
     * @var int
     */
    public $id = 0;
    
    /**
     * Номер страницы при постраничном выводе.
     * @var int
     */
    public $page = 1;
    
    /**
     * Дата, за которую надо вывести элементы.
     * @var DateTime
     */
    public $dt = null;
}
