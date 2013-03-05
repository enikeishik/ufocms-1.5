<?php
require_once 'classes/abstract/UfoInsertionItemModule.php';
/**
 * ����� ������� ������ �������.
 * 
 * @author enikeishik
 *
 */
class UfoModNewsIns extends UfoInsertionItemModule
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
        $settings->setValues($this->getSettings($insertion->SourceId));
        
        $sql = 'SELECT Id,DateCreate,Title,Author,Icon,Announce,Body,ViewedCnt' .
                ' FROM ' . $this->db->getTablePrefix() . 'news' .
                ' WHERE SectionId=' . $insertion->SourceId . 
                    ' AND IsHidden=0 AND DateCreate<=NOW()' . 
                    ' AND (IsTimered=0 OR DateCreate<=DATE_ADD(NOW(), INTERVAL - ' .
                    $settings->TimerOffset . 
                    ' MINUTE))' .
                ' ORDER BY DateCreate DESC, Id DESC' .
                ' LIMIT ' . $insertion->ItemsStart . ', ' . $insertion->ItemsCount;
        $items = $this->db->getRowsByQuery($sql);
        ob_start();
        if (is_array($items) && 0 < count($items)) {
            $settings->itemsCount = count($items);
            $this->template->drawItemBegin($insertion, $settings, $options);
            foreach ($items as $item) {
                $this->template->drawItemContent($insertion, $settings, $item, $options);
                $settings->itemNumber++;
            }
            $this->template->drawItemEnd($insertion, $settings, $options);
        } else {
            $this->template->drawItemEmpty($insertion, $settings, $options);
        }
        return ob_get_clean();
    }
    
    /**
     * ��������� ������������� ���������� ������ ��� �������.
     * @param int $sectionId    ������������� �������
     * @return array|false
     */
    protected function getSettings($sectionId)
    {
        $sql = 'SELECT IconAttributes,AnnounceLength,TimerOffset' . 
               ' FROM ' . $this->db->getTablePrefix() . 'news_sections' . 
               ' WHERE SectionId=' . $sectionId;
        return $this->db->getRowByQuery($sql);
    }
}