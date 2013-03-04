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
    const C_MOD_CLASS_NAME = 'UfoModNews';
    const C_INS_CLASS_NAME = 'UfoModNewsIns';
    const C_INS_SETTINGS_CLASS_NAME = 'UfoModNewsInsSettings';
    
    /**
     * ������-��������� ��� �������� ��������� ������ ������� � �������������� ��������� �������.
     * @var UfoModNewsInsSettings
     */
    protected $settings = null;
    
    /**
     * ��������� ����������� �������� ����� �������.
     * @param UfoInsertionItemStruct $insertion    ������ �������� �������
     * @param string $path                         ���� �������-��������� �������
     * @param array $options = null                �������������� ������, ������������ ������ ������� �������
     * @return string
     * @todo �������� 'Settings' �� ���������
     */
    public function generateItem(UfoInsertionItemStruct $insertion, $path, array $options = null)
    {
        $struct = self::C_INS_SETTINGS_CLASS_NAME;
        $this->loadClass($struct,
                        $this->config->modulesDir .
                        $this->config->directorySeparator .
                        self::C_MOD_CLASS_NAME);
        $this->settings = new $struct();
        $this->settings->path = $path;
        $this->settings->setValues($this->getSettings($insertion->SourceId));
        
        $sql = 'SELECT Id,DateCreate,Title,Author,Icon,Announce,Body,ViewedCnt' .
                ' FROM ' . $this->db->getTablePrefix() . 'news' .
                ' WHERE SectionId=' . $insertion->SourceId . 
                    ' AND IsHidden=0 AND DateCreate<=NOW()' . 
                    ' AND (IsTimered=0 OR DateCreate<=DATE_ADD(NOW(), INTERVAL - ' .
                    $this->settings->TimerOffset . 
                    ' MINUTE))' .
                ' ORDER BY DateCreate DESC, Id DESC' .
                ' LIMIT ' . $insertion->ItemsStart . ', ' . $insertion->ItemsCount;
        $items = $this->db->getRowsByQuery($sql);
        ob_start();
        if (is_array($items) && 0 < count($items)) {
            $this->settings->itemsCount = count($items);
            $this->template->drawItemBegin($insertion, $this->settings, $options);
            foreach ($items as $item) {
                $this->template->drawItemContent($insertion, $this->settings, $item, $options);
                $this->settings->itemNumber++;
            }
            $this->template->drawItemEnd($insertion, $this->settings, $options);
        } else {
            $this->template->drawItemEmpty($insertion, $this->settings, $options);
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