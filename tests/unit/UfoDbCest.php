<?php
require_once 'Tools.php';

class UfoDbCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
const SQL_CREATE_TABLE = <<<'EOD'
CREATE TABLE IF NOT EXISTS 
codeception_unit_test (
`Id` int(11) NOT NULL auto_increment, 
`Title` varchar(255) NOT NULL default '', 
PRIMARY KEY  (`Id`)) 
ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1
EOD;
    
    private $db = null;
    private $sql = array('CreateTable'        => self::SQL_CREATE_TABLE, 
                         'InsertCorrect'      => 'INSERT INTO codeception_unit_test (Title) VALUES(\'One\'),(\'Two\'),(\'Three\')', 
                         'SelectCorrect'      => 'SELECT * FROM codeception_unit_test', 
                         'SelectIncorrect'    => 'SELECT * FROM skdubfub', 
                         'SelectWhereCorrect' => 'SELECT * FROM codeception_unit_test WHERE Title!=\'\'');
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
    }
    
    /**
     * ���������� � ��.
     */
    public function connect(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create `UfoDb` instance');
        $I->execute(function() {
            $cfg = new UfoConfig();
            $this->db = new UfoDb($cfg->dbSettings);
            return $this->db;
        });
        $I->seeResultIs('UfoDb');
    }
    
    /**
     * ���� ����������� SQL ������� �� �������� ������� (CREATE TABLE).
     * � ���� ����� �������� ������ �� ����� �������������� �����, 
     * ������� ����� ��������, ���� ��� ��������� �������� �����.
     */
    public function queryCreateTable(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `query`');
        $I->executeMethod($this->db, 'query', $this->sql['CreateTable']);
        $I->seeMethodNotReturns($this->db, 'query', false, array($this->sql['CreateTable']));
    }
    
    /**
     * ���� ����������� SQL ������� �� ������� (SELECT).
     * � ���� ����� �������� ������ �� ����� ����� �������� �����.
     */
    public function querySelectCorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `query`');
        $I->executeMethod($this->db, 'query', $this->sql['SelectCorrect']);
        $I->seeMethodNotReturns($this->db, 'query', false, array($this->sql['SelectCorrect']));
    }
    
    /**
     * ���� ����������� SQL ������� �� ������� (SELECT).
     */
    public function querySelectIncorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `query`');
        $I->executeMethod($this->db, 'query', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($this->db, 'query', false, array($this->sql['SelectIncorrect']));
    }
    
    /**
     * ���� ����������� SQL ������� �� ������� (INSERT).
     * � ���� ����� �������� ������ �� ����� ����� �������� �����.
     */
    public function queryInsertCorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `query`');
        $I->executeMethod($this->db, 'query', $this->sql['InsertCorrect']);
        $I->seeMethodNotReturns($this->db, 'query', false, array($this->sql['InsertCorrect']));
    }
    
    /**
     * ���� ������ getRowByQuery � ���������� �������� �� ������� (SELECT).
     */
    public function getRowByQueryCorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `getRowByQuery`');
        $I->executeMethod($this->db, 'getRowByQuery', $this->sql['SelectCorrect']);
        $I->seeMethodNotReturns($this->db, 'getRowByQuery', false, array($this->sql['SelectCorrect']));
    }
    
    /**
     * ���� ������ getRowByQuery � ���������� �������� �� ������� (SELECT).
     */
    public function getRowByQueryIncorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `getRowByQuery`');
        $I->executeMethod($this->db, 'getRowByQuery', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($this->db, 'getRowByQuery', false, array($this->sql['SelectIncorrect']));
    }
    
    /**
     * ���� ������ getRowsByQuery � ���������� �������� �� ������� (SELECT).
     */
    public function getRowsByQueryCorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `getRowsByQuery`');
        $I->executeMethod($this->db, 'getRowsByQuery', $this->sql['SelectCorrect']);
        $I->seeMethodNotReturns($this->db, 'getRowsByQuery', false, array($this->sql['SelectCorrect']));
    }
    
    /**
     * ���� ������ getRowsByQuery � ���������� �������� �� ������� (SELECT).
     */
    public function getRowsByQueryIncorrect(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `getRowsByQuery`');
        $I->executeMethod($this->db, 'getRowsByQuery', $this->sql['SelectIncorrect']);
        $I->seeMethodReturns($this->db, 'getRowsByQuery', false, array($this->sql['SelectIncorrect']));
    }
    
    /**
     * �������� ���������� � ��.
     */
    public function closeTest(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `close`');
        $I->executeMethod($this->db, 'close');
    }
}
