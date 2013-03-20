<?php
require_once 'Tools.php';

class UfoSiteCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    private $container = null;
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDbModel.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDebug.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        $this->container = new UfoContainer();
        $this->container->setConfig(new UfoConfig());
        $this->container->setDb(new UfoDb($this->container->getConfig()->dbSettings));
        $this->container->setDbModel(new UfoDbModel($this->container->getDb()));
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoSite`');
        $I->execute(function() {
            $obj1 = null;
            $obj2 = null;
            $obj3 = null;
            $obj4 = null;
            try {
                $obj1 = new UfoSite('/', $this->container);
                $obj2 = new UfoSite('/users/1/', $this->container, '/users/');
                try {
                    $obj3 = new UfoSite('/asd', $this->container);
                } catch (Exception $ee) {
                    
                }
                try {
                    $obj4 = new UfoSite('/asdasdasdasdasdasdasd/', $this->container);
                } catch (Exception $ee) {
                    
                }
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj1) && !is_null($obj2) && is_null($obj3) && is_null($obj4);
        });
        $I->seeResultEquals(true);
    }
    
    public function getSiteParam(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSite('/', $this->container);
            $ret1 = $obj->getSiteParam('NotExistingSiteParameter', 'DefaultValueForRequestedParameter');
            $ret2 = $obj->getSiteParam('SiteTitle', 'DefaultValueForRequestedParameter');
            return 'DefaultValueForRequestedParameter' == $ret1 && 'DefaultValueForRequestedParameter' != $ret2;
        });
        $I->seeResultEquals(true);
    }
    
    public function getSiteParams(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSite('/', $this->container);
            $ret = $obj->getSiteParams();
            return is_array($ret);
        });
        $I->seeResultEquals(true);
    }
    
    public function getPathRaw(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoSite('/', $this->container);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, '/');
    }
    
    public function getPathParsed(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoSite('/', $this->container);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, '/');
    }
    
    public function getPathParsedWithParams(\CodeGuy $I) {
        $this->showTest('getPathParsed with params');
        $obj = new UfoSite('/users/1/', $this->container, '/users/');
    	$I->wantTo('execute method `getPathParsed` with params');
        $I->executeMethod($obj, 'getPathParsed');
        $I->seeMethodReturns($obj, 'getPathParsed', '/users/');
    }
    
    public function getPathParams(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoSite('/', $this->container);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
        $I->seeMethodReturns($obj, __FUNCTION__, array());
    }
    
    public function getPathParamsWithParams(\CodeGuy $I) {
        $this->showTest('getPathParams with params');
        $obj = new UfoSite('/users/1/2/asd/', $this->container, '/users/');
    	$I->wantTo('execute method `getPathParams` with params');
        $I->executeMethod($obj, 'getPathParams');
        $I->seeMethodReturns($obj, 'getPathParams', array('1', '2', 'asd'));
    }
}
