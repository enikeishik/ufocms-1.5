<?php
/**
 * ����� ������.
 * 
 * @author enikeishik
 *
 */
class UfoSearch
{
    use UfoTools;
    
    /**
     * ���������� ���������� ��������� ��������.
     * @var boolean
     */
    const QUERIES_LOG_ENABLED = true;
    
    /**
     * ������������ ���������� ���� � ��������� �������.
     * @var int
     */
    const QUERIES_MAXWORDS = 5;
    
    /**
     * ����������� ������ �����.
     * @var int
     */
    const QUERIES_MINWORDLEN = 3;
    
    /**
     * ����������� �� ���������� ���������� ����������� ������.
     * @var int
     */
    const RAWSEARCH_LIMIT = 1000;
    
    /**
     * ����������� �� ������� ����������� ������ (�� ���� ������) � ���.
     * @var int
     */
    const RESULTS_INSERT_LIMIT = 100;
    
    /**
     * ��� � ������ ���� �����.
     * @var int
     */
    const RELEVANCE_PHRASE = 1000;
    
    /**
     * ��� � ������ ���� ����.
     * @var int
     */
    const RELEVANCE_ALLWORDS = 100;
    
    /**
     * ��� � ������ ���� ��� ���������.
     * @var int
     */
    const RELEVANCE_STEMMEDWORDS = 10;
    
    /**
     * ��� � ������ ������� �����.
     * @var int
     */
    const RELEVANCE_WORD = 1;
    
    /**
     * ��� � ������, ���� ������� ������� � Title, MetaDescription, MetaKeywords � ���������� ��������.
     * @var int
     */
    const RELEVANCE_TDKC = 8;
    
    /**
     * ��� � ������, ���� ������� ������� � Title � ���������� ��������.
     * @var int
     */
    const RELEVANCE_TC = 6;
    
    /**
     * ��� � ������, ���� ������� ������� � Title.
     * @var int
     */
    const RELEVANCE_T = 4;
    
    /**
     * ��� � ������, ���� ������� ������� � ���������� ��������.
     * @var int
     */
    const RELEVANCE_C = 2;
    
    /**
     * ������ �� ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ������ �� ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ���������� ��������� �����������.
     * @var int
     */
    protected $count = -1;
    
    /**
     * �����������.
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * @throws Exception
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
    }
    
    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
    }
    
    /**
     * ���������� ���������� ��������� �����������.
     * ������� ������������ �� ������� ������� �����������, ������� �������������� ���������� ����������� ���������� �����.
     * @param string $query           ��������� ������
     * @param string $path = ''       ���� �������
     * @param int $moduleid = null    ������������� ������ �������
     * @return int
     */
    public function getResultsCount($query, $path = '', $moduleid = null)
    {
        if (-1 != $this->count) {
            return $this->count;
        }
        
        $this->debug->trace('Search results count', __CLASS__, __METHOD__, false);
        
        $ret = 0;
        if (0 == strlen($query)) {
            return $ret;
        }
        $q = $this->safeSql($query, true);
        
        //�������� ����� ���������� �������
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search_results';
        if (0 < strlen($path) && isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else if (isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Query='" . $q . "')";
        } else if (0 < strlen($path)) {
            $sql .= " WHERE (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else {
            $sql .= " WHERE Query='" . $q . "'";
        }
        if ($row = $this->db->getRowByQuery($sql)) {
            $ret = $row['Cnt'];
        }
        
        $this->debug->trace('Search results count complete', __CLASS__, __METHOD__, true);
        
        return $ret;
    }
    
    /**
     * ���������� ���������� ������.
     * @param string $query           ��������� ������
     * @param int $page = 1           ������� �������� ������
     * @param int $pageLength = 10    ���������� ������� �� ��������
     * @param string $path = ''       ���� �������
     * @param int $moduleid = null    ������������� ������ �������
     * @return array<array>
     * @todo �������� ������ "Windows-1251" �� ��������� ��� ���� ������� ������������
     */
    public function getResults($query, $page = 1, $pageLength = 10, $path = '', $moduleid = null)
    {
        if (0 == strlen($query) || 1 > $page || 1 > $pageLength) {
            return false;
        }
        
        $this->debug->trace('Search results', __CLASS__, __METHOD__, false);
        
        //���������� ���������� ��������� ��������
        if (self::QUERIES_LOG_ENABLED) {
            if (1 == $page && !isset($_GET['nolog'])) {
                $this->logSearchQuery($query);
            }
        }
        
        $q = $this->safeSql($query, true);
        //��������� �������� � ������ ������� ���������,
        //��������� ����������� ��������� ������� PHP
        //���������� �������� � ������� ���������� ��������
        $q = mb_strtolower($q, "Windows-1251");
        
        //��������� ���������� �� ��� ����� ������ � ������� ����������� ������
        //���� ��� ��� ������� - ������ "�����" ����� �� �������
        $rowsFinded = 1;
        if (!$this->isResultsExists($q)) {
            $rowsFinded = $this->rawSearch($q);
        } else if ($this->isResultsExpired($q)) {
            $this->resultsClearOld($q);
            $rowsFinded = $this->rawSearch($q);
        }
        
        /* DEBUG api_DebugInfo('Search query start'); */
        $search[] = array();
        //������ ����� �� ����������� ���� ������� ����������
        //��� ���� ������������� RawSearch � ������ > 0
        if (0 < $rowsFinded) {
            //������ ����� �� ������� ����������� ������
            $sql = 'SELECT Relevance, Flag, ModuleId, Url, Title, Descr, Content' . 
                   ' FROM ' . $this->db->getTablePrefix() . 'search_results';
            if (0 < strlen($path) && isset($moduleid)) {
                $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                        " AND (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                        " AND (Query='" . $q . "')";
            } else if (isset($moduleid)) {
                $sql .= " WHERE (ModuleId=" . $moduleid . ")" . 
                        " AND (Query='" . $q . "')";
            } else if (0 < strlen($path)) {
                $sql .= " WHERE (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                        " AND (Query='" . $q . "')";
            } else {
                $sql .= " WHERE Query='" . $q . "'";
            }
            $sql .= ' ORDER BY Flag DESC, Relevance DESC' . 
                    ' LIMIT ' . (($page - 1) * $pageLength) . ', ' . $pageLength;
            
            //��� ���������� �������������� �� ���������� ������� getRowsByQuery
            $result = $this->db->query($sql);
            if (false === $result) {
                return $search;
            }
            if (0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $search[] = $row;
                }
            }
            $result->free();
        }
        
        $this->debug->trace('Search results complete', __CLASS__, __METHOD__, true);
        
        return $search;
    }
    
    /**
     * ���������� ������ ���� �� ���������� �������.
     * @param string $query    ��������� ������
     * @return array<string>
     */
    protected function getQueryWords($query)
    {
        //return explode(' ', $query);
        return preg_split('/[\s,;:\.\?!=��]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * �������� ������� ������������ ���������� ������.
     * @param string $query    ��������� ������
     * @return boolean
     */
    protected function isResultsExists($query)
    {
        $this->debug->trace('Search results exists', __CLASS__, __METHOD__, false);
        
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search_results' . 
               " WHERE Query='" . $query . "'";
        $result = $this->db->query($sql);
        if (false !== $result) {
            if (0 < $result->num_rows) {
                if ($row = $result->fetch_assoc()) {
                    $this->debug->trace('Search results exists complete', __CLASS__, __METHOD__, true);
                    return 0 != $row['Cnt'];
                }
            }
            $result->free();
        }
        
        $this->debug->trace('Search results exists complete', __CLASS__, __METHOD__, true);
        
        return false;
    }
    
    /**
     * �������� ������������ ������������ ���������� ������.
     * @param string $query    ��������� ������
     * @return boolean
     */
    protected function isResultsExpired($query)
    {
        $this->debug->trace('Search results expired', __CLASS__, __METHOD__, false);
        
        $sql = 'SELECT UNIX_TIMESTAMP(DateCreate) AS dtm' .
               ' FROM ' . $this->db->getTablePrefix() . 'search_results' .
               " WHERE Query='" . $query . "'" .
               ' LIMIT 1';
        $result = $this->db->query($sql);
        if (false !== $result) {
            if (0 < $result->num_rows) {
                if ($row = $result->fetch_assoc()) {
                    $dtm = $row['dtm'];
                    $this->debug->trace('Search results expired complete', __CLASS__, __METHOD__, true);
                    return (time() - $dtm) > C_EXE_SEARCH_RESULTS_EXPIRATION;
                }
            }
            $result->free();
        }
        
        $this->debug->trace('Search results expired complete', __CLASS__, __METHOD__, true);
        
        return true;
    }
    
    /**
     * �������� ����������� ������ ������������� �������.
     * @param string $query    ��������� ������
     * @return boolean
     */
    protected function resultsDelete($query)
    {
        return $this->db->query('DELETE FROM ' . $this->db->getTablePrefix() . 'search_results' . 
                                " WHERE Query='" . $query . "'");
    }
    
    /**
     * �������� ���������� ����������� ������.
     * @param string $query    ��������� ������
     * @return boolean
     */
    protected function resultsClearOld($query)
    {
        //������� ���������� ���������� ���� ��������,
        //����� ����� �� ������� ���� ��� ��� ����� �������������
        //�� � ������� ��� ���� ���������� "�����"
        //��� ��� ����� � ������ ������� ���������
        //� ���� ��� ���� �� ����� ��������� ���������
        return $this->db->query('DELETE FROM ' . $this->db->getTablePrefix() . 'search_results' . 
                                ' WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(DateCreate))>' . C_EXE_SEARCH_RESULTS_EXPIRATION);
    }
    
    /**
     * �������� ������ � ����������� ������.
     * @return int
     */
    protected function resultsClearDoubles()
    {
        $sql = 'DELETE t2 FROM ' . $this->db->getTablePrefix() . 'search_results AS t1, ' . 
                                   $this->db->getTablePrefix() . 'search_results AS t2' . 
               ' WHERE t1.Query=t2.Query AND t1.Url=t2.Url AND t1.Relevance>t2.Relevance';
        $this->db->query($sql);
        $this->debug->trace('Clear doubles, affected rows: ' . $this->db->affected_rows, __CLASS__, __METHOD__, false);
        return $this->db->affected_rows;
    }
}
