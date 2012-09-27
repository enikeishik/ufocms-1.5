<?php
require_once 'captcha.php';

/**
 * ����� CAPTCHA, � ���������� � ��������� �����.
 */
class UfoCaptchaFs extends UfoCaptcha
{
    /**
     * ��� �����-��������� ������.
     */
    const STACK_FILE = '~captchagen_stack.txt';
    
    /**
     * ��� ���������� �����, ������������ � GET ��� ������� ��������.
     *
     * @var string
     */
    protected $htmlGetFieldKey = 'key';
    
    /**
     * ��� ���� �����, � ������� ���������� ���� (� POST �������), 
     * ��� ������������� CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldKey = '!CaptchaKey';
    
    /**
     * ��� ���� �����, � ������� ���������� ������ �������� CAPTCHA.
     *
     * @var string
     */
    protected $htmlPostFieldValue = '!CaptchaValue';
    
    /**
     * ���� �����, ������ ��������� ����� ��������.
     *
     * @var string
     */
    protected $stackFile = '';
    
    /**
     * ����� ����� ������.
     *
     * @var int
     */
    protected $stackLifetime = 600;
    
    /**
     * ���������� ������. ���� ����.
     *
     * @var array
     */
    protected $bgcolor = array('red' => 0xEE, 'green' => 0xEE, 'blue' => 0xFF);
    
    /**
     * ���������� ������. ���� ������.
     *
     * @var array
     */
    protected $fgcolor = array('red' => 0x99, 'green' => 0x99, 'blue' => 0xCC);
    
    /**
     * ���������� ������. ������� ������ ������� JPEG.
     *
     * @var int
     */
    protected $jpegQuality = 20;
    
    /**
     * ���������� ������. �����.
     *
     * @var int
     */
    protected $font = 5;
    
    /**
     * ���������� ������. ����������� ����� ���������.
     *
     * @var string
     */
    protected $letterSeparator = ' ';
    
    
    /**
     * �����������.
     *
     * @param string $tempPath   ���� � ����� ��������� ������
     * @param string $htmlGetFieldKey = null      ��� ���������� ����� CAPTCHA, ������������ � GET
     * @param string $htmlPostFieldKey = null     ��� ���� �����, � ������� ���������� ���� CAPTCHA � POST
     * @param string $htmlPostFieldValue = null   ��� ���� �����, � ������� ���������� �������� CAPTCHA � POST
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
     * ��������� �������� CAPTCHA �� ����� �� ���������.
     *
     * @param string $key   ���� ��� ������� �� ���� ������
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
     * ��������� ���� ������ ���������, ��� ������������ �������������.
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
     * ���������� ����� � �������� � ���������.
     *
     * @param string $key     ����
     * @param string $value   ��������
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
     * ������� ��������� �� ������ ������.
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
     * ����������� �������� � �������.
     *
     * @return void
     */
    private function showImageError()
    {
        //������� �������� � �������� 'ERROR', ���� ����� ������ ��������
        @header('Content-type: image/gif');
        echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\xFF\xFF\xFF" . 
             "\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00\x2C\x00\x00\x00\x00" . 
             "\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3B";
    }
    
    /**
     * ����������� �������� CAPTCHA.
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
        
        //���������� �������� � �������, ������� �� �� �������������
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
     * ���������� ������ ������ � CAPTCHA,
     * ����� �������������� ��� ���������� CAPTCHA � �������.
     *
     * @param boolean $retunrValue = false  ���������� �������� CAPTCHA
     * ��� ��������� ����� � true, ����� ������������ �������� CAPTCHA, 
     * ����� �������������� ��� ������� � ������������
     *
     * @return array(GetFieldKey, PostFieldKey, PostFieldValue, CaptchaKey[, CaptchaValue])
     */
    public function getCaptcha($retunrValue = false)
    {
        //���������� ���� ��� ������ ������
        $key = time() . rand(0, 1000000);
        //���������� ��������� ������
        $value = rand(1000, 9999);
        //������� ���������� ������ � ��������� �����
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
     * ��������������� �������, ���������� ������, 
     * � ������� ����������� HTML ��� ����������� CAPTCHA,
     * ���� ��� � ������������ ��������.
     *
     * @return string
     */
    public function show($html = false, $silent = false)
    {
        //���-������ ����������, ������������ ���������� ���������� ������
        $params = $this->getCaptcha();
        //����� ��������� ������, ������������ � �������
        $str = '';
        require_once($_SERVER['DOCUMENT_ROOT'] . '/_templates/__captcha.php');
        return $str;
    }
    
    /**
     * ��������� ��������� ����������� CAPTCHA
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
