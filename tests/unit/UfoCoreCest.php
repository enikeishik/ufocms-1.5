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
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCoreDbModel.php';
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
    
    public function getPageIsNull(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->execute(function() {
            return is_null($this->obj->getPage());
        });
        $I->seeResultEquals(true);
    }
    
    public function initPhp(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function setPathRaw(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
    }
    
    public function isSystemPath(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $ret1 = $this->obj->isSystemPath();
            var_dump($ret1);
            $_GET['path'] = '/sitemap/'; //временно устанавливаем путь служебного раздела
            $this->obj->setPathRaw();
            $ret2 = $this->obj->isSystemPath();
            $_GET['path'] = '/'; //возвращаем реальный путь
            $this->obj->setPathRaw();
            var_dump($ret2);
            return !$ret1 && $ret2;
        });
        $I->seeResultEquals(true);
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
    
    public function getPage(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->execute(function() {
            return !is_null($this->obj->getPage());
        });
        $I->seeResultEquals(true);
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
    
    public function generateError(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $core = new UfoCore(new UfoConfig());
            $core->generateError(500, 'Test error');
            $page = $core->getPage();
            return !is_null($page) && false !== strpos($page, '500 Internal Server Error');
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
                $container->setCoreDbModel(new UfoCoreDbModel($container->getDb()));
                $this->obj->setContainer($container);
                return '' != $this->obj->insertion();
            } catch (Exeption $e) {
                echo $e->getMessage();
                return false;
            }
        });
        $I->seeResultEquals(true);
    }
    
    //tests with errors
    
    public function errorInDb(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `initDb` with error');
        $I->execute(function() {
            $core = new UfoCore(new UfoConfig(array('dbLogin' => 'toor')));
            ob_start();
            $core->run();
            $ret = ob_get_clean();
            return false !== strpos($ret, '500 Internal Server Error');
        });
        $I->seeResultEquals(true);
    }
    
    public function errorInSitePathBad(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `initSite` with error PathBad');
        $I->execute(function() {
            $_GET['path'] = '/#$%/';
            $core = new UfoCore(new UfoConfig());
            ob_start();
            $core->run();
            $ret = ob_get_clean();
            return false !== strpos($ret, '404 Not Found');
        });
        $I->seeResultEquals(true);
    }
    
    public function errorInSitePathUnclosed(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `initSite` with error PathUnclosed');
        $I->execute(function() {
            $_GET['path'] = '/asd';
            $core = new UfoCore(new UfoConfig());
            ob_start();
            $core->run();
            $ret = ob_get_clean();
            return false !== strpos($ret, '301 Moved Permanently');
        });
        $I->seeResultEquals(true);
    }
    
    public function errorInSiteFilenotexists(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `initSite` with error Filenotexists');
        $I->execute(function() {
            $_GET['path'] = '/asd.htm';
            $core = new UfoCore(new UfoConfig());
            ob_start();
            $core->run();
            $ret = ob_get_clean();
            return false !== strpos($ret, '404 Not Found');
        });
        $I->seeResultEquals(true);
    }
    
    public function errorInSitePathComplex(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `initSite` with error PathComplex');
        $I->execute(function() {
            $_GET['path'] = '/a/b/c/d/e/f/g/h/i/j/k/l/m/n/o/p/q/u/v/w/x/y/z/';
            $core = new UfoCore(new UfoConfig());
            ob_start();
            $core->run();
            $ret = ob_get_clean();
            return false !== strpos($ret, '404 Not Found');
        });
        $I->seeResultEquals(true);
    }
    
    public function errorInSitePathNotexists(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `initSite` with error PathNotexists');
        $I->execute(function() {
            $_GET['path'] = '/asdasdasdasdasdasd/';
            $core = new UfoCore(new UfoConfig());
            ob_start();
            $core->run();
            $ret = ob_get_clean();
            return false !== strpos($ret, '404 Not Found');
        });
        $I->seeResultEquals(true);
    }
}
