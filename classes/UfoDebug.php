<?php
/**
 * Класс отладки.
 * 
 * @author enikeishik
 *
 */
class UfoDebug
{
    /**
     * Уровень протоколирования отладочной информации.
     * @var int
     */
    protected $debugLevel = 0;
    
    /**
     * Время начала выполнения скрипта.
     * @var float
     */
    protected $pageStartTime = 0;
    
    /**
     * Конструктор.
     * @param int $debugLevel = 0    уровень протоколирования отладочной информации
     */
    public function __construct($debugLevel = 0)
    {
        if (0 < $debugLevel && 9 >= $debugLevel) {
            $this->debugLevel = $debugLevel;
        }
    }
    
    /**
     * Установка времени начала выполнения скрипта.
     * @param float $time = null    время начала
     */
    public function setPageStartTime($time = null)
    {
        if (is_null($time)) {
            list($msec, $sec) = explode(chr(32), microtime());
            $this->pageStartTime = $sec + $msec;
        } else {
            $this->pageStartTime = $time;
        }
    }
    
    /**
     * Возвращает время начала выполнения скрипта.
     * @return float
     */
    public function getPageStartTime()
    {
        return $this->pageStartTime;
    }
    
    /**
     * Возвращает количество секунд, прошедших с момента начала выполнения скрипта.
     * @return float
     */
    public function getPageExecutionTime()
    {
        list($msec, $sec) = explode(chr(32), microtime());
        $now = $sec + $msec;
        return $now - $this->pageStartTime;
    }
    
    /**
     * Протоколирование отладочной информации.
     * @param string $message
     * @todo вместо echo использовать объект протоколирования.
     */
    public function log($message)
    {
        if (0 == $this->debugLevel) {
            return;
        }
        switch ($this->debugLevel) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
                echo $this->getPageExecutionTime() . "\t" . 
                     'M: ' . memory_get_usage() . '; MT: ' . memory_get_usage(true) . "\t" . 
                     $message . "<br />\r\n";
                ob_flush();
                break;
        }
    }
}
