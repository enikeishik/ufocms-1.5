<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModDocuments extends UfoModule
{
    /**
     * Переопределена здесь чтобы получить тип текущего класса, 
     * а не абстрактного родительского класса (для IDE).
     * @var UfoModDocumentsParams
     */
    protected $params = null;
    
    public function getTitle()
    {
        return '';
    }
    
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
