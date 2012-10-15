<?php
/**
 * Класс вспомогательных функционалов.
 *
 * Предоставляет статичные методы для выполнения различных действий.
 */
class UfoTools
{
    /**
     * Проверка строкового значения на соответствия числу.
     *
     * @param string $str    проверяемое значение
     *
     * @return boolean
     */
    public static function isInt($str)
    {
        return ctype_digit((string) $str) && ($str <= PHP_INT_MAX) && ($str > (PHP_INT_MAX * -1));
    }
    
    /**
     * Проверяет содержит ли массив только значения типа int
     *
     * @param array $arr    массив проверяемых значений
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
     * Проверяет содержит ли строка значения типа int разделенные разделителем.
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
     * Проверка на соответствие Email адресу.
     *
     * @param string $str    проверяемое значение
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
     * Проверка строки содержащий путь раздела на отсутствие нежелательных символов.
     *
     * Для Windows систем надо добавить проверку вида:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    проверяемое значение
     *
     * @return boolean
     */
    public static function isPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
    
    /**
     * Преобразование SQL выражения в безопасное.
     * В текущей реализации просто экранируются апострофы посредством 
     * PHP функции addslashes.
     *
     * @param string $str    преобразуемая строка SQL выражения
     *
     * @return string
     */
    public static function safeSql($str)
    {
        return addslashes($str);
    }
    
    /**
     * Преобразование JS выражения в строку.
     * Преобразование производится за счет экранирования 
     * всех спец. символов (\, ', \r, \n).
     *
     * @param string  $str                 преобразуемая строка JS выражения
     * @param boolean $convertLt = true    преобразовывать также симполы `<'
     *
     * @return string
     */
    public static function jsAsString($str, $convertLt = true)
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
     * Вставка в текст тэгов параграфа.
     *
     * @param string $str            преобразуемая строка
     * @param string $nl = "\r\n"    символ(ы) перевода строк
     *
     * @return string
     */
    public static function insertParagraphs($str, $nl = "\r\n")
    {
        return str_replace('<p></p>' . $nl, 
                           '', 
                           '<p>' . str_replace($nl, 
                                               '</p>' . $nl . '<p>', 
                                               trim($str)) . '</p>' . $nl);
    }
    
    /**
     * Удаление тэгов параграфа из текста.
     *
     * @param string $str            преобразуемая строка
     * @param string $nl = "\r\n"    символ(ы) перевода строк
     *
     * @return string
     */
    public static function removeParagraphs($str, $nl = "\r\n")
    {
        return trim(preg_replace('/(' . addcslashes($nl, '\0\r\n\f\v\t') . '){2}/', 
                                 $nl, 
                                 str_replace(array('<p>', '</p>'), 
                                             array('', $nl), 
                                             $str)));
    }
    
    /**
     * Получение первого параграфа текста.
     * Если текст разбит на параграфы тэгами <p>, 
     * эта функция возвращает первый параграф 
     * (вместе с тэгами параграфа).
     * Иначе возвращает пустую строку.
     *
     * @param string $str    исходный текст
     *
     * @return string
     */
    public static function getFirstParagraph($str)
    {
        return substr($str, 0, stripos($str, '<p>', 3));
    }
    
    /**
     * Динамическая загрузка классов и интерфейсов.
     *
     * @param string $class                имя загружаемого класса
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
     * Проверка соответствия текущей версии PHP и требуемой системой.
     */
    public static function isPhpUptodate()
    {
        return PHP_MAJOR_VERSION >= UfoConfig::$phpVersionRequired['Major'] 
               && PHP_MINOR_VERSION >= UfoConfig::$phpVersionRequired['Minor'] 
               && PHP_RELEASE_VERSION >= UfoConfig::$phpVersionRequired['Release'];
    }
}
