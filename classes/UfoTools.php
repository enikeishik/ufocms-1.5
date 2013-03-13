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
}
