<?php
require_once 'Tools.php';

use Codeception\Util\Stub;

class UfoCaptchaTest extends \Codeception\TestCase\Test
{
    use Tools;
    
    const DS = DIRECTORY_SEPARATOR;
    
    
    /**
     * @var UfoCaptchaDummy
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
        require_once $root . self::DS . 'classes' . self::DS . 'UfoContainer.php';
        require_once 'UfoCaptchaDummy.php';
        $container = new UfoContainer();
        $config = new UfoConfig();
        $container->setConfig($config);
        $debug = new UfoDebug($config);
        $container->setDebug($debug);
        $this->obj = new UfoCaptchaDummy($container);
    }
    
    public function __destruct()
    {
        
    }
    
    // tests
    public function testSetImageParams()
    {
        $this->showTestCase(__CLASS__);
        $this->showTest(__FUNCTION__);
        
        echo 'captchaStruct: ' . "\r\n"; var_dump($this->obj->captchaStruct); echo "\r\n"; ob_flush();
        
        $params = new UfoCaptchaStruct();
        $params->bgColor = array('red' => 0xFF, 'green' => 0xFF, 'blue' => 0xFF);
        $params->fgColor = array('red' => 0x00, 'green' => 0x00, 'blue' => 0x00);
        $params->jpegQuality = 50;
        $params->fontSize = 7;
        $params->letterSeperator = '-';
        
        $this->obj->setImageParams($params);
        
        echo 'captchaStruct: ' . "\r\n"; var_dump($this->obj->captchaStruct); echo "\r\n"; ob_flush();
    }
    
    public function testGetStorageData()
    {
        $this->showTest(__FUNCTION__);
        $this->codeGuy->seeMethodReturns($this->obj, 'getStorageData', array());
    }
    
    public function testGetDataByKey()
    {
        $this->showTest(__FUNCTION__);
        $this->codeGuy->seeMethodReturns($this->obj, 'getDataByKey', false, array('qwe'));
    }
}
