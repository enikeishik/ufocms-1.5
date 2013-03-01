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
     * Часть SQL запроса, содержащая список полей раздела в таблице базы данных.
     * Списки полей могут получаться динамически из классов-структур, поэтому это поле хранит полученные списки для повторного использования.
     * @var string
     */
    protected $fieldsSql = array();
    
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
    public function getSection($section, $isParentId = false)
    {
        if (!array_key_exists(__METHOD__, $this->fieldsSql) 
        || '' == $this->fieldsSql[__METHOD__]) {
            //получаем поля таблицы из полей класса-структуры
            $this->loadClass('UfoSectionStruct');
            $arr = get_class_vars('UfoSectionStruct');
            $sql = '';
            foreach ($arr as $fld => $val) {
                $sql .= ',`' . $fld . '`';
            }
            $this->fieldsSql[__METHOD__] = substr($sql, 1);
        }
        $sql = 'SELECT ' . $this->fieldsSql[__METHOD__] . 
               ' FROM ' . $this->db->getTablePrefix() . 'sections' . 
               ' WHERE ';
        if (is_int($section)) {
            $sql .= ($isParentId ? 'parentid' : 'id') . '=' . $section;
        } else if (is_string($section) && '/' == $section) {
            $sql .= 'id=-1';
        } else if (is_string($section) && $this->isPath($section)) {
            $sql .= "path='" . $section . "'";
        } else {
            throw new Exception('Incorrect $section: ' . var_export($section, true));
        }
        if (!$isParentId) {
            return $this->db->getRowByQuery($sql);
        } else {
            return $this->db->getRowsByQuery($sql);
        }
    }
    
    /**
     * Получение имени модуля по его идентификатору.
     * @param int $moduleId    идентификатор модуля
     * @return string|false;
     */
    public function getModuleName($moduleId)
    {
        $sql = 'SELECT mfile' . 
               ' FROM ' . $this->db->getTablePrefix() . 'modules' . 
               ' WHERE muid=' . $moduleId;
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['mfile'];
        }
        return false;
    }
    
    /**
     * Получение данных вставки.
     * @param int $targetId      идентификатор раздела в котором выводится вставка
     * @param int $placeId       идентификатор места в котором выводится вставка
     * @param int $offset = 0    выбирать элементы начиная с $offset
     * @param int $limit = 0     выбрать всего $limit элементов (если $limit > 0)
     * @return array(string $mfileins, string $path, UfoInsertionItemStruct)|false
     */
    public function getInsertionItems($targetId, $placeId, $offset = 0, $limit = 0)
    {
        $prefix = $this->db->getTablePrefix();
        $sql = '';
        $arr = get_class_vars('UfoInsertionItemStruct');
        foreach ($arr as $fld => $val) {
            $sql .= '`i.' . $fld . '`,';
        }
        unset($arr);
        $sql = 'SELECT ' . $sql . 's.path,m.mfileins' . 
               ' FROM ' . $prefix . 'insertions AS i' . 
               ' INNER JOIN ' . $prefix . 'sections AS s ON s.id=i.SourceId' . 
               ' INNER JOIN ' . $prefix . 'modules AS m ON m.muid=s.moduleid' . 
               ' WHERE (i.TargetId=' . $targetId . ' OR i.TargetId=0)' . 
               ' AND i.PlaceId=' . $placeId . 
               ' AND s.isenabled!=0 AND m.isenabled!=0' . 
               ' ORDER BY i.OrderId';
        if (0 !=$offset && 0 != $limit) {
            $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        } else if (0 != $limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[] = array(array_pop($row), array_pop($row), new UfoInsertionItemStruct($row, false));
        }
        $result->free();
        return $arr;
    }
    
    /**
     * Проверка существования заданного пути в БД.
     * @param string $path    проверяемый путь (данные не проверяются на корректность!)
     * @return boolean
     */
    public function isPathExists($path)
    {
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'sections' .
               " WHERE path='" . $path . "'";
        $row = $this->db->getRowByQuery($sql);
        return 0 < $row['Cnt'];
    }
    
    /**
     * Получение максимально полного пути из переданного набора путей.
     * @param array $paths    проверяемый набор путей (данные не проверяются на корректность!)
     * @return string|false
     */
    public function getMaxExistingPath(array $paths)
    {
        $sql = 'SELECT path FROM ' . $this->db->getTablePrefix() . 'sections' . 
               " WHERE path IN('" . implode("','", $paths) . "')" . 
               ' ORDER BY path DESC' . 
               ' LIMIT 1';
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['path'];
        }
        return false;
    }
    
    /**
     * Получение набора параметров сайта.
     * return array|string
     */
    public function getSiteParams()
    {
        $sql = 'SELECT PType,PName,PValue,PDefault' .
               ' FROM ' . $this->db->getTablePrefix() . 'siteparams';
        return $this->db->getRowsByQuery($sql);
    }
}