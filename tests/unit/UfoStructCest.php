<?php
require_once 'Tools.php';

class UfoStructCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'classes' . self::DS . 'abstract' . self::DS . 'UfoStruct.php';
        require_once __DIR__ . self::DS . 'UfoStructDummy.php';
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoStructDummy`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoStructDummy();
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function toString(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoStructDummy();
            $obj->fArray = array('key1' => 1, 'key2' => 'val2');
            $obj->fObject = new StdClass();
            try {
                $ret = (string) $obj;
                var_dump($ret);
                $res = is_string($ret);
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
                $res = false;
            }
            return $res;
        });
        $I->seeResultEquals(true);
    }
    
    public function setFields(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoStructDummy();
            $obj2 = new UfoStructDummy();
            $obj2->fInt = 0;
            $obj2->fFloat = 0.0;
            $obj2->fString = '';
            $obj2->fBoolean = false;
            $obj2->fArray = null;
            $obj2->fObject = null;
            $obj2->fVariant = null;
            $obj->setFields($obj2);
            $expected = 'UfoStructDummy {fInt: 0	fFloat: 0	fString: 	fBoolean: 	fArray: <null>	fObject: <null>	fVariant: <null>}';
            $actual = (string) $obj;
            echo 'expected: '; var_dump($expected);
            echo 'actual:   '; var_dump($actual);
            $res = $expected == $actual;
            return $res;
        });
        $I->seeResultEquals(true);
    }
    
    public function setValues(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoStructDummy();
            //set with casting
            $obj->setValues(array('fInt' => 0, 'fFloat' => 0.0, 'fString' => '', 'fBoolean' => false, 'fArray' => null, 'fObject' => null, 'fVariant' => 'test'));
            $expected1 = 'UfoStructDummy {fInt: 0	fFloat: 0	fString: 	fBoolean: 	fArray: <null>	fObject: <null>	fVariant: test}';
            $actual1 = (string) $obj;
            echo 'with casting' . "\r\n";
            echo 'expected: '; var_dump($expected1);
            echo 'actual:   '; var_dump($actual1);
            //set without casting
            $obj->setValues(array('fInt' => 'test', 'fVariant' => 123), false);
            $expected2 = 'UfoStructDummy {fInt: test	fFloat: 0	fString: 	fBoolean: 	fArray: <null>	fObject: <null>	fVariant: 123}';
            $actual2 = (string) $obj;
            echo 'without casting' . "\r\n";
            echo 'expected: '; var_dump($expected2);
            echo 'actual:   '; var_dump($actual2);
            $res = $expected1 == $actual1 && $expected2 == $actual2;
            return $res;
        });
        $I->seeResultEquals(true);
    }
}
