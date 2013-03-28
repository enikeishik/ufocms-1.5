<?php
require_once 'Tools.php';

class UfoUsersCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var UfoContainer
     */
    private $container = null;
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoUsers.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCoreDbModel.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        $this->container = new UfoContainer();
        $this->container->setConfig(new UfoConfig());
        $this->container->setDb(new UfoDb($this->container->getConfig()->dbSettings));
        $this->container->setCoreDbModel(new UfoCoreDbModel($this->container->getDb()));
        $this->container->setSite(new UfoSite('/', '', $this->container));
        $core = new UfoCore($this->container->getConfig());
        $core->setContainer($this->container);
        $this->container->setCore($core);
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoUsers`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoUsers($this->container);
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    /*
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
    */
}
