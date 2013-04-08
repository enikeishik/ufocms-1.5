<?php
require_once 'Tools.php';

use Codeception\Util\Stub;

class UfoSearchTest extends \Codeception\TestCase\Test
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
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
        require_once $root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once 'UfoSearchDummy.php';
        $container = new UfoContainer();
        $container->setConfig(new UfoConfig());
        $container->setDb(new UfoDb($container->getConfig()->dbSettings));
        $this->obj = new UfoSearchDummy($container);
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
}