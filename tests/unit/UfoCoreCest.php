<?php

class UfoCoreCest
{
    const DS = DIRECTORY_SEPARATOR;
    private $obj = null;
    private $root = '';
    
    public function __construct()
    {
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        //$this->obj = new UfoCore(new UfoConfig(array('cacheFsDir' => $this->root . self::DS . '_cache')));
        $this->obj = new UfoCore(new UfoConfig());
    }
    
    /**
     * Тестируем методы трейта
     */
    public function loadClass(\CodeGuy $I) {
        $class = 'UfoCacheFs';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class);
        $I->execute(function() use ($class) {
            return class_exists($class);
        });
        $I->seeResultEquals(true);
    }
    
    public function loadModule(\CodeGuy $I) {
        $class = 'UfoModDocuments';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class);
        $I->execute(function() use ($class) {
            return class_exists($class);
        });
        $I->seeResultEquals(true);
    }
    
    /**
     * Тестируем методы класса
     */
    public function initPhp(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function setPathRaw(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function tryCache(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
        $I->seeMethodReturns($this->obj, __FUNCTION__, false);
    }

    public function initDb(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function initSite(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function initSection(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function generatePage(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function finalize(\CodeGuy $I) {
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function shutdown(\CodeGuy $I) {
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
}
