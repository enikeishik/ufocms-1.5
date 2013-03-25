<?php
/**
 * Трейт вспомогательных функционалов.
 * 
 * @author enikeishik
 *
 */
trait UfoTools
{
    /**
     * Динамическая загрузка классов и интерфейсов.
     * @param string $class                имя загружаемого класса
     * @param string $path                 путь к пакте с файлами классов
     * @param string $extention = 'php'    расширение файлов с классами
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
     * Динамическая загрузка модулей (основного класса модуля).
     * Модули располагаются в собственных подпапках, совпадающих по названию с основным классом модуля:
     * modules/MyModuleName/MyModuleName.php
     * @param string $module               имя загружаемого модуля (основного класса модуля)
     * @param string $path                 путь к пакте с файлами модулей
     * @param string $extention = 'php'    расширение файлов с основными классами модулей
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
     * Динамическая загрузка структур модулей (класс-структура служебного раздела).
     * @param string $module               имя загружаемого модуля (основного класса модуля)
     * @param string $insertion            имя загружаемого класса-структуры
     * @param string $path                 путь к пакте с файлами модулей
     * @param string $extention = 'php'    расширение файлов с основными классами модулей
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
     * Динамическая загрузка вставок модулей (класса вставки модуля).
     * Модули располагаются в собственных подпапках, совпадающих по названию с основным классом модуля:
     * modules/MyModuleName/MyModuleNameIns.php
     * @param string $module               имя основного класса модуля
     * @param string $insertion            имя загружаемого класса вставки модуля
     * @param string $path                 путь к пакте с файлами модулей
     * @param string $extention = 'php'    расширение файлов с классами вставок модулей
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
     * Динамическая загрузка шаблонов.
     * @param string $module               имя загружаемого шаблона (основного класса шаблона)
     * @param string $path                 путь к пакте с файлами шаблонов
     * @param string $extention = 'php'    расширение файлов с основными классами шаблонов
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
     * Динамическая загрузка макетов.
     * @param UtoTemplate &$tpl                 ссылка на объект шаблона модуля
     * @param string      $layout = 'index'     имя загружаемого макета (имя файла макета без расширения)
     * @param string      $path                 путь к пакте с файлами макетов
     * @param string      $extention = 'php'    расширение файлов с макетами
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
     * Проверка строки содержащий путь раздела на соответствие шаблону 
     * `/some/section-path/` и отсутствие нежелательных символов (`//`, `..`).
     *
     * Для Windows систем надо добавить проверку отсутствия подстрок вида:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    проверяемое значение
     * @param boolean $closingSlashRequired = false    обязательно наличие закрывающего слэша
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
     * Проверка строки на отсутствие нежелательных символов для формирования пути раздела.
     *
     * Для Windows систем надо добавить проверку вида:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    проверяемое значение
     *
     * @return boolean
     */
    public function isSafeForPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
    
    /**
     * Проверка строкового значения на соответствия числу.
     *
     * @param string $str    проверяемое значение
     *
     * @return boolean
     */
    public function isInt($str)
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
     * Проверяет содержит ли строка значения типа int разделенные разделителем.
     *
     * @param string $str    строка значений, разделенных разделителем
     * @param string $sep    строка-разделитель значений
     *
     * @return boolean
     */
    public function isStringOfIntegers($str, $sep = ',')
    {
        return $this->isArrayOfIntegers(array_map(function($str) { return trim($str); },
        explode($sep, $str)));
    }
    
    /**
     * Проверка на соответствие Email адресу.
     *
     * @param string $str    проверяемое значение
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
     * Добавление нулей к числу слева или справа до заданной длинны.
     *
     * @param string|int $num            исходное число
     * @param int        $digitsTotal    общее количество символов в итоговой строке (количество разрядов в итоговом "числе")
     * @param boolean    $left = true    добавлять нули слева (true) или справа (false)
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
     * Проверяет, может ли отформатированная строка представлять дату/время.
     *
     * @param string $str                  исходная строка с датой
     * @param string $formst = 'Y-m-d|'    формат даты
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
     * Возвращает объект даты/времени из отформатированной строки.
     *
     * @param string $str                  исходная строка с датой
     * @param string $formst = 'Y-m-d|'    формат даты
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
     * Преобразование SQL выражения в безопасное.
     * В текущей реализации просто экранируются апострофы посредством
     * PHP функции addslashes.
     * @param string $str     преобразуемая строка SQL выражения
     * @param boolean $cut    обрезать строку до размера текстового поля (255 символов)
     * @return string
     * @todo заменить 255 на константу или поле конфигурации
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
     * Преобразование JS выражения в строку.
     * Преобразование производится за счет экранирования
     * всех спец. символов (\, ', \r, \n).
     *
     * @param string  $str                 преобразуемая строка JS выражения
     * @param boolean $convertLt = true    преобразовывать также симполы `<'
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
     * Запись сообщения в файл протокола.
     * @param string $message     текст сообщения
     * @param string $logPath     путь к файлу протокола
     * @param $logExt = '.log'    расширение файла протокола
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
     * Отправка писем электронной почты.
     * @param string $to                получатель(ли) письма
     * @param string $subject           тема отправляемого письма
     * @param string $message           текст отправляемого письма
     * @param string $headers = null    дополнительные заголовки письма
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
