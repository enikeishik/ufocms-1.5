<?php
/**
 * Класс CAPTCHA.
 * Содержит методы для генерации и проверки.
 * 
 * @author enikeishik
 *
 */
class UfoCaptcha
{
    use UfoTools;
    
    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Имя переменной ключа, передаваемой в GET при запросе картинки.
     * @var string
     */
    protected $htGetFieldKey = 'key';
    
    /**
     * Имя переменной ключа, передаваемой в POST при использовании CAPTCHA.
     * @var string
     */
    protected $htPostFieldKey = '!CaptchaKey';
    
    /**
     * Имя переменной значения, передаваемой в POST при использовании CAPTCHA.
     * @var string
     */
    protected $htPostFieldValue = '!CaptchaValue';
    
    /**
     * Файл-хранилище временных ключей.
     * @var string
     */
    protected $storageFile = '';
    
    /**
     * Время жизни ключей в хранилище.
     * @var int
     */
    protected $storageLifetime = 600;
    
    /**
     * Объект-структура с параметрами генерируемой картинки.
     * @var UfoCaptchaStruct
     */
    protected $captchaStruct = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->storageFile = $this->config->tmpDir . '/~captchagen_stack.txt';
        
        $this->loadClass('UfoCaptchaStruct');
        $this->captchaStruct = new UfoCaptchaStruct();
        $this->captchaStruct->bgColor = array('red' => 0xEE, 'green' => 0xEE, 'blue' => 0xFF);
        $this->captchaStruct->fgColor = array('red' => 0x99, 'green' => 0x99, 'blue' => 0xCC);
        $this->captchaStruct->jpegQuality = 20;
        $this->captchaStruct->fontSize = 5;
        $this->captchaStruct->letterSeperator = ' ';
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * Установка параметров генерируемой картинки.
     * @param UfoCaptchaStruct $params    параметры генерируемой картинки
     */
    public function setImageParams(UfoCaptchaStruct $params)
    {
        $this->captchaStruct = $params;
    }
    
    public function getData()
    {
        
    }
    
    public function drawImage()
    {
        
    }
    
    public function draw()
    {
        $this->loadTemplate('UfoTemplateCaptcha');
        $tpl = new UfoTemplateCaptcha($this);
        $tpl->drawCaptcha();
    }
    
    public function check()
    {
        if (!isset($_POST[$this->htPostFieldKey])
        || !isset($_POST[$this->htPostFieldValue])) {
            return false;
        }
        $key = $_POST[$this->htPostFieldKey];
        $value = $_POST[$this->htPostFieldValue];
        if ('' == $key || '' == $value) {
            return false;
        }
        
        $arr = $this->getStorageData();
        for ($i = 0, $arrCount = count($arr); $i < $arrCount; $i++) {
            $item = explode("\t", $arr[$i]);
            if (3 == count($item)) {
                if ($key == $item[1]) {
                    return ($value == $item[2]);
                }
            }
        }
        return false;
    }
    
    protected function getStorageData()
    {
        if (!is_readable($this->storageFile)) {
            return array();
        }
        $data = file_get_contents($this->storageFile);
        if (!$data) {
            return array();
        }
        return explode("\n",
                       str_replace("\r", '', $data));
    }
    
    protected function getDataByKey($key)
    {
        if ('' == $key) {
            return false;
        }
        $arr = $this->getStorageData();
        for ($i = 0, $arrCount = count($arr); $i < $arrCount; $i++) {
            $item = explode("\t", $arr[$i]);
            if (3 == count($item)) {
                if ($key == $item[1]) {
                    return $item[2];
                }
            }
        }
        return false;
    }
    
    protected function saveToStorage($timestamp, $key, $value)
    {
        if (!$handle = fopen($this->storageFile, 'a')) {
            return false;
        }
        fwrite($handle, $timestamp . "\t" . $key . "\t" . $value . "\r\n");
        fclose($handle);
        return true;
    }
    
    protected function clearOldStorageData()
    {
        $oldTimestamp = time() - $this->storageLifetime;
        $arr = $this->getStorageData();
        $arr_ = array();
        for ($i = 0, $arrCount = count($arr); $i < $arrCount; $i++) {
            $item = explode("\t", $arr[$i]);
            if (3 == count($item)) {
                if (((int) $item[0]) > $oldTimestamp) {
                    $arr_[] = $arr[$i] . "\r\n";
                }
            }
        }
        if (!$handle = fopen($this->storageFile, 'w')) {
            return false;
        }
        fwrite($handle, implode('', $arr_));
        fclose($handle);
        return true;
    }
}
