<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения данных раздела.
 * 
 * @author enikeishik
 *
 */
class UfoSectionStruct extends UfoStruct
{
    public $id = 0;
    public $topid = 0;
    public $parentid = 0;
    public $orderid = 0;
    public $levelid = 0;
    public $isparent = false;
    public $moduleid = 0;
    public $designid = 0;
    public $mask = '';
    public $path = '';
    public $image = '';
    public $timage = '';
    public $indic = '';
    public $title = '';
    public $metadesc = '';
    public $metakeys = '';
    public $isenabled = false;
    public $insearch = false;
    public $inmenu = false;
    public $inlinks = false;
    public $inmap = false;
    public $shtitle = 0;
    public $shmenu = 0;
    public $shlinks = 0;
    public $shcomments = 0;
    public $shrating = 0;
    public $flsearch = 0;
    public $flcache = 0;
}
