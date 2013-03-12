<?php
require_once 'exceptions/UfoExceptionPathBad.php';
require_once 'exceptions/UfoExceptionPathComplex.php';
require_once 'exceptions/UfoExceptionPathEmpty.php';
require_once 'exceptions/UfoExceptionPathFilenotexists.php';
require_once 'exceptions/UfoExceptionPathNotexists.php';
require_once 'exceptions/UfoExceptionPathUnclosed.php';

/**
 * ����� �����.
 * ������������� ������ ��� ������� � ���������� �����, ���� � ���������� ����.
 * 
 * @author enikeishik
 *
 */
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
     * ������ �� ������ ��� ������ � ����� ������.
     * @var UfoDbModel
     */
    protected $dbModel = null;
    
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
        $this->config =& $container->getConfig();
        $this->db =& $container->getDb();
        $this->dbModel =& $container->getDbModel();
        
        if ($arr = $this->dbModel->getSiteParams()) {
            foreach ($arr as $param) {
                switch($param['PType']) {
                    default:
                        $this->siteParams[$param['PName']] = $param['PValue'];
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
     * ��������� �������� ��������� ����� �� �����.
     * @param string $name           ��� ���������
     * @param mixed $default = ''    �������� ��-���������
     * @return mixed
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
     * ���������� ���� � �����������.
     * @return string
     */
    public function getPathRaw()
    {
        return $this->pathRaw;
    }
    
    /**
     * ���������� ����� ����, ��������������� ���� �������, ��� ����������.
     * @return string
     */
    public function getPathParsed()
    {
        return $this->path;
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
            throw new UfoExceptionPathEmpty('Path empty, main page redirect required');
        }
        
        //���� � ���� ���� ������������ �������, �������� ������ 404
        if (!$this->isPath($path, false)) {
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
        if ($this->dbModel->isPathExists($path)) {
            $this->path = $path;
        //���� ���, ��������� ���� �� ������, ����� ��������� ��������� � ����
        } else {
            //������ ������ ����
            $pathParts = explode('/', $path);
            //������� ������� �����, ����� �� ���� ������ ��������� � �������
            array_shift($pathParts);
            array_pop($pathParts);
            $pathPartsCount = count($pathParts);
        
            //���� ����������� ������ ����������, �������
            if ($this->config->pathNestingLimit < $pathPartsCount) {
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
            
            if (!$this->path = $this->dbModel->getMaxExistingPath($paths)) {
                throw new UfoExceptionPathNotexists('Path not exists');
                return;
            }
        }
    }
}
