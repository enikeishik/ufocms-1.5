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
    
    public function insertParagraphs(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array(" �������� �����\r\n�������� �� ������\r\n", 
                        "<p>�������� �����</p>\r\n<p>�������� �� ������</p>", 
                        "\r\n");
        $vals[] = array(" �������� �����\r\n�������� �� ������ ", 
                        "<p>�������� �����</p>\r\n<p>�������� �� ������</p>", 
                        "\r\n");
        $vals[] = array("�������� ����� � ���� ������", 
                        "<p>�������� ����� � ���� ������</p>", 
                        "\r\n");
        $vals[] = array("", 
                        "<p></p>", 
                        "\r\n");
        $vals[] = array(" �������� �����\r\n�������� �� ������ ", 
                        "<p>�������� �����\r\n�������� �� ������</p>", 
                        "");
        $vals[] = array("�������� ����� � ���� ������", 
                        "<p>�������� ����� � ���� ������</p>", 
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
        $this->showTest(__FUNCTION__);
        $vals[] = array("<p>�������� �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "�������� �����\r\n�������� �� ������", 
                        "\r\n");
        $vals[] = array("<p>�������� ����� � ���� ������</p>\r\n", 
                        "�������� ����� � ���� ������", 
                        "\r\n");
        $vals[] = array("<p>�������� �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "�������� �����\r\n�������� �� ������", 
                        "");
        $vals[] = array("<p>�������� ����� � ���� ������</p>\r\n", 
                        "�������� ����� � ���� ������", 
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
        $this->showTest(__FUNCTION__);
        $vals[] = array("<p>�������� �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����</p>\r\n");
        $vals[] = array("�������� �����<p>�������� ����� ������</p>\r\n", 
                        "�������� �����");
        $vals[] = array("�������� �����.<br />����� ����� ������", 
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
    
    public function cutNice(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "�������� �����,", 
                        20, 0, true);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����,", 
                        20, 0, false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "����, ������ ������", 
                        20, 10, true);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "� �����, ������", 
                        20, 10, false);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "", 
                        0, 10, false);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "", 
                        -10, 10, false);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "", 
                        20, -10, false);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "�������� �����", 
                        20, 0, true);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "<p>��������", 
                        20, 0, false);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "", 
                        20, 20, true);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "", 
                        20, 20, false);
        $vals[] = array("<p>�������� �����</p>\r\n", 
                        "�", 
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
        $this->showTest(__FUNCTION__);
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� ...������\r\n", 
                        20, '...');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� ... �����\r\n", 
                        20, ' ... ');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� .. ������\r\n", 
                        20, '..');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� .. ������\r\n", 
                        20, ' .. ');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� ��...�� ������\r\n", 
                        25, '...');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� � ... � ������\r\n", 
                        25, ' ... ');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� ���..�� ������\r\n", 
                        25, '..');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�������� �� .. � ������\r\n", 
                        25, ' .. ');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "...", 
                        3, '...');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�..", 
                        3, '..');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "", 
                        2, '...');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "", 
                        0, '');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "", 
                        -10, '');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "�\n", 
                        2, '');
        $vals[] = array("�������� �����, ������ ������ ���������� ������� �����.\r\n����� �������� �� ������\r\n", 
                        "��\n", 
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
        $this->showTest(__FUNCTION__);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.<!-- separator --></p>\r\n<p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p><!-- separator -->\r\n<p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<!-- separator --><p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<!-- separator --><p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p>�������� �� ������</p>", 
                        '<!-- separator -->', true);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p><!-- separator -->�������� �� ������</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.<!-- separator --> </p>\r\n<p>�������� �� ������</p>\r\n", 
                        "", 
                        '<!-- separator -->', false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.<!-- separator --> </p>\r\n<p>�������� �� ������</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����. </p>", 
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
        $this->showTest(__FUNCTION__);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.<!-- separator --></p>\r\n<p>�������� �� ������<!-- separator --></p>\r\n<p>��������� ���</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>", 
                        '<!-- separator -->', 0, false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.<!-- separator --></p>\r\n<p>�������� �� ������<!-- separator --></p>\r\n<p>��������� ���</p>\r\n", 
                        "</p>\r\n<p>�������� �� ������</p>", 
                        '<!-- separator -->', 1, false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.<!-- separator --></p>\r\n<p>�������� �� ������<!-- separator --></p>\r\n<p>��������� ���</p>\r\n", 
                        "\r\n<p>��������� ���</p>\r\n", 
                        '<!-- separator -->', 2, false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p><!-- separator -->�������� �� ������</p>\r\n<p><!-- separator -->��������� ���</p>\r\n", 
                        "<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n", 
                        '<!-- separator -->', 0, false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p><!-- separator -->�������� �� ������</p>\r\n<p><!-- separator -->��������� ���</p>\r\n", 
                        "�������� �� ������</p>\r\n", 
                        '<!-- separator -->', 1, false);
        $vals[] = array("<p>�������� �����, ������ ������ ���������� ������� �����.</p>\r\n<p><!-- separator -->�������� �� ������</p>\r\n<p><!-- separator -->��������� ���</p>\r\n", 
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
        $this->showTest(__FUNCTION__);
        $vals[] = array("<img src=\"/path/image.jpg\" alt=\"\" />", 
                        "/path/image.jpg");
        $vals[] = array("<p>�������� <img src=\"/path/image.jpg\" alt=\"\" /> �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "/path/image.jpg");
        $vals[] = array("<p>�������� <img src=\"/����%20�%20��������/����0001.JPG\" alt=\"\" /> �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "/����%20�%20��������/����0001.JPG");
        $vals[] = array("�������� �����<p>�������� <IMG SRC='/path-with_smth.spec,chars/~image.jpeg' ALT=''>����� ������</p>\r\n", 
                        "/path-with_smth.spec,chars/~image.jpeg");
        $vals[] = array("<p>�������� <img border=\"0\" src=\"/path/image.jpg\" alt=\"\" /> �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "/path/image.jpg");
        $vals[] = array("<p>�������� <img border=0 src=/path/image.jpg alt=ABC /> �����</p>\r\n<p>�������� �� ������</p>\r\n", 
                        "/path/image.jpg");
        $vals[] = array("�������� �����.<br />����� ����� ������", 
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
}
