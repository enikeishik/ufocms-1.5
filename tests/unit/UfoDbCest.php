<?php

class UfoDbCest
{
    public $class = 'UfoDb';
    private $dbSettings = array('Host'     => 'sql09.freemysql.net', 
                                'Username' => 'lifehacker', 
                                'Password' => 'ahbvecrekm', 
                                'Database' => 'lifehacker');
const SQL_CREATE_TABLE = <<<'EOD'
CREATE TABLE IF NOT EXISTS 
codeception_unit_test (
`Id` int(11) NOT NULL auto_increment, 
`Title` varchar(255) NOT NULL default '', 
PRIMARY KEY  (`Id`)) 
ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1
EOD;
    private $sql = array('CreateTable'     => self::SQL_CREATE_TABLE, 
                         'SelectCorrect'   => 'SELECT * FROM codeception_unit_test', 
                         'SelectIncorrect' => 'SELECT * FROM skdubfub');
    
    /**
     * Соединение с БД.
     */
    public function connect(\CodeGuy $I) {
        $I->wantTo('create `UfoDb` instance');
        $I->execute(function() {
            $db = new UfoDb($this->dbSettings['Host'], 
                            $this->dbSettings['Username'], 
                            $this->dbSettings['Password'], 
                            $this->dbSettings['Database']);
            return $db;
        });
        $I->seeResultIs('UfoDb');
    }
    
    /**
     * Закрытие соединения с БД.
     */
    public function close(\CodeGuy $I) {
        $I->wantTo('execute method `close`');
        $db = new UfoDb($this->dbSettings['Host'], 
                        $this->dbSettings['Username'], 
                        $this->dbSettings['Password'], 
                        $this->dbSettings['Database']);
        $I->executeMethod($db, 'close');
    }
    
    /**
     * Тест правильного SQL запроса на создание таблицы (CREATE TABLE).
     */
    public function queryCreateTable(\CodeGuy $I) {
        $I->wantTo('execute method `query`');
        $db = new UfoDb($this->dbSettings['Host'], 
                        $this->dbSettings['Username'], 
                        $this->dbSettings['Password'], 
                        $this->dbSettings['Database']);
        $I->executeMethod($db, 'query', $this->sql['CreateTable']);
        $I->seeMethodNotReturns($db, 'query', false, array($this->sql['CreateTable']));
    }
    
    /**
     * Тест правильного SQL запроса на выборку (SELECT).
     */
    public function querySelectCorrect(\CodeGuy $I) {
        $I->wantTo('execute method `query`');
        $db = new UfoDb($this->dbSettings['Host'], 
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
        $db = new UfoDb($this->dbSettings['Host'], 
                        $this->dbSettings['Username'], 
                        $this->dbSettings['Password'], 
                        $this->dbSettings['Database']);
        $I->executeMethod($db, 'query', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($db, 'query', false, array($this->sql['SelectIncorrect']));
    }
}
