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
        $vals[] = array('фыв йцу', array('фыв', 'йцу'));
        $vals[] = array('qwe, asd', array('qwe', 'asd'));
        $vals[] = array('фыв: йцу', array('фыв', 'йцу'));
        $vals[] = array('фыв - йцу', array('фыв', 'йцу'));
        $vals[] = array('Весна - время, когда распускаются цветы.', array('Весна', 'время', 'когда', 'распускаются', 'цветы'));
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
        //проверяем существование несуществующего запроса
        $ret = $this->obj->isResultsExists($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
        //вставляем в таблицу результатов запрос
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (Query) VALUES(\'' . $query . '\')';
        $this->db->query($sql);
        //проверяем существование вставленного запроса
        $ret = $this->obj->isResultsExists($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
    }
    
    public function testIsResultsExpired()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test expired';
        //проверяем устаревание несуществующего запроса
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', true, array($query));
        //вставляем в таблицу результатов запрос с текущей меткой времени
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (DateCreate,Query) VALUES(NOW(),\'' . $query . '\')';
        $this->db->query($sql);
        //проверяем устаревание вставленного запроса
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', false, array($query));
        //изменяем метку времени на заведомо устаревшую
        $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search_results SET DateCreate=\'1970-01-01 00:00:00\' WHERE Query=\'' . $query . '\'';
        $this->db->query($sql);
        //проверяем устаревание измененного запроса
        $ret = $this->obj->isResultsExpired($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExpired', true, array($query));
    }
    
    public function testResultsDelete()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test delete';
        //вставляем в таблицу результатов запрос с текущей меткой времени
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (Query) VALUES(\'' . $query . '\')';
        $this->db->query($sql);
        //проверяем существование вставленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //удаляем
        $ret = $this->obj->resultsDelete($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsDelete', true, array($query));
        //проверяем отсутствие удаленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
    }
    
    public function testResultsClearOld()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test clearold';
        //вставляем в таблицу результатов запрос
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results (DateCreate,Query) VALUES(NOW(),\'' . $query . '\')';
        $this->db->query($sql);
        //проверяем существование вставленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //очищаем
        $ret = $this->obj->resultsClearOld($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearOld', true, array($query));
        //проверяем существование вставленного запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
        //изменяем метку времени на заведомо устаревшую
        $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search_results SET DateCreate=\'1970-01-01 00:00:00\' WHERE Query=\'' . $query . '\'';
        $this->db->query($sql);
        //очищаем
        $ret = $this->obj->resultsClearOld($query);
        var_dump($ret);
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearOld', true, array($query));
        //проверяем отсутствие запроса
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
    }
    
    public function testResultsClearDoubles()
    {
        $this->showTest(__FUNCTION__);
        //проверяем на пустой таблице что нет удаленных дублей
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearDoubles', 0);
        //вставляем данные с дублями
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search_results' . 
                  ' (Relevance)' . 
                  " VALUES (1),(10),(100),(1000)";
        $this->db->query($sqlIns);
        //проверяем что дубли удалены
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsClearDoubles', 3);
    }
    
    public function testGetLongWords()
    {
        $this->showTest(__FUNCTION__);
        //QUERIES_MINWORDLEN = 3
        $vals[] = array(array('1234', '123', '12', '1', ''), array('1234', '123'));
        $vals[] = array(array('qwer', 'qwe', 'qw', 'q', ''), array('qwer', 'qwe'));
        $vals[] = array(array('йцук', 'йцу', 'йц', 'й', ''), array('йцук', 'йцу'));
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
        //проверяем отсутствие существования еще не вставленного результата
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', false, array($query));
        //вставляем результаты в таблицу результатов
        $sql = ",(NOW(),1000,0,1,'" . $query . "','/url/','head title','meta description text','content of page')" . 
               ",(NOW(),1000,0,1,'" . $query . "','/url/','head title','meta description text','content of page')";
        $this->codeGuy->seeMethodReturns($this->obj, 'resultsAdd', true, array($sql));
        //проверяем существование вставленного результата
        $this->codeGuy->seeMethodReturns($this->obj, 'isResultsExists', true, array($query));
    }
    
    public function testSearchWordExec()
    {
        $this->showTest(__FUNCTION__);
        $word = 'test_search_word_exec';
        $query = 'test search word exec test_search_word_exec';
        //проверяем отсутствие слова в таблице сырых данных
        $sql = 'SELECT Flag, ModuleId, Url, Title, MetaDesc, Content' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search' . 
               " WHERE Content LIKE '%" . $word . "%'";
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWordExec', false, array($sql, 1, $query));
        //вставляем в таблицу сырых данных данные, содержащие искомое слово
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('Test content with searching word " . $word . ", test content with searching word. Test content with searching word.')";
        $this->db->query($sqlIns);
        //проверяем наличие строки содержащей слово в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWordExec', 1, array($sql, 1, $query));
        //вставляем в таблицу сырых данных еще данные, содержащие искомое слово
        $this->db->query($sqlIns);
        //проверяем наличие двух строк, содержащих слова в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWordExec', 2, array($sql, 1, $query));
    }
    
    public function testSearchWords()
    {
        $this->showTest(__FUNCTION__);
        $query = 'test search words';
        $words = array('test', 'search', 'words', 'so'); //'so' присуствует только во второй вставляемой строке
        //проверяем отсутствие слов в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 0, array($words, 1, $query));
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 0, array($words, 1, $query, true));
        //вставляем в таблицу сырых данных данные, содержащие искомые слова
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('This is a " . $query . " in content.'),('This is a " . $query . " in some other content.')";
        $this->db->query($sqlIns);
        //проверяем наличие строк, содержащих слова в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 2, array($words, 1, $query));
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWords', 1, array($words, 1, $query, true));
    }
    
    public function testSearchWord()
    {
        $this->showTest(__FUNCTION__);
        $query = 'some test_search_word included';
        $word = 'test_search_word';
        //проверяем отсутствие слов в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWord', 0, array($word, 1, $query));
        //вставляем в таблицу сырых данных данные, содержащие искомое слово
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('This is a " . $query . " in content.')," . 
                  "('This is a " . $query . " in some other content.')";
        $this->db->query($sqlIns);
        //проверяем наличие строк, содержащих слова в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'searchWord', 2, array($word, 1, $query));
    }
    
    public function testRawSearchStemmed()
    {
        $this->showTest(__FUNCTION__);
        $query = 'красивые чайки летали';
        $words = explode(' ', $query);
        //проверяем отсутствие слов в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearchStemmed', 0, array($query, $words));
        //вставляем в таблицу сырых данных данные, содержащие искомые слова
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Content)' . 
                  " VALUES ('Над рекой " . $query . " и ловили рыбу.')," . 
                  "('Речные птицы - чайки - красивы когда летают.')";
        $this->db->query($sqlIns);
        //проверяем наличие строк, содержащих слова в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearchStemmed', 2, array($query, $words));
    }
    
    public function testRawSearch()
    {
        $this->showTest(__FUNCTION__);
        $query = 'плыли облака';
        //проверяем отсутствие слов в таблице сырых данных
        $this->codeGuy->seeMethodReturns($this->obj, 'rawSearch', 0, array($query));
        //вставляем в таблицу сырых данных данные, содержащие искомые слова
        $sqlIns = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
                  ' (Url,Content)' . 
                  " VALUES ('/url1/','Над рекой " . $query . " и отражались в воде.')," . 
                  "('/url2/','Облако плыло над рекой.')";
        $this->db->query($sqlIns);
        //проверяем наличие строк, содержащих слова в таблице сырых данных
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
        //очищаем таблицы с сырыми данными и результатами поиска
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search';
        $this->db->query($sql);
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search_results';
        $this->db->query($sql);
        //заведомо не существующие поиски
        $vals[] = array(array('not_exists_query', 1, 10, '', null), array());
        $vals[] = array(array('incorrect_params', -1, 10, '', null), false);
        $vals[] = array(array('плыли облака', 1, 10, '', null), array());
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getResults($v[0][0], $v[0][1], $v[0][2], $v[0][3], $v[0][4]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getResults', $v[1], $v[0]);
        }
        //создаем сырые данные
        $vals = array();
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
               ' (Url,Content)' . 
               " VALUES ('/url1/','Над рекой плыли облака и отражались в воде.')," . 
               "('/url2/','Облако плыло над рекой.')";
        $this->db->query($sql);
        //заведомо существующие поиски
        $vals[] = array(array('плыли облака', 1, 10, '', null), array());
        $vals[] = array(array('облака над рекой', 1, 10, '', null), array());
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
        //очищаем таблицы с сырыми данными и результатами поиска
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search';
        $this->db->query($sql);
        $sql = 'TRUNCATE TABLE ' . $this->db->getTablePrefix() . 'search_results';
        $this->db->query($sql);
        //заведомо не существующие поиски
        $vals[] = array(array('not_exists_query', '', null), 0);
        $vals[] = array(array('плыли облака', '', null), 0);
        foreach ($vals as $v) {
            echo 'test:     '; var_dump($v[0]);
            $ret = $this->obj->getResultsCount($v[0][0], $v[0][1], $v[0][2]);
            echo 'expected: '; var_dump($v[1]);
            echo 'actual:   '; var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'getResultsCount', $v[1], $v[0]);
        }
        //создаем сырые данные
        $vals = array();
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' . 
               ' (Url,Content)' . 
               " VALUES ('/url1/','Над рекой плыли облака и отражались в воде.')," . 
               "('/url2/','Облако плыло над рекой.')";
        $this->db->query($sql);
        //заведомо существующие поиски
        $vals[] = array(array('плыли облака', '', null), 2);
        $vals[] = array(array('облака над рекой', '', null), 2);
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
