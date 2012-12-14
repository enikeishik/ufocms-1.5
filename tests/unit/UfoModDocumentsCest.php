<?php
require_once 'Tools.php';

class UfoModDocumentsCest
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
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoToolsExt.php';
        require_once $this->root . self::DS . 'modules' . self::DS . 'UfoModDocuments' . self::DS . 'UfoModDocuments.php';
    }
    
    /*
    public function initPhp(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function main(\CodeGuy $I) {
        $I->execute(function() {
            UfoCore::main();
            return true;
        });
        $I->seeResultEquals(true);
    }
    */
}
