<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения дополнительных данных (установок модуля раздела, вычисляемых данных) элемента вставки.
 *
 * @author enikeishik
 *
 */
class UfoInsertionItemSettings extends UfoStruct
{
    /**
     * Путь раздела-источника.
     * @var string
     */
    public $path = '';
    
    /**
     * Имя файла вставки модуля.
     * @var string
     */
    public $mfileins = '';
}
