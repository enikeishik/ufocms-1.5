<?php
require_once 'classes/abstract/UfoDbModel.php';
/**
 * ����� �������������� ������ ������ � ��.
 * 
 * @author enikeishik
 *
 */
class UfoSysSitemapDbModel extends UfoDbModel
{
    /**
     * ��������� ������ ��� ������.
     * @param string $sortField           ����, �� �������� ����� ������������� ������
     * @param string $sortDesc = false    �������� ����������
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
