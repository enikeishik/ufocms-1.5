<?php
class UfoDebug
{
    private $pageStartTime = 0;
    
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
}
