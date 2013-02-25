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
     *
     * @todo �������� �������� ��-��������� ����������� (�����������?)
     *
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
     * ������ ������������� � ����������� ���������, 
     * ����������� �� �������� � �������� ������� ������:
     * modules/MyModuleName/MyModuleName.php
     *
     * @todo �������� �������� ��-��������� ����������� (�����������?)
     *
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
     * ������ ������������� � ����������� ���������,
     * ����������� �� �������� � �������� ������� ������:
     * modules/MyModuleName/MyModuleNameIns.php
     *
     * @todo �������� �������� ��-��������� ����������� (�����������?)
     *
     * @param string $module               ��� ������������ ������ (��������� ������ ������)
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $extention = 'php'    ���������� ������ � �������� ������� �������
     * @param string $suffix = 'Ins'       �������
     */
    public function loadInsertionModule($module,
                                        $path = 'modules',
                                        $extention = 'php', 
                                        $suffix = 'Ins')
    {
        $this->loadClass($module . $suffix,
                $path . DIRECTORY_SEPARATOR . $module,
                $extention);
    }
    
    
    /**
     * ������������ �������� ��������.
     *
     * @todo �������� �������� ��-��������� ����������� (�����������?)
     *
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
     *
     * @todo �������� �������� ��-��������� ����������� (�����������?)
     *
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
     *
     * @return boolean
     */
    public function isPath($str)
    {
        return (1 == preg_match('/\/[A-Za-z0-9~_\/\-\.]+\//i', $str)
                && 0 == preg_match('/(\/{2})|(\.{2})/i', $str));
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
}
