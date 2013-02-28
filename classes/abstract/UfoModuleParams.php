<?php
require_once 'UfoStruct.php';
/**
 * Абстрактный класс-структура для хранения данных модуля.
 * Здесь определены общие поля, которые должны быть определены независимо от конкретного модуля.
 *
 * @author enikeishik
 * 
 */
abstract class UfoModuleParams extends UfoStruct
{
    /**
     * Идентификатор конкретного элемента.
     * @var int
     */
    public $id = 0;

    /**
     * Номер страницы комментариев при постраничном выводе.
     * @var int
     */
    public $comments = 1;

    /**
     * Вывод данных в формате RSS.
     * @var boolean
     */
    public $rss = false;
}
