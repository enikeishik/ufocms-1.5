<?php
require_once 'Tools.php';

use Codeception\Util\Stub;

class UfoIndexerTest extends \Codeception\TestCase\Test
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
    const DB_TABLE_PREFIX = 'codeception_';
    
    /**
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * @var UfoSearchDummy
     */
    protected $obj = null;
   
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;
    
protected $html1 = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<meta content="text/html; charset=windows-1251" http-equiv="content-type">
		<title>���������� ���� title</title>
		<meta name="Keywords" content="���������� ���� ���� Keywords">
		<meta name="Description" content="���������� ���� ���� Description">
		<meta name="Copyright" content="(C)">
		<meta NAME="Robots" content="all">
		<meta NAME="document-state" content="dynamic">
		<link href="/favicon.ico" rel="SHORTCUT ICON">
        <link href="/style.css" rel="stylesheet" type="text/css">
    </head>

	<body><!--content-->
		<p>���������� ��������.</p><!--/content-->
    </body>
</html>
EOD;

protected $html2 = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<meta content="text/html; charset=windows-1251" http-equiv="content-type" />
		<title>���������� ���� title</title>
		<meta name="keywords" content="���������� ���� ���� keywords" />
		<meta name="description" content="���������� ���� ���� description" />
		<meta name="copyright" content="(C)" />
		<meta name="robots" content="all" />
		<meta name="document-state" content="dynamic" />
		<link href="/favicon.ico" rel="shortcut icon" />
        <link href="/style.css" rel="stylesheet" type="text/css" />
    </head>
	<body><!--content-->
		<p>���������� ��������.</p><!--/content-->
    </body>
</html>
EOD;
    
    protected function _before()
    {
    }
    
    protected function _after()
    {
    }
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDebug.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once 'UfoIndexerDummy.php';
        $container = new UfoContainer();
        $config = new UfoConfig(array('dbTablePrefix' => self::DB_TABLE_PREFIX));
        setlocale(LC_ALL, $config->phpLocales);
        mb_internal_encoding($config->mbInternalEncoding);
        $container->setConfig($config);
        $debug = new UfoDebug($config);
        $container->setDebug($debug);
        $this->db = new UfoDb($container->getConfig()->dbSettings, $debug);
        $container->setDb($this->db);
        $this->obj = new UfoIndexerDummy($container);
        
        $prefix = $this->db->getTablePrefix();
$sql[] = <<<EOD
CREATE TEMPORARY TABLE `{$prefix}search` (
  `Id` int(11) NOT NULL auto_increment,
  `Flag` int(11) NOT NULL default '0',
  `ModuleId` int(11) NOT NULL default '0',
  `Url` varchar(255) NOT NULL default '',
  `Title` varchar(255) NOT NULL default '',
  `MetaDesc` varchar(255) NOT NULL default '',
  `MetaKeys` varchar(255) NOT NULL default '',
  `Content` text NOT NULL,
  `Hash` varchar(32) NOT NULL default '',
  `DateIndex` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`Id`),
  KEY `Url` (`Url`),
  KEY `Hash` (`Hash`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
EOD;
        foreach ($sql as $s) {
            $this->db->query($s);
        }
    }
    
    public function __destruct()
    {
        $sql[] = 'DROP TABLE ' . $this->db->getTablePrefix() . 'search';
        foreach ($sql as $s) {
            $this->db->query($s);
        }
    }
    
    // tests
    public function testGetTitle()
    {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $vals[] = array($this->html1, '���������� ���� title');
        $vals[] = array($this->html2, '���������� ���� title');
        foreach ($vals as $v) {
            echo 'test `' . $v[0] . '`' . "\r\n";
            $ret = $this->obj->getTitle($v[0]);
            var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getTitle', $v[1], array($v[0]));
        }
    }
    
    public function testGetMeta()
    {
        $this->showTest(__FUNCTION__);
        $vals[] = array($this->html1, 'keywords', '���������� ���� ���� Keywords');
        $vals[] = array($this->html1, 'description', '���������� ���� ���� Description');
        $vals[] = array($this->html2, 'keywords', '���������� ���� ���� keywords');
        $vals[] = array($this->html2, 'description', '���������� ���� ���� description');
        foreach ($vals as $v) {
            echo 'test `' . $v[0] . '`' . "\r\n";
            $ret = $this->obj->getMeta($v[0], $v[1]);
            var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getMeta', $v[2], array($v[0], $v[1]));
        }
    }
    
    public function testIsIndexExists()
    {
        $this->showTest(__FUNCTION__);
        $url = '/some-section/';
        //��������� ������������� ���������������
        $ret = $this->obj->isIndexExists($url);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexExists', false, array($url));
        //��������� � �������
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search (Url) VALUES(\'' . $url . '\')';
        $this->db->query($sql);
        //��������� ������������� ������������
        $ret = $this->obj->isIndexExists($url);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexExists', true, array($url));
    }
    
    public function testIsIndexChanged()
    {
        $this->showTest(__FUNCTION__);
        $url = '/some-section/';
        $title = '��������� ��������';
        $desc = '�������� ��������';
        $keys = '�������� ����� ��������';
        $index = '���������� ����� ��������, ��� �����.';
        $hash = md5($title . $desc . $keys . $index);
        //��������� ����������� ��������������� �������
        $ret = $this->obj->isIndexChanged($url, $hash);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexChanged', false, array($url, $hash));
        //��������� � �������
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
               ' (Url,Title,MetaDesc,MetaKeys,Content,Hash)' . 
               " VALUES('" . $url . "','" . $title . "','" . $desc . "','" . $keys . "','" . $index . "','" . $hash . "')";
        $this->db->query($sql);
        //���������
        $ret = $this->obj->isIndexChanged($url, $hash);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexChanged', false, array($url, $hash));
        //��������
        $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search' . 
               " SET Hash='' WHERE Url='" . $url . "'";
        $this->db->query($sql);
        //���������
        $ret = $this->obj->isIndexChanged($url, $hash);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexChanged', true, array($url, $hash));
    }
    
    public function testIndex()
    {
        $this->showTest(__FUNCTION__);
        $url = '/some-section/';
        $url2 = '/some-section2/';
        $content = $this->html2;
        //��������� �������� �������
        echo 'create index test' . "\r\n";
        $ret = $this->obj->index($url, $content, true);
        var_dump($ret);
        //url2, ��������� ������ ��� url ��� ������ ���� � �� ����� ������ �������� �� ���� �� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'index', true, array($url2, $content, true));
        //��������� ������� �������
        echo 'check index exisings (using isIndexExists)' . "\r\n";
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexExists', true, array($url));
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexExists', true, array($url2));
        //��������� �������� �������
        echo 'delete index test' . "\r\n";
        $ret = $this->obj->index($url, $content, false);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'index', false, array($url2, $content, false));
        //��������� ��� ������ ��������
        echo 'check index deleted (using isIndexExists)' . "\r\n";
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexExists', false, array($url));
        $this->codeGuy->seeMethodReturns($this->obj, 'isIndexExists', false, array($url2));
    }
}
