<?php

class UfoToolsCest
{
    public $class = 'UfoTools';
    private $dbSettings = array('Host'     => 'sql09.freemysql.net', 
                                'Username' => 'lifehacker', 
                                'Password' => 'ahbvecrekm', 
                                'Database' => 'lifehacker');
    private $corePath = '';
    
    function __construct()
    {
        $this->corePath = __DIR__ . DIRECTORY_SEPARATOR . 
                          '..' . DIRECTORY_SEPARATOR . 
                          '..' . DIRECTORY_SEPARATOR  . 
                          '_core';
    }
    
    /**
     * Тесты метода isInt с разными параметрами.
     */
    public function isInt(\CodeGuy $I) {
        $vals[] = array(0, true);
        $vals[] = array('123456', true);
        $vals[] = array('12asd56', false);
        $vals[] = array('12345678901', false);
        $I->wantTo('execute method `isInt`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                var_dump($v[0]);
                $ret = UfoTools::isInt($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    /**
     * Тесты метода isArrayOfIntegers с разными параметрами.
     */
    public function isArrayOfIntegers(\CodeGuy $I) {
        $vals[] = array(array('12', 345, '2346236', 0), true);
        $vals[] = array(array('12', 345, 3.14, '2346236', 0), false);
        $vals[] = array(array('12', 345, 123456789123, '2346236', 0), false);
        $vals[] = array(array('12', 345, '123a', '2346236', 0), false);
        $I->wantTo('execute method `isArrayOfIntegers`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                print_r($v[0]);
                $ret = UfoTools::isArrayOfIntegers($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    /**
     * Тесты метода isStringOfIntegers с разными параметрами.
     */
    public function isStringOfIntegers(\CodeGuy $I) {
        $vals[] = array('12, 345, 123, 2346236, 0', true);
        $vals[] = array('12, 3.45, 123, 2346236, 0', false);
        $vals[] = array('12, 3a45, 123, 2346236, 0', false);
        $vals[] = array('12, 123456789123, 123, 2346236, 0', false);
        $I->wantTo('execute method `isStringOfIntegers`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                $ret = UfoTools::isStringOfIntegers($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    /**
     * Тесты метода isEmail с разными параметрами.
     */
    public function isEmail(\CodeGuy $I) {
        $vals[] = array('abc@mysite.com', true);
        $vals[] = array('John.Smith.agent-007@section1.itdep.my-site.com', true);
        $vals[] = array('abc_efg@mysite.com', true);
        $vals[] = array('abc@my_site.com', false);
        $vals[] = array('abc@mysite.c', false);
        $vals[] = array('abc@mysite', false);
        $I->wantTo('execute method `isEmail`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                $ret = UfoTools::isEmail($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    /**
     * Тесты метода isPath с разными параметрами.
     */
    public function isPath(\CodeGuy $I) {
        $vals[] = array('/abc/def/index.html', true);
        $vals[] = array('/~abc/d-ef/_index.html', true);
        $vals[] = array('../abc/', false);
        $vals[] = array('//abc/', false);
        $vals[] = array('/abc+def/', false);
        $vals[] = array('/abc,def/', false);
        $vals[] = array('\abc\def/', false);
        $I->wantTo('execute method `isPath`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                $ret = UfoTools::isPath($v[0]);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals($v[1]);
        }
    }
    
    /**
     * Тесты метода safeSql с разными параметрами.
     */
    public function safeSql(\CodeGuy $I) {
        $vals[] = array('SELECT * FROM mytable WHERE myid=123', 
                        'SELECT * FROM mytable WHERE myid=123');
        $vals[] = array("SELECT * FROM mytable WHERE mystring='abc'", 
                        "SELECT * FROM mytable WHERE mystring=\'abc\'");
        $I->wantTo('execute method `safeSql`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                $res = UfoTools::safeSql($v[0]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    /**
     * Тесты метода jsAsString с разными параметрами.
     */
    public function jsAsString(\CodeGuy $I) {
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
        $I->wantTo('execute method `jsAsString`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                echo 'flag `' . (int) $v[2] . '`' . "\r\n";
                $res = UfoTools::jsAsString($v[0], $v[2]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    /**
     * Тесты метода insertParagraphs с разными параметрами.
     */
    public function insertParagraphs(\CodeGuy $I) {
        $vals[] = array(" Тестовый текст\r\nразбитый на строки ", 
                        "<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "\r\n");
        $vals[] = array("Тестовый текст в одну строку", 
                        "<p>Тестовый текст в одну строку</p>\r\n", 
                        "\r\n");
        $I->wantTo('execute method `insertParagraphs`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                echo 'flag `' . $v[2] . '`' . "\r\n";
                $res = UfoTools::insertParagraphs($v[0], $v[2]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    /**
     * Тесты метода removeParagraphs с разными параметрами.
     */
    public function removeParagraphs(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "Тестовый текст\r\nразбитый на строки", 
                        "\r\n");
        $vals[] = array("<p>Тестовый текст в одну строку</p>\r\n", 
                        "Тестовый текст в одну строку", 
                        "\r\n");
        $I->wantTo('execute method `removeParagraphs`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                echo 'flag `' . $v[2] . '`' . "\r\n";
                $res = UfoTools::removeParagraphs($v[0], $v[2]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    /**
     * Тесты метода getFirstParagraph с разными параметрами.
     */
    public function getFirstParagraph(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст</p>\r\n");
        $vals[] = array("Тестовый текст<p>Параграф после текста</p>\r\n", 
                        "Тестовый текст");
        $vals[] = array("Тестовый текст.<br />Текст после текста", 
                        "");
        $I->wantTo('execute method `getFirstParagraph`');
        foreach ($vals as $v) {
            $I->execute(function() use ($v) {
                echo 'test `' . $v[0] . '`' . "\r\n";
                echo 'expected result `' . $v[1] . '`' . "\r\n";
                $res = UfoTools::getFirstParagraph($v[0]);
                echo 'actual result `' . $res . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    /**
     * Тест загрузки существующего класса.
     */
    public function loadExistingClass(\CodeGuy $I) {
        $I->wantTo('test method `loadClass`');
        $I->testMethod('UfoTools::loadClass');
        
        $I->execute(function() {
            $class = 'UfoDummy';
            UfoTools::loadClass($class, $this->corePath);
            $obj = new $class;
            echo $obj . "\r\n";
            return class_exists($class);
        });
        $I->seeResultEquals(true);
    }
    
    /**
     * Тест загрузки несуществующего класса.
     */
    public function loadNoexistingClass(\CodeGuy $I) {
        $I->wantTo('test method `loadClass`');
        $I->testMethod('UfoTools::loadClass');
        
        $I->execute(function() {
            $class = 'UfoBaddummy';
            @UfoTools::loadClass($class, $this->corePath);
            return class_exists($class);
        });
        $I->seeResultEquals(false);
    }
    
    /**
     * Тест проверки версии PHP.
     */
    public function isPhpUptodate(\CodeGuy $I) {
        $I->wantTo('test method `isPhpUptodate`');
        $I->testMethod('UfoTools::isPhpUptodate');
        
        $I->execute(function() {
            return UfoTools::isPhpUptodate();
        });
        $I->seeResultEquals(true);
    }
}
