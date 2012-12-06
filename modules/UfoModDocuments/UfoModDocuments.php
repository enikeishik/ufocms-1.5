<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModDocuments extends UfoModule
{
    public function getTitle()
    {
        
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
    
    public function getPage(&$debug = null)
    {
        ob_start();
        $this->loadLayout($this->template);
        return ob_get_clean();
    }
}
