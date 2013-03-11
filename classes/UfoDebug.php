<?php
require_once 'UfoDebugStruct.php';
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
     * Время начала выполнения последней команды.
     * @var float
     */
    protected $lastStartTime = 0;
    
    /**
     * Буфер отладочной информации.
     * @var array
     */
    protected $buffer = array();
    
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
     * Возвращает количество секунд, прошедших с момента начала выполнения (скрипта).
     * @return float
     * @see UfoDebug::getExecutionTime
     */
    public function getPageExecutionTime()
    {
        return $this->getExecutionTime();
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
     * Установка времени начала выполнения последней команды.
     * @param float $time = null    время начала
     */
    public function setLastStartTime($time = null)
    {
        if (is_null($time)) {
            list($msec, $sec) = explode(chr(32), microtime());
            $this->lastStartTime = $sec + $msec;
        } else {
            $this->lastStartTime = $time;
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
     * Возвращает количество секунд, прошедших с момента начала выполнения (скрипта).
     * @param float $startTime = -1    время начала выполнения (-1 - начало выполнения скрипта)
     * @return float
     */
    public function getExecutionTime($startTime = -1)
    {
        if (-1 == $startTime) {
            $startTime = $this->pageStartTime;
        }
        list($msec, $sec) = explode(chr(32), microtime());
        $now = $sec + $msec;
        return $now - $startTime;
    }
    
    public function getBuffer()
    {
        return $this->buffer;
    }
    
    /**
     * Протоколирование отладочной информации.
     * @param string $message            текст сообщения
     * @param string $class = ''         класс вызова
     * @param string $method = ''        метод вызова
     * @param boolean $isTail = false    точка вызова, false - в начале метода, true - в конце метода
     * @todo доделать.
     */
    public function log($message, $class = '', $method = '', $isTail = false)
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
                $ds = new UfoDebugStruct();
                $ds->message = $message;
                $ds->scriptTime = $this->getExecutionTime();
                $ds->memoryUsed = memory_get_usage();
                $ds->memoryUsedTotal = memory_get_usage(true);
                $ds->className = $class;
                $ds->methodName = $method;
                if (!$isTail) {
                    $this->buffer[] = $ds;
                    $this->setLastStartTime();
                } else {
                    $ds->blockTime = $this->getExecutionTime($this->lastStartTime);
                    $this->buffer[] = $ds;
                }
                break;
        }
    }
}
