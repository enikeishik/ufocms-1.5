<?php
/**
 *
 */
class UfoCore
{
    /**
     * Получение идентификатора раздела по его URL.
     *
     * @param string $url    URL раздела
     *
     * @return int|false
     */
    public static function getIdByUrl($url)
    {
        $sql = 'SELECT ' . C_DB_SECTIONS_FIELDS . 
               ' FROM ' . C_DB_TABLE_PREFIX . 'sections' . 
               " WHERE path='" . api_GetSafePath($path) . "'";
        $result = mysql_query($sql);
        if ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
            mysql_free_result($result);
            return $row;
        } else {
            mysql_free_result($result);
            return false;
        }
    }
    
    /**
     * Динамическая загрузка классов и интерфейсов.
     *
     * @param string $class    [путь]имя загружаемого класса
     *
     * @return void
     */
    public static function loadClass($class)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }
        if (false === strpos($class, DIRECTORY_SEPARATOR) && false !== strpos($class, ',')) {
            $file = str_replace(',', DIRECTORY_SEPARATOR, $class) . '.php';
        } else {
            $file = $class . '.php';
        }
        include $file;
    }
}
