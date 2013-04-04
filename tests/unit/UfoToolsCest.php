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
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCoreDbModel.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
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
    
    public function loadModuleStruct(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $class = 'UfoSysSitemap';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class, $class . 'Struct');
        $I->execute(function() use ($class) {
            return class_exists($class . 'Struct');
        });
        $I->seeResultEquals(true);
    }
    
    public function loadModuleDbModel(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $class = 'UfoSysSitemap';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class, $class . 'DbModel');
        $I->execute(function() use ($class) {
            return class_exists($class . 'DbModel');
        });
        $I->seeResultEquals(true);
    }
    
    public function loadInsertionModule(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $class = 'UfoModNews';
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__, $class, $class . 'Ins');
        $I->execute(function() use ($class) {
            return class_exists($class . 'Ins');
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
            $container->setCoreDbModel(new UfoCoreDbModel($container->getDb()));
            $container->setSite(new UfoSite('/', '', $container));
            $section = new UfoSection('/', $container);
            $section->initModule();
            $container->setSection($section);
            $core = new UfoCore($container->getConfig());
            $core->setContainer($container);
            $container->setCore($core);
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
            //in codeception header (and similar) generate an error, 
            //because codeception already make output before
            //so we use @ in here
            @$obj->redirect($url);
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
    
    /**
     * Тестируем методы трейта UfoToolsExt
     */
    public function isInt(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $vals[] = array(0, true);
        $vals[] = array('123456', true);
        $vals[] = array('12asd56', false);
        $vals[] = array('12345678901', false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                var_dump($v[0]);
                $ret = $this->obj->$f($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    public function isArrayOfIntegers(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array(array('12', 345, '2346236', 0), true);
        $vals[] = array(array('12', 345, 3.14, '2346236', 0), false);
        $vals[] = array(array('12', 345, 123456789123, '2346236', 0), false);
        $vals[] = array(array('12', 345, '123a', '2346236', 0), false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                print_r($v[0]);
                $ret = $this->obj->$f($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    public function isStringOfIntegers(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('12, 345, 123, 2346236, 0', true);
        $vals[] = array('12, 3.45, 123, 2346236, 0', false);
        $vals[] = array('12, 3a45, 123, 2346236, 0', false);
        $vals[] = array('12, 123456789123, 123, 2346236, 0', false);
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
    
    public function isEmail(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('abc@mysite.com', true);
        $vals[] = array('John.Smith.agent-007@section1.itdep.my-site.com', true);
        $vals[] = array('abc_efg@mysite.com', true);
        $vals[] = array('abc@my_site.com', false);
        $vals[] = array('abc@mysite.c', false);
        $vals[] = array('abc@mysite', false);
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
    
    public function appendDigits(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array(1, '01', 2, true);
        $vals[] = array(1, '10', 2, false);
        $vals[] = array('1', '01', 2, true);
        $vals[] = array('1', '10', 2, false);
        $vals[] = array('11', '11', 2, true);
        $vals[] = array('11', '11', 2, false);
        $vals[] = array('11', '00011', 5, true);
        $vals[] = array('11', '11000', 5, false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                echo 'digitsTotal `' . $v[2] . '`' . "\r\n";
                echo 'left `' . (int) $v[3] . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2], $v[3]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function isDate(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('2015-02-01', true);
        $vals[] = array('1960-12-5', true);
        $vals[] = array('2000-1-13', true);
        $vals[] = array('01-01-01', true);
        $vals[] = array('2012-13-01', true);
        $vals[] = array('2222-22-22', true); //2223-10-22
        $vals[] = array('20-12-13-01', false);
        $vals[] = array('20-01', false);
        $vals[] = array('', false);
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
    
    public function dateFromString(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('2015-02-01', true);
        $vals[] = array('1960-12-5', true);
        $vals[] = array('2000-1-13', true);
        $vals[] = array('01-01-01', true);
        $vals[] = array('2012-13-01', true);
        $vals[] = array('2222-22-22', true); //2223-10-22
        $vals[] = array('20-12-13-01', false);
        $vals[] = array('20-01', false);
        $vals[] = array('', false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                $ret = $this->obj->$f($v[0]);
                var_dump($ret);
                return is_a($ret, 'DateTime');
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    public function safeSql(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('SELECT * FROM mytable WHERE myid=123', 
                        'SELECT * FROM mytable WHERE myid=123');
        $vals[] = array("SELECT * FROM mytable WHERE mystring='abc'", 
                        "SELECT * FROM mytable WHERE mystring=\'abc\'");
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                $res = $this->obj->$f($v[0]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function jsAsString(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array('var i = 0; function retVal(v) { return v; } var j = retVal(i);', 
                        'var i = 0; function retVal(v) { return v; } var j = retVal(i);', 
                        true);
        $vals[] = array("var s = '';\r\nfunction retVal(v)\r\n{\r\n\treturn v;\r\n}\r\nvar newS = retVal(s);", 
                        "var s = \'\';\\r\\nfunction retVal(v)\\r\\n{\\r\\n\treturn v;\\r\\n}\\r\\nvar newS = retVal(s);", 
                        true);
        $vals[] = array('var s = "<p>abc</p>";', 
                        'var s = "<!p>abc<!/p>";', 
                        true);
        $vals[] = array('var s = "<p>abc</p>";', 
                        'var s = "<p>abc</p>";', 
                        false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                echo 'flag `' . (int) $v[2] . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function writeLog(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->execute(function() {
            $cfg = new UfoConfig();
            $ret = $this->obj->writeLog('test', $cfg->logDebug);
            echo 'expected: > 0' . "\r\n";
            echo 'actual:   '; var_dump($ret); echo "\r\n";
            return $ret > 0;
        });
        $I->seeResultEquals(true);
    }
    
    public function sendmail(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->execute(function() {
            //$cfg = new UfoConfig();
            $ret = $this->obj->sendmail('test@test', 'test', 'test', 'From: test@test');
            echo 'expected: true' . "\r\n";
            echo 'actual:   '; var_dump($ret); echo "\r\n";
            return $ret;
        });
        $I->seeResultEquals(true);
    }
}
