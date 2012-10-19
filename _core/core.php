<?php
/**
 * ����� ������� ���������������� �������.
 *
 * ������������� ��������� ������ 
 * ��� ��������� ��������� ���������� � �������.
 */
class UfoCore
{
    /**
     * ��������� �������������� ������� �� ��� ����.
     *
     * @param string $path    ���� ������� (����� URL)
     *
     * @return int|false
     */
    public static function getSectionIdByPath($path)
    {
        if (!UfoTools::isPath($path)) {
            return false;
        }
        $sql = 'SELECT ' . C_DB_SECTIONS_FIELDS . 
               ' FROM ' . C_DB_TABLE_PREFIX . 'sections' . 
               " WHERE path='" . $path . "'";
        return UfoDb::getRowByQuery($sql);
    }
    
    public static function getSectionById($id)
    {
        $sql = 'SELECT ' . C_DB_SECTIONS_FIELDS . 
               ' FROM ' . C_DB_TABLE_PREFIX . 'sections' . 
               ' WHERE id=' . $id;
        return UfoDb::getRowByQuery($sql);
    }
    
    public static function getSectionsByParentId($id)
    {
        $sql = 'SELECT ' . C_DB_SECTIONS_FIELDS . 
               ' FROM ' . C_DB_TABLE_PREFIX . 'sections' . 
               ' WHERE parentid=' . $id;
        return UfoDb::getRowsByQuery($sql);
    }
}
