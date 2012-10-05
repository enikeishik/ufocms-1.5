<?php
/**
 * ����� ��������������� ������������.
 *
 * ������������� ��������� ������ ��� ���������� ��������� ��������.
 */
class UfoTools
{
    /**
     * �������� ���������� �������� �� ������������ �����.
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public static function isInt($str)
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
    public static function isArrayOfIntegers(array $arr)
    {
        foreach ($arr as $val) {
            if (!self::isInt($val)) {
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
    public static function isStringOfIntegers($str, $sep = ',')
    {
        return self::isArrayOfIntegers(array_map(function($str) { return trim($str); }, 
                                                 explode($sep, $str)));
    }
    
    /**
     * �������� �� ������������ Email ������.
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public static function isEmail($str)
    {
        if (0 == strlen($str)) {
            return false;
        }
        return (bool) preg_match('/[a-z0-9_\-\.]+@[a-z0-9\-\.]+\.[a-z]{2,6}/i', $str);
    }
    
    /**
     * �������� ������ ���������� ���� ������� �� ���������� ������������� ��������.
     *
     * ��� Windows ������ ���� �������� �������� ����:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public static function isPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
    
    /**
     * ������������ �������� ������� � �����������.
     *
     * @param string $class                ��� ������������ ������
     * @param string $path                 ���� � ����� � ������� �������
     * @param string $prefix = 'Ufo'       ������� ������� � �������
     * @param string $extention = 'php'    ���������� ������ � ��������
     *
     * @return void
     */
    public static function loadClass($class, 
                                     $path, 
                                     $prefix = 'Ufo', 
                                     $extention = 'php')
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }
        $file = $path . DIRECTORY_SEPARATOR . 
                strtolower(substr_replace($class, '', 0, strlen($prefix))) . 
                '.' . $extention;
        include $file;
    }
    
    /**
     * �������� ������������ ������� ������ PHP � ��������� ��������.
     */
    public static function isPhpUptodate()
    {
        return PHP_MAJOR_VERSION >= UfoConfig::$phpVersionRequired['Major'] 
               && PHP_MINOR_VERSION >= UfoConfig::$phpVersionRequired['Minor'] 
               && PHP_RELEASE_VERSION >= UfoConfig::$phpVersionRequired['Release'];
    }
}
