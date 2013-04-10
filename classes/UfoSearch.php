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
     * ���������� ����, � ������� ������� �������� ���������� ��������� ��������.
     * @var int
     */
    const QUERIES_LOG_DAYS = 30;
    
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
     * ����� ����� ����������� ������ � ��������.
     * @var int
     */
    const RESULTS_EXPIRATION = 600; 
    
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
        
        if (0 == strlen($query)) {
            $this->debug->trace('Search results count, query is empty', __CLASS__, __METHOD__, true);
            return 0;
        }
        $q = $this->safeSql($query, true);
        
        //�������� ����� ���������� �������
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search_results';
        if (0 < strlen($path) && isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Url LIKE '" . $this->safeSql($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else if (isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Query='" . $q . "')";
        } else if (0 < strlen($path)) {
            $sql .= " WHERE (Url LIKE '" . $this->safeSql($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else {
            $sql .= " WHERE Query='" . $q . "'";
        }
        if ($row = $this->db->getRowByQuery($sql)) {
            $this->count = $row['Cnt'];
        } else {
            $this->count = -1;
        }
        
        $this->debug->trace('Search results count complete', __CLASS__, __METHOD__, true);
        
        return $this->count;
    }
    
    /**
     * ���������� ���������� ������.
     * @param string $query           ��������� ������
     * @param int $page = 1           ������� �������� ������
     * @param int $pageLength = 10    ���������� ������� �� ��������
     * @param string $path = ''       ���� �������
     * @param int $moduleid = null    ������������� ������ �������
     * @return array<array>
     */
    public function getResults($query, $page = 1, $pageLength = 10, $path = '', $moduleid = null)
    {
        if (0 == strlen($query) || 1 > $page || 1 > $pageLength) {
            return false;
        }
        
        $this->debug->trace('Search results', __CLASS__, __METHOD__, false);
        
        $q = $this->safeSql($query, true);
        
        //���������� ���������� ��������� ��������
        if (self::QUERIES_LOG_ENABLED) {
            if (1 == $page && !isset($_GET['nolog'])) {
                $this->logSearchQuery($query);
            }
        }
        
        //��������� �������� � ������ ������� ���������,
        //��������� ����������� ��������� ������� PHP
        //���������� �������� � ������� ���������� ��������
        $q = mb_strtolower($q);
        
        //��������� ���������� �� ��� ����� ������ � ������� ����������� ������
        //���� ��� ��� ������� - ������ "�����" ����� �� �������
        $rowsFinded = 1;
        if (!$this->isResultsExists($q)) {
            $rowsFinded = $this->rawSearch($q);
        } else if ($this->isResultsExpired($q)) {
            $this->resultsClearOld($q);
            $rowsFinded = $this->rawSearch($q);
        }
        
        $search[] = array();
        //������ ����� �� ����������� ���� ������� ����������
        //��� ���� ������������� RawSearch � ������ > 0
        if (0 < $rowsFinded) {
            //������ ����� �� ������� ����������� ������
            $sql = 'SELECT Relevance, Flag, ModuleId, Url, Title, Descr, Content' . 
                   ' FROM ' . $this->db->getTablePrefix() . 'search_results';
            if (0 < strlen($path) && isset($moduleid)) {
                $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                        " AND (Url LIKE '" . $this->safeSql($path) . "%')" . 
                        " AND (Query='" . $q . "')";
            } else if (isset($moduleid)) {
                $sql .= " WHERE (ModuleId=" . $moduleid . ")" . 
                        " AND (Query='" . $q . "')";
            } else if (0 < strlen($path)) {
                $sql .= " WHERE (Url LIKE '" . $this->safeSql($path) . "%')" . 
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
        return preg_split('/ - |[\s,;:\.\?!=��]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
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
                    return (time() - $dtm) > self::RESULTS_EXPIRATION;
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
                                ' WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(DateCreate))>' . self::RESULTS_EXPIRATION);
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
    
    /**
     * ���������� ����� ������ ����������� ������.
     * @param array words
     * return array
     */
    protected function getLongWords(array $words)
    {
        $arr = array();
        foreach ($words as $word) {
            if (self::QUERIES_MINWORDLEN <= strlen($word)) {
                $arr[] = $word;
            }
        }
        return $arr;
    }
    
    /**
     * ������ ���������� ������� � ����� �� ������� � ������� ����������� ������ � ��������� ������� � ���������� ������� ������������� ��� ������� ���������� ��������.
     * @param string $query    ������������ ��������� ������
     * @return int             ���������� ��������� �����������
     * @todo �������� ��� ����������� ������ �����������, ����� ����� �� ��������� RawSearch ��� ����� ��������
     */
    protected function rawSearch($query)
    {
        $this->debug->trace('Raw search', __CLASS__, __METHOD__, false);
        
        //���������� ��������� ����������� � ����,
        //��� ���� ����������� �� ������ ��� RAWSEARCH_LIMIT
        $rowsFinded = 0;
        $rowsFindedEnough = false;
        
        //��������� ��������� ������ �� �����, ����� ������ �� ������� �����
        $words = array_unique($this->getQueryWords($query));
        $wordsCount = count($words);
        
        //*************************************
        //������� ���� ��������� ������ �������
        $rowsFinded += $this->searchWord($query, self::RELEVANCE_PHRASE, $query);
        //���� ����� �� ������ ��� ����, ������ ����
        $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
        
        //���� ����� � ������� ��������� � ��� �� ����� ���������� �����������
        //���� �� ���� ������ � �������
        if (1 < $wordsCount && !$rowsFindedEnough) {
            $words = $this->getLongWords($words);
            $wordsCount = count($words);
            if (0 == $wordsCount) {
                $this->debug->trace('Raw search complete, all words in query are too small', __CLASS__, __METHOD__, true);
                return $rowsFinded;
            }
            
            if (1 < $wordsCount) {
                //******************************************
                //���� ���� ��������� ���� ��� ����� �������
                //� ������������ �������, � �� ��� � ��������� �������
                $rowsFinded += $this->searchWords($words, self::RELEVANCE_ALLWORDS, $query);
                $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
            } else if ($words[0] != $query) {
                //******************************************************************************
                //���� �������� ������ ���� �����, ���� �� ���� � ������� �������� �������������
                //���� ������ ���� �������� ������ �������� ������ �����, ���������� ����
                $rowsFinded += $this->searchWord($words[0], self::RELEVANCE_ALLWORDS, $query);
                $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
            }
            
            if (!$rowsFindedEnough) {
                $rowsFinded += $this->rawSearchStemmed($query, $words);
                $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
            }
            
        }
        
        if (0 < $rowsFinded) {
            //********************************************************************
            //������� �����, ����� ���������, ��������� �� �������� ������� ������
            //� �� ��� ��� ������� �������� �������� ����� ������� ����� � ������ � ���������
            $ret = $this->resultsClearDoubles();
            if (false !== $ret) {
                $rowsFinded -= $ret;
            }
        }
        
        $this->debug->trace('Raw search complete', __CLASS__, __METHOD__, true);
        
        return $rowsFinded;
    }
    
    /**
     * ����� �� ������ �� ���������� ������� ��� ���������.
     * @param string $query    ������������ ��������� ������
     * @param array $words     ������ ���� �� ���������� �������
     * @return int             ���������� ��������� �����������
     */
    protected function rawSearchStemmed($query, array $words)
    {
        $this->debug->trace('Raw search stemmed', __CLASS__, __METHOD__, false);
        
        $rowsFinded = 0;
        
        //�������� ��������� � ��. ��������� ����� UfoSearchStemmer
        $this->loadClass('UfoSearchStemmer');
        $stemmer = new UfoSearchStemmer();
        $wordsStemmed = array();
        foreach ($words as $word) {
            $wordsStemmed[] = $stemmer->stem($word);
        }
        if (0 < count($wordsStemmed)) {
            //**********************************
            //���� �� ���� ������, ��� ���������
            $rowsFinded = $this->searchWords($wordsStemmed, self::RELEVANCE_STEMMEDWORDS, $query, true);
        }
        
        $this->debug->trace('Raw search stemmed complete', __CLASS__, __METHOD__, true);
        
        return $rowsFinded;
    }
    
    /**
     * ����� �����/�����.
     * ������������ SQL �������� ��� ������ � ��������� ����� ������� ���������� ������� � �������� ���� �������� �� ����������.
     * @param string $word            ��������� ����� ��� ����� �� �����, ������ ����� �� ������ �������� ������
     * @param int $relevanceFactor    ��������� ��� ������� �������������, ��� ����� ����� ������, ��� ��������� ���� ����� - ������
     * @param string $query           ������������ ��������� ������
     * @return int                    ���������� ��������� �����������
     */
    protected function searchWord($word, $relevanceFactor, $query)
    {
        $rowsFinded = 0;
        $sql = 'SELECT Flag, ModuleId, Url, Title, MetaDesc, Content' .
               ' FROM ' . $this->db->getTablePrefix() . 'search';
        
        //���� ������� ������, ��� ������� ����������� � ������ ���� (���������, ��������, ���������, �������)
        $sqlWhere = " WHERE Title LIKE '%" . $word . "%'" .
                    " AND MetaDesc LIKE '%" . $word . "%'" .
                    " AND MetaKeys LIKE '%" . $word . "%'" .
                    " AND Content LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_TDKC), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        //����� ���� ������, ��� ������� ����������� � ��������� � ��������
        $sqlWhere = " WHERE Title LIKE '%" . $word . "%'" .
                    " AND Content LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_TC), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        //����� ���� ������, ��� ������� ����������� � ��������
        $sqlWhere = " WHERE Title LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_T), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        //����� ���� ������, ��� ������� ����������� � ��������
        $sqlWhere = " WHERE Content LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_C), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        return $rowsFinded;
    }
    
    /**
     * ����� ���� ����.
     * ������������ SQL �������� ��� ������ � ��������� ����� ������� ���������� ������� � �������� ���� �������� �� ����������.
     * @param string $w                    ������ ���� �� ��������� �����, ������ ����� �� ������ �������� ������
     * @param int $relevanceFactor         ��������� ��� ������� �������������, ��� ����� ����� ������, ��� ��������� ���� ����� - ������
     * @param string $query                ������������ ��������� ������
     * @param $ignoreMinwordlen = false    ������������ ����������� ������ �����, ������������ ��� ���� ��� ���������
     * @return int                         ���������� ��������� �����������
     */
    protected function searchWords(array $words, $relevanceFactor, $query, $ignoreMinwordlen = false)
    {
        $this->debug->trace('Search words', __CLASS__, __METHOD__, false);
        
        $rowsFinded = 0;
        $wordsCount = count($words);
        
        $sql = 'SELECT Flag, ModuleId, Url, Title, MetaDesc, Content' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search';
        
        $sqlWhereTdkc = "";
        $sqlWhereTc = "";
        $sqlWhereT = "";
        $sqlWhereC = "";
        foreach ($words as $word) {
            if ($ignoreMinwordlen || self::QUERIES_MINWORDLEN <= strlen($word)) {
                //���� ������� ������, ��� ������� ����������� � ������ ���� (���������, ��������, ���������, �������)
                $sqlWhereTdkc .= " AND Title LIKE '%" . $word . "%'" . 
                                 " AND MetaDesc LIKE '%" . $word . "%'" . 
                                 " AND MetaKeys LIKE '%" . $word . "%'" . 
                                 " AND Content LIKE '%" . $word . "%'";
                //����� ���� ������, ��� ������� ����������� � ��������� � ��������
                $sqlWhereTc .= " AND Title LIKE '%" . $word . "%'" . 
                               " AND Content LIKE '%" . $word . "%'";
                //����� ���� ������, ��� ������� ����������� � ��������
                $sqlWhereT .= " AND Title LIKE '%" . $word . "%'";
                //����� ���� ������, ��� ������� ����������� � ��������
                $sqlWhereC .= " AND Content LIKE '%" . $word . "%'";
            }
        }
        if (0 < strlen($sqlWhereTdkc)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereTdkc, 4), 
                                         ($relevanceFactor * self::RELEVANCE_TDKC), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        if (0 < strlen($sqlWhereTc)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereTc, 4), 
                                         ($relevanceFactor * self::RELEVANCE_TC), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        if (0 < strlen($sqlWhereT)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereT, 4), 
                                         ($relevanceFactor * self::RELEVANCE_T), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        if (0 < strlen($sqlWhereC)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereC, 4), 
                                         ($relevanceFactor * self::RELEVANCE_C), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        
        $this->debug->trace('Search words complete', __CLASS__, __METHOD__, true);
        
        return $rowsFinded;
    }
    
    /**
     * ���������� SQL �������� � ������� ���������� ������� � ������ ���������� ����������� � ������� ����������� ������ ��� �� ����������� ������ �� ���� �������.
     * @param string $sql       SQL ������ � ������� ���������� �������
     * @param int $relevance    ������ �������������
     * @param string $query     ������������ ��������� ������
     * @return int|false        ���������� ��������� �����������
     */
    protected function searchWordExec($sql, $relevance, $query)
    {
        if (false !== $result = $this->db->query($sql)) {
            $rowsFinded = $result->num_rows;
            if (0 == $rowsFinded) {
                $result->free();
                return 0;
            } else if (self::RAWSEARCH_LIMIT < $rowsFinded) {
                $result->free();
                return false;
            } else if (0 > $rowsFinded) {
                $result->free();
                return false;
            }
            
            $sql = '';
            $cnt = 0;
            while ($row = $result->fetch_assoc()) {
                $sql .= ',(NOW(),' . 
                        $relevance . ',' . 
                        $row['Flag'] . ',' . 
                        $row['ModuleId'] . ',' . 
                        "'" . $query . "'," . 
                        "'" . $this->safeSql($row['Url']) . "'," . 
                        "'" . $this->safeSql($row['Title']) . "'," . 
                        "'" . $this->safeSql($row['MetaDesc']) . "'," . 
                        "'" . $this->safeSql($row['Content']) . "')";
                $cnt++;
                //������ ������� � ������� ����������� ������ 
                //�������� �� RESULTS_INSERT_LIMIT �������
                if (0 == ($cnt % self::RESULTS_INSERT_LIMIT)) {
                    $this->resultsAdd($sql);
                    $sql = '';
                }
            }
            $this->resultsAdd($sql);
            $result->free();
            return $rowsFinded;
        }
        return false;
    }
    
    /**
     * ������� ����������� ������ � ������� �����������.
     * @param string $results    ����� SQL ������� � �������
     * @return boolean
     */
    protected function resultsAdd($results)
    {
        return $this->db->query('INSERT INTO ' . $this->db->getTablePrefix() . 'search_results' .
                                ' (DateCreate,Relevance,Flag,ModuleId,Query,Url,Title,Descr,Content)' .
                                ' VALUES ' . substr($results, 1));
    }
    
    /**
     * ������ ��������� ��������.
     * @param string $query     ������������ ��������� ������
     * @todo test
     */
    protected function logSearchQuery($query)
    {
        $this->debug->trace('Log search query', __CLASS__, __METHOD__, false);
        
        $prefix = $this->db->getTablePrefix();
        
        //������� ���������� �������
        $this->db->query('DELETE FROM ' . $prefix . 'search_queries' . 
                         ' WHERE DATEDIFF(dtm, NOW())>' . self::QUERIES_LOG_DAYS);
        
        //��������� ����� ������ (� ������� ������� ��������)
        $this->db->query('INSERT INTO ' . $prefix . 'search_queries' . 
                         " (dtm, query) VALUES(NOW(), '" . $query . "')");
        
        //��������� ��� �� ��� ����� ������ (� ������� ���������� ��������)
        $exists = false;
        $result = $this->db->query('SELECT COUNT(*) AS Cnt' . 
                                   ' FROM ' . $prefix . 'search_queries_stat' . 
                                   " WHERE query='" . $query . "'");
        if (false !== $result) {
            if (0 < $result->num_rows) {
                if ($row = $result->fetch_assoc()) {
                    if (0 < $row['Cnt']) {
                        $exists = true;
                    }
                }
            }
        }
        $result->free();
        
        //��������� ��� ��������� ���������� ��������
        if ($exists) {
            //�������� ���������� ����� �������� ���� ��� ����
            $cnt = 0;
            $result = $this->db->query('SELECT COUNT(*) AS Cnt' . 
                                       ' FROM ' . $prefix . 'search_queries' . 
                                       " WHERE query='" . $query . "'");
            if (false !== $result) {
                if (0 < $result->num_rows) {
                    if ($row = $result->fetch_assoc()) {
                        $cnt = (int) $row['Cnt'];
                    }
                }
            }
            $result->free();
            
            $sql = 'UPDATE ' . $prefix . 'search_queries_stat' . 
                   ' SET dtm=NOW(), cnttmp=' . $cnt . ', cntttl=cntttl+1' . 
                   " WHERE query='" . $query . "'";
        } else {
            $sql = 'INSERT INTO ' . $prefix . 'search_queries_stat' . 
                   ' (dtm, query, cnttmp, cntttl)' . 
                   " VALUES(NOW(), '" . $query . "', 1, 1)";
        }
        $this->db->query($sql);
        
        $this->debug->trace('Log search query complete', __CLASS__, __METHOD__, true);
    }
}
