<?php
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
     * Динамическая загрузка шаблонов/макетов.
     *
     * @todo заменить значения по-умолчанию константами (глобальными?)
     *
     * @param string $module               имя загружаемого модуля (основного класса модуля)
     * @param string $path                 путь к пакте с файлами модулей
     * @param string $extention = 'php'    расширение файлов с основными классами модулей
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
    public static function isSafeForPath($str)
    {
        return (0 == preg_match('/[^A-Za-z0-9~_\/\-\.]|(\/{2})|(\.{2})/i', 
                                $str));
    }
}
