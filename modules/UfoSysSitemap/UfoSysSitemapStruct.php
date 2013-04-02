<?php
require_once 'classes/UfoSectionStruct.php';

/**
 * Класс-структура для хранения данных модуля.
 * @author enikeishik
 */
class UfoSysSitemapStruct extends UfoSectionStruct
{
    public $path = '/sitemap/';
    public $indic = 'Карта сайта';
    public $title = 'Карта сайта';
    public $flcache = 1;
}
