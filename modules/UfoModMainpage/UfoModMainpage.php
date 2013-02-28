<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModMainpage extends UfoModule
{
    /**
     * Переопределена здесь чтобы получить тип текущего класса, 
     * а не абстрактного родительского класса (для IDE).
     * @var UfoModMainpageParams
     */
    protected $params = null;
    
    public function getContent()
    {
        $sql = 'SELECT body' .
               ' FROM ' . $this->db->getTablePrefix() . 'mainpage';
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['body'];
        }
        return '';
    }
}
