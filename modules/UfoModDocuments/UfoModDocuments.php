<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModDocuments extends UfoModule
{
    /**
     * �������������� ����� ����� �������� ��� �������� ������, 
     * � �� ������������ ������������� ������ (��� IDE).
     * @var UfoModDocumentsParams
     */
    protected $params = null;
    
    public function getContent()
    {
        $sql = 'SELECT Body' .
               ' FROM ' . $this->db->getTablePrefix() . 'documents' .
               ' WHERE SectionId=' . $this->sectionFields->id;
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['Body'];
        }
        return '';
    }
}
