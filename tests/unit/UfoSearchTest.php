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
        
        $prefix = self::DB_TABLE_PREFIX;
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
ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1
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
$sql[] = <<<EOD
CREATE TEMPORARY TABLE `{$prefix}search_results` (
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
        $sql[] = 'DROP TABLE ' . self::DB_TABLE_PREFIX . 'search';
        $sql[] = 'DROP TABLE ' . self::DB_TABLE_PREFIX . 'search_queries';
        $sql[] = 'DROP TABLE ' . self::DB_TABLE_PREFIX . 'search_queries_stat';
        $sql[] = 'DROP TABLE ' . self::DB_TABLE_PREFIX . 'search_results';
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
        $vals[] = array('фыв йцу', array('фыв', 'йцу'));
        $vals[] = array('qwe, asd', array('qwe', 'asd'));
        $vals[] = array('фыв: йцу', array('фыв', 'йцу'));
        $vals[] = array('фыв - йцу', array('фыв', 'йцу'));
        $vals[] = array('¬есна - врем€, когда распускаютс€ цветы.', array('¬есна', 'врем€', 'когда', 'распускаютс€', 'цветы'));
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
        //провер€ем существование несуществующего запроса
        $ret = $this->obj->isResultsExists($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
        //вставл€ем в таблицу результатов запрос
        $sql = 'INSERT INTO ' . self::DB_TABLE_PREFIX . 'search_results (Query) VALUES(\'' . $query . '\')';
        $this->db->query($sql);
        //провер€ем существование вставленного запроса
        $ret = $this->obj->isResultsExists($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
    }
    
    public function testIsResultsExpired()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test expired';
        //провер€ем устаревание несуществующего запроса
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', true, array($query));
        //вставл€ем в таблицу результатов запрос с текущей меткой времени
        $sql = 'INSERT INTO ' . self::DB_TABLE_PREFIX . 'search_results (DateCreate,Query) VALUES(NOW(),\'' . $query . '\')';
        $this->db->query($sql);
        //провер€ем устаревание вставленного запроса
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', false, array($query));
        //измен€ем метку времени на заведомо устаревшую
        $sql = 'UPDATE ' . self::DB_TABLE_PREFIX . 'search_results SET DateCreate=\'1970-01-01 00:00:00\' WHERE Query=\'' . $query . '\'';
        $this->db->query($sql);
        //провер€ем устаревание измененного запроса
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', true, array($query));
    }
    
    public function testResultsDelete()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test delete';
        //вставл€ем в таблицу результатов запрос с текущей меткой времени
        $sql = 'INSERT INTO ' . self::DB_TABLE_PREFIX . 'search_results (Query) VALUES(\'' . $query . '\')';
        $this->db->query($sql);
        //провер€ем существование вставленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //удал€ем
        $ret = $this->obj->resultsDelete($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsDelete', true, array($query));
        //провер€ем отсутствие удаленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
    }
    
    public function testResultsClearOld()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test clearold';
        //вставл€ем в таблицу результатов запрос
        $sql = 'INSERT INTO ' . self::DB_TABLE_PREFIX . 'search_results (DateCreate,Query) VALUES(NOW(),\'' . $query . '\')';
        $this->db->query($sql);
        //провер€ем существование вставленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //очищаем
        $ret = $this->obj->resultsClearOld($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearOld', true, array($query));
        //провер€ем существование вставленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //измен€ем метку времени на заведомо устаревшую
        $sql = 'UPDATE ' . self::DB_TABLE_PREFIX . 'search_results SET DateCreate=\'1970-01-01 00:00:00\' WHERE Query=\'' . $query . '\'';
        $this->db->query($sql);
        //очищаем
        $ret = $this->obj->resultsClearOld($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearOld', true, array($query));
        //провер€ем отсутствие запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
    }
}
