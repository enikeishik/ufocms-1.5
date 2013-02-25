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
     *
     * @todo заменить значения по-умолчанию константами (глобальными?)
     *
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
     * Модули располагаются в собственных подпапках, 
     * совпадающих по названию с основным классом модуля:
     * modules/MyModuleName/MyModuleName.php
     *
     * @todo заменить значения по-умолчанию константами (глобальными?)
     *
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
     * Модули располагаются в собственных подпапках,
     * совпадающих по названию с основным классом модуля:
     * modules/MyModuleName/MyModuleNameIns.php
     *
     * @todo заменить значения по-умолчанию константами (глобальными?)
     *
     * @param string $module               имя загружаемого модуля (основного класса модуля)
     * @param string $path                 путь к пакте с файлами модулей
     * @param string $extention = 'php'    расширение файлов с классами вставок модулей
     * @param string $suffix = 'Ins'       суффикс
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
     * Динамическая загрузка шаблонов.
     *
     * @todo заменить значения по-умолчанию константами (глобальными?)
     *
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
     *
     * @todo заменить значения по-умолчанию константами (глобальными?)
     *
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
     *
     * @return boolean
     */
    public function isPath($str)
    {
        return (1 == preg_match('/\/[A-Za-z0-9~_\/\-\.]+\//i', $str)
                && 0 == preg_match('/(\/{2})|(\.{2})/i', $str));
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
}
