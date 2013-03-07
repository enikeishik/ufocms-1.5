<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения данных ошибки.
 * 
 * @author enikeishik
 *
 */
class UfoErrorStruct extends UfoStruct
{
    public $code = 0;
    public $text = '';
}
