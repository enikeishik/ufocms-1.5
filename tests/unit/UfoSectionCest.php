<?php
require_once 'Tools.php';

class UfoSectionCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    private $container = null;
    private $mainSectionId = -1;
    private $sectionId = -1;
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDbModel.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        $this->container = new UfoContainer();
        $this->container->setConfig(new UfoConfig(array('cacheFsDir' => $root . self::DS . '_cache')));
        $this->container->setDb(new UfoDb($this->container->getConfig()->dbSettings));
        $this->container->setDbModel(new UfoDbModel($this->container->getDb()));
        $this->container->setSite(new UfoSite('/', $this->container));
        $this->container->setSection(new UfoSection(new UfoSectionStruct(array('id' => -1, 'moduleid' => -1)), $this->container));
        $core = new UfoCore($this->container->getConfig());
        $core->setContainer($this->container);
        $this->container->setCore($core);
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoSection`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoSection($this->sectionId,  
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
        $obj = new UfoSection($this->sectionId,  
                              $this->container);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
    }
    
    public function getModule(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
        	$obj->initModule();
    		$mod =& $obj->getModule();
        	return is_a($mod, 'UfoModDocuments');
        });
        $I->seeResultEquals(true);
    }
    
    public function getPage(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
        	$obj->initModule();
        	$page = $obj->getPage();
        	return is_string($page) && '' != $page;
        });
        $I->seeResultEquals(true);
    }
    
    public function getField(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
    	$I->testMethod('UfoSection.' . __FUNCTION__);
        $obj = new UfoSection($this->sectionId,  
                              $this->container);
    	$I->executeTestedMethodOn($obj, 'id');
    	$I->seeMethodReturns($obj, __FUNCTION__, $this->sectionId, array('id'));
    }
    
    public function getFields(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
        	$fields = $obj->getFields();
        	return is_object($fields) && is_a($fields, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }

    public function isMain(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->testMethod('UfoSection.' . __FUNCTION__);
        $obj = new UfoSection($this->sectionId,  
                              $this->container);
        $I->executeTestedMethodOn($obj);
        $I->seeMethodReturns($obj, __FUNCTION__, true, array());
    }
    
    public function getParentArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
    		$parent = $obj->getParentArray();
        	return is_null($parent);//is_array($parent) && array_key_exists('title', $parent);
        });
        $I->seeResultEquals(true);
    }
    
    public function getParent(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
            $parent = $obj->getParent();
            return is_null($parent);//is_object($parent) && is_a($parent, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function getTopArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
    		$top = $obj->getTopArray();
        	return is_null($top);//is_array($top) && array_key_exists('title', $top);
        });
        $I->seeResultEquals(true);
    }
    
    public function getTop(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
            $top = $obj->getTop();
            return is_null($top);//is_object($top) && is_a($top, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function getChildrenArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
    		$children = $obj->getChildrenArray();
    		return is_null($children);//is_array($children) && is_array($children[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getChildren(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
    		$children = $obj->getChildren();
    		return is_null($children);//is_array($children) && is_object($children[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getNeighborsArray(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
    		$neighbors = $obj->getNeighborsArray();
    		return is_null($neighbors);//is_array($neighbors) && is_array($neighbors[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getNeighbors(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->sectionId,  
                                  $this->container);
    		$neighbors = $obj->getNeighbors();
    		return is_null($neighbors);//is_array($neighbors) && is_object($neighbors[0]);
        });
        $I->seeResultEquals(true);
    }
}
