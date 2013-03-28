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
     * @var UfoCoreDbModel
     */
    protected $coreDbModel = null;
    
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
     * @param string $pathRaw             �������������� ���� �������� �������
     * @param string $pathSystem          ���� ���������� �������, ��������� ������� �� ����������� � ��
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    public function __construct($pathRaw, $pathSystem, UfoContainer &$container)
    {
        $this->config =& $container->getConfig();
        $this->db =& $container->getDb();
        $this->coreDbModel =& $container->getCoreDbModel();
        
        if ($arr = $this->coreDbModel->getSiteParams()) {
            foreach ($arr as $param) {
                switch($param['PType']) {
                    default:
                        $this->siteParams[$param['PName']] = $param['PValue'];
                }
            }
        }
        
        $this->pathRaw = $pathRaw;
        if ('' == $pathSystem) {
            $this->parsePath();
        } else {
            $this->parsePathSystem($pathSystem);
        }
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
     * �������� ���� �� ������������.
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists
     */
    protected function checkPath()
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
    }
    
    /**
     * ������ ��������������� ���� ������� �� ���� ������� � ���������.
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    protected function parsePath()
    {
        $this->checkPath();
        
        $path = $this->pathRaw;
        
        //���������� ������������ �� ���� � ��
        if ($this->coreDbModel->isPathExists($path)) {
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
            
            if (!$this->path = $this->coreDbModel->getMaxExistingPath($paths)) {
                throw new UfoExceptionPathNotexists('Path not exists');
                return;
            }
        }
    }
    
    /**
     * ������ ��������������� ���� ������� �� ���� ������� � ���������.
     * @param string $pathSystem    ���� ���������� �������, ��� ����������
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    protected function parsePathSystem($pathSystem)
    {
        $this->checkPath();
        
        $this->path = $pathSystem;
    }
}
