<?php
use Codeception\Util\Stub;

class UfoToolsTest extends \Codeception\TestCase\Test
{
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
    
    const DS = DIRECTORY_SEPARATOR;
    /**
     * @var UfoCore
     */
    private $obj = null;
    /**
     * @var string
     */
    private $root = '';
    
    public function __construct()
    {
        $_GET['path'] = '/';
        $this->root = __DIR__ . self::DS . '..' . self::DS . '..';
        require_once $this->root . self::DS . 'config.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoTools.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoDb.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCoreDbModel.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSite.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSection.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoSectionStruct.php';
        require_once $this->root . self::DS . 'classes' . self::DS . 'UfoCore.php';
        require_once __DIR__ . self::DS . 'UfoToolsDummy.php';
        $this->obj = new UfoToolsDummy();
    }
    
    // tests
    public function testIsPath()
    {
        $vals[] = array('/asd/', true);
        $vals[] = array('/asd/qwe-zxc/~123_vfr/index.html', true);
        $vals[] = array('/', false);
        $vals[] = array('', false);
        $vals[] = array('/as..d/', false);
        foreach ($vals as $v) {
            echo 'test `' . $v[0] . '`' . "\r\n";
            $ret = $this->obj->isPath($v[0]);
            var_dump($ret);
            $this->codeGuy->seeMethodReturns($this->obj, 'isPath', $v[1], array($v[0]));
        }
    }

}