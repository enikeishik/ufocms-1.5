<?php
class UfoDebug
{
    protected $debugEnabled = false;
    protected $pageStartTime = 0;
    
    public function __construct($debugEnabled = true)
    {
        $this->debugEnabled = $debugEnabled;
    }
    
    public function setPageStartTime($time = null)
    {
        if (is_null($time)) {
            list($msec, $sec) = explode(chr(32), microtime());
            $this->pageStartTime = $sec + $msec;
        } else {
            $this->pageStartTime = $time;
        }
    }
    
    public function getPageStartTime()
    {
        return $this->pageStartTime;
    }
    
    public function getPageExecutionTime()
    {
        list($msec, $sec) = explode(chr(32), microtime());
        $now = $sec + $msec;
        return $now - $this->pageStartTime;
    }
    
    /**
     * 
     * @param string $message
     * @todo вместо echo использовать объект протоколирования
     */
    public function log($message)
    {
        if (!$this->debugEnabled) {
            return;
        }
        echo $this->getPageExecutionTime() . "\t" . $message . "\r\n";
        ob_flush();
    }
}
