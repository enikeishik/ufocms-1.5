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
     * �������������� ���� �������� �������.
     * @var string
     */
    private $pathRaw = '';
    
    /**
     * ���� ���������� �������.
     * @var string
     */
    private $pathSystem = '';
    
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
     * ����� ����� ����������.
     * ��������������� ��������� �������� ������ ������� UfoCore.
     */
    public static function main()
    {
        $core = new UfoCore(new UfoConfig());
        $core->initPhp();
        $core->setPathRaw();
        //���� ������ �� ���������, ������� ������������ ���
        if (!$core->isSystemPath() && $core->tryCache()) {
            echo $core->page;
            return;
        }
        //� ������ ������ ���������� � ����� ������, ������������ ������� �������� ������ �� ����.
        if (!$core->initDb()) {
            $this->page = $this->loadCache($this->cache);
            if (!is_null($core->page)) {
                $this->debug->trace('Using cache');
                echo $core->page;
            } else {
                $this->debug->trace('Cache not exists, generating error');
                //error 500
                $this->loadClass('UfoError');
                $this->loadClass('UfoErrorStruct');
                $e = new UfoError(new UfoErrorStruct(500, 'Database connection error'), 
                                  $this->getContainer());
                $core->page = $e->getPage();
                echo $core->page;
            }
            return;
        }
        $core->initDbModel();
        try {
            $core->initSite();
        } catch (Exception $e) {
            echo $core->page;
            exit();
        }
        try {
            $core->initSection();
        } catch (Exception $e) {
            echo $core->page;
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
        $this->loadClass('UfoDebug');
        $this->debug = new UfoDebug($this->config);
        $container =& $this->getContainer();
        $container->setConfig($this->config);
        $container->setDebug($this->debug);
        $container->setCore($this);
        
        //����� ���������� �������
        $this->debug->setPageStartTime();
        $this->debug->trace('Execution started', __CLASS__, __METHOD__, false);
    }
    
    /**
     * ������������� �����.
     */
    public function initPhp()
    {
        $this->debug->trace('Init PHP', __CLASS__, __METHOD__, false);
        
        //�������� ����� ������ � .htaccess ��� �����,
        //���� �� ���-������� �� ����������� ��������� php �������� ��� .htaccess
        ini_set('display_errors', $this->config->phpDisplayErrors);
        ini_set('error_reporting', $this->config->phpErrorReportingLevel);
        
        //������������� ������� ����, to avoid warning in date function
        date_default_timezone_set($this->config->phpTimezone);
        
        //������������� ������, for strcomp, str_replace etc
        setlocale(LC_ALL, $this->config->phpLocales);
        
        //������������� ����������� ����������� ������, ��� �� ����������������
        set_error_handler(array(&$this, 'errorHandler'));
        
        $this->debug->trace('Init PHP complete', __CLASS__, __METHOD__, true);
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
        //����� ���������� ���� ���������� �������
        $this->pathSystem = '';
        $this->debug->trace('PathRaw: ' . $this->pathRaw);
    }
    
    /**
     * �������� �������� �� ������� ������ ���������.
     * ������ ����� ������ ����������� ������ ���� ���, ����� ���� ��������� ���� pathSystem.
     * @return boolean
     */
    public function isSystemPath()
    {
        if ('' != $this->pathSystem) {
            return true;
        }
        foreach ($this->config->systemSections as $path => $class) {
            if (0 === strpos($this->pathRaw, $path)) {
                $this->pathSystem = $path;
                return true;
            }
        }
        return false;
    }
    
    /**
     * ������� ������������� ���� ��� ������� ��������.
     * @return boolean
     */
    public function tryCache()
    {
        $this->debug->trace('Trying use cache', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings);
        $this->page = $this->loadCache($this->cache);
        $this->debug->trace(is_null($this->page) ? 'Cache not used' : 'Using cache', __CLASS__, __METHOD__, true);
        return !is_null($this->page);
    }
    
    /**
     * ��������� ���������� � ����� ������.
     * @return boolean
     */
    public function initDb()
    {
        $this->debug->trace('Init database connection', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDb');
        $this->db = new UfoDb($this->config->dbSettings, $this->debug);
        if (0 == $this->db->connect_errno) {
            $this->debug->trace('Init database connection complete', __CLASS__, __METHOD__, true);
            return true;
        } else {
            $this->debug->trace('Init database connection error: ' . 
                              $this->db->connect_error . ', trying cache', __CLASS__, __METHOD__, true);
            return false;
        }
        
    }
    
    /**
     * ������������� ������� ������ ���� ������.
     */
    public function initDbModel()
    {
        $this->debug->trace('Creating database model', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDbModel');
        $this->dbModel = new UfoDbModel($this->db);
        $this->debug->trace('Creating database model complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ��������� ������ ����� � ��������� ���� �������.
     * @throws Exception
     */
    public function initSite()
    {
        $this->debug->trace('Loading container', __CLASS__, __METHOD__, false);
        $container =& $this->getContainer();
        $container->setDb($this->db);
        $container->setDbModel($this->dbModel);
        $this->debug->trace('Loading container complete', __CLASS__, __METHOD__, true);
        
        $this->debug->trace('Creating site object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSite');
        try {
            $this->site = 
                new UfoSite($this->pathRaw, $this->pathSystem, $container);
            $this->debug->trace('Creating site object complete', __CLASS__, __METHOD__, true);
        } catch (UfoExceptionPathEmpty $e) {
            $this->debug->trace($e->getMessage());
            //$this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/');
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(301, $e->getMessage(), '/'), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathBad $e) {
            $this->debug->trace($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathUnclosed $e) {
            $this->debug->trace($e->getMessage());
            //$this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->pathRaw . '/');
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(301, $e->getMessage(), $this->pathRaw . '/'), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathFilenotexists $e) {
            $this->debug->trace($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathComplex $e) {
            $this->debug->trace($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathNotexists $e) {
            $this->debug->trace($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            $this->debug->trace($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(500, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        }
        $this->pathRaw = $this->site->getPathRaw();
        $this->path = $this->site->getPathParsed();
        $this->debug->trace('Path: ' . $this->path);
    }
    
    /**
     * ������������� ������� �������� �������.
     * @throws Exception
     * @todo ������������� ��������� ���������� �������
     */
    public function initSection()
    {
        $this->debug->trace('Loading container', __CLASS__, __METHOD__, false);
        $container =& $this->getContainer();
        $container->setSite($this->site);
        $this->debug->trace('Loading container complete', __CLASS__, __METHOD__, true);
        
        $this->debug->trace('Trying get section object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSection');
        $this->loadClass('UfoSectionStruct');
        try {
            if ('' == $this->pathSystem) {
                $this->section = new UfoSection($this->path, $container);
            } else {
                $this->section = new UfoSystemSection($this->pathSystem, $container);
            }
            $this->debug->trace('Section object created', __CLASS__, __METHOD__, true);
        } catch (Exception $e) {
            $this->debug->trace('Exception: ' . $e->getMessage(), __CLASS__, __METHOD__, true);
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(500, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * ��������� ����������� ������� �������� 
     * ����������� ������� ������ �������������� ������� ������.
     */
    public function generatePage()
    {
        $this->debug->trace('Initialize module', __CLASS__, __METHOD__, false);
        try {
            $this->section->initModule();
            $this->debug->trace('Initialize module complete', __CLASS__, __METHOD__, true);
        } catch (Exception $e) {
            $this->debug->trace('Exception: ' . $e->getMessage(), __CLASS__, __METHOD__, true);
            throw new Exception($e->getMessage());
        }
        $this->debug->trace('Generating page', __CLASS__, __METHOD__, false);
        $this->page = $this->section->getPage();
        $this->debug->trace('Generating page complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * �������� ���������� � ����� ������.
     */
    public function finalize()
    {
        $this->debug->trace('Finalization', __CLASS__, __METHOD__, false);
        if (!is_null($this->db)) {
            $this->db->close();
            unset($this->db);
        }
        $this->debug->trace('Finalization complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ����������� �������� ����������� ����� ���������� ������ �������.
     */
    public function shutdown()
    {
        $this->debug->trace('Shutdown', __CLASS__, __METHOD__, false);
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
        if ('' != $this->config->logPerformance) {
            $this->writeLog($this->pathRaw . "\t" . $this->debug->getPageExecutionTime(), 
                            $this->config->logPerformance);
        }
        $this->debug->trace('Shutdown complete', __CLASS__, __METHOD__, true);
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
    
    /**
     * ���������� ������ PHP.
     * @param int $errno           ������� ������ � ���� ������ �����
     * @param string $errstr       ��������� �� ������
     * @param string $errfile      ��� �����, � ������� ��������� ������
     * @param string $errline      ����� ������, � ������� ��������� ������
     * @param array $errcontext    ������ ���� ����������, ������������ � ������� ���������, ��� ��������� ������
     * @todo ����� ������ ��� ���������� �������, �������� ����������������
     */
    public function errorHandler($errno, $errstr, 
                                 $errfile = null, $errline = null, 
                                 array $errcontext = null)
    {
        $data = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $data = $_SERVER['REMOTE_ADDR'];
        }
        $data .= "\t" . $errno . "\t" . $errfile . "\t" . $errline . "\t" . $errstr;
        if (isset($_SERVER['REQUEST_URI'])) {
            $data .= "\t" . $_SERVER['REQUEST_URI'];
        } else {
            $data .= "\t";
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $data .= "\t" . $_SERVER['HTTP_REFERER'];
        } else {
            $data .= "\t";
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $data .= "\t" . $_SERVER['HTTP_USER_AGENT'];
        } else {
            $data .= "\t";
        }
        
        $this->writeLog($data, $this->config->logError);
        if ($this->config->debugDisplay) {
            echo $data;
        }
        
        return false; //call default error handler
    }
    
    /*
     * 
     */
    
    /**
     * ������� ���������� �� ��������.
     * @param array $options = null    ��������� �������, �������������� ������
     * @return string
     */
    public function insertion(array $options = null)
    {
        $targetId = $this->section->getFields()->id;
        $placeId = 0;
        $offset = 0;
        $limit = 0;
        if (is_array($options)) {
            if (array_key_exists('PlaceId', $options)) {
                $placeId = (int) $options['PlaceId'];
            }
            if (array_key_exists('Offset', $options)) {
                $offset = (int) $options['Offset'];
                if ($offset < 1) {
                    $offset = 0;
                }
            }
            if (array_key_exists('Limit', $options)) {
                $limit = (int) $options['Limit'];
                if ($limit < 1) {
                    $limit = 0;
                }
            }
        }
        $this->loadClass('UfoInsertion');
        $insertion = new UfoInsertion($this->getContainer());
        return $insertion->generate($targetId, $placeId, $offset, $limit, $options);
    }
}
