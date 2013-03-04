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
    const C_MOD_CLASS_NAME = 'UfoModDocuments';
    const C_INS_CLASS_NAME = 'UfoModDocumentsIns';
    const C_INS_SETTINGS_CLASS_NAME = 'UfoModDocumentsInsSettings';
    
    /**
     * Объект-структура для хранения установок модуля раздела и дополнительных парметров вставки.
     * @var UfoModDocumentsInsSettings
     */
    protected $settings = null;
    
    /**
     * Генерация содержимого элемента блока вставки.
     * @param UfoInsertionItemStruct $insertion    данные элемента вставки
     * @param string $path                         путь раздела-источника вставки
     * @param array $options = null                дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     * @todo заменить 'Settings' на константу
     */
    public function generateItem(UfoInsertionItemStruct $insertion, $path, array $options = null)
    {
        $struct = self::C_INS_SETTINGS_CLASS_NAME;
        $this->loadClass($struct, $this->config->modulesDir .
                                  $this->config->directorySeparator .
                                  self::C_MOD_CLASS_NAME);
        $this->settings = new $struct();
        $this->settings->path = $path;
        
        $sql = 'SELECT body' . 
               ' FROM ' . $this->db->getTablePrefix() . 'documents' . 
               ' WHERE SectionId=' . $insertion->SourceId;
        ob_start();
        if ($row = $this->db->getRowByQuery($sql)) {
            //not used $this->template->drawItemBegin($insertion, $this->settings, $options);
            $this->template->drawItemContent($insertion, $this->settings, $row, $options);
            //not used $this->template->drawItemEnd($insertion, $this->settings, $options);
        } else {
            $this->template->drawItemEmpty($insertion, $this->settings, $options);
        }
        return ob_get_clean();
    }
}
