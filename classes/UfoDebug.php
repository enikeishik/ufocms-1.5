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
    use UfoTools;
    
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    private $config = null;
    
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
     * Счетчик количества SQL запросов к БД.
     * @var int
     */
    protected $dbQueriesCounter = 0;
    
    /**
     * Максимальное количество используемой памяти.
     * @var int
     */
    protected $memoryUsedMax = 0;
    
    /**
     * Максимальное количество реально используемой памяти.
     * @var int
     */
    protected $memoryUsedTotalMax = 0;
    
    /**
     * Конструктор.
     * @param UfoConfig &$config    ссылка на объект конфигурации
     */
    public function __construct(UfoConfig &$config)
    {
        $this->config =& $config;
        $this->debugLevel = $config->debugLevel;
        if (0 > $this->debugLevel 
        || ('' == $this->config->logDebug && false == $this->config->debugDisplay)) {
            $this->debugLevel = 0;
        } else if (9 < $this->debugLevel) {
            $this->debugLevel = 9;
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
    
    /**
     * @return array: UfoDebugStruct
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
    
    /**
     * Возвращает количество SQL запросов к БД.
     * @return int
     */
    public function getDbQueriesCounter()
    {
        return $this->dbQueriesCounter;
    }
    
    /**
     * Возвращает максимальное количество используемой памяти.
     * @return int
     */
    public function getMemoryUsedMax()
    {
        return $this->memoryUsedMax;
    }
    
    /**
     * Возвращает максимальное количество реально используемой памяти.
     * @return int
     */
    public function getMemoryUsedTotalMax()
    {
        return $this->memoryUsedTotalMax;
    }
    
    /**
     * Сбор отладочной информации.
     * @param string $message            текст сообщения
     * @param string $class = ''         класс вызова
     * @param string $method = ''        метод вызова
     * @param boolean $isTail = false    точка вызова, false - в начале метода, true - в конце метода
     * @todo доделать.
     */
    public function trace($message, $class = '', $method = '', $isTail = false)
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
                    $this->setLastStartTime();
                } else {
                    $ds->blockTime = $this->getExecutionTime($this->lastStartTime);
                }
                $this->buffer[] = $ds;
                if ($this->memoryUsedMax < $ds->memoryUsed) {
                    $this->memoryUsedMax = $ds->memoryUsed;
                }
                if ($this->memoryUsedTotalMax < $ds->memoryUsedTotal) {
                    $this->memoryUsedTotalMax = $ds->memoryUsedTotal;
                }
                $this->log($ds);
                break;
        }
    }
    
    /**
     * Сбор SQL запросов к БД.
     * @param string $query              SQL запрос
     * @param string $error              ошибка БД
     * @param boolean $isTail = false    точка вызова, false - в начале метода, true - в конце метода
     */
    public function traceSql($query, $error, $isTail = false)
    {
        if (0 == $this->debugLevel) {
            return;
        }
        $ds = new UfoDebugStruct();
        $ds->scriptTime = $this->getExecutionTime();
        $ds->memoryUsed = memory_get_usage();
        $ds->memoryUsedTotal = memory_get_usage(true);
        if (!$isTail) {
            $this->setLastStartTime();
        } else {
            $ds->blockTime = $this->getExecutionTime($this->lastStartTime);
        }
        $ds->dbQuery = $query;
        $ds->dbError = $error;
        $this->buffer[] = $ds;
        $this->dbQueriesCounter++;
        if ($this->memoryUsedMax < $ds->memoryUsed) {
            $this->memoryUsedMax = $ds->memoryUsed;
        }
        if ($this->memoryUsedTotalMax < $ds->memoryUsedTotal) {
            $this->memoryUsedTotalMax = $ds->memoryUsedTotal;
        }
        $this->log($ds);
    }
    
    /**
     * Протоколирование отладочнойинформации.
     * @param UfoDebugStruct $ds    данные отладки
     */
    protected function log(UfoDebugStruct $ds)
    {
        $this->writeLog((string) $ds, $this->config->logDebug);
    }
}
