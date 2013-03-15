<?php
require_once 'Tools.php';

class UfoInsertionCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var UfoInsertion
     */
    private $obj = null;
    
    public function __construct()
    {
        $root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $root . self::DS . 'config.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDbModel.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoInsertion.php';
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoInsertion`');
        $I->execute(function() {
            $this->obj = null;
            $cfg = new UfoConfig();
            $db = new UfoDb($cfg->dbSettings);
            try {
                $this->obj = new UfoInsertion(new UfoContainer(array('config' => $cfg, 'db' => $db, 'dbModel' => new UfoDbModel($db))));
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($this->obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function generate(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $ret = $this->obj->generate(-1, 0);
            return '' != $ret;
        });
        $I->seeResultEquals(true);
    }
    
    public function generateItem(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $ins = new UfoInsertionItemStruct(array('SourceId' => 0));
            $set = new UfoInsertionItemSettings(array('mfileins' => 'ins_news.php'));
            $ret = $this->obj->generateItem($ins, $set);
            return '' != $ret;
        });
        $I->seeResultEquals(true);
    }
}
