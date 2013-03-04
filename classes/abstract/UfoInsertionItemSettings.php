<?php
require_once 'UfoStruct.php';
/**
 * Класс-структура для хранения дополнительных данных (установок модуля раздела) элемента вставки.
 *
 * @author enikeishik
 *
 */
abstract class UfoInsertionItemSettings extends UfoStruct
{
    /**
     * Путь раздела-источника.
     * @var string
     */
    public $path = '';
}
