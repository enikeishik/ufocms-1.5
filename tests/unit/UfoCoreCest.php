<?php
require_once 'Tools.php';

class UfoCoreCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var UfoCore
     */
    private $obj = null;
    /**
     * @var string
     */
    private $root = '';
    
    public function __construct()
    {
        $_GET['path'] = '/';
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDbModel.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        //$cfg = new UfoConfig(array('cacheFsDir' => $this->root . self::DS . '_cache', 
        //                           'logDebug' => $this->root . self::DS . '_logs\dg'));
        //$cfg = new UfoConfig();
        //print_r($cfg);
        //$this->obj = new UfoCore($cfg);
        $this->obj = new UfoCore(new UfoConfig());
    }
    
    public function initPhp(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function setPathRaw(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function tryCache(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
        $I->seeMethodReturns($this->obj, __FUNCTION__, false);
    }
    
    public function initDb(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function initDbModel(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function initSite(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function initSection(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function generatePage(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function finalize(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function shutdown(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function main(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->execute(function() {
            UfoCore::main();
            return true;
        });
        $I->seeResultEquals(true);
    }
    
    public function setContainer(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            try {
                $this->obj->setContainer(new UfoContainer());
                return true;
            } catch (Exeption $e) {
                echo $e->getMessage();
                return false;
            }
        });
        $I->seeResultEquals(true);
    }
    
    public function errorHandler(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            try {
                return $this->obj->errorHandler(0, '');
            } catch (Exeption $e) {
                echo $e->getMessage();
                return true;
            }
        });
        $I->seeResultEquals(false);
    }
    
    public function insertion(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            try {
                $container = new UfoContainer();
                $container->setConfig(new UfoConfig());
                $container->setDb(new UfoDb($container->getConfig()->dbSettings));
                $container->setDbModel(new UfoDbModel($container->getDb()));
                $this->obj->setContainer($container);
                return '' != $this->obj->insertion();
            } catch (Exeption $e) {
                echo $e->getMessage();
                return false;
            }
        });
        $I->seeResultEquals(true);
    }
}
