<?php
require_once 'Tools.php';

class UfoToolsCest
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
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once __DIR__ . self::DS . 'UfoToolsDummy.php';
        $this->obj = new UfoToolsDummy();
    }
    
    /**
     * Тестируем методы трейта UfoTools
     */
    public function loadClass(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $class = 'UfoCacheFs';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class);
        $I->execute(function() use ($class) {
            return class_exists($class);
        });
        $I->seeResultEquals(true);
    }
    
    public function loadModule(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $class = 'UfoModDocuments';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class);
        $I->execute(function() use ($class) {
            return class_exists($class);
        });
        $I->seeResultEquals(true);
    }
    
    public function loadTemplate(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $class = 'UfoTplDocuments';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class);
        $I->execute(function() use ($class) {
            return class_exists($class);
        });
        $I->seeResultEquals(true);
    }
    
    public function loadLayout(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $template = 'UfoTplDocuments';
            $this->obj->loadTemplate($template);
            $container = new UfoContainer();
            $container->setConfig(new UfoConfig());
            $container->setDb(new UfoDb($container->getConfig()->dbSettings));
            $container->setCore(new UfoCore($container->getConfig()));
            $container->setSite(new UfoSite('/', $container));
            $section = new UfoSection('/', $container);
            $section->initModule();
            $container->setSection($section);
            $tpl = new $template($container);
            $this->obj->loadLayout($tpl);
            return true;
        });
        $I->seeResultEquals(true);
    }
    
    public function redirect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
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
    
    public function isPath(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('/asd/', true);
        $vals[] = array('/asd/qwe-zxc/~123_vfr/index.html', true);
        $vals[] = array('/', false);
        $vals[] = array('', false);
        $vals[] = array('/as..d/', false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                $ret = $this->obj->$f($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }

    public function isSafeForPath(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('/asd/', true);
        $vals[] = array('/asd/qwe-zxc/~123_vfr/index.html', true);
        $vals[] = array('/', true);
        $vals[] = array('', true);
        $vals[] = array('/as..d/', false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                $ret = $this->obj->$f($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
}
