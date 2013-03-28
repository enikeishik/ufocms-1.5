<?php
require_once 'Tools.php';

class UfoSystemSectionCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    private $container = null;
    private $pathSystem = '/sitemap/';
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCoreDbModel.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSystemSection.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        $this->container = new UfoContainer();
        $this->container->setConfig(new UfoConfig());
        $this->container->setDb(new UfoDb($this->container->getConfig()->dbSettings));
        $this->container->setCoreDbModel(new UfoCoreDbModel($this->container->getDb()));
        $this->container->setSite(new UfoSite($this->pathSystem, $this->pathSystem, $this->container));
        $core = new UfoCore($this->container->getConfig());
        $core->setContainer($this->container);
        $this->container->setCore($core);
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoSystemSection`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoSystemSection($this->pathSystem, 
                                            $this->container);
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function initModule(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $obj = new UfoSystemSection($this->pathSystem, 
                                    $this->container);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
    }
    
    public function getModule(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
        	$obj->initModule();
    		$mod =& $obj->getModule();
        	return is_a($mod, 'UfoSysSitemap');
        });
        $I->seeResultEquals(true);
    }
    
    public function getPage(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
        	//инициализируем объект раздела у объекта ядра
            //необходимо, поскольку при генерации страницы
            //используются вставки, которые требуют объект раздела
            $this->container->setSection($obj);
            $core =& $this->container->getCore();
            $core->setContainer($this->container);
            
            $obj->initModule();
        	$page = $obj->getPage();
        	return is_string($page) && '' != $page;
        });
        $I->seeResultEquals(true);
    }
    
    public function getField(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
    	$I->testMethod('UfoSystemSection.' . __FUNCTION__);
        $obj = new UfoSystemSection($this->pathSystem, 
                                    $this->container);
    	$I->executeTestedMethodOn($obj, 'path');
    	$I->seeMethodReturns($obj, __FUNCTION__, $this->pathSystem, array('path'));
    }
    
    public function getFields(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
        	$fields = $obj->getFields();
        	return is_object($fields) && is_a($fields, 'UfoSysSitemapStruct');
        });
        $I->seeResultEquals(true);
    }

    public function isMain(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->testMethod('UfoSection.' . __FUNCTION__);
        $obj = new UfoSystemSection($this->pathSystem, 
                                    $this->container);
        $I->executeTestedMethodOn($obj);
        $I->seeMethodReturns($obj, __FUNCTION__, false, array());
    }
    
    public function getParentArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
    		$parent = $obj->getParentArray();
        	return is_null($parent);
        });
        $I->seeResultEquals(true);
    }
    
    public function getParent(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
            $parent = $obj->getParent();
            return is_null($parent);
        });
        $I->seeResultEquals(true);
    }
    
    public function getTopArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
    		$top = $obj->getTopArray();
        	return is_array($top);
        });
        $I->seeResultEquals(true);
    }
    
    public function getTop(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
            $top = $obj->getTop();
        	return is_object($top) && is_a($top, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function getChildrenArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
    		$children = $obj->getChildrenArray();
    		echo 'expected: '; var_dump(null); echo "\r\n";
            echo 'actual:   '; var_dump($children); echo "\r\n";
            return is_null($children);
        });
        $I->seeResultEquals(true);
    }
    
    public function getChildren(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
    		$children = $obj->getChildren();
    		return is_null($children);
        });
        $I->seeResultEquals(true);
    }
    
    public function getNeighborsArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
    		$neighbors = $obj->getNeighborsArray();
    		return is_array($neighbors) && is_array($neighbors[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getNeighbors(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSystemSection($this->pathSystem, 
                                        $this->container);
    		$neighbors = $obj->getNeighbors();
    		return is_array($neighbors) && is_object($neighbors[0]);
        });
        $I->seeResultEquals(true);
    }
}
