<?php
require_once 'Tools.php';

class UfoDebugCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDebug.php';
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoDebug`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoDebug(new UfoConfig());
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function getPageExecutionTime(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodNotReturns($obj, __FUNCTION__, 0, array());
    }
    
    public function getPageExecutionTimeReal(\CodeGuy $I) {
        $this->showTest('getPageExecutionTime in real situation');
    	$I->wantTo('execute method `getPageExecutionTime`');
        $I->execute(function() {
            $obj = new UfoDebug(new UfoConfig());
            $obj->setPageStartTime(); //устанавливаем время начала выполнения
            return 0 == round($obj->getPageExecutionTime(), 2); //проверяем что прошло "почти" 0 секунд
        });
        $I->seeResultEquals(true);
    }
    
    public function setPageStartTime(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->executeMethod($obj, __FUNCTION__, 1);
    }
    
    public function setLastStartTime(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->executeMethod($obj, __FUNCTION__, 1);
    }
    
    public function getPageStartTime(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, 0, array());
    }
    
    public function getExecutionTime(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodNotReturns($obj, __FUNCTION__, 0, array());
    }
    
    public function getExecutionTimeReal(\CodeGuy $I) {
        $this->showTest('getExecutionTime in real situation');
    	$I->wantTo('execute method `getExecutionTime`');
        $I->execute(function() {
            $obj = new UfoDebug(new UfoConfig());
            list($msec, $sec) = explode(chr(32), microtime());
            $now = $sec + $msec;
            return 0 == round($obj->getExecutionTime($now), 2); //проверяем что прошло "почти" 0 секунд
        });
        $I->seeResultEquals(true);
    }
    
    public function getBuffer(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, array(), array());
    }
    
    public function getDbQueriesCounter(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, 0, array());
    }
    
    public function getMemoryUsedMax(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, 0, array());
    }
    
    public function getMemoryUsedTotalMax(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, 0, array());
    }
    
    public function trace(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__, 'test MESSAGE');
    }
    
    public function traceSql(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoDebug(new UfoConfig());
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__, 'test SQL', 'test ERROR');
    }
    
    public function getMemoryReal(\CodeGuy $I) {
        $this->showTest('getMemoryUsedMax and getMemoryUsedTotalMax in real situation');
        $I->execute(function() {
            $obj = new UfoDebug(new UfoConfig());
            $obj->trace('test memory usage');
            return 0 != $obj->getMemoryUsedMax() && 0 != $obj->getMemoryUsedTotalMax();
        });
        $I->seeResultEquals(true);
    }
    
    public function getDbQueriesCounterReal(\CodeGuy $I) {
        $this->showTest('getDbQueriesCounter in real situation');
        $I->execute(function() {
            $obj = new UfoDebug(new UfoConfig());
            $obj->traceSql('test SQL counter', 'test error', true); //true - to calc sql counter
            $ret = $obj->getDbQueriesCounter();
            echo 'expected: '; var_dump(1); echo "\r\n";
            echo 'actual:   '; var_dump($ret); echo "\r\n";
            return 1 == $ret;
        });
        $I->seeResultEquals(true);
    }
}
