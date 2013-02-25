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
     * ���������� ����� ������ ���������� �������.
     * @return float
     */
    public function getPageStartTime()
    {
        return $this->pageStartTime;
    }
    
    /**
     * ���������� ���������� ������, ��������� � ������� ������ ���������� �������.
     * @return float
     */
    public function getPageExecutionTime()
    {
        list($msec, $sec) = explode(chr(32), microtime());
        $now = $sec + $msec;
        return $now - $this->pageStartTime;
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
                echo $this->getPageExecutionTime() . "\t" . 
                     'M: ' . memory_get_usage() . '; MT: ' . memory_get_usage(true) . "\t" . 
                     $message . "<br />\r\n";
                ob_flush();
                break;
        }
    }
}
