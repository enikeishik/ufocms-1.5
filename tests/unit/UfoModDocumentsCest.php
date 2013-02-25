<?php
require_once 'Tools.php';

class UfoModDocumentsCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var UfoContainer
     */
    private $container = null;
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
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoToolsExt.php';
        require_once $this->root . self::DS . 'modules' . self::DS . 'UfoModDocuments' . self::DS . 'UfoModDocuments.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        $this->container = new UfoContainer();
        $this->container->setConfig(new UfoConfig(array('cacheFsDir' => $this->root . self::DS . '_cache')));
        $this->container->setDb(new UfoDb($this->container->getConfig()->dbSettings));
        $this->container->setCore(new UfoCore($this->container->getConfig()));
        $this->container->setSite(new UfoSite('/', $this->container));
        $this->container->setSection(new UfoSection(new UfoSectionStruct(array('moduleid' => -1)), $this->container));
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoModDocuments`');
        $I->execute(function() {
            $obj = null;
            try {
                $obj = new UfoModDocuments($this->container);
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function getTitle(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoModDocuments($this->container);
        	return is_string($obj->getTitle());
        });
        $I->seeResultEquals(true);
    }
    
    public function getContent(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoModDocuments($this->container);
        	return is_string($obj->getContent());
        });
        $I->seeResultEquals(true);
    }
    
    public function getPage(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
    	$I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $obj = new UfoModDocuments($this->container);
        	return is_string($obj->getPage());
        });
        $I->seeResultEquals(true);
    }
}
