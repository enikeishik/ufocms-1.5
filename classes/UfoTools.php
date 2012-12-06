<?php
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
     *
     * @return void
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
     *
     * @return void
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
     * ������������ �������� ��������/�������.
     *
     * @todo �������� �������� ��-��������� ����������� (�����������?)
     *
     * @param string $module               ��� ������������ ������ (��������� ������ ������)
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $extention = 'php'    ���������� ������ � ��������� �������� �������
     *
     * @return void
     */
    public function loadTemplate($template, 
                                 $path = 'templates', 
                                 $extention = 'php')
    {
        $this->loadClass($template, 
                         $path, 
                         $extention);
    }
    
    public function loadLayout(UfoTemplate &$tpl, 
                               $layout = 'index', 
                               $path = 'templates', 
                               $extention = 'php')
    {
        include $path . DIRECTORY_SEPARATOR . $layout . '.' . $extention;
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
    public static function isSafeForPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
}
