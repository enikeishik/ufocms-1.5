<?php
/**
 * ����� �������.
 * 
 * @author enikeishik
 *
 */
class UfoDebug
{
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
     * �����������.
     * @param int $debugLevel = 0    ������� ���������������� ���������� ����������
     */
    public function __construct($debugLevel = 0)
    {
        if (0 < $debugLevel && 9 >= $debugLevel) {
            $this->debugLevel = $debugLevel;
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
     * ���������������� ���������� ����������.
     * @param string $message
     * @todo ������ echo ������������ ������ ����������������.
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
                echo 'PT: ' . round($this->getExecutionTime(), 4) . "s.\t" . 
                     'PBT: ' . round($this->getExecutionTime($this->lastStartTime) * 100, 4) . "ms.\t" . 
                     'M: ' . memory_get_usage() . 
                     'b; MT: ' . memory_get_usage(true) . "b\t" . 
                     $message . "<br />\r\n";
                $this->setLastStartTime();
                ob_flush();
                break;
        }
    }
}
