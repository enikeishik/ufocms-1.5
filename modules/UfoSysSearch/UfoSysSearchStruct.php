<?php
require_once 'classes/UfoSectionStruct.php';

/**
 * Класс-структура для хранения данных модуля.
 * @author enikeishik
 */
class UfoSysSearchStruct extends UfoSectionStruct
{
    public $path = '/search/';
    public $indic = 'Поиск по сайту';
    public $title = 'Поиск по сайту';
    public $flcache = 0;
}
