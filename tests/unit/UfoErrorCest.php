<?php
require_once 'Tools.php';

class UfoErrorCest
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var UfoError
     */
    private $obj = null;
    /**
     * @var string
     */
    private $root = '';
    
    public function __construct()
    {
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoError.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoErrorStruct.php';
    }
    
    public function createObject(\CodeGuy $I) {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        $I->wantTo('create object `UfoError`');
        $I->execute(function() {
            $this->obj = null;
            try {
                $this->obj = new UfoError(new UfoErrorStruct(0, ''), new UfoContainer(array('config' => new UfoConfig())));
            } catch (Exception $e) {
                echo 'Exception occurred: ' . $e . "\r\n";
            }
            return !is_null($this->obj);
        });
        $I->seeResultEquals(true);
    }
    
    public function getError(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->execute(function() {
            $ret = $this->obj->getError();
            return is_a($ret, 'UfoErrorStruct');
        });
        $I->seeResultEquals(true);
    }
    
    public function getPage(\CodeGuy $I) {
        $this->showTest(__FUNCTION__);
        $I->wantTo('execute method `' . __FUNCTION__ . '`');
        $I->executeMethod($this->obj, __FUNCTION__);
        $I->seeMethodNotReturns($this->obj, __FUNCTION__, '');
    }
}
