<?php
/**
 * “рейт вспомогательных функционалов.
 */
trait UfoTools
{
    /**
     * ƒинамическа€ загрузка классов и интерфейсов.
     *
     * @todo заменить значени€ по-умолчанию константами (глобальными?)
     *
     * @param string $class                им€ загружаемого класса
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
     * ƒинамическа€ загрузка модулей (основного класса модул€).
     * ћодули располагаютс€ в собственных подпапках, 
     * совпадающих по названию с основным классом модул€:
     * modules/MyModuleName/MyModuleName.php
     *
     * @todo заменить значени€ по-умолчанию константами (глобальными?)
     *
     * @param string $module               им€ загружаемого модул€ (основного класса модул€)
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
     * ƒинамическа€ загрузка шаблонов.
     *
     * @todo заменить значени€ по-умолчанию константами (глобальными?)
     *
     * @param string $module               им€ загружаемого шаблона (основного класса шаблона)
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
     * ƒинамическа€ загрузка макетов.
     *
     * @todo заменить значени€ по-умолчанию константами (глобальными?)
     *
     * @param UtoTemplate &$tpl                 ссылка на объект шаблона модул€
     * @param string      $layout = 'index'     им€ загружаемого макета (им€ файла макета без расширени€)
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
     * ѕроверка строки содержащий путь раздела на соответствие шаблону 
     * `/some/section-path/` и отсутствие нежелательных символов (`//`, `..`).
     *
     * ƒл€ Windows систем надо добавить проверку отсутстви€ подстрок вида:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    провер€емое значение
     *
     * @return boolean
     */
    public function isPath($str)
    {
        return (1 == preg_match('/\/[A-Za-z0-9~_\/\-\.]+\//i', $str)
                && 0 == preg_match('/(\/{2})|(\.{2})/i', $str));
    }
    
    /**
     * ѕроверка строки на отсутствие нежелательных символов дл€ формировани€ пути раздела.
     *
     * ƒл€ Windows систем надо добавить проверку вида:
     * /(AUX|PRN|NUL|COM\d|CON|LPT\d)+\s/i
     *
     * @param string $str    провер€емое значение
     *
     * @return boolean
     */
    public function isSafeForPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
}
