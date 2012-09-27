<?php
require_once 'captcha.php';

/**
 * Класс CAPTCHA, с хранилищем в текстовом файле.
 */
class UfoCaptchaFs extends UfoCaptcha
{
    /**
     * Имя файла-хранилища данных.
     */
    const STACK_FILE = '~captchagen_stack.txt';
    
    /**
     * Имя переменной ключа, передаваемой в GET при запросе картинки.
     *
     * @var string
     */
    protected $htmlGetFieldKey = 'key';
    
    /**
     * Имя поля формы, в котором передается ключ (в POST запросе), 
     * при использовании CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldKey = '!CaptchaKey';
    
    /**
     * Имя поля формы, в которое посетитель вводит значение CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldValue = '!CaptchaValue';
    
    /**
     * Файл стека, хранит временные ключи картинок.
     *
     * @var string
     */
    protected $stackFile = '';
    
    /**
     * Время жизни ключей.
     *
     * @var int
     */
    protected $stackLifetime = 600;
    
    /**
     * Оформление каптчи. Цвет фона.
     *
     * @var array
     */
    protected $bgcolor = array('red' => 0xEE, 'green' => 0xEE, 'blue' => 0xFF);
    
    /**
     * Оформление каптчи. Цвет текста.
     *
     * @var array
     */
    protected $fgcolor = array('red' => 0x99, 'green' => 0x99, 'blue' => 0xCC);
    
    /**
     * Оформление каптчи. Степень сжатия формата JPEG.
     *
     * @var int
     */
    protected $jpegQuality = 20;
    
    /**
     * Оформление каптчи. Шрифт.
     *
     * @var int
     */
    protected $font = 5;
    
    /**
     * Оформление каптчи. Разделитель между символами.
     *
     * @var string
     */
    protected $letterSeparator = ' ';
    
    
    /**
     * Конструктор.
     *
     * @param string $tempPath   путь к папке временных файлов
     * @param string $htmlGetFieldKey = null      имя переменной ключа CAPTCHA, передаваемой в GET
     * @param string $htmlPostFieldKey = null     имя поля формы, в котором передается ключ CAPTCHA в POST
     * @param string $htmlPostFieldValue = null   имя поля формы, в котором передается значение CAPTCHA в POST
     */
    public function __construct($tempPath, 
                                $htmlGetFieldKey = null, 
                                $htmlPostFieldKey = null, 
                                $htmlPostFieldValue = null)
    {
        $this->stackFile = $tempPath . DIRECTORY_SEPARATOR . self::STACK_FILE;
        if (!is_null($htmlGetFieldKey)) {
            $this->htmlGetFieldKey = (string) $htmlGetFieldKey;
        }
        if (!is_null($htmlPostFieldKey)) {
            $this->htmlPostFieldKey = (string) $htmlPostFieldKey;
        }
        if (!is_null($htmlPostFieldValue)) {
            $this->htmlPostFieldValue = (string) $htmlPostFieldValue;
        }
    }
    
    /**
     * Получение значения CAPTCHA по ключу из хранилища.
     *
     * @param string $key   ключ для выборки по нему данных
     *
     * @return string | false
     */
    protected function fromStack($key)
    {
        if ('' == $key) {
            return false;
        }
        $arr = $this->getStackData();
        for ($i = 0; $i < count($arr); $i++) {
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
     * Получение всех данных хранилища, для последующего использования.
     *
     * @return array
     */
    private function getStackData()
    {
        if (!is_readable($this->stackFile)) {
            return array();
        }
        $data = file_get_contents($this->stackFile);
        if (!$data) {
            return array();
        }
        return explode("\n", 
                       str_replace("\r", '', $data));
    }
    
    /**
     * Сохранение ключа и значения в хранилище.
     *
     * @param string $key     ключ
     * @param string $value   значение
     *
     * @return boolean
     */
    protected function intoStack($key, $value)
    {
        if (!$handle = fopen($this->stackFile, 'a')) {
            return false;
        }
        fwrite($handle, time() . "\t" . $key . "\t" . $value . "\r\n");
        fclose($handle);
        return true;
    }
    
    /**
     * Очистка хранилища от старых данных.
     *
     * @return void
     */
    protected function clearOldStack()
    {
        $old_timestamp = time() - $this->stackLifetime;
        $arr = $this->getStackData();
        $arr_ = array();
        for ($i = 0; $i < count($arr); $i++) {
            $item = explode("\t", $arr[$i]);
            if (3 == count($item)) {
                if (((int) $item[0]) > $old_timestamp) {
                    $arr_[] = $arr[$i] . "\r\n";
                }
            }
        }
        
        if (!$handle = fopen($this->stackFile, 'w')) {
            return false;
        }
        fwrite($handle, implode('', $arr_));
        fclose($handle);
        return true;
    }
    
    /**
     * Отображение картинки с ошибкой.
     *
     * @return void
     */
    private function showImageError()
    {
        //выводим картинку с надписью 'ERROR', пока вывод пустой картинки
        @header('Content-type: image/gif');
        echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\xFF\xFF\xFF" . 
             "\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00\x2C\x00\x00\x00\x00" . 
             "\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3B";
    }
    
    /**
     * Отображение картинки CAPTCHA.
     *
     * @return void
     */
    public function showImage()
    {
        if (!isset($_GET[$this->htmlGetFieldKey])) {
            return false;
        }
        
        $data = $this->fromStack($_GET[$this->htmlGetFieldKey]);
        if (false === $data) {
            $this->showImageError();
            return false;
        }
        if ($this->letterSeparator != '') {
            $data_length = strlen($data);
            $data_separated = '';
            for ($i = 0; $i < $data_length; $i++) {
                $data_separated .= $data{$i} . $this->letterSeparator;
            }
            $data = substr($data_separated, 0, strlen($data_separated) - strlen($this->letterSeparator));
        }
        
        //генерируем картинку с данными, искажая их по необходимости
        $img = @imagecreate(120, 60);
        if (false === $img) {
            $this->showImageError();
            return false;
        }
        
        $x = rand(5, 60);
        $y = rand(5, 40);
        $bg =  imagecolorallocate($img, $this->bgcolor['red'], $this->bgcolor['green'], $this->bgcolor['blue']);
        $fg =  imagecolorallocate($img, $this->fgcolor['red'], $this->fgcolor['green'], $this->fgcolor['blue']);
        imagestring($img, $this->font, $x, $y, $data, $fg);
        @header('Content-type: image/jpeg');
        imagejpeg($img, null, $this->jpegQuality);
        imagedestroy($img);
    }
    
    /**
     * Возвращает массив данных о CAPTCHA,
     * может использоваться для оформления CAPTCHA в шаблоне.
     *
     * @param boolean $retunrValue = false  возвращать значение CAPTCHA
     * при установке флага в true, также возвращается значение CAPTCHA, 
     * может использоваться для отладки и тестирования
     *
     * @return array(GetFieldKey, PostFieldKey, PostFieldValue, CaptchaKey[, CaptchaValue])
     */
    public function getCaptcha($retunrValue = false)
    {
        //уникальный ключ для каждой записи
        $key = time() . rand(0, 1000000);
        //генерируем случайные данные
        $value = rand(1000, 9999);
        //убираем устаревшие записи и добавляем новую
        $this->clearOldStack();
        $this->intoStack($key, $value);
        if (!$retunrValue) {
            return array('GetFieldKey'    => $this->htmlGetFieldKey,
                         'PostFieldKey'   => $this->htmlPostFieldKey,
                         'PostFieldValue' => $this->htmlPostFieldValue,
                         'CaptchaKey'     => $key);
        } else {
            return array('GetFieldKey'    => $this->htmlGetFieldKey, 
                         'PostFieldKey'   => $this->htmlPostFieldKey, 
                         'PostFieldValue' => $this->htmlPostFieldValue, 
                         'CaptchaKey'     => $key, 
                         'CaptchaValue'   => $value);
        }
    }
    
    /**
     * Вспомогательная функция, подключает шаблон, 
     * в котором формируется HTML код отображения CAPTCHA,
     * этот код и возвращается функцией.
     *
     * @return string
     */
    public function show($html = false, $silent = false)
    {
        //хэш-массив параметров, передаваемых процедурам оформления вывода
        $params = $this->getCaptcha();
        //вызов процедуры вывода, определенной в шаблоне
        $str = '';
        require_once($_SERVER['DOCUMENT_ROOT'] . '/_templates/__captcha.php');
        return $str;
    }
    
    /**
     * Проверяет введенную посетителем CAPTCHA
     *
     * @return boolean
     */
    public function check()
    {
        if (!isset($_POST[$this->htmlPostFieldKey]) 
            || !isset($_POST[$this->htmlPostFieldValue])) {
            return false;
        }
        $key = $_POST[$this->htmlPostFieldKey];
        $value = $_POST[$this->htmlPostFieldValue];
        if ('' == $key || '' == $value) {
            return false;
        }
        
        $arr = $this->getStackData();
        for ($i = 0; $i < count($arr); $i++) {
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
