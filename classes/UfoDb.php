<?php
/**
 *  ласс абстрактного обращени€ к базе данных.
 *
 * ¬ насто€щее врем€ представл€ет собой простую обертку дл€ MySQLi
 */
class UfoDb extends mysqli
{
    private $connected = false;
    private $tablePrefix = '';
    
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
    
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }
    
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
