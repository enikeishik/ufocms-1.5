<?php
require_once 'classes/abstract/UfoInsertionItemModule.php';
/**
 * Класс вставки модуля документы.
 * 
 * @author enikeishik
 *
 */
class UfoModDocumentsIns extends UfoInsertionItemModule
{
    /**
     * Генерация содержимого элемента блока вставки.
     * Параметр $settings при выполнении имеет тип UfoMod*InsSettings и содержит все поля структуры соответствующей структуры.
     * @param UfoInsertionItemStruct $insertion     данные элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные элемента вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
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
