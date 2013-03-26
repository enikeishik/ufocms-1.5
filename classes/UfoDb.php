<?php
/**
 * Класс абстрактного обращения к базе данных.
 *
 * В настоящее время представляет собой простую обертку для MySQLi
 * 
 * @author enikeishik
 *
 */
class UfoDb extends mysqli
{
    /**
     * 
     * @var boolean
     */
    protected $connected = false;
    
    /**
     * 
     * @var string
     */
    protected $tablePrefix = '';
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $ufoDebug = null;
    
    /**
     * Конструктор.
     * @param UfoDbSettings $settings    объект-хранилище параметров доступа к БД
     * @param UfoDebug &$debug = null    ссылка на объект отладки
     * @throws Exception
     */
    public function __construct(UfoDbSettings $settings, UfoDebug &$debug = null)
    {
        $this->ufoDebug =& $debug;
        if (!$this->connected) {
            //подавляем вывод ошибок, т.к. иначе (даже при try-catch) выдается Warning
            @parent::__construct($settings->getHost(), 
                                 $settings->getUser(), 
                                 $settings->getPassword(), 
                                 $settings->getName());
            if (0 != $this->connect_errno) {
                throw new Exception($this->connect_error);
            }
            if ('' != $cs = $settings->getCharset()) {
                $this->query('SET NAMES ' . $cs);
            }
            $this->tablePrefix = $settings->getPrefix();
            $this->connected = true;
        }
    }
    
    public function close()
    {
        parent::close();
        $this->connected = false;
    }
    
    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        if (!is_null($this->ufoDebug)) {
            $this->ufoDebug->traceSql($query, $this->error, false);
            $ret = parent::query($query, $resultmode);
            $this->ufoDebug->traceSql($query, $this->error, true);
            return $ret;
        } else {
            return parent::query($query, $resultmode);
        }
    }
    
    /**
     * Получение глобального префикса таблиц базы данных.
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }
    
    /**
     * Получение строки из базы данных по SQL запросу.
     * @param string $sql
     * @return array|false
     */
    public function getRowByQuery($sql)
    {
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }
        if ($row = $result->fetch_assoc()) {
            $result->free();
            return $row;
        } else {
            $result->free();
            return false;
        }
    }
    
    /**
     * Получение строк из базы данных по SQL запросу.
     * @param string $sql
     * @return array|false
     */
    public function getRowsByQuery($sql)
    {
        $rows = array();
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
        return $rows;
    }
}
