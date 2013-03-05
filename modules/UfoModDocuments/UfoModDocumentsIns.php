<?php
require_once 'classes/abstract/UfoInsertionItemModule.php';
/**
 * ����� ������� ������ ���������.
 * 
 * @author enikeishik
 *
 */
class UfoModDocumentsIns extends UfoInsertionItemModule
{
    /**
     * ��������� ����������� �������� ����� �������.
     * �������� $settings ��� ���������� ����� ��� UfoMod*InsSettings � �������� ��� ���� ��������� ��������������� ���������.
     * @param UfoInsertionItemStruct $insertion     ������ �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ �������� ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, 
                                 UfoInsertionItemSettings $settings, 
                                 array $options = null)
    {
        $sql = 'SELECT body' . 
               ' FROM ' . $this->db->getTablePrefix() . 'documents' . 
               ' WHERE SectionId=' . $insertion->SourceId;
        ob_start();
        if ($row = $this->db->getRowByQuery($sql)) {
            //not used $this->template->drawItemBegin($insertion, $settings, $options);
            $this->template->drawItemContent($insertion, $settings, $row, $options);
            //not used $this->template->drawItemEnd($insertion, $settings, $options);
        } else {
            $this->template->drawItemEmpty($insertion, $settings, $options);
        }
        return ob_get_clean();
    }
}
