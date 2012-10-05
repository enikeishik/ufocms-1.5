<?php
/**
 *  ласс вспомогательных функционалов.
 *
 * ѕредоставл€ет статичные методы дл€ выполнени€ различных действий.
 */
class UfoTools
{
    /**
     * ѕроверка строкового значени€ на соответстви€ числу.
     *
     * @param string $str    провер€емое значение
     *
     * @return boolean
     */
    public static function isInt($str)
    {
        return ctype_digit((string) $str) && ($str <= PHP_INT_MAX) && ($str > (PHP_INT_MAX * -1));
    }
    
    /**
     * ѕровер€ет содержит ли массив только значени€ типа int
     *
     * @param array $arr    массив провер€емых значений
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
     * ѕровер€ет содержит ли строка значени€ типа int разделенные разделителем.
     *
     * @param string $str    строка значений, разделенных разделителем
     * @param string $sep    строка-разделитель значений
     *
     * @return boolean
     */
    public static function isStringOfIntegers($str, $sep = ',')
    {
        return self::isArrayOfIntegers(array_map(function($str) { return trim($str); }, 
                                                 explode($sep, $str)));
    }
    
    /**
     * ѕроверка на соответствие Email адресу.
     *
     * @param string $str    провер€емое значение
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
     * ѕроверка строки содержащий путь раздела на отсутствие нежелательных символов.
     *
     * ƒл€ Windows систем надо добавить проверку вида:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    провер€емое значение
     *
     * @return boolean
     */
    public static function isPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
    
    /**
     * ƒинамическа€ загрузка классов и интерфейсов.
     *
     * @param string $class                им€ загружаемого класса
     * @param string $path                 путь к пакте с файлами классов
     * @param string $prefix = 'Ufo'       префикс классов в системе
     * @param string $extention = 'php'    расширение файлов с классами
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
     * ѕроверка соответстви€ текущей версии PHP и требуемой системой.
     */
    public static function isPhpUptodate()
    {
        return PHP_MAJOR_VERSION >= UfoConfig::$phpVersionRequired['Major'] 
               && PHP_MINOR_VERSION >= UfoConfig::$phpVersionRequired['Minor'] 
               && PHP_RELEASE_VERSION >= UfoConfig::$phpVersionRequired['Release'];
    }
}
