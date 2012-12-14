<?php
trait Tools
{
    protected function showTest($name)
    {
        echo "\r\n" . '--------------------' . $name . '--------------------' . "\r\n";
    }
    
    protected function showTestCase($name)
    {
        echo "\r\n\r\n" . '====================' . $name . '====================' . "\r\n";
    }
}
