<?php
require_once 'Tools.php';

use Codeception\Util\Stub;

class UfoSysSearchTest extends \Codeception\TestCase\Test
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
    const DB_TABLE_PREFIX = 'codeception_';
    
    /**
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * @var UfoSysSearch
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
        require_once $root . self::DS . 'errors.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoToolsExt.php';
        require_once $root . self::DS . 'modules' . self::DS . 'UfoSysSearch' . self::DS . 'UfoSysSearch.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDebug.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCoreDbModel.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoSystemSection.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        $this->container = new UfoContainer();
        $config = new UfoConfig(array('dbTablePrefix' => self::DB_TABLE_PREFIX));
        setlocale(LC_ALL, $config->phpLocales);
        mb_internal_encoding($config->mbInternalEncoding);
        $this->container->setConfig($config);
        $debug = new UfoDebug($config);
        $this->container->setDebug($debug);
        $this->db = new UfoDb($this->container->getConfig()->dbSettings, $debug);
        $this->container->setDb($this->db);
        $this->container->setCoreDbModel(new UfoCoreDbModel($this->container->getDb()));
        $this->container->setSite(new UfoSite('/search/', '/search/', $this->container));
        $this->container->setSection(new UfoSystemSection('/search/', $this->container));
        $core = new UfoCore($this->container->getConfig());
        $core->setContainer($this->container);
        $this->container->setCore($core);
        
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
    public function testConstruct()
    {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $this->obj = null;
        try {
            $this->obj = new UfoSysSearch($this->container);
        } catch (Exception $e) {
            echo 'Exception occurred: ' . $e . "\r\n";
        }
        echo 'Object class is ' . get_class($this->obj);
        ob_flush();
        $this->obj = null;
    }
    
    public function testGetContent()
    {
        $this->showTest(__FUNCTION__);
        $this->obj = new UfoSysSearch($this->container);
        
        //проверяем наличие пустого запроса
        $_GET['q'] = '';
        $ret = $this->obj->getContent();
        var_dump($ret);
        ob_flush();
        $this->codeGuy->seeMethodReturns($this->obj, 'getContent', false);
        
        //проверяем наличие несуществующего запроса
        $query = 'test';
        $_GET['q'] = $query;
        $ret = $this->obj->getContent();
        var_dump($ret);
        ob_flush();
        $this->codeGuy->seeMethodReturns($this->obj, 'getContent', array('Count' => 0));
         
         //вставляем данные
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('" . $query . "')";
        $this->db->query($sqlIns);
        //проверяем наличие существующего запроса
        $ret = $this->obj->getContent();
        var_dump($ret);
        ob_flush();
        $this->codeGuy->seeMethodNotReturns($this->obj, 'getContent', false);
   }
}