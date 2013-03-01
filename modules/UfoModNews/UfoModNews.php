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
               ' FROM ' . $this->db->getTablePrefix() . 'news_sections' . 
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
               ' WHERE Id=' . $this->params->id . 
               ' AND IsHidden=0';
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * ������������ ������� �������� ������ ���������.
     * @return array|false
     */
    public function getItems()
    {
        $prefix = $this->db->getTablePrefix();
        $sql_where = ' WHERE SectionId=' . $this->section->getField('id') . 
                     ' AND IsHidden=0 AND DateCreate<=NOW()' . 
                     ' AND (IsTimered=0 OR DateCreate<=DATE_ADD(NOW(), INTERVAL - ' .
                     $this->moduleSettings['TimerOffset'] . 
                     ' MINUTE))';
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $prefix . 'news' . 
               $sql_where;
        if (!$row = $this->db->getRowByQuery($sql)) {
            return false;
        }
        if (0 == $rowsCount = $row['Cnt']) {
            return false;
        }
        
        $sql = 'SELECT Id,DateCreate,Title,Author,Icon,Announce,Body,ViewedCnt' . 
               ' FROM ' . $prefix . 'news' . 
               $sql_where . 
               ' ORDER BY DateCreate DESC, Id DESC' . 
               ' LIMIT ' . ($this->params->page - 1) * $this->moduleSettings['PageLength'] . 
               ', ' . $this->moduleSettings['PageLength'];
        if(!$rows = $this->db->getRowsByQuery($sql)) {
            return false;
        }
        //for backward compatibility
        $this->moduleSettings['TotalRecords'] = $rowsCount;
        return $rows;
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
