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
    /**
     * Error code.
     * @var int
     */
    public $code = 0;
    
    /**
     * Error text.
     * @var string
     */
    public $text = '';
    
    /**
     * Путь переадресации текущей страницы.
     * @var string
     */
    public $pathRedirect = '';
    
    /**
     * Конструктор.
     * @param int $code       код ошибки
     * @param string $text    текст ошибки
     */
    public function __construct($code, $text, $pathRedirect = '')
    {
        $this->code = $code;
        $this->text = $text;
        $this->pathRedirect = $pathRedirect;
    }
}
