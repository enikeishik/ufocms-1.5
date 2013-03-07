<?php
require_once 'UfoTools.php';
/**
 * �������� ����� ����������.
 * 
 * �������� ����� ����� � ���� ������������ ������ UfoCore::main(), 
 * � ������� ��������������� ���������� �������� ������.
 * 
 * @author enikeishik
 *
 */
final class UfoCore
{
    use UfoTools;
    
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    private $config = null;
    
    /**
     * �������������� ���� �������� �������.
     * @var string
     */
    private $pathRaw = '';
    
    /**
     * ���� �������� �������.
     * @var string
     */
    private $path = '';
    
    /**
     * ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    private $db = null;
    
    /**
     * ������ ��� ������ ������� ������.
     * @var UfoDbModel
     */
    private $dbModel = null;
    
    /**
     * ������ ��� ������ � �����.
     * @var UfoCache
     */
    private $cache = null;
    
    /**
     * ������-��������� ��� �������� ������ �����.
     * @var UfoSite
     */
    private $site = null;
    
    /**
     * ������ �������� ������� �����.
     * @var UfoSection
     */
    private $section = null;
    
    /**
     * ���������� ������� ��������.
     * @var string
     */
    private $page = null;
    
    /**
     * ������ �������.
     * @var UfoDebug
     */
    private $debug = null;
    
    /**
     * ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    private $container = null;
    
    /**
     * ����� ����� ����������.
     * ��������������� ��������� �������� ������ ������� UfoCore.
     */
    public static function main()
    {
        $core = new UfoCore(new UfoConfig());
        $core->initPhp();
        $core->setPathRaw();
        if ($core->tryCache()) {
            echo $core->page;
            return;
        }
        //� ������ ������ ���������� � ����� ������, ������������ ������� �������� ������ �� ����.
        if (!$core->initDb()) {
            $this->page = $this->loadCache($this->cache);
            if (!is_null($core->page)) {
                $this->debug->log('Using cache');
                echo $core->page;
            } else {
                $this->debug->log('Cache not exists, generating error');
                //error 500
            }
            return;
        }
        $core->initDbModel();
        try {
            $core->initSite();
        } catch (Exception $e) {
            exit();
        }
        try {
            $core->initSection();
        } catch (Exception $e) {
            exit();
        }
        $core->generatePage();
        $core->finalize();
        $core->shutdown();
        echo $core->page;
    }
    
    /**
     * �����������.
     * @param UfoConfig &$config    ������ �� ������ ������������
     */
    public function __construct(UfoConfig &$config)
    {
        $this->config =& $config;
    }
    
    /**
     * ������������� �����.
     */
    public function initPhp()
    {
        //�������� ����� ������ � .htaccess ��� �����,
        //���� �� ���-������� �� ����������� ��������� php �������� ��� .htaccess
        ini_set('display_errors', $this->config->phpDisplayErrors);
        ini_set('error_reporting', $this->config->phpErrorReportingLevel);
        
        //������������� ������� ����, to avoid warning in date function
        date_default_timezone_set($this->config->phpTimezone);
        
        //������������� ������, for strcomp, str_replace etc
        setlocale(LC_ALL, $this->config->phpLocales);
        
        //������������� ����������� ����������� ������, ��� �� ����������������
        //require_once('__errors.php');
        //set_error_handler('err_Php');
        
        $this->loadClass('UfoDebug');
        $this->debug = new UfoDebug($this->config->debugLevel);
        //����� ���������� �������
        $this->debug->setPageStartTime();
        $this->debug->log('Execution started');
    }
    
    /**
     * ������������� ��������������� ���� �������.
     */
    public function setPathRaw()
    {
        //mod_rewrite ������������ �������� ���� � ������ �������
        //����� ���������� ������� �� 404 ������, ��� ��� ��������� ���������
        //mod_rewrite � .htaccess � �������� ����� �����
        if (isset($_GET['path']) && '' != $_GET['path']) {
            $this->pathRaw = $_GET['path'];
        //����� ��� ������� ��������
        } else {
            $this->pathRaw = '/';
        }
        $this->debug->log('PathRaw: ' . $this->pathRaw);
    }
    
    /**
     * ������� ������������� ���� ��� ������� ��������.
     * @return boolean
     */
    public function tryCache()
    {
        $this->debug->log('Trying use cache');
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings);
        $this->page = $this->loadCache($this->cache);
        $this->debug->log(is_null($this->page) ? 'Cache not used' : 'Using cache');
        return !is_null($this->page);
    }
    
    /**
     * ��������� ���������� � ����� ������.
     * @return boolean
     */
    public function initDb()
    {
        $this->debug->log('Init database connection', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDb');
        $this->db = new UfoDb($this->config->dbSettings);
        if (0 == $this->db->connect_errno) {
            $this->debug->log('Init database connection complete', __CLASS__, __METHOD__, true);
            return true;
        } else {
            $this->debug->log('Init database connection error: ' . 
                              $this->db->connect_error . ', trying cache', __CLASS__, __METHOD__, true);
            return false;
        }
        
    }
    
    /**
     * ������������� ������� ������ ���� ������.
     */
    public function initDbModel()
    {
        $this->debug->log('Creating database model', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDbModel');
        $this->dbModel = new UfoDbModel($this->db);
        $this->debug->log('Creating database model complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ��������� ������ ����� � ��������� ���� �������.
     * @throws Exception
     */
    public function initSite()
    {
        $this->debug->log('Loading container', __CLASS__, __METHOD__, false);
        $container =& $this->getContainer();
        $container->setConfig($this->config);
        $container->setDb($this->db);
        $container->setDbModel($this->dbModel);
        $container->setDebug($this->debug);
        $container->setCore($this);
        $this->debug->log('Loading container complete', __CLASS__, __METHOD__, true);
        
        $this->debug->log('Creating site object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSite');
        try {
            $this->site = 
                new UfoSite($this->pathRaw, 
                            $container);
            $this->debug->log('Creating site object complete', __CLASS__, __METHOD__, true);
        } catch (UfoExceptionPathEmpty $e) {
            $this->debug->log('UfoExceptionPathEmpty');
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/');
            } else {
                //err http 404
            }
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathBad $e) {
            $this->debug->log('UfoExceptionPathBad');
            //err http 404
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathUnclosed $e) {
            $this->debug->log('UfoExceptionPathUnclosed');
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->pathRaw . '/');
            } else {
                //err http 404
            }
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathFilenotexists $e) {
            $this->debug->log('UfoExceptionPathFilenotexists');
            //err http 404
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathComplex $e) {
            $this->debug->log('UfoExceptionPathComplex');
            //err http 404
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathNotexists $e) {
            $this->debug->log('UfoExceptionPathNotexists');
            //err http 404
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            $this->debug->log('Exception: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        $this->pathRaw = $this->site->getPathRaw();
        $this->path = $this->site->getPathParsed();
        $this->debug->log('Path: ' . $this->path);
    }
    
    /**
     * ������������� ������� �������� �������.
     * @todo errorHamdlerModule
     * @throws Exception
     */
    public function initSection()
    {
        $this->debug->log('Loading container', __CLASS__, __METHOD__, false);
        $container =& $this->getContainer();
        $container->setSite($this->site);
        $this->debug->log('Loading container complete', __CLASS__, __METHOD__, true);
        
        $this->debug->log('Trying get section object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSection');
        try {
            $this->section = 
                new UfoSection($this->path, 
                               $container);
            $this->debug->log('Section object created', __CLASS__, __METHOD__, true);
        } catch (Exception $e) {
            $this->debug->log('Exception: ' . $e->getMessage(), __CLASS__, __METHOD__, true);
            //$this->module =& errorHamdlerModule(500)
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * ��������� ����������� ������� �������� 
     * ����������� ������� ������ �������������� ������� ������.
     */
    public function generatePage()
    {
        $this->debug->log('Initialize module', __CLASS__, __METHOD__, false);
        try {
            $this->section->initModule();
            $this->debug->log('Initialize module complete', __CLASS__, __METHOD__, true);
        } catch (Exception $e) {
            $this->debug->log('Exception: ' . $e->getMessage(), __CLASS__, __METHOD__, true);
            throw new Exception($e->getMessage());
        }
        $this->debug->log('Generating page', __CLASS__, __METHOD__, false);
        $this->page = $this->section->getPage();
        $this->debug->log('Generating page complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * �������� ���������� � ����� ������.
     */
    public function finalize()
    {
        $this->debug->log('Finalization', __CLASS__, __METHOD__, false);
        if (!is_null($this->db)) {
            $this->db->close();
            unset($this->db);
        }
        $this->debug->log('Finalization complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ����������� �������� ����������� ����� ���������� ������ �������.
     */
    public function shutdown()
    {
        $this->debug->log('Shutdown', __CLASS__, __METHOD__, false);
        if ($this->config->cacheFsSavetime > 0) {
            //��������������� ����������� ������ ��-���������
            restore_error_handler();
            //��������� ����� ������
            ini_set('display_errors', '0');
            ini_set('error_reporting', 0);
            //������������ �������, ���������� �� ���������� ���������� �������
            register_shutdown_function('UfoCacheFs::deleteOld', 
                                       $this->config->cacheFsSettings);
        }
        $this->debug->log('Shutdown complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * �������� ������ ������� �������� �� ����.
     * @param UfoCache $cache               ������ ������ � �����
     * @param boolean  $override = false    ������������� ��������� ���, ���� ���� �� �������
     * @return string|null
     */
    private function loadCache(UfoCache $cache, $override = false)
    {
        if ($override || ($cache->exists() && !$cache->expired())) {
            if ($data = $cache->load()) {
                return $data;
            }
        }
        return null;
    }
    
    /**
     * ��������� ������ �� ������-��������� ������ �� ������� � ��� �������� ��� �������������.
     * @return UfoContainer
     */
    private function &getContainer()
    {
        if (is_null($this->container)) {
            $this->loadClass('UfoContainer');
            $this->container = new UfoContainer();
        }
        return $this->container;
    }
    
    /**
     * ��������� ������ �� ������-��������� ������ �� �������.
     * @param UfoContainer &$container
     */
    public function setContainer(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
    }
    
    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    private function unpackContainer()
    {
        if (!is_null($config =& $this->container->getConfig())) {
            $this->config =& $config;
        }
        if (!is_null($db =& $this->container->getDb())) {
            $this->db =& $db;
        }
        if (!is_null($dbModel =& $this->container->getDbModel())) {
            $this->dbModel =& $dbModel;
        }
        if (!is_null($debug =& $this->container->getDebug())) {
            $this->debug =& $debug;
        }
        if (!is_null($site =& $this->container->getSite())) {
            $this->site =& $site;
        }
        if (!is_null($section =& $this->container->getSection())) {
            $this->section =& $section;
        }
    }
    
    /*
     * 
     */
    
    /**
     * ������� ���������� �� ��������.
     * @param array $params = null    ��������� �������, �������������� ������
     * @return string
     */
    public function insertion(array $params = null)
    {
        $targetId = $this->section->getFields()->id;
        $placeId = 0;
        $offset = 0;
        $limit = 0;
        if (is_array($params)) {
            if (array_key_exists('PlaceId', $params)) {
                $placeId = (int) $params['PlaceId'];
            }
            if (array_key_exists('Offset', $params)) {
                $offset = (int) $params['Offset'];
                if ($offset < 1) {
                    $offset = 0;
                }
            }
            if (array_key_exists('Limit', $params)) {
                $limit = (int) $params['Limit'];
                if ($limit < 1) {
                    $limit = 0;
                }
            }
        }
        $this->loadClass('UfoInsertion');
        $insertion = new UfoInsertion($this->getContainer());
        return $insertion->generate($targetId, $placeId, $offset, $limit, $params);
    }
}
