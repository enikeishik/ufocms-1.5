<?php
require_once 'classes/abstract/UfoIneractiveModule.php';
/**
 * Класс модуля Гостевой книги.
 * @author enikeishik
 */
class UfoModGbook extends UfoIneractiveModule
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
     * @var UfoModGbookParams
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
        if (1 == $this->params->action) {
            $this->addItem();
        }
    }
    
    /**
     * Получение установленных параметров модуля для данного раздела.
     * @return array|false
     */
    protected function getSettings()
    {
        $sql = 'SELECT Id,BodyHead,BodyFoot,PageLength,Orderby,' . 
               'IsModerated,IsReferer,IsCaptcha,MessageMaxLen,' . 
               'AlertEmail,AlertEmailSubj,AlertEmailBody,' . 
               'PostMessage,PostMessageErr,PostMessageBad' . 
               ' FROM ' . $this->db->getTablePrefix() . 'gbook_sections' . 
               ' WHERE SectionId=' . $this->section->getField('id');
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * Формирование массива данных одного элемента.
     * @return array|false
     */
    public function getItem()
    {
        $sql = 'SELECT DateCreate,DateAnswer,UIP,USign,UEmail,UUrl,UMessage,AIP,ASign,AEmail,AUrl,AMessage' . 
               ' FROM ' . $this->db->getTablePrefix() . 'gbook' . 
               ' WHERE Id=' . $this->params->id . 
               ' AND IsHidden=0';
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * Формирование массива массивов данных элементов.
     * @return array|false
     */
    public function getItems()
    {
        $prefix = $this->db->getTablePrefix();
        $sql_where = ' WHERE SectionId=' . $this->section->getField('id') . 
                     ' AND IsHidden=0 AND DateCreate<=NOW()';
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $prefix . 'gbook' . 
               $sql_where;
        if (!$row = $this->db->getRowByQuery($sql)) {
            return false;
        }
        if (0 == $rowsCount = $row['Cnt']) {
            return false;
        }
        
        $sql = 'SELECT Id,DateAnswer,UIP,USign,UEmail,UUrl,UMessage,AIP,ASign,AEmail,AUrl,AMessage' . 
               ' FROM ' . $prefix . 'gbook' . 
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
     * Получение идентификатора текущего элемента, возвращает 0 если запрошен не элемент, а список.
     * @return int
     */
    public function getItemId()
    {
        return $this->params->id;
    }
    
    public function addItem()
    {
        
    }
    
    public function updateItem()
    {
        throw new Exception('Unsupported method');
    }
    
    public function deleteItem()
    {
        throw new Exception('Unsupported method');
    }
}
