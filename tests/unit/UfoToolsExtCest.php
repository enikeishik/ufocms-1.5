<?php
require_once 'Tools.php';

class UfoToolsExtCest
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
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoToolsExt.php';
        require_once __DIR__ . self::DS . 'UfoToolsExtDummy.php';
        $this->obj = new UfoToolsExtDummy();
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
    public function safeSql(\CodeGuy $I) {
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
    
    public function insertParagraphs(\CodeGuy $I) {
        $vals[] = array(" Тестовый текст\r\nразбитый на строки\r\n", 
                        "<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>", 
                        "\r\n");
        $vals[] = array(" Тестовый текст\r\nразбитый на строки ", 
                        "<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>", 
                        "\r\n");
        $vals[] = array("Тестовый текст в одну строку", 
                        "<p>Тестовый текст в одну строку</p>", 
                        "\r\n");
        $vals[] = array("", 
                        "<p></p>", 
                        "\r\n");
        $vals[] = array(" Тестовый текст\r\nразбитый на строки ", 
                        "<p>Тестовый текст\r\nразбитый на строки</p>", 
                        "");
        $vals[] = array("Тестовый текст в одну строку", 
                        "<p>Тестовый текст в одну строку</p>", 
                        "");
        $vals[] = array("", 
                        "<p></p>", 
                        "\r\n");
        $vals[] = array("", 
                        "<p></p>", 
                        "");
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                echo 'flag `' . str_replace($sc, $scr, $v[2]) . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function removeParagraphs(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "Тестовый текст\r\nразбитый на строки", 
                        "\r\n");
        $vals[] = array("<p>Тестовый текст в одну строку</p>\r\n", 
                        "Тестовый текст в одну строку", 
                        "\r\n");
        $vals[] = array("<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "Тестовый текст\r\nразбитый на строки", 
                        "");
        $vals[] = array("<p>Тестовый текст в одну строку</p>\r\n", 
                        "Тестовый текст в одну строку", 
                        "");
        $vals[] = array("", 
                        "", 
                        "\r\n");
        $vals[] = array("", 
                        "", 
                        "");
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                echo 'flag `' . str_replace($sc, $scr, $v[2]) . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function getFirstParagraph(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст</p>\r\n");
        $vals[] = array("Тестовый текст<p>Параграф после текста</p>\r\n", 
                        "Тестовый текст");
        $vals[] = array("Тестовый текст.<br />Текст после текста", 
                        "");
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                $res = $this->obj->$f($v[0]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function сutNice(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "Тестовый текст,", 
                        20, 0, true);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст,", 
                        20, 0, false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "екст, первая строка", 
                        20, 10, true);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "й текст, первая", 
                        20, 10, false);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "", 
                        0, 10, false);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "", 
                        -10, 10, false);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "", 
                        20, -10, false);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "Тестовый текст", 
                        20, 0, true);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "<p>Тестовый", 
                        20, 0, false);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "", 
                        20, 20, true);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "", 
                        20, 20, false);
        $vals[] = array("<p>Тестовый текст</p>\r\n", 
                        "й", 
                        20, 10, false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                echo 'length `' . $v[2] . '`' . "\r\n";
                echo 'offset `' . $v[3] . '`' . "\r\n";
                echo 'removeTags `' . (int) $v[4] . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2], $v[3], $v[4]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function cutMiddle(\CodeGuy $I) {
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый ...строки\r\n", 
                        20, '...');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый ... троки\r\n", 
                        20, ' ... ');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый .. строки\r\n", 
                        20, '..');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый .. строки\r\n", 
                        20, ' .. ');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый те...на строки\r\n", 
                        25, '...');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый т ... а строки\r\n", 
                        25, ' ... ');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый тек..на строки\r\n", 
                        25, '..');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Тестовый те .. а строки\r\n", 
                        25, ' .. ');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "...", 
                        3, '...');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Т..", 
                        3, '..');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "", 
                        2, '...');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "", 
                        0, '');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "", 
                        -10, '');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Т\n", 
                        2, '');
        $vals[] = array("Тестовый текст, первая строка содержащая краткий анонс.\r\nТекст разбитый на строки\r\n", 
                        "Те\n", 
                        3, '');
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                echo 'length `' . $v[2] . '`' . "\r\n";
                echo 'cutStub `' . $v[3] . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2], $v[3]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function cutBySeparator(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.<!-- separator --></p>\r\n<p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p><!-- separator -->\r\n<p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<!-- separator --><p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<!-- separator --><p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p>разбитый на строки</p>", 
                        '<!-- separator -->', true);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p><!-- separator -->разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.<!-- separator --> </p>\r\n<p>разбитый на строки</p>\r\n", 
                        "", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.<!-- separator --> </p>\r\n<p>разбитый на строки</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс. </p>", 
                        '<!-- separator -->', true);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                echo 'separator `' . $v[2] . '`' . "\r\n";
                echo 'more `' . (int) $v[3] . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2], $v[3]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function getTextPartBySeparator(\CodeGuy $I) {
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.<!-- separator --></p>\r\n<p>разбитый на строки<!-- separator --></p>\r\n<p>несколько раз</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>", 
                        '<!-- separator -->', 0, false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.<!-- separator --></p>\r\n<p>разбитый на строки<!-- separator --></p>\r\n<p>несколько раз</p>\r\n", 
                        "</p>\r\n<p>разбитый на строки</p>", 
                        '<!-- separator -->', 1, false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.<!-- separator --></p>\r\n<p>разбитый на строки<!-- separator --></p>\r\n<p>несколько раз</p>\r\n", 
                        "\r\n<p>несколько раз</p>\r\n", 
                        '<!-- separator -->', 2, false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p><!-- separator -->разбитый на строки</p>\r\n<p><!-- separator -->несколько раз</p>\r\n", 
                        "<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n", 
                        '<!-- separator -->', 0, false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p><!-- separator -->разбитый на строки</p>\r\n<p><!-- separator -->несколько раз</p>\r\n", 
                        "разбитый на строки</p>\r\n", 
                        '<!-- separator -->', 1, false);
        $vals[] = array("<p>Тестовый текст, первая строка содержащая краткий анонс.</p>\r\n<p><!-- separator -->разбитый на строки</p>\r\n<p><!-- separator -->несколько раз</p>\r\n", 
                        "\r\n", 
                        '<!-- separator -->', 2, false);
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                echo 'separator `' . $v[2] . '`' . "\r\n";
                echo 'part `' . $v[3] . '`' . "\r\n";
                echo 'more `' . (int) $v[4] . '`' . "\r\n";
                $res = $this->obj->$f($v[0], $v[2], $v[3], $v[4]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function srcFromImg(\CodeGuy $I) {
        $vals[] = array("<img src=\"/path/image.jpg\" alt=\"\" />", 
                        "/path/image.jpg");
        $vals[] = array("<p>Тестовый <img src=\"/path/image.jpg\" alt=\"\" /> текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "/path/image.jpg");
        $vals[] = array("<p>Тестовый <img src=\"/Путь%20к%20картинке/Фото0001.JPG\" alt=\"\" /> текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "/Путь%20к%20картинке/Фото0001.JPG");
        $vals[] = array("Тестовый текст<p>Параграф <IMG SRC='/path-with_smth.spec,chars/~image.jpeg' ALT=''>после текста</p>\r\n", 
                        "/path-with_smth.spec,chars/~image.jpeg");
        $vals[] = array("<p>Тестовый <img border=\"0\" src=\"/path/image.jpg\" alt=\"\" /> текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "/path/image.jpg");
        $vals[] = array("<p>Тестовый <img border=0 src=/path/image.jpg alt=ABC /> текст</p>\r\n<p>разбитый на строки</p>\r\n", 
                        "/path/image.jpg");
        $vals[] = array("Тестовый текст.<br />Текст после текста", 
                        "");
        $f = __FUNCTION__;
        foreach ($vals as $v) {
            $I->execute(function() use ($v, $f) {
                $sc = array("\r\n", "\r", "\n");
                $scr = array('\r\n', '\r', '\n');
                echo 'test `' . str_replace($sc, $scr, $v[0]) . '`' . "\r\n";
                echo 'expected result `' . str_replace($sc, $scr, $v[1]) . '`' . "\r\n";
                $res = $this->obj->$f($v[0]);
                echo 'actual result `' . str_replace($sc, $scr, $res) . '`' . "\r\n";
                $ret = ($v[1] == $res);
                var_dump($ret);
                return $ret;
            });
            $I->seeResultEquals(true);
        }
    }
    
    public function appendDigits(\CodeGuy $I) {
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
}
