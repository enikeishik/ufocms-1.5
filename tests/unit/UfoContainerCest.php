<?php
require_once 'Tools.php';

class UfoContainerCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var string
     */
    private $root = '';
    
    public function __construct()
    {
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoToolsExt.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDbModel.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDebug.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
        require_once $this->root . self::DS . 'modules' . self::DS . 'UfoModDocuments' . self::DS . 'UfoModDocuments.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoError.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoErrorStruct.php';
    }
    
    public function createEmptyObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoContainer` without parameters');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoContainer();
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function createFilledObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoContainer` with parameters');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoContainer(array('config' => new UfoConfig()));
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function configSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setConfig`, `getConfig`');
        $I->execute(function() {
            $obj = new UfoContainer();
            $ret1 = $obj->getConfig();
            $obj->setConfig(new UfoConfig());
            $ret2 = $obj->getConfig();
            return is_null($ret1) && is_a($ret2, 'UfoConfig');
        });
        $I->seeResultEquals(true);
    }
    
    public function dbSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setDb`, `getDb`');
        $I->execute(function() {
            $obj = new UfoContainer();
            $ret1 = $obj->getDb();
            $cfg = new UfoConfig();
            $obj->setDb(new UfoDb($cfg->dbSettings));
            $ret2 = $obj->getDb();
            return is_null($ret1) && is_a($ret2, 'UfoDb');
        });
        $I->seeResultEquals(true);
    }
    
    public function dbModelSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setDbModel`, `getDbModel`');
        $I->execute(function() {
            $obj = new UfoContainer();
            $ret1 = $obj->getDbModel();
            $cfg = new UfoConfig();
            $obj->setDbModel(new UfoDbModel(new UfoDb($cfg->dbSettings)));
            $ret2 = $obj->getDbModel();
            return is_null($ret1) && is_a($ret2, 'UfoDbModel');
        });
        $I->seeResultEquals(true);
    }
    
    public function debugSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setDebug`, `getDebug`');
        $I->execute(function() {
            $obj = new UfoContainer();
            $ret1 = $obj->getDebug();
            $obj->setDebug(new UfoDebug(new UfoConfig()));
            $ret2 = $obj->getDebug();
            return is_null($ret1) && is_a($ret2, 'UfoDebug');
        });
        $I->seeResultEquals(true);
    }
    
    public function coreSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setCore`, `getCore`');
        $I->execute(function() {
            $obj = new UfoContainer();
            $ret1 = $obj->getCore();
            $obj->setCore(new UfoCore(new UfoConfig()));
            $ret2 = $obj->getCore();
            return is_null($ret1) && is_a($ret2, 'UfoCore');
        });
        $I->seeResultEquals(true);
    }
    
    public function siteSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setSite`, `getSite`');
        $I->execute(function() {
            $cfg = new UfoConfig();
            $db = new UfoDb($cfg->dbSettings);
            $dbModel = new UfoDbModel(new UfoDb($cfg->dbSettings));
            $obj = new UfoContainer(array('config' => $cfg, 'db' => $db, 'dbModel' => $dbModel));
            
            $ret1 = $obj->getSite();
            $obj->setSite(new UfoSite('/', '', $obj));
            $ret2 = $obj->getSite();
            return is_null($ret1) && is_a($ret2, 'UfoSite');
        });
        $I->seeResultEquals(true);
    }
    
    public function sectionSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setSection`, `getSection`');
        $I->execute(function() {
            $cfg = new UfoConfig();
            $db = new UfoDb($cfg->dbSettings);
            $dbModel = new UfoDbModel(new UfoDb($cfg->dbSettings));
            $obj = new UfoContainer(array('config' => $cfg, 'db' => $db, 'dbModel' => $dbModel));
            
            $ret1 = $obj->getSection();
            $obj->setSection(new UfoSection('/', $obj));
            $ret2 = $obj->getSection();
            return is_null($ret1) && is_a($ret2, 'UfoSection');
        });
        $I->seeResultEquals(true);
    }
    
    public function sectionStructSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setSectionStruct`, `getSectionStruct`');
        $I->execute(function() {
            $obj = new UfoContainer();
            $ret1 = $obj->getSectionStruct();
            $obj->setSectionStruct(new UfoSectionStruct());
            $ret2 = $obj->getSectionStruct();
            return is_null($ret1) && is_a($ret2, 'UfoSectionStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function moduleSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setModule`, `getModule`');
        $I->execute(function() {
            $cfg = new UfoConfig();
            $db = new UfoDb($cfg->dbSettings);
            $dbModel = new UfoDbModel(new UfoDb($cfg->dbSettings));
            $obj = new UfoContainer(array('config' => $cfg, 'db' => $db, 'dbModel' => $dbModel));
            $site = new UfoSite('/', '', $obj);
            $obj->setSite($site);
            $section = new UfoSection(new UfoSectionStruct(array('id' => -1, 'moduleid' => -1)), $obj);
            $obj->setSection($section);
            
            $ret1 = $obj->getModule();
            $obj->setModule(new UfoModDocuments($obj));
            $ret2 = $obj->getModule();
            return is_null($ret1) && is_a($ret2, 'UfoModule');
        });
        $I->seeResultEquals(true);
    }
    
    public function errorSetGet(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute methods `setError`, `getError`');
        $I->execute(function() {
            $obj = new UfoContainer(array('config' => new UfoConfig())); //config нужен для UfoError
            $ret1 = $obj->getError();
            $obj->setError(new UfoError(new UfoErrorStruct(0, ''), $obj));
            $ret2 = $obj->getError();
            return is_null($ret1) && is_a($ret2, 'UfoError');
        });
        $I->seeResultEquals(true);
    }
}
