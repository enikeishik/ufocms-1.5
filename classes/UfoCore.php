<?php
require_once 'UfoTools.php';
/**
 * �������� ����� ����������.
 * 
 * �������� ����� ����� � ���� ������������ ������ UfoCore::main(), 
 * ������� ������� ������ ���� � �������� ��� ��������� ����� run, 
 * � ������� ��������������� ���������� �������� ������ ����.
 * 
 * @author enikeishik
 *
 */
final class UfoCore
{
    use UfoTools;
    
    /**
     * ������ ������ ��������� �������� ������.
     * @var UfoErrors
     */
    private $errors = null;
    
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
     * ������ ��� ������ � ������� ������.
     * @var UfoCoreDbModel
     */
    private $coreDbModel = null;
    
    /**
     * ������ ��� ������ � �����.
     * @var UfoCache
     */
    private $cache = null;
    
    /**
     * ������ ��� ������ � ������������������� �������������� �����.
     * @var UfoUsers
     */
    private $users = null;
    
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
     * ������ ������.
     * @var UfoError
     */
    private $error = null;
    
    /**
     * ����� ����� ����������.
     */
    public static function main()
    {
        $core = new UfoCore(new UfoConfig());
        $core->run();
        $core->drawDebug();
    }
    
    /**
     * ���������������� ���������� �������� ������� ������� UfoCore.
     * �������� � ��������� �����, ����� ����� ������ � ������� ������ ������.
     * ��� ������ ����� ����� � ������������ ����� �������� ��������, ��������� ��� ���� ��� ������������.
     */
    public function run()
    {
        $this->initPhp();
        
        $this->setPathRaw();
        
        $this->checkSystemPath();
        
        //������� ������������ ���
        if ($this->tryCache()) {
            //����� ��������������� �������� (���)
            $this->drawPage();
            return;
        }
        
        //� ������ ������ ���������� � ����� ������, ������������ ������� �������� ������ �� ����.
        try {
            $this->initDb();
        } catch (Exception $e) {
            //����������� ������
            $this->writeLog($e->getMessage(), $this->config->logError);
            
            $this->debug->trace('Trying use cache', __CLASS__, __METHOD__, false);
            $this->page = $this->loadCache($this->cache);
            if (!is_null($this->page)) {
                $this->debug->trace('Trying use cache complete', __CLASS__, __METHOD__, true);
                //����� ��������������� �������� (���)
                $this->drawPage();
            } else {
                $this->debug->trace('Trying use cache fail: cache not exists', __CLASS__, __METHOD__, true);
                $this->debug->trace('Generating error', __CLASS__, __METHOD__, false);
                $this->generateError(500, 'Database connection error');
                $this->debug->trace('Generating error complete', __CLASS__, __METHOD__, false);
                //����� ��������������� �������� (������)
                $this->drawPage();
            }
            return;
        }
        
        //������������� ������ ��������������� ������
        $this->initDbModel();
        
        //������������� ������� �����
        try {
            $this->initSite();
        } catch (Exception $e) {
            //����������� ������
            $this->writeLog($e->getMessage(), $this->config->logError);
            
            //����� ��������������� �������� (������)
            $this->drawPage();
            return;
        }
        
        //������������� ������� ���������� ������������������� �������������� �����
        if ($this->config->usersEnabled) {
            $this->loadClass('UfoUsers');
            try {
                $this->users = new UfoUsers($this->getContainer());
            } catch (Exception $e) {
                //����������� ������
                $this->writeLog($e->getMessage(), $this->config->logError);
                
                //���� ������������ ��������� ����������� ������ 
                //��� ����������� ������������� - ����������, 
                //����� - ���������� ������
                if ($this->config->usersOverrideError) {
                    $this->config->usersEnabled = false;
                } else {
                    //error 500
                    $this->generateError(500, 'Users initialisation error');
                    //����� ��������������� �������� (������)
                    $this->drawPage();
                    return;
                }
            }
        }
        
        //������������� ������� �������� ������� �����
        try {
            $this->initSection();
        } catch (Exception $e) {
            //����������� ������
            $this->writeLog($e->getMessage(), $this->config->logError);
            
            //����� ��������������� �������� (������)
            $this->drawPage();
            return;
        }
        
        //��������� ����������� ��������
        $this->generatePage();
        if (!is_null($this->error)) {
            $err = $this->error->getError();
            $this->generateError($err->code, $err->text, $err->pathRedirect);
        } else if ($this->config->indexNative) {
            
        }
        
        //�������� ���������� � ��, ����������� ��������
        $this->finalize();
        
        //����� ��������������� ��������
        $this->drawPage();
        
        //���������� ��������� �������� (������� ���� � �.�.)
        $this->shutdown();
    }
    
    /**
     * �����������.
     * @param UfoConfig &$config    ������ �� ������ ������������
     */
    public function __construct(UfoConfig &$config)
    {
        $this->errors = new UfoErrors();
        $this->config =& $config;
        $this->loadClass('UfoDebug');
        $this->debug = new UfoDebug($this->config);
        $container =& $this->getContainer();
        $container->setErrors($this->errors);
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
        
        //������������� ���������� ��������� �������
        mb_internal_encoding($this->config->mbInternalEncoding);
        
        //�������� ����������� ������
        ob_implicit_flush(false);
        ob_start();
        
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
        //ErrorDocument � .htaccess
        } else if (isset($_GET['error'])) {
            $this->generateError((int) $_GET['error'], 'External error');
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
     */
    public function checkSystemPath()
    {
        //��� ����� ������ ��������� ���������� $pathRaw
        //����� ���������� ��� ����������� PHP ��������� ������
        //������������ ��������� ���������� � ���� ������
        foreach ($this->config->systemSections as $path => $class) {
            if (0 === strpos($this->pathRaw, $path)) {
                $this->pathSystem = $path;
                return;
            }
        }
    }
    
    /**
     * ������� ������������� ���� ��� ������� ��������.
     * @return boolean
     */
    public function tryCache()
    {
        $this->debug->trace('Trying use cache', 
                            __CLASS__, __METHOD__, false);
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings, 
                                      $this->errors);
        $this->page = $this->loadCache($this->cache);
        $this->debug->trace(is_null($this->page) ? 'Cache not used' : 'Using cache', 
                            __CLASS__, __METHOD__, true);
        return !is_null($this->page);
    }
    
    /**
     * ��������� ���������� � ����� ������.
     * @throws Exception
     */
    public function initDb()
    {
        $this->debug->trace('Init database connection', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDb');
        
        try {
            $this->db = new UfoDb($this->config->dbSettings, $this->debug);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->debug->trace('Init database connection error: ' . $error, 
                                __CLASS__, __METHOD__, true);
            throw new Exception($error);
        }
        
        if (0 == $this->db->connect_errno) {
            $container =& $this->getContainer();
            $container->setDb($this->db);
            $this->debug->trace('Init database connection complete', 
                                __CLASS__, __METHOD__, true);
        } else {
            $this->debug->trace('Init database connection error: ' . $this->db->connect_error, 
                                __CLASS__, __METHOD__, true);
            throw new Exception($this->db->connect_error);
        }
}
    
    /**
     * ������������� ������� ������ ���� ������.
     */
    public function initDbModel()
    {
        $this->debug->trace('Creating database model', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoCoreDbModel');
        $this->coreDbModel = new UfoCoreDbModel($this->db);
        $container =& $this->getContainer();
        $container->setCoreDbModel($this->coreDbModel);
        $this->debug->trace('Creating database model complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ��������� ������ ����� � ��������� ���� �������.
     * @throws Exception
     */
    public function initSite()
    {
        $this->debug->trace('Creating site object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSite');
        try {
            $this->site = 
                new UfoSite($this->pathRaw, $this->pathSystem, $this->getContainer());
            $container =& $this->getContainer();
            $container->setSite($this->site);
            $this->debug->trace('Creating site object complete', __CLASS__, __METHOD__, true);
        } catch (UfoExceptionPathEmpty $e) { //�� �����������, �.�. setPathRaw ��������� ���� ������
            $this->debug->trace($e->getMessage());
            //$this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/');
            $this->generateError(301, $e->getMessage(), '/');
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathBad $e) {
            $this->debug->trace($e->getMessage());
            $this->generateError(404, $e->getMessage());
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathUnclosed $e) {
            $this->debug->trace($e->getMessage());
            //$this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->pathRaw . '/');
            $this->generateError(301, $e->getMessage(), $this->pathRaw . '/');
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathFilenotexists $e) {
            $this->debug->trace($e->getMessage());
            $this->generateError(404, $e->getMessage());
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathComplex $e) {
            $this->debug->trace($e->getMessage());
            $this->generateError(404, $e->getMessage());
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathNotexists $e) {
            $this->debug->trace($e->getMessage());
            $this->generateError(404, $e->getMessage());
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            $this->debug->trace($e->getMessage());
            $this->generateError(500, $e->getMessage());
            throw new Exception($e->getMessage());
        }
        $this->pathRaw = $this->site->getPathRaw();
        $this->path = $this->site->getPathParsed();
        $this->debug->trace('Path: ' . $this->path);
    }
    
    /**
     * ������������� ������� �������� �������.
     * @throws Exception
     */
    public function initSection()
    {
        $this->debug->trace('Trying get section object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSection');
        $this->loadClass('UfoSectionStruct');
        try {
            if ('' == $this->pathSystem) {
                $this->section = new UfoSection($this->path, $this->getContainer());
            } else {
                $this->loadClass('UfoSystemSection');
                $this->section = new UfoSystemSection($this->pathSystem, $this->getContainer());
            }
            $this->debug->trace('Section object created', __CLASS__, __METHOD__, true);
        } catch (Exception $e) {
            $this->debug->trace('Exception: ' . $e->getMessage(), __CLASS__, __METHOD__, true);
            $this->generateError(500, $e->getMessage());
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
     * ���������� ����������� ������� ��������.
     */
    public function indexPage()
    {
        if ('' != $this->pathSystem || is_null($this->section)) {
            return;
        }
        if ($this->section->getModule()->getParam('rss')) {
            return;
        }
        $this->debug->trace('Indexing page', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoIndexer');
        $idx = new UfoIndexer($this->getContainer());
        $idx->index($this->pathRaw, 
                    $this->page, 
                    ($this->section->getField('isenabled') && $this->section->getField('insearch')), 
                    $this->section->getField('flsearch'), 
                    $this->section->getField('moduleid'));
        $this->debug->trace('Indexing page complete', __CLASS__, __METHOD__, true);
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
     * ����� ��������������� ��������.
     * ����� ��� ���� ������������ ��� ������ ������.
     */
    public function drawPage()
    {
        $this->debug->trace('Drawing page', __CLASS__, __METHOD__, false);
        
        echo $this->page;
        
        //����� ������ �������
        ob_flush();
        flush();
        
        //������� ����� ���� �������
        if ($this->config->obHardFlush) {
            while (ob_get_level()) {
                ob_end_flush();
            }
        }
        
        $this->debug->trace('Drawing page complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ���������� ��������� �������� ����� ������ �������� �������.
     */
    public function shutdown()
    {
        $this->debug->trace('Shutdown', __CLASS__, __METHOD__, false);
        
        //��������� ����� ������
        ini_set('display_errors', '0');
        ini_set('error_reporting', 0);
        
        //�������� ����
        if ($this->section && $this->section->getField('flcache')) {
            $this->cache->save($this->page);
        }
        
        //������� ���� �� ���������� ������, ���� ������ ����� �������� ����
        if ('files' == $this->config->cacheType && $this->config->cacheFsSavetime > 0) {
            $this->cache->deleteOld();
        }
        
        //������ ������� ���������� ������� � ��� ������������������
        if ('' != $this->config->logPerformance) {
            $this->writeLog($this->pathRaw . "\t" . $this->debug->getPageExecutionTime(), 
                            $this->config->logPerformance);
        }
        
        $this->debug->trace('Shutdown complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * ����� ���������� ����������.
     */
    public function drawDebug()
    {
        if ($this->config->debugDisplay) {
            //������ ����� �����������, ���� ��� �������
            if (false === ob_get_length()) {
                ob_start();
            }
            $this->debug->draw();
        }
    }
    
    /**
     * �������� �� ������ ���������.
     * @return boolean
     */
    public function isSystemPath()
    {
        return '' != $this->pathSystem;
    }
    
    /**
     * ���������� ��� ���������� ������ (�������, �������, ��������� ���������� � �.�.).
     * @return string (browser|printer|mobile)
     * @todo realisation, tests
     */
    public function getUserAgent()
    {
        return 'browser';
    }
    
    /**
     * ���������� ���������� ������� ��������.
     * @return string|null
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * �������� ������ ������� �������� �� ����.
     * @param UfoCache $cache               ������ ������ � �����
     * @param boolean  $override = false    ������������� ��������� ���, ���� ���� �� �������
     * @return string|null
     */
    private function loadCache(UfoCache $cache, $override = false)
    {
        if ($override || (!$cache->expired())) {
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
        if (!is_null($coreDbModel =& $this->container->getCoreDbModel())) {
            $this->coreDbModel =& $coreDbModel;
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
     * ��������� ����������� �������� ���������� ��������� ������.
     * @param int $code                    ��� ������
     * @param string $text                 ����� ������
     * @param string $pathRedirect = ''    ���� ������������� ��� ������ 301, 302
     */
    public function generateError($errno, $errstr, $pathRedirect = '')
    {
        $this->loadClass('UfoError');
        $this->loadClass('UfoErrorStruct');
        $ufoErr = new UfoError(new UfoErrorStruct($errno, $errstr, $pathRedirect),
                               $this->getContainer());
        $this->page = $ufoErr->getPage();
    }
    
    /**
     * ����������� ������.
     * @param int $code                    ��� ������
     * @param string $text                 ����� ������
     * @param string $pathRedirect = ''    ���� ������������� ��� ������ 301, 302
     */
    public function registerError($errno, $errstr, $pathRedirect = '')
    {
        $this->loadClass('UfoError');
        $this->loadClass('UfoErrorStruct');
        $this->error = new UfoError(new UfoErrorStruct($errno, $errstr, $pathRedirect),
                                    $this->getContainer());
    }
    
    /**
     * ���������� ������ PHP.
     * @param int $errno           ��� ������
     * @param string $errstr       ����� ������
     * @param string $errfile      ��� �����, � ������� ��������� ������
     * @param string $errline      ����� ������, � ������� ��������� ������
     * @param array $errcontext    ������ ���� ����������, ������������ � ������� ���������, ��� ��������� ������
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
        $this->debug->trace('Creating insertion', __CLASS__, __METHOD__, false);
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
        if (3 > $this->config->debugLevel) {
            return $insertion->generate($targetId, $placeId, $offset, $limit, $options);
        } else {
            $ret = $insertion->generate($targetId, $placeId, $offset, $limit, $options);
            $this->debug->trace('Creating insertion complete', __CLASS__, __METHOD__, true);
            return $ret;
        }
    }
}
