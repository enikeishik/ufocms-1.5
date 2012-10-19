<?php
/**
 *  ласс абстрактного обращени€ к базе данных.
 *
 * ¬ насто€щее врем€ представл€ет собой простую обертку дл€ MySQLi
 */
class UfoDb extends mysqli
{
    private static $instance = null;
    
    private function __construct($host, $username, $password, $database)
    {
        parent::__construct($host, $username, $password, $database);
    }
    
    public static function singleton($host, $username, $password, $database)
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className($host, $username, $password, $database);
        }
        return self::$instance;
    }
    
    public static function getInstance()
    {
        return self::$instance;
    }
    
    public static function getRowByQuery($sql)
    {
        $db = self::$instance;
        $result = $db->query($sql);
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
    
    public static function getRowsByQuery($sql)
    {
        $rows = array();
        $db = self::$instance;
        $result = $db->query($sql);
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
