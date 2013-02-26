<?php
require_once 'UfoTools.php';
/**
 * Класс представляющий модель данных в БД.
 * Предоставляет методы для выполнения стандартных для системы запросов к БД, абстрагируя хранилище данных.
 * 
 * @author enikeishik
 *
 */
class UfoDbModel
{
    use UfoTools;
    
    /**
     * Ссылка на объект работы с базой данных.
     * @var UfoDb
     */
    private $db = null;
    
    /**
     * Конструктор.
     * @param UfoDb $db    ссылка на объект для работы с БД
     */
    public function __construct(UfoDb &$db)
    {
        $this->db =& $db;
    }
    
    /**
     * Получение данных раздела сайта.
     * @param mixed $section    идентификатор или путь раздела сайта
     * @throws Exception
     * @return array|false
     */
    public function getSection($section)
    {
        $sql = 'SELECT ' . $this->fieldsSql .
               ' FROM ' . $this->db->getTablePrefix() . 'sections' .
               ' WHERE ';
        if (is_int($section)) {
            $sql .= 'id=' . $section;
        } else if (is_string($section) && '/' == $section) {
            $sql .= 'id=-1';
        } else if (is_string($section) && $this->isPath($section)) {
            $sql .= "path='" . $section . "'";
        } else {
            throw new Exception('Incorrect $section: ' . var_export($section, true));
        }
        return $this->db->getRowByQuery($sql);
    }
}