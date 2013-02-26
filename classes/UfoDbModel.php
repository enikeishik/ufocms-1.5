<?php
require_once 'UfoTools.php';
/**
 * ����� �������������� ������ ������ � ��.
 * ������������� ������ ��� ���������� ����������� ��� ������� �������� � ��, ����������� ��������� ������.
 * 
 * @author enikeishik
 *
 */
class UfoDbModel
{
    use UfoTools;
    
    /**
     * ������ �� ������ ������ � ����� ������.
     * @var UfoDb
     */
    private $db = null;
    
    /**
     * �����������.
     * @param UfoDb $db    ������ �� ������ ��� ������ � ��
     */
    public function __construct(UfoDb &$db)
    {
        $this->db =& $db;
    }
    
    /**
     * ��������� ������ ������� �����.
     * @param mixed $section    ������������� ��� ���� ������� �����
     * @throws Exception
     * @return array|false
     */
    public function getSection($section)
    {
        $sql = 'SELECT ' . $this->fieldsSql .
               ' FROM ' . $this->db->getTablePrefix() . 'sections' .
               ' WHERE ';
        if (is_int($section)) {
            $sql .= 'id=' . $section;
        } else if (is_string($section) && '/' == $section) {
            $sql .= 'id=-1';
        } else if (is_string($section) && $this->isPath($section)) {
            $sql .= "path='" . $section . "'";
        } else {
            throw new Exception('Incorrect $section: ' . var_export($section, true));
        }
        return $this->db->getRowByQuery($sql);
    }
}