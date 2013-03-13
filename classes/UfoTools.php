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
}
