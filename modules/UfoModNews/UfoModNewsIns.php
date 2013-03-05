<?php
require_once 'classes/abstract/UfoInsertionItemModule.php';
/**
 *  ласс вставки модул€ новости.
 * 
 * @author enikeishik
 *
 */
class UfoModNewsIns extends UfoInsertionItemModule
{
    /**
     * √енераци€ содержимого элемента блока вставки.
     * ѕараметр $settings при выполнении имеет тип UfoMod*InsSettings и содержит все пол€ структуры соответствующей структуры.
     * @param UfoInsertionItemStruct $insertion     данные элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные элемента вставки (путь раздела-источника, установки модул€ и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
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
     * ѕолучение установленных параметров модул€ дл€ раздела.
     * @param int $sectionId    идентификатор раздела
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