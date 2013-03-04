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
    const C_MOD_CLASS_NAME = 'UfoModDocuments';
    const C_INS_CLASS_NAME = 'UfoModDocumentsIns';
    const C_INS_SETTINGS_CLASS_NAME = 'UfoModDocumentsInsSettings';
    
    /**
     * ������-��������� ��� �������� ��������� ������ ������� � �������������� ��������� �������.
     * @var UfoModDocumentsInsSettings
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
