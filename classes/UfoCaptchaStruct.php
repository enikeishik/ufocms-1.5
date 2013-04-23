<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения параметров картинки, генерируемой CAPTCHA.
 * 
 * @author enikeishik
 *
 */
class UfoCaptchaStruct extends UfoStruct
{
    public $bgColor = array();
    public $fgColor = array();
    public $jpegQuality = 0;
    public $fontSize = 0;
    public $letterSeperator = '';
}
