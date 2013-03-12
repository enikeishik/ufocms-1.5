<?php
/**
 * ����� ������������ ��������� � ���� ������.
 *
 * � ��������� ����� ������������ ����� ������� ������� ��� MySQLi
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
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $ufoDebug = null;
    
    public function __construct(UfoDbSettings $settings, UfoDebug &$debug = null)
    {
        $this->ufoDebug =& $debug;
        if (!$this->connected) {
            parent::__construct($settings->getHost(), 
                                $settings->getUser(), 
                                $settings->getPassword(), 
                                $settings->getName());
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
        $this->ufoDebug->logSql($query, $this->error, false);
        $ret = parent::query($query, $resultmode);
        $this->ufoDebug->logSql($query, $this->error, true);
        return $ret;
    }
    
    /**
     * ��������� ����������� �������� ������ ���� ������.
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }
    
    /**
     * ��������� ������ �� ���� ������ �� SQL �������.
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
     * ��������� ����� �� ���� ������ �� SQL �������.
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
