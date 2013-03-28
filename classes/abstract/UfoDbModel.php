<?php
require_once 'classes/UfoToolsExt.php';
/**
 * Абрстрактный класс представляющий модель данных в БД.
 *
 * @author enikeishik
 *
 */
abstract class UfoDbModel
{
    use UfoTools;
    
    /**
     * Ссылка на объект работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
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
     * Получение списка полей раздела сайта для SQL запроса.
     * @return string
     */
    public function getSectionFields()
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
        return $this->fieldsSql[__METHOD__];
    }
}
