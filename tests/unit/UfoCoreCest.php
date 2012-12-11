<?php

class UfoCoreCest
{
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
    
    public function redirect(\CodeGuy $I) {
        $url = 'http://www.mysite.com/page1/';
        $out = '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\r\n" .
               '<HTML><HEAD>' . "\r\n" .
               '<TITLE>301 Moved Permanently</TITLE>' . "\r\n" .
               '</HEAD><BODY>' . "\r\n" .
               '<H1>Moved Permanently</H1>' . "\r\n" .
               'The document has moved <a href="' . $url . '">here</a>.<P>' . "\r\n" .
               '</BODY></HTML>' . "\r\n";
        $obj =& $this->obj;
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() use ($obj, $url, $out) {
            ob_start();
            $obj->redirect($url);
            return ob_get_clean() == $out;
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
