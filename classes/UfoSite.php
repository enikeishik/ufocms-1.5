<?php
require_once 'exceptions/UfoExceptionPathBad.php';
require_once 'exceptions/UfoExceptionPathComplex.php';
require_once 'exceptions/UfoExceptionPathEmpty.php';
require_once 'exceptions/UfoExceptionPathFilenotexists.php';
require_once 'exceptions/UfoExceptionPathNotexists.php';
require_once 'exceptions/UfoExceptionPathUnclosed.php';

class UfoSite
{
    use UfoTools;
    
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ������ �� ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    protected $siteParams = array();
    
    protected $path = '';
    protected $pathRaw = '';
    protected $pathParams = array();
    
    /**
     * �����������.
     * @param string       $pathRaw       �������������� ���� �������� �������
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    public function __construct($pathRaw, UfoContainer &$container)
    {
        $this->config = $container->getConfig();
        $this->db = $container->getDb();
        
        $sql = 'SELECT PType,PName,PValue,PDefault' . 
               ' FROM ' . $this->db->getTablePrefix() . 'siteparams';
        if ($rows = $this->db->getRowsByQuery($sql)) {
            foreach ($rows as $row) {
                switch($row['PType']) {
                    default:
                        $this->siteParams[$row['PName']] = $row['PValue'];
                }
            }
        }
        
        $this->pathRaw = $pathRaw;
        $this->parsePath();
        if ($this->path != $this->pathRaw) {
            $this->pathParams = explode('/', 
                                        substr($this->pathRaw, 
                                               strlen($this->path), 
                                               -1));
        }
    }
    
    /**
     * @deprecated
     */
    public function getSiteParam($name, $default = '')
    {
        return (array_key_exists($name, $this->siteParams) ? $this->siteParams[$name] : $default);
    }
    
    /**
     * ���������� ������������� ������ ���������� �����.
     * @return array
     */
    public function getSiteParams()
    {
        return $this->siteParams;
    }
    
    /**
     * ���������� ���������� ���� (�������) ��� ����������.
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * @see UfoSite::getPath()
     */
    public function getParsedPath()
    {
        return $this->getPath();
    }
    
    /**
     * ���������� ������ ����������, ���������� � URL ����� ��������� ���� �������.
     * @return array
     */
    public function getPathParams()
    {
        return $this->pathParams;
    }
    
    /**
     * ������ ��������������� ���� ������� �� ���� ������� � ���������.
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    protected function parsePath()
    {
        $path = $this->pathRaw;
        
        //main page
        if ('/' == $path) {
            $this->path = $path;
            return;
        }
        if ('' == $path) {
            throw new UfoExceptionPathEmpty('Main page redirect required');
        }
        
        //���� � ���� ���� ������������ �������, �������� ������ 404
        if (!$this->isPath($path)) {
            throw new UfoExceptionPathBad('Bad path');
        }
        
        //��������� ���� ������ ������ �� �������������
        //���� �� ������� ������� ����� � ���� �����, ������ ������������� ����, ������ 404
        //����� ��������� ���� � �������������� �� ����������������� �����
        if ('/' != $path{strlen($path) - 1}) {
            if (false === strpos($path, '.')) {
                $path .= '/';
                $this->pathRaw .= '/';
                //�������������� ������ ���� ��� POST �������, ����� ������������
                if (!isset($_SERVER['REQUEST_METHOD'])) {
                    throw new UfoExceptionPathUnclosed('Closing slash omitted');
                    return;
                } else if (0 != strcasecmp('POST', $_SERVER['REQUEST_METHOD'])) {
                    throw new UfoExceptionPathUnclosed('Closing slash omitted');
                    return;
                }
            } else {
                throw new UfoExceptionPathFilenotexists('Asking for a file which not exists');
                return;
            }
        }
        
        //���������� ������������ �� ���� � ��
        $sql = 'SELECT COUNT(*) AS Cnt FROM ' . $this->db->getTablePrefix() . 'sections' .
               " WHERE path='" . $path . "'";
        $row = $this->db->getRowByQuery($sql);
        
        //���� ���, ��������� ���� �� ������, ����� ��������� ��������� � ����
        if (0 == $row['Cnt']) {
            /* DEBUG echo 'path: ' . $path . "<br />\n"; */
            //������ ������ ����
            $pathParts = explode('/', $path);
            //������� ������� �����, ����� �� ���� ������ ��������� � �������
            array_shift($pathParts);
            array_pop($pathParts);
            $pathPartsCount = count($pathParts);
            /* DEBUG echo '<pre>'; var_dump($arr_path); echo "</pre>\n"; */
        
            //���� ����������� ������ ����������, �������
            if ($this->config->sitePathNestingLimit < $pathPartsCount) {
                //�������� ������ 404
                throw new UfoExceptionPathComplex('Path is too complex');
                return;
            }
        
            //�������� ������ ��������� �����
            $paths = array('/');
            for ($i = 0; $i < $pathPartsCount; $i++) {
                $paths[$i + 1] = $paths[$i] . $pathParts[$i] . '/';
            }
            //������� �������� ����, ��������� �������� (�������) �������� ������ ������������
            array_shift($paths);
            /* DEBUG echo '<pre>'; var_dump($arr_paths); echo "</pre>\n"; */
        
            $sql = 'SELECT path FROM ' . $this->db->getTablePrefix() . 'sections' .
                   " WHERE path IN('" . implode("','", $paths) . "')" .
                   ' ORDER BY path DESC' .
                   ' LIMIT 1';
            /* DEBUG echo '<code>' . $sql . "</code><br />\n"; */
            if ($row = $this->db->getRowByQuery($sql)) {
                $this->path = $row['path'];
            } else {
                throw new UfoExceptionPathNotexists('Path not exists');
            }
        }
    }
}
