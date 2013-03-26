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
    
    /**
     * �����������.
     * @param UfoDbSettings $settings    ������-��������� ���������� ������� � ��
     * @param UfoDebug &$debug = null    ������ �� ������ �������
     * @throws Exception
     */
    public function __construct(UfoDbSettings $settings, UfoDebug &$debug = null)
    {
        $this->ufoDebug =& $debug;
        if (!$this->connected) {
            //��������� ����� ������, �.�. ����� (���� ��� try-catch) �������� Warning
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
