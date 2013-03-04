<?php
require_once 'classes/abstract/UfoModuleParams.php';
/**
 * Класс-структура для хранения данных модуля.
 * @author enikeishik
 */
class UfoModNewsParams extends UfoModuleParams
{
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
