<?php
/**
 * ����� ��������������� ������������.
 * 
 * @author enikeishik
 *
 */
trait UfoTools
{
    /**
     * ������������ �������� ������� � �����������.
     * @param string $class                ��� ������������ ������
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $extention = 'php'    ���������� ������ � ��������
     */
    public function loadClass($class, 
                              $path = 'classes', 
                              $extention = 'php')
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }
        include $path . DIRECTORY_SEPARATOR . $class . '.' . $extention;
    }
    
    /**
     * ������������ �������� ������� (��������� ������ ������).
     * ������ ������������� � ����������� ���������, ����������� �� �������� � �������� ������� ������:
     * modules/MyModuleName/MyModuleName.php
     * @param string $module               ��� ������������ ������ (��������� ������ ������)
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $extention = 'php'    ���������� ������ � ��������� �������� �������
     */
    public function loadModule($module, 
                               $path = 'modules', 
                               $extention = 'php')
    {
        $this->loadClass($module, 
                         $path . DIRECTORY_SEPARATOR . $module,  
                         $extention);
    }

    /**
     * ������������ �������� �������� ������� (�����-��������� ���������� �������).
     * @param string $module               ��� ������������ ������ (��������� ������ ������)
     * @param string $insertion            ��� ������������ ������-���������
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $extention = 'php'    ���������� ������ � ��������� �������� �������
     */
    public function loadModuleStruct($module,
                                     $struct,
                                     $path = 'modules',
                                     $extention = 'php')
    {
        $this->loadClass($struct,
                         $path . DIRECTORY_SEPARATOR . $module,
                         $extention);
    }
    
    /**
     * ������������ �������� ������� ������� (������ ������� ������).
     * ������ ������������� � ����������� ���������, ����������� �� �������� � �������� ������� ������:
     * modules/MyModuleName/MyModuleNameIns.php
     * @param string $module               ��� ��������� ������ ������
     * @param string $insertion            ��� ������������ ������ ������� ������
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $extention = 'php'    ���������� ������ � �������� ������� �������
     */
    public function loadInsertionModule($module, 
                                        $insertion, 
                                        $path = 'modules',
                                        $extention = 'php')
    {
        $this->loadClass($insertion,
                         $path . DIRECTORY_SEPARATOR . $module,
                         $extention);
    }
    
    
    /**
     * ������������ �������� ��������.
     * @param string $module               ��� ������������ ������� (��������� ������ �������)
     * @param string $path                 ���� � ����� � ������� ��������
     * @param string $extention = 'php'    ���������� ������ � ��������� �������� ��������
     */
    public function loadTemplate($template, 
                                 $path = 'templates', 
                                 $extention = 'php')
    {
        $this->loadClass($template, 
                         $path, 
                         $extention);
    }
    
    /**
     * ������������ �������� �������.
     * @param UtoTemplate &$tpl                 ������ �� ������ ������� ������
     * @param string      $layout = 'index'     ��� ������������ ������ (��� ����� ������ ��� ����������)
     * @param string      $path                 ���� � ����� � ������� �������
     * @param string      $extention = 'php'    ���������� ������ � ��������
     */
    public function loadLayout(UfoTemplate &$tpl, 
                               $layout = 'index', 
                               $path = 'templates', 
                               $extention = 'php')
    {
        include $path . DIRECTORY_SEPARATOR . $layout . '.' . $extention;
    }
    
    public function redirect($url, $code = 301)
    {
        $hdrs[] = 'HTTP/1.0 301 Moved Permanently';
        $hdrs[] = 'Location: ' . $url;
        $out = '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\r\n" . 
               '<HTML><HEAD>' . "\r\n" .
               '<TITLE>301 Moved Permanently</TITLE>' . "\r\n" .
               '</HEAD><BODY>' . "\r\n" .
               '<H1>Moved Permanently</H1>' . "\r\n" .
               'The document has moved <a href="' . $url . '">here</a>.<P>' . "\r\n" .
               '</BODY></HTML>' . "\r\n";
        foreach ($hdrs as $hdr) {
            header($hdr);
        }
        echo $out;
    }
    
    /**
     * �������� ������ ���������� ���� ������� �� ������������ ������� 
     * `/some/section-path/` � ���������� ������������� �������� (`//`, `..`).
     *
     * ��� Windows ������ ���� �������� �������� ���������� �������� ����:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    ����������� ��������
     * @param boolean $closingSlashRequired = false    ����������� ������� ������������ �����
     * @return boolean
     */
    public function isPath($str, $closingSlashRequired = true)
    {
        if ($closingSlashRequired) {
            return (1 == preg_match('/\/[A-Za-z0-9~_\/\-\.]+\//i', $str)
                    && 0 == preg_match('/(\/{2})|(\.{2})/i', $str));
        } else {
            return (1 == preg_match('/\/[A-Za-z0-9~_\/\-\.]+/i', $str)
                    && 0 == preg_match('/(\/{2})|(\.{2})/i', $str));
        }
    }
    
    /**
     * �������� ������ �� ���������� ������������� �������� ��� ������������ ���� �������.
     *
     * ��� Windows ������ ���� �������� �������� ����:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public function isSafeForPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
    
    /**
     * �������� ���������� �������� �� ������������ �����.
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public function isInt($str)
    {
        return ctype_digit((string) $str) && ($str <= PHP_INT_MAX) && ($str > (PHP_INT_MAX * -1));
    }
    
    /**
     * ��������� �������� �� ������ ������ �������� ���� int
     *
     * @param array $arr    ������ ����������� ��������
     *
     * @return boolean
     */
    public function isArrayOfIntegers(array $arr)
    {
        foreach ($arr as $val) {
            if (!$this->isInt($val)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * ��������� �������� �� ������ �������� ���� int ����������� ������������.
     *
     * @param string $str    ������ ��������, ����������� ������������
     * @param string $sep    ������-����������� ��������
     *
     * @return boolean
     */
    public function isStringOfIntegers($str, $sep = ',')
    {
        return $this->isArrayOfIntegers(array_map(function($str) { return trim($str); },
        explode($sep, $str)));
    }
    
    /**
     * �������� �� ������������ Email ������.
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public function isEmail($str)
    {
        if (0 == strlen($str)) {
            return false;
        }
        return (bool) preg_match('/[a-z0-9_\-\.]+@[a-z0-9\-\.]{2,}\.[a-z]{2,6}/i', $str);
    }
    
    /**
     * ���������� ����� � ����� ����� ��� ������ �� �������� ������.
     *
     * @param string|int $num            �������� �����
     * @param int        $digitsTotal    ����� ���������� �������� � �������� ������ (���������� �������� � �������� "�����")
     * @param boolean    $left = true    ��������� ���� ����� (true) ��� ������ (false)
     *
     * @return string
     */
    public function appendDigits($num, $digitsTotal, $left = true)
    {
        return str_pad((string) $num,
                $digitsTotal,
                '0',
                $left ? STR_PAD_LEFT : STR_PAD_RIGHT);
    }
    
    /**
     * ���������, ����� �� ����������������� ������ ������������ ����/�����.
     *
     * @param string $str                  �������� ������ � �����
     * @param string $formst = 'Y-m-d|'    ������ ����
     *
     * @return boolean
     */
    public function isDate($str, $format = 'Y-m-d|')
    {
        if (false === $dtm = DateTime::createFromFormat($format, $str)) {
            return false;
        }
        if (false === $str = $dtm->format('Y-m-d')) {
            return false;
        }
        $arr = explode('-', $str);
        if (3 != count($arr)) {
            return false;
        }
        return checkdate($arr[1], $arr[2], $arr[0]);
    }
    
    /**
     * ���������� ������ ����/������� �� ����������������� ������.
     *
     * @param string $str                  �������� ������ � �����
     * @param string $formst = 'Y-m-d|'    ������ ����
     *
     * @return DateTime
     */
    public function dateFromString($str, $format = 'Y-m-d|')
    {
        if (false !== $dtm = DateTime::createFromFormat($format, $str)) {
            return $dtm;
        }
        return null;
    }
    
    /**
     * �������������� SQL ��������� � ����������.
     * � ������� ���������� ������ ������������ ��������� �����������
     * PHP ������� addslashes.
     * @param string $str     ������������� ������ SQL ���������
     * @param boolean $cut    �������� ������ �� ������� ���������� ���� (255 ��������)
     * @return string
     * @todo �������� 255 �� ��������� ��� ���� ������������
     */
    public function safeSql($str, $cut = false)
    {
        if (!$cut) {
            return addslashes($str);
        } else {
            return addslashes(substr($str, 0, 255));
        }
    }
    
    /**
     * �������������� JS ��������� � ������.
     * �������������� ������������ �� ���� �������������
     * ���� ����. �������� (\, ', \r, \n).
     *
     * @param string  $str                 ������������� ������ JS ���������
     * @param boolean $convertLt = true    ��������������� ����� ������� `<'
     *
     * @return string
     */
    public function jsAsString($str, $convertLt = true)
    {
        if ($convertLt) {
            return str_replace(array("\\", "'", '<', "\r", "\n"),
                    array("\\\\", "\\'", '<!', '\r', '\n'),
                    $str);
        } else {
            return str_replace(array("\\", "'", "\r", "\n"),
                    array("\\\\", "\\'", '\r', '\n'),
                    $str);
        }
    }
    
    /**
     * ������ ��������� � ���� ���������.
     * @param string $message     ����� ���������
     * @param string $logPath     ���� � ����� ���������
     * @param $logExt = '.log'    ���������� ����� ���������
     * @return int|false
     */
    public function writeLog($message, $logPath, $logExt = '.log')
    {
        if($fhnd = fopen($_SERVER['DOCUMENT_ROOT'] . $logPath . date('ymd') . $logExt, 'a')) {
            return @fwrite($fhnd, date('Y.m.d H:i:s') . "\t" .
                    microtime() . "\t" .
                    $message . "\r\n");
        }
    }
    
    /**
     * �������� ����� ����������� �����.
     * @param string $to                ����������(��) ������
     * @param string $subject           ���� ������������� ������
     * @param string $message           ����� ������������� ������
     * @param string $headers = null    �������������� ��������� ������
     * @todo tests
     */
    public function sendmail($to, $subject, $message, $headers = null)
    {
        switch ($this->config->mailEngine) {
            case 0:
                return mail($to, $subject, $message, $headers);
                break;
            case 1:
                return false;
                break;
            default:
                return false;
        }
    }
}
