<?php
require_once 'classes/abstract/UfoDbModel.php';
/**
 * Класс представляющий модель данных в БД.
 * 
 * @author enikeishik
 *
 */
class UfoSysSitemapDbModel extends UfoDbModel
{
    /**
     * Получение данных для модуля.
     * @param string $sortField           поле, по которому будут отсортированы строки
     * @param string $sortDesc = false    обратная сортировка
     */
    public function getContent($sortField, $sortDesc = false)
    {
        $sql = 'SELECT ' . $this->getSectionFields() . 
               ' FROM ' . $this->db->getTablePrefix() . 'sections' . 
               ' WHERE isenabled<>0 AND inmap<>0' . 
               ' ORDER BY ' . $sortField;
        if ($sortDesc) {
            $sql .= ' DESC';
        }
        return $this->db->getRowsByQuery($sql);
    }
}
