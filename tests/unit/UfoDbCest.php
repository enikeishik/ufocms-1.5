<?php

class UfoDbCest
{
    public $class = 'UfoDb';
    private $dbSettings = array('Host'     => '', 
                                'Username' => '', 
                                'Password' => '', 
                                'Database' => '');
const SQL_CREATE_TABLE = <<<'EOD'
CREATE TABLE IF NOT EXISTS 
codeception_unit_test (
`Id` int(11) NOT NULL auto_increment, 
`Title` varchar(255) NOT NULL default '', 
PRIMARY KEY  (`Id`)) 
ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1
EOD;
    private $sql = array('CreateTable'        => self::SQL_CREATE_TABLE, 
                         'InsertCorrect'      => 'INSERT INTO codeception_unit_test (Title) VALUES(\'One\'),(\'Two\'),(\'Three\')', 
                         'SelectCorrect'      => 'SELECT * FROM codeception_unit_test', 
                         'SelectIncorrect'    => 'SELECT * FROM skdubfub', 
                         'SelectWhereCorrect' => 'SELECT * FROM codeception_unit_test WHERE Title!=\'\'');
    
    /**
     * Соединение с БД.
     */
    public function connect(\CodeGuy $I) {
        $I->wantTo('create `UfoDb` instance');
        $I->execute(function() {
            $db = UfoDb::singleton($this->dbSettings['Host'], 
                                   $this->dbSettings['Username'], 
                                   $this->dbSettings['Password'], 
                                   $this->dbSettings['Database']);
            return $db;
        });
        $I->seeResultIs('UfoDb');
    }
    
    /**
     * Тест правильного SQL запроса на создание таблицы (CREATE TABLE).
     * В этом тесте получаем объект БД через дополнительный метод, 
     * который нужно вызывать, если уже вызывался основной метод.
     */
    public function queryCreateTable(\CodeGuy $I) {
        $I->wantTo('execute method `query`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'query', $this->sql['CreateTable']);
        $I->seeMethodNotReturns($db, 'query', false, array($this->sql['CreateTable']));
    }
    
    /**
     * Тест правильного SQL запроса на выборку (SELECT).
     * В этом тесте получаем объект БД снова через основной метод.
     */
    public function querySelectCorrect(\CodeGuy $I) {
        $I->wantTo('execute method `query`');
        $db = UfoDb::singleton($this->dbSettings['Host'], 
                               $this->dbSettings['Username'], 
                               $this->dbSettings['Password'], 
                               $this->dbSettings['Database']);
        $I->executeMethod($db, 'query', $this->sql['SelectCorrect']);
        $I->seeMethodNotReturns($db, 'query', false, array($this->sql['SelectCorrect']));
    }
    
    /**
     * Тест правильного SQL запроса на выборку (SELECT).
     */
    public function querySelectIncorrect(\CodeGuy $I) {
        $I->wantTo('execute method `query`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'query', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($db, 'query', false, array($this->sql['SelectIncorrect']));
    }
    
    /**
     * Тест правильного SQL запроса на вставку (INSERT).
     * В этом тесте получаем объект БД снова через основной метод.
     */
    public function queryInsertCorrect(\CodeGuy $I) {
        $I->wantTo('execute method `query`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'query', $this->sql['InsertCorrect']);
        $I->seeMethodNotReturns($db, 'query', false, array($this->sql['InsertCorrect']));
    }
    
    /**
     * Тест метода getRowByQuery с правильным запросом на выборку (SELECT).
     */
    public function getRowByQueryCorrect(\CodeGuy $I) {
        $I->wantTo('execute method `getRowByQuery`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'getRowByQuery', $this->sql['SelectCorrect']);
        $I->seeMethodNotReturns($db, 'getRowByQuery', false, array($this->sql['SelectCorrect']));
    }
    
    /**
     * Тест метода getRowByQuery с правильным запросом на выборку (SELECT).
     */
    public function getRowByQueryIncorrect(\CodeGuy $I) {
        $I->wantTo('execute method `getRowByQuery`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'getRowByQuery', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($db, 'getRowByQuery', false, array($this->sql['SelectIncorrect']));
    }
    
    /**
     * Тест метода getRowsByQuery с правильным запросом на выборку (SELECT).
     */
    public function getRowsByQueryCorrect(\CodeGuy $I) {
        $I->wantTo('execute method `getRowsByQuery`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'getRowsByQuery', $this->sql['SelectCorrect']);
        $I->seeMethodNotReturns($db, 'getRowsByQuery', false, array($this->sql['SelectCorrect']));
    }
    
    /**
     * Тест метода getRowsByQuery с правильным запросом на выборку (SELECT).
     */
    public function getRowsByQueryIncorrect(\CodeGuy $I) {
        $I->wantTo('execute method `getRowsByQuery`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'getRowsByQuery', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($db, 'getRowsByQuery', false, array($this->sql['SelectIncorrect']));
    }
    
    /**
     * Закрытие соединения с БД.
     */
    public function closeTest(\CodeGuy $I) {
        $I->wantTo('execute method `close`');
        $db = UfoDb::getInstance();
        $I->executeMethod($db, 'close');
    }
}
