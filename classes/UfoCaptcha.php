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
    
    /**
     * Получение данных из хранилища.
     * @return array
     */
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
    
    /**
     * Получение значения по ключу.
     * @return string
     */
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
    
    /**
     * Сохранение данных в хранилище.
     * @param string $timestamp    метка времени создания данных
     * @param string $key          ключ данных
     * @param string $value        значение данных
     * @return boolean
     */
    protected function saveToStorage($timestamp, $key, $value)
    {
        if (!$handle = fopen($this->storageFile, 'a')) {
            return false;
        }
        fwrite($handle, $timestamp . "\t" . $key . "\t" . $value . "\r\n");
        fclose($handle);
        return true;
    }
    
    /**
     * Очистка хранилища от устаревших данных.
     * @return boolean
     */
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
    
    /**
     * Возвращает строку случайных данных определенной длинны.
     * @param int $strLen = 4         длина генерируемой строки
     * @param string $strPad = '0'    символ дополнения до заданной длинны
     * @return string
     */
    protected function getRandomData($strLen = 4, $strPad = '0')
    {
        return str_pad((string) rand(1, 9999), $strLen, $strPad, STR_PAD_LEFT);
    }
    
    /**
     * Вывод картинки с ошибкой.
     */
    public function drawImageError()
    {
        //выводим картинку с надписью 'ERROR', пока вывод пустой картинки
        @header('Content-type: image/gif');
        echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\xFF\xFF\xFF" .
             "\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00\x2C\x00\x00\x00\x00" .
             "\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3B";
    }
    
    /**
     * Отображение картинки с CAPTCHA.
     * @todo заменить числа константами
     */
    public function drawImage()
    {
        if (!isset($_GET[$this->htGetFieldKey])) {
            return false;
        }
        
        $data = $this->getDataByKey($_GET[$this->htGetFieldKey]);
        if (false === $data) {
            $this->drawImageError();
            return false;
        }
        if ($this->captchaStruct->letterSeparator != '') {
            $dataLength = strlen($data);
            $dataSeparated = '';
            for ($i = 0; $i < $dataLength; $i++) {
                $dataSeparated .= $data{$i} . $this->captchaStruct->letterSeparator;
            }
            $data = substr($dataSeparated, 
                           0, 
                           strlen($dataSeparated) - strlen($this->captchaStruct->letterSeparator));
        }
        
        //генерируем картинку с данными, искажая их по необходимости
        if (false === $img = @imagecreate(120, 60)) {
            $this->drawImageError();
            return false;
        }
        $x = rand(5, 60);
        $y = rand(5, 40);
        $bg =  @imagecolorallocate($img, 
                                   $this->captchaStruct->bgColor['red'], 
                                   $this->captchaStruct->bgColor['green'], 
                                   $this->captchaStruct->bgColor['blue']);
        $fg =  @imagecolorallocate($img, 
                                   $this->captchaStruct->fgColor['red'], 
                                   $this->captchaStruct->fgColor['green'], 
                                   $this->captchaStruct->fgColor['blue']);
        if (!@imagestring($img, $this->captchaStruct->fontSize, $x, $y, $data, $fg)) {
            $this->drawImageError();
            return false;
        }
        @header('Content-type: image/jpeg');
        if (!@imagejpeg($img, null, $this->jpeg_quality)) {
            $this->drawImageError();
            return false;
        }
        @imagedestroy($img);
        return true;
    }
    
    /**
     * Получение данных CAPTCHA (имена полей, значение ключа).
     * @return array
     */
    public function getData()
    {
        //генерируем случайные данные
        $randomData = $this->getRandomData();
        //отметка текущего времени
        $timestamp = time();
        //уникальный ключ для каждой записи
        $ticket = $timestamp . rand(0, 1000000);
        //убираем устаревшие записи и добавляем новую
        $this->clearOldStorageData();
        $this->saveToStorage($timestamp, $ticket, $randomData);
        return array('GetFieldKey'    => $this->htGetFieldKey, 
                     'PostFieldKey'   => $this->htPostFieldKey, 
                     'PostFieldValue' => $this->htPostFieldValue, 
                     'Ticket'         => $ticket);
    }
    
    /**
     * Вывод HTML кода CAPTCHA.
     */
    public function draw()
    {
        $this->loadTemplate('UfoTemplateCaptcha');
        $tpl = new UfoTemplateCaptcha($this);
        $tpl->drawCaptcha();
    }
    
    /**
     * Возвращает сгенерированных HTML код CAPTCHA.
     * @return string
     */
    public function getCaptcha()
    {
        ob_start();
        $this->draw();
        return ob_get_clean();
    }
    
    /**
     * Проверка переданных клиентом данных на соответствие показанным.
     * @return boolean
     */
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
}
