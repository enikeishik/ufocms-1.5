<?php

class UfoSectionCest
{
    const DS = DIRECTORY_SEPARATOR;
    private $config = null;
    private $db = null;
    private $root = '';
    private $mainSectionId = -1;
    private $sectionId = -1;
    
    public function __construct()
    {
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        $this->config = new UfoConfig(array('cacheFsDir' => $this->root . self::DS . '_cache'));
        $this->db = new UfoDb($this->config->dbSettings);
    }
    
    public function createObject(\CodeGuy $I) {
        $I->wantTo('create object `UfoSection`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoSection($this->config, $this->db, $this->sectionId);
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function initModule(\CodeGuy $I) {
    	$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($obj, __FUNCTION__);
    }
    
    public function getModule(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
        	$obj->initModule();
    		$mod =& $obj->getModule();
        	return is_a($mod, 'UfoModDocuments');
        });
        $I->seeResultEquals(true);
    }
    
    public function getPage(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
        	$obj->initModule();
        	$page = $obj->getPage();
        	return is_string($page) && '' != $page;
        });
        $I->seeResultEquals(true);
    }
    
    public function getField(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
    	$I->testMethod('UfoSection.' . __FUNCTION__);
    	$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    	$I->executeTestedMethodOn($obj, 'id');
    	$I->seeMethodReturns($obj, __FUNCTION__, $this->sectionId, array('id'));
    }
    
    public function getFields(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
        	$fields = $obj->getFields();
        	return is_object($fields) && is_a($fields, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }

    public function isMain(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->testMethod('UfoSection.' . __FUNCTION__);
        $obj = new UfoSection($this->config, $this->db, $this->mainSectionId);
        $I->executeTestedMethodOn($obj);
        $I->seeMethodReturns($obj, __FUNCTION__, true, array());
    }
    
    public function getParentArray(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    		$parent = $obj->getParentArray();
        	return is_null($parent);//is_array($parent) && array_key_exists('title', $parent);
        });
        $I->seeResultEquals(true);
    }
    
    public function getParent(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->config, $this->db, $this->sectionId);
            $parent = $obj->getParent();
            return is_null($parent);//is_object($parent) && is_a($parent, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function getTopArray(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    		$top = $obj->getTopArray();
        	return is_null($top);//is_array($top) && array_key_exists('title', $top);
        });
        $I->seeResultEquals(true);
    }
    
    public function getTop(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoSection($this->config, $this->db, $this->sectionId);
            $top = $obj->getTop();
            return is_null($top);//is_object($top) && is_a($top, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function getChildrenArray(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    		$children = $obj->getChildrenArray();
    		return is_null($children);//is_array($children) && is_array($children[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getChildren(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    		$children = $obj->getChildren();
    		return is_null($children);//is_array($children) && is_object($children[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getNeighborsArray(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    		$neighbors = $obj->getNeighborsArray();
    		return is_null($neighbors);//is_array($neighbors) && is_array($neighbors[0]);
        });
        $I->seeResultEquals(true);
    }
    
    public function getNeighbors(\CodeGuy $I) {
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
    		$obj = new UfoSection($this->config, $this->db, $this->sectionId);
    		$neighbors = $obj->getNeighbors();
    		return is_null($neighbors);//is_array($neighbors) && is_object($neighbors[0]);
        });
        $I->seeResultEquals(true);
    }
}
