<?php
require_once 'Tools.php';

use Codeception\Util\Stub;

class UfoSearchTest extends \Codeception\TestCase\Test
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
        require_once 'UfoSearchDummy.php';
        $container = new UfoContainer();
        $config = new UfoConfig(array('dbTablePrefix' => self::DB_TABLE_PREFIX));
        setlocale(LC_ALL, $config->phpLocales);
        mb_internal_encoding($config->mbInternalEncoding);
        $container->setConfig($config);
        $debug = new UfoDebug($config);
        $container->setDebug($debug);
        $this->db = new UfoDb($container->getConfig()->dbSettings, $debug);
        $container->setDb($this->db);
        $this->obj = new UfoSearchDummy($container);
        
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
$sql[] = <<<EOD
CREATE TEMPORARY TABLE `{$prefix}search_queries` (
  `id` int(11) NOT NULL auto_increment,
  `dtm` datetime NOT NULL default '0000-00-00 00:00:00',
  `query` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
EOD;
$sql[] = <<<EOD
CREATE TEMPORARY TABLE `{$prefix}search_queries_stat` (
  `id` int(11) NOT NULL auto_increment,
  `dtm` datetime NOT NULL default '0000-00-00 00:00:00',
  `query` varchar(255) NOT NULL default '',
  `cnttmp` int(11) NOT NULL default '0',
  `cntttl` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
EOD;
//Not TEMPORARY for testResultsClearDoubles and the same
$sql[] = <<<EOD
CREATE TABLE `{$prefix}search_results` (
  `Id` int(11) NOT NULL auto_increment,
  `DateCreate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Relevance` int(11) NOT NULL default '0',
  `Flag` int(11) NOT NULL default '0',
  `ModuleId` int(11) NOT NULL default '0',
  `Query` varchar(255) NOT NULL default '',
  `Url` varchar(255) NOT NULL default '',
  `Title` varchar(255) NOT NULL default '',
  `Descr` varchar(255) NOT NULL default '',
  `Content` text NOT NULL,
  PRIMARY KEY  (`Id`),
  KEY `Query` (`Query`),
  KEY `Url` (`Url`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
EOD;
        foreach ($sql as $s) {
            $this->db->query($s);
        }
    }
    
    public function __destruct()
    {
        $sql[] = 'DROP TABLE ' . $this->db->getTablePrefix() . 'search';
        $sql[] = 'DROP TABLE ' . $this->db->getTablePrefix() . 'search_queries';
        $sql[] = 'DROP TABLE ' . $this->db->getTablePrefix() . 'search_queries_stat';
        $sql[] = 'DROP TABLE ' . $this->db->getTablePrefix() . 'search_results';
        foreach ($sql as $s) {
            $this->db->query($s);
        }
    }
    
    // tests
    public function testGetQueryWords()
    {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $vals[] = array('123 456', array('123', '456'));
        $vals[] = array('qwe asd', array('qwe', 'asd'));
        $vals[] = array('��� ���', array('���', '���'));
        $vals[] = array('qwe, asd', array('qwe', 'asd'));
        $vals[] = array('���: ���', array('���', '���'));
        $vals[] = array('��� - ���', array('���', '���'));
        $vals[] = array('����� - �����, ����� ������������ �����.', array('�����', '�����', '�����', '������������', '�����'));
        foreach ($vals as $v) {
            echo 'test `' . $v[0] . '`' . "\r\n";
            $ret = $this->obj->getQueryWords($v[0]);
            var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getQueryWords', $v[1], array($v[0]));
        }
    }
    
    public function testIsResultsExists()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test exists';
        //��������� ������������� ��������������� �������
        $ret = $this->obj->isResultsExists($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
        //��������� � ������� ����������� ������
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (Query) VALUES(\'' . $query . '\')';
        $this->db->query($sql);
        //��������� ������������� ������������ �������
        $ret = $this->obj->isResultsExists($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
    }
    
    public function testIsResultsExpired()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test expired';
        //��������� ����������� ��������������� �������
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', true, array($query));
        //��������� � ������� ����������� ������ � ������� ������ �������
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (DateCreate,Query) VALUES(NOW(),\'' . $query . '\')';
        $this->db->query($sql);
        //��������� ����������� ������������ �������
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', false, array($query));
        //�������� ����� ������� �� �������� ����������
        $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search_results SET DateCreate=\'1970-01-01 00:00:00\' WHERE Query=\'' . $query . '\'';
        $this->db->query($sql);
        //��������� ����������� ����������� �������
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', true, array($query));
    }
    
    public function testResultsDelete()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test delete';
        //��������� � ������� ����������� ������ � ������� ������ �������
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (Query) VALUES(\'' . $query . '\')';
        $this->db->query($sql);
        //��������� ������������� ������������ �������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //�������
        $ret = $this->obj->resultsDelete($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsDelete', true, array($query));
        //��������� ���������� ���������� �������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
    }
    
    public function testResultsClearOld()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test clearold';
        //��������� � ������� ����������� ������
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (DateCreate,Query) VALUES(NOW(),\'' . $query . '\')';
        $this->db->query($sql);
        //��������� ������������� ������������ �������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //�������
        $ret = $this->obj->resultsClearOld($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearOld', true, array($query));
        //��������� ������������� ������������ �������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //�������� ����� ������� �� �������� ����������
        $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search_results SET DateCreate=\'1970-01-01 00:00:00\' WHERE Query=\'' . $query . '\'';
        $this->db->query($sql);
        //�������
        $ret = $this->obj->resultsClearOld($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearOld', true, array($query));
        //��������� ���������� �������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
    }
    
    public function testResultsClearDoubles()
    {
        $this->showTest(__FUNCTION__);
        //��������� �� ������ ������� ��� ��� ��������� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearDoubles', 0);
        //��������� ������ � �������
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results' . 
                  ' (Relevance)' . 
                  " VALUES (1),(10),(100),(1000)";
        $this->db->query($sqlIns);
        //��������� ��� ����� �������
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearDoubles', 3);
    }
    
    public function testGetLongWords()
    {
        $this->showTest(__FUNCTION__);
        //QUERIES_MINWORDLEN = 3
        $vals[] = array(array('1234', '123', '12', '1', ''), array('1234', '123'));
        $vals[] = array(array('qwer', 'qwe', 'qw', 'q', ''), array('qwer', 'qwe'));
        $vals[] = array(array('����', '���', '��', '�', ''), array('����', '���'));
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getLongWords($v[0]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getLongWords', $v[1], array($v[0]));
        }
    }
    
    public function testResultsAdd()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test results add';
        //��������� ���������� ������������� ��� �� ������������ ����������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
        //��������� ���������� � ������� �����������
        $sql = ",(NOW(),1000,0,1,'" . $query . "','/url/','head title','meta description text','content of page')" . 
               ",(NOW(),1000,0,1,'" . $query . "','/url/','head title','meta description text','content of page')";
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsAdd', true, array($sql));
        //��������� ������������� ������������ ����������
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
    }
    
    public function testSearchWordExec()
    {
        $this->showTest(__FUNCTION__);
        $word = 'test_search_word_exec';
        $query = 'test search word exec test_search_word_exec';
        //��������� ���������� ����� � ������� ����� ������
        $sql = 'SELECT Flag, ModuleId, Url, Title, MetaDesc, Content' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search' . 
               " WHERE Content LIKE '%" . $word . "%'";
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWordExec', false, array($sql, 1, $query));
        //��������� � ������� ����� ������ ������, ���������� ������� �����
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('Test content with searching word " . $word . ", test content with searching word. Test content with searching word.')";
        $this->db->query($sqlIns);
        //��������� ������� ������ ���������� ����� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWordExec', 1, array($sql, 1, $query));
        //��������� � ������� ����� ������ ��� ������, ���������� ������� �����
        $this->db->query($sqlIns);
        //��������� ������� ���� �����, ���������� ����� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWordExec', 2, array($sql, 1, $query));
    }
    
    public function testSearchWords()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test search words';
        $words = array('test', 'search', 'words', 'so'); //'so' ����������� ������ �� ������ ����������� ������
        //��������� ���������� ���� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 0, array($words, 1, $query));
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 0, array($words, 1, $query, true));
        //��������� � ������� ����� ������ ������, ���������� ������� �����
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('This is a " . $query . " in content.'),('This is a " . $query . " in some other content.')";
        $this->db->query($sqlIns);
        //��������� ������� �����, ���������� ����� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 2, array($words, 1, $query));
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 1, array($words, 1, $query, true));
    }
    
    public function testSearchWord()
    {
        $this->showTest(__FUNCTION__);
        $query = 'some test_search_word included';
        $word = 'test_search_word';
        //��������� ���������� ���� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWord', 0, array($word, 1, $query));
        //��������� � ������� ����� ������ ������, ���������� ������� �����
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('This is a " . $query . " in content.')," . 
                  "('This is a " . $query . " in some other content.')";
        $this->db->query($sqlIns);
        //��������� ������� �����, ���������� ����� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWord', 2, array($word, 1, $query));
    }
    
    public function testRawSearchStemmed()
    {
        $this->showTest(__FUNCTION__);
        $query = '�������� ����� ������';
        $words = explode(' ', $query);
        //��������� ���������� ���� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearchStemmed', 0, array($query, $words));
        //��������� � ������� ����� ������ ������, ���������� ������� �����
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('��� ����� " . $query . " � ������ ����.')," . 
                  "('������ ����� - ����� - ������� ����� ������.')";
        $this->db->query($sqlIns);
        //��������� ������� �����, ���������� ����� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearchStemmed', 2, array($query, $words));
    }
    
    public function testRawSearch()
    {
        $this->showTest(__FUNCTION__);
        $query = '����� ������';
        //��������� ���������� ���� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearch', 0, array($query));
        //��������� � ������� ����� ������ ������, ���������� ������� �����
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Url,Content)' . 
                  " VALUES ('/url1/','��� ����� " . $query . " � ���������� � ����.')," . 
                  "('/url2/','������ ����� ��� �����.')";
        $this->db->query($sqlIns);
        //��������� ������� �����, ���������� ����� � ������� ����� ������
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearch', 2, array($query));
    }
    
    public function testLogSearchQuery()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test log query';
        $this->codeGuy->seeMethodReturns($this->obj, 'logSearchQuery', null, array($query));
    }
    
    public function testGetResults()
    {
        $this->showTest(__FUNCTION__);
        //$this->codeGuy->seeMethodReturns($this->obj, 'getResults', , array($query, $page = 1, $pageLength = 10, $path = '', $moduleid = null));
        //������� ������� � ������ ������� � ������������ ������
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search';
        $this->db->query($sql);
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search_results';
        $this->db->query($sql);
        //�������� �� ������������ ������
        $vals[] = array(array('not_exists_query', 1, 10, '', null), array());
        $vals[] = array(array('incorrect_params', -1, 10, '', null), false);
        $vals[] = array(array('����� ������', 1, 10, '', null), array());
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getResults($v[0][0], $v[0][1], $v[0][2], $v[0][3], $v[0][4]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getResults', $v[1], $v[0]);
        }
        //������� ����� ������
        $vals = array();
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
               ' (Url,Content)' . 
               " VALUES ('/url1/','��� ����� ����� ������ � ���������� � ����.')," . 
               "('/url2/','������ ����� ��� �����.')";
        $this->db->query($sql);
        //�������� ������������ ������
        $vals[] = array(array('����� ������', 1, 10, '', null), array());
        $vals[] = array(array('������ ��� �����', 1, 10, '', null), array());
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getResults($v[0][0], $v[0][1], $v[0][2], $v[0][3], $v[0][4]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodNotReturns($this->obj, 'getResults', $v[1], $v[0]);
        }
    }
    
    public function testGetResultsCount()
    {
        $this->showTest(__FUNCTION__);
        //$this->codeGuy->seeMethodReturns($this->obj, 'getResultsCount', , array($query, $path = '', $moduleid = null));
        //������� ������� � ������ ������� � ������������ ������
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search';
        $this->db->query($sql);
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search_results';
        $this->db->query($sql);
        //�������� �� ������������ ������
        $vals[] = array(array('not_exists_query', '', null), 0);
        $vals[] = array(array('����� ������', '', null), 0);
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getResultsCount($v[0][0], $v[0][1], $v[0][2]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getResultsCount', $v[1], $v[0]);
        }
        //������� ����� ������
        $vals = array();
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
               ' (Url,Content)' . 
               " VALUES ('/url1/','��� ����� ����� ������ � ���������� � ����.')," . 
               "('/url2/','������ ����� ��� �����.')";
        $this->db->query($sql);
        //�������� ������������ ������
        $vals[] = array(array('����� ������', '', null), 2);
        $vals[] = array(array('������ ��� �����', '', null), 2);
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getResults($v[0][0], 1, 10, $v[0][1], $v[0][2]);
            $ret = $this->obj->getResultsCount($v[0][0], $v[0][1], $v[0][2]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getResultsCount', $v[1], $v[0]);
        }
    }
}
