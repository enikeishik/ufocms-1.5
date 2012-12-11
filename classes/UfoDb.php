<?php
/**
 *  ласс абстрактного обращени€ к базе данных.
 *
 * ¬ насто€щее врем€ представл€ет собой простую обертку дл€ MySQLi
 */
class UfoDb extends mysqli
{
    protected $connected = false;
    protected $tablePrefix = '';
    
    public function __construct(UfoDbSettings $settings)
    {
        if (!$this->connected) {
            parent::__construct($settings->getHost(), 
                                $settings->getUser(), 
                                $settings->getPassword(), 
                                $settings->getName());
            $this->tablePrefix = $settings->getPrefix();
            $this->connected = true;
        }
    }
    
    /**
     * ѕолучение глобального префикса таблиц базы данных.
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }
    
    /**
     * ѕолучение строки из базы данных по SQL запросу.
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
     * ѕолучение строк из базы данных по SQL запросу.
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
