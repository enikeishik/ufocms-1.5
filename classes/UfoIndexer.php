<?php
/**
 * ����� ���������� ����������� �������.
 * 
 * @author enikeishik
 *
 */
class UfoIndexer
{
    use UfoTools;
    
    /**
     * ����� � ���� ��������, ������������ ������ �������������� �����������.
     * @var string
     */
    const MARK_INDEX_START = '<!--content-->';
    
    /**
     * ����� � ���� ��������, ������������ ����� �������������� �����������.
     * @var string
     */
    const MARK_INDEX_STOP = '<!--/content-->';
    
    /**
     * ������ �� ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    protected $config = null;
    
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
        $this->config =& $this->container->getConfig();
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
    }
    
    /**
     * �������������� ��������.
     * @param string $url                  URL ������������� ��������
     * @param string $content = null       ������������� �������, ���� �����������, ������� �� ������ ������
     * @param boolean $insearch = false    ����, ���������� ������ �� ��������������� ������� ������ ��������, ���� ��� � ������ �������, �� ������ ���������
     * @param int $flsearch = 0            �������������� �����, ����� �������������� ��� ������, ��� ��������� ��������� ������� ����� (��� ������ ������������ �������� ���������� �� ����� ����), ��� ��� ������ ������ �� ���������� ������
     * @param int $moduleid = 0            ������������� ������ ������������� �� ������������� ��������, ����� �������������� ��� ������ ������ �� ������������ �������
     * @return boolean
     */
    public function index($url, $content = null, $insearch = false, $flsearch = 0, $moduleid = 0)
    {
        if ('/' != substr($url, strlen($url) - 1, strlen($url))) {
            $url .= '/';
        }
        
        if (!$insearch) {
            //��������� ���� �� ������
            $cnt = 0;
            $sql = 'SELECT COUNT(*) AS Cnt FROM ' . $this->db->getTablePrefix() . 'search' .
                   " WHERE Url='" . $this->safeSql($url) . "'";
            $result = $this->db->query($sql);
            if ($row = $result->fetch_assoc()) {
                $cnt = $row['Cnt'];
            }
            $result->free();
            if ($cnt > 0) {
                $sql = 'DELETE FROM ' . $this->db->getTablePrefix() . 'search' .
                       " WHERE Url='" . $this->safeSql($url) . "'";
                $this->db->query($sql);
            }
            return false;
        }
        
        if (!isset($content)) {
            $content = ob_get_contents();
        }
        $contentRaw = $content;
        
        //���� ���� ����� ����������� �������������� ��������, �� �������� �� ���� ������
        if (false !== stripos($content, self::MARK_INDEX_START)) {
            $content = substr($content, 
                              stripos($content, 
                                      self::MARK_INDEX_START) + strlen(self::MARK_INDEX_START));
            if (false !== stripos($content, self::MARK_INDEX_STOP)) {
                $content = substr($content, 
                                  0, 
                                  stripos($content, 
                                          self::MARK_INDEX_STOP));
            }
        }
        $title = $this->getTitle($contentRaw);
        $descr = $this->getMeta($contentRaw, 'description');
        $keys  = $this->getMeta($contentRaw, 'keywords');
        $index = trim(preg_replace('/\s{2,}|\r|\n|\t/',
                                   ' ',
                                   preg_replace('/&[^;]*;/', 
                                                ' ',
                                                preg_replace('/<[^>]*>/', 
                                                             ' ',
                                                             preg_replace('/<style[^>]*?>.*?<\/style>/si', 
                                                                          '',
                                                                          preg_replace('/<script[^>]*?>.*?<\/script>/si', 
                                                                                       '', 
                                                                                       $content))))));
        $hash  = md5($title . $descr . $keys . $index);
        
        if ($this->isIndexExists($url)) {
            if (!$this->isIndexChanged($url, $hash)) {
                return false;
            }
            $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search' .
                   " SET Flag=" . $flsearch . ", " .
                   " ModuleId=" . $moduleid . ", " .
                   " Title='" . $this->safeSql($title) . "', " .
                   " MetaDesc='" . $this->safeSql($descr) . "', " .
                   " MetaKeys='" . $this->safeSql($keys) . "', " .
                   " Content='" . $this->safeSql($index) . "', " .
                   " Hash='" . $hash . "', " .
                   " DateIndex=NOW() " .
                   " WHERE Url='" . $this->safeSql($url) . "'";
        } else {
            $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' .
                   ' (Flag,ModuleId,Url,Title,MetaDesc,MetaKeys,Content,Hash,DateIndex)' .
                   " VALUES(" . $flsearch . "," . $moduleid . 
                   ",'" . $this->safeSql($url) .
                   "','" . $this->safeSql($title) .
                   "','" . $this->safeSql($descr) .
                   "','" . $this->safeSql($keys) .
                   "','" . $this->safeSql($index) .
                   "','" . $hash . "',NOW())";
        }
        return $this->db->query($sql);
    }
    
    /**
     * �������� ������������� ������� ��� URL.
     * @param string $url    URL �������� ��� ������� ����������� ������� �������
     * @return boolean
     */
    protected function isIndexExists($url)
    {
        $sql = 'SELECT COUNT(*) AS Cnt' .
               ' FROM ' . $this->db->getTablePrefix() . 'search' .
               " WHERE Url='" . $this->safeSql($url) . "'";
        $result = $this->db->query($sql);
        if (false === $result) {
            return false;
        }
        if (0 == $result->num_rows) {
            return false;
        }
        if (!$row = $result->fetch_assoc()) {
            return false;
        }
        $result->free();
        return 0 != (int) $row['Cnt'];
    }
    
    /**
     * ����������� ��������� �� ������ URL.
     * @param string $url      URL ��������
     * @param $string $hash    ��� ����������� �������
     * @return boolean
     */
    protected function isIndexChanged($url, $hash)
    {
        $sql = 'SELECT COUNT(*) AS Cnt' .
               ' FROM ' . $this->db->getTablePrefix() . 'search' .
               " WHERE Url='" . $this->safeSql($url) . "'" .
               " AND Hash!='" . $hash . "'";
        $result = $this->db->query($sql);
        if (false === $result) {
            return false;
        }
        if (0 == $result->num_rows) {
            return false;
        }
        if (!$row = $result->fetch_assoc()) {
            return false;
        }
        $result->free();
        return 0 != (int) $row['Cnt'];
    }
    
    /**
     * ��������� ����������� HTML ���� TITLE.
     * @param string $content    ���������� (HTML ���) ��������
     * @return string 
     */
    protected function getTitle($content)
    {
        $start = stripos($content, '<title');
        if (false === $start) {
            return '';
        }
    
        $startTitle = strpos($content, '>', $start + 6);
        if (false === $startTitle) {
            return '';
        }
    
        $stop = stripos($content, '</title>', $startTitle + 1);
        if (false === $stop) {
            return '';
        }
    
        $len = $stop - $startTitle - 1;
        if ($this->config->dbVarcharLengthLimit < $len) {
            $len = $this->config->dbVarcharLengthLimit;
        }
        return substr($content, $startTitle + 1, $len);
    }
    
    /**
     * ��������� ����������� ���� ����.
     * @param string $content     ���������� (HTML ���) ��������
     * @param string $metaname    ��� ��������
     * @return string 
     */
    protected function getMeta($content, $metaname)
    {
        $offset = 0;
        $length = strlen($content);
        do {
            $start = stripos($content, '<meta', $offset);
            if (false === $start) {
                return '';
            }
    
            $stop = strpos($content, '>', $start + 5);
            if (false === $stop) {
                return '';
            }
    
            $meta = substr($content, $start + 5, $stop - $start - 5);
            
            // ��������� ��������, ��������� ��� ����� ����� '<meta'
            // ��� ������ ���� ������, ��� ��� stripos > 0 ���� �����
            if (stripos($meta, 'name=' . $metaname) 
            || stripos($meta, 'name="' . $metaname)
            || stripos($meta, "name='" . $metaname)) {
                $start = stripos($meta, ' content=');
                if (false !== $start) {
                    $quot = substr($meta, $start + 9, 1);
                    if ('"' != $quot && "'" != $quot) {
                        //���� �������� �������� �� ��������� � �������, ���������� ������ ������
                        return '';
                    }
                    $stop = strpos($meta, $quot, $start + 10);
                    if (false !== $stop) {
                        $len = $stop - $start - 10;
                        if ($this->config->dbVarcharLengthLimit < $len) {
                            $len = $this->config->dbVarcharLengthLimit;
                        }
                        return substr($meta, $start + 10, $len);
                    } else {
                        $len = $this->config->dbVarcharLengthLimit;
                        return substr($meta, $start + 10, $len);
                    }
                }
            }
            $offset = $stop;
        } while ($offset < $length);
    
        return '';
    }
}
