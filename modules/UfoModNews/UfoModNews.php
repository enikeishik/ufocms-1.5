<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModNews extends UfoModule
{
    /**
     * ������������� ���������� ��������.
     * @var int
     */
    protected $id = 0;
    
    /**
     * ������-��������� �������� �������� ����������, ������������ � URL.
     * �������������� ����� ����� �������� ��� �������� ������, 
     * � �� ������������ ������������� ������ (��� IDE).
     * @var UfoModNewsParams
     */
    protected $params = null;
    
    /**
     * ������ ������������� ���������� ������ ��� ������� �������.
     * @var array
     */
    protected $moduleSettings = null;
    
    /**
     * ���������������� �����������.
     * �������� ����������� ������������� ������, 
     * ����� �������� ������������� ��������� ������ ������� �������. 
     * @param UfoContainer &$container
     */
    public function __construct(UfoContainer &$container)
    {
        parent::__construct($container);
        $this->moduleSettings = $this->getSettings();
    }
    
    /**
     * ��������� ������������� ���������� ������ ��� ������� �������.
     * @return array
     */
    protected function getSettings()
    {
        $sql = 'SELECT Id,BodyHead,BodyFoot,IconAttributes,PageLength,AnnounceLength,TimerOffset,IsArchive' . 
               ' FROM ' . $this->db->getTablePrefix() . 'news' . 
               ' WHERE SectionId=' . $this->section->getField('id');
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * ������������ ������� ������ ������ ��������.
     * @return array|false
     */
    public function getItem()
    {
        $sql = 'SELECT Id,DateCreate,Title,Author,Icon,Announce,Body,ViewedCnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'news' . 
               ' WHERE Id=' . $this->id . 
               ' AND IsHidden=0';
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * ������������ ������� �������� ������ ���������.
     * @return array:array
     */
    public function getItems()
    {
        $sql = 'SELECT COUNT(*) AS Cnt';
        $sql = 'SELECT Id,DateCreate,Title,Author,Icon,Announce,Body,ViewedCnt' . 
               ' WHERE SectionId=' . $this->section->getField('id') . 
               ' AND IsHidden=0 AND DateCreate<=NOW()' . 
               ' AND (IsTimered=0 OR DateCreate<=DATE_ADD(NOW(), INTERVAL - ' .
               $this->moduleSettings['TimerOffset'] . 
               ' MINUTE))' . 
               ' ORDER BY DateCreate DESC, Id DESC' . 
               ' LIMIT , ';
        return $this->db->getRowsByQuery($sql);
    }
    
    /**
     * ��������� �������������� �������� ��������, ���������� 0 ���� �������� �� �������, � ������.
     * @return int
     */
    public function getItemId()
    {
        return $this->params->id;
    }
}
