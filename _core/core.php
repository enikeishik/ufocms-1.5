<?php
/**
 * Класс базовой функциональности системы.
 *
 * Предоставляет статичные методы 
 * для получения различной информации о системе.
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
    public static function getSectionIdByUrl($url)
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
}
