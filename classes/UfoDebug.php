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
     * @var array<UfoDebugStruct>
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
     * Возвращает элемент буфера с максимальным временем выполнения блока.
     * @return UfoDebugStruct|null
     */
    public function getBlockMaxExecutionTime()
    {
        $max = 0;
        $maxIdx = -1;
        $cnt = count($this->buffer);
        for ($i = 0; $i < $cnt; $i++) {
            if ($max < $this->buffer[$i]->blockTime) {
                $max = $this->buffer[$i]->blockTime;
                $maxIdx = $i;
            }
        }
        if (-1 != $maxIdx) {
            return $this->buffer[$maxIdx];
        } else {
            return null;
        }
    }
    
    /**
     * Возвращает список вызовов функций.
     * @return string
     */
    protected function getStackTrace()
    {
        $stack = array_reverse(debug_backtrace());
        $arr = array();
        $i = 0;
        foreach ($stack as $item) {
            if (isset($item['class'])) {
                $arr[] = str_pad('', $i * 2) . $item['class'] . '::' . $item['function'];
            } else {
                $arr[] = str_pad('', $i * 2) . $item['function'];
            }
            $i++;
        }
        array_pop($arr);
        return implode("\r\n", $arr);
    }
    
    /**
     * Сбор отладочной информации.
     * @param string $message            текст сообщения
     * @param string $class = ''         класс вызова
     * @param string $method = ''        метод вызова
     * @param boolean $isTail = false    точка вызова, false - в начале метода, true - в конце метода
     */
    public function trace($message, $class = '', $method = '', $isTail = false)
    {
        if (0 == $this->debugLevel) {
            return;
        }
        
        $ds = new UfoDebugStruct();
        $ds->scriptTime = $this->getExecutionTime();
        $ds->className = $class;
        $ds->methodName = $method;
        $ds->message = $message;
        
        if (1 < $this->debugLevel) {
            $ds->memoryUsed = memory_get_usage();
            $ds->memoryUsedTotal = memory_get_usage(true);
            if ($this->memoryUsedMax < $ds->memoryUsed) {
                $this->memoryUsedMax = $ds->memoryUsed;
            }
            if ($this->memoryUsedTotalMax < $ds->memoryUsedTotal) {
                $this->memoryUsedTotalMax = $ds->memoryUsedTotal;
            }
            
            if (2 < $this->debugLevel) {
                if (!$isTail) {
                    $this->setLastStartTime();
                } else {
                    $ds->blockTime = $this->getExecutionTime($this->lastStartTime);
                }
                
                if (3 < $this->debugLevel) {
                    
                    if (4 < $this->debugLevel) {
                        $ds->callStack = $this->getStackTrace();
                    }
                }
            }
        }
        $this->buffer[] = $ds;
        $this->log($ds);
    }
    
    /**
     * Вычленение из SQL запроса основной команды и таблиц.
     * @param string $sql
     * @return string [SQL_COMMAND table1 table2 ...]
     */
    protected function parseSql($sql)
    {
        $sql = ltrim($sql);
        $ret = array();
        if (0 === stripos($sql, 'SELECT ')) {
            $ret[] = 'SELECT';
        } else if (0 === stripos($sql, 'INSERT ')) {
            $ret[] = 'INSERT';
        } else if (0 === stripos($sql, 'UPDATE ')) {
            $ret[] = 'UPDATE';
        } else if (0 === stripos($sql, 'DELETE ')) {
            $ret[] = 'DELETE';
        } else if (0 === stripos($sql, 'TRUNCATE ')) {
            $ret[] = 'TRUNCATE';
        } else if (0 === stripos($sql, 'DROP ')) {
            $ret[] = 'DROP';
        } else {
            return '';
        }
        $tables = array();
        $pattern = '/' . $this->config->dbTablePrefix . '[^ ]+/';
        if (preg_match_all($pattern, $sql, $tables)) {
            return implode(' ', array_merge($ret, $tables[0]));
        }
        return $ret[0];
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
        $ds->message = $this->parseSql($query);
        if ($isTail) {
            $this->dbQueriesCounter++;
        }
        
        if (1 < $this->debugLevel) {
            $ds->memoryUsed = memory_get_usage();
            $ds->memoryUsedTotal = memory_get_usage(true);
            if ($this->memoryUsedMax < $ds->memoryUsed) {
                $this->memoryUsedMax = $ds->memoryUsed;
            }
            if ($this->memoryUsedTotalMax < $ds->memoryUsedTotal) {
                $this->memoryUsedTotalMax = $ds->memoryUsedTotal;
            }
            
            if (2 < $this->debugLevel) {
                if (!$isTail) {
                    $this->setLastStartTime();
                } else {
                    $ds->blockTime = $this->getExecutionTime($this->lastStartTime);
                }
                
                if (3 < $this->debugLevel) {
                    $ds->dbQuery = $query;
                    $ds->dbError = $error;
                    
                    if (4 < $this->debugLevel) {
                        $ds->callStack = $this->getStackTrace();
                    }
                }
            }
        }
        $this->buffer[] = $ds;
        $this->log($ds);
    }
    
    /**
     * Протоколирование отладочнойинформации.
     * @param UfoDebugStruct $ds    данные отладки
     */
    protected function log(UfoDebugStruct $ds)
    {
        $this->writeLog(str_replace("\n", ' ', str_replace("\r", '', (string) $ds)), 
                        $this->config->logDebug);
    }
}
