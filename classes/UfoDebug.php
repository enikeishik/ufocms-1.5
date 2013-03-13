<?php
require_once 'UfoDebugStruct.php';
/**
 * ����� �������.
 * 
 * @author enikeishik
 *
 */
class UfoDebug
{
    use UfoTools;
    
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    private $config = null;
    
    /**
     * ������� ���������������� ���������� ����������.
     * @var int
     */
    protected $debugLevel = 0;
    
    /**
     * ����� ������ ���������� �������.
     * @var float
     */
    protected $pageStartTime = 0;
    
    /**
     * ����� ������ ���������� ��������� �������.
     * @var float
     */
    protected $lastStartTime = 0;
    
    /**
     * ����� ���������� ����������.
     * @var array
     */
    protected $buffer = array();
    
    /**
     * ������� ���������� SQL �������� � ��.
     * @var int
     */
    protected $dbQueriesCounter = 0;
    
    /**
     * ������������ ���������� ������������ ������.
     * @var int
     */
    protected $memoryUsedMax = 0;
    
    /**
     * ������������ ���������� ������� ������������ ������.
     * @var int
     */
    protected $memoryUsedTotalMax = 0;
    
    /**
     * �����������.
     * @param UfoConfig &$config    ������ �� ������ ������������
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
     * ���������� ���������� ������, ��������� � ������� ������ ���������� (�������).
     * @return float
     * @see UfoDebug::getExecutionTime
     */
    public function getPageExecutionTime()
    {
        return $this->getExecutionTime();
    }
    
    /**
     * ��������� ������� ������ ���������� �������.
     * @param float $time = null    ����� ������
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
     * ��������� ������� ������ ���������� ��������� �������.
     * @param float $time = null    ����� ������
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
     * ���������� ����� ������ ���������� �������.
     * @return float
     */
    public function getPageStartTime()
    {
        return $this->pageStartTime;
    }
    
    /**
     * ���������� ���������� ������, ��������� � ������� ������ ���������� (�������).
     * @param float $startTime = -1    ����� ������ ���������� (-1 - ������ ���������� �������)
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
     * ���������� ���������� SQL �������� � ��.
     * @return int
     */
    public function getDbQueriesCounter()
    {
        return $this->dbQueriesCounter;
    }
    
    /**
     * ���������� ������������ ���������� ������������ ������.
     * @return int
     */
    public function getMemoryUsedMax()
    {
        return $this->memoryUsedMax;
    }
    
    /**
     * ���������� ������������ ���������� ������� ������������ ������.
     * @return int
     */
    public function getMemoryUsedTotalMax()
    {
        return $this->memoryUsedTotalMax;
    }
    
    /**
     * ���� ���������� ����������.
     * @param string $message            ����� ���������
     * @param string $class = ''         ����� ������
     * @param string $method = ''        ����� ������
     * @param boolean $isTail = false    ����� ������, false - � ������ ������, true - � ����� ������
     * @todo ��������.
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
     * ���� SQL �������� � ��.
     * @param string $query              SQL ������
     * @param string $error              ������ ��
     * @param boolean $isTail = false    ����� ������, false - � ������ ������, true - � ����� ������
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
     * ���������������� ��������������������.
     * @param UfoDebugStruct $ds    ������ �������
     */
    protected function log(UfoDebugStruct $ds)
    {
        $this->writeLog((string) $ds, $this->config->logDebug);
    }
}
