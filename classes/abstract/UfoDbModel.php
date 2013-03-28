<?php
require_once 'classes/UfoToolsExt.php';
/**
 * ������������ ����� �������������� ������ ������ � ��.
 *
 * @author enikeishik
 *
 */
abstract class UfoDbModel
{
    use UfoTools;
    
    /**
     * ������ �� ������ ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ����� SQL �������, ���������� ������ ����� ������� � ������� ���� ������.
     * ������ ����� ����� ���������� ����������� �� �������-��������, ������� ��� ���� ������ ���������� ������ ��� ���������� �������������.
     * @var string
     */
    protected $fieldsSql = array();
    
    /**
     * �����������.
     * @param UfoDb $db    ������ �� ������ ��� ������ � ��
    */
    public function __construct(UfoDb &$db)
    {
        $this->db =& $db;
    }
    
    /**
     * ��������� ������ ����� ������� ����� ��� SQL �������.
     * @return string
     */
    public function getSectionFields()
    {
        if (!array_key_exists(__METHOD__, $this->fieldsSql)
                || '' == $this->fieldsSql[__METHOD__]) {
            //�������� ���� ������� �� ����� ������-���������
            $this->loadClass('UfoSectionStruct');
            $arr = get_class_vars('UfoSectionStruct');
            $sql = '';
            foreach ($arr as $fld => $val) {
                $sql .= ',`' . $fld . '`';
            }
            $this->fieldsSql[__METHOD__] = substr($sql, 1);
        }
        return $this->fieldsSql[__METHOD__];
    }
}
