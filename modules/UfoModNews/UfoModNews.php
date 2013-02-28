<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModNews extends UfoModule
{
    /**
     * Идентификатор выбранного элемента.
     * @var int
     */
    protected $id = 0;
    
    /**
     * Объект-структура хранящий значения параметров, передаваемых в URL.
     * Переопределена здесь чтобы получить тип текущего класса, 
     * а не абстрактного родительского класса (для IDE).
     * @var UfoModNewsParams
     */
    protected $params = null;
    
    /**
     * Массив установленных параметров модуля для данного раздела.
     * @var array
     */
    protected $moduleSettings = null;
    
    /**
     * Переопределенный конструктор.
     * Вызывает конструктор родительского класса, 
     * затем получает установленные параметры модуля данного раздела. 
     * @param UfoContainer &$container
     */
    public function __construct(UfoContainer &$container)
    {
        parent::__construct($container);
        $this->moduleSettings = $this->getSettings();
    }
    
    /**
     * Получение установленных параметров модуля для данного раздела.
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
     * Формирование массива данных одного элемента.
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
     * Формирование массива массивов данных элементов.
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
     * Получение идентификатора текущего элемента, возвращает 0 если запрошен не элемент, а список.
     * @return int
     */
    public function getItemId()
    {
        return $this->params->id;
    }
}
