<?php
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
        if (!$core->initDb()) {
            echo $core->page;
            return;
        }
        $core->initSite();
        $core->initSection();
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
        ini_set('display_errors', '1');
        ini_set('error_reporting', E_ALL);
        
        //������������� ������� ����, to avoid warning in date function
        date_default_timezone_set('Europe/Moscow');
        
        //������������� ������, for strcomp, str_replace etc
        setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
        
        //������������� ����������� ����������� ������, ��� �� ����������������
        //require_once('__errors.php');
        //set_error_handler('err_Php');
        
        if ($this->config->debug) {
            //DEBUG: ����� ���������� �������
            $this->loadClass('UfoDebug');
            $this->debug = new UfoDebug();
            $this->debug->setPageStartTime();
        }
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
    }
    
    /**
     * ������� ������������� ���� ��� ������� ��������.
     * @return boolean
     */
    public function tryCache()
    {
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings);
        $this->page = $this->loadCache($this->cache);
        return !is_null($this->page);
    }
    
    /**
     * ��������� ���������� � ����� ������.
     * � ������ ������ ���������� � ����� ������, ������������ ������� �������� ������ �� ����.
     * @todo errorHamdlerModule
     * @throws Exception
     */
    public function initDb()
    {
        $this->loadClass('UfoDb');
        try {
            $this->db = new UfoDb($this->config->dbSettings);
            return true;
        } catch (Exception $e) {
            //�������� �������� ������ �� ����
            $this->page = $this->loadCache($this->cache);
            if (!is_null($this->page)) {
                return false;
            } else {
                //$this->module =& errorHamdlerModule(500)
                throw new Exception($e->getMessage());
            }
        }
    }
    
    /**
     * ��������� ������ ����� � ��������� ���� �������.
     */
    public function initSite()
    {
        $container =& $this->getContainer();
        $container->setConfig($this->config);
        $container->setDb($this->db);
        $container->setDebug($this->debug);
        
        $this->loadClass('UfoSite');
        $error = true;
        try {
            $this->site = 
                new UfoSite($this->pathRaw, 
                            $container);
            $error = false;
        } catch (UfoExceptionPathEmpty $e) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/');
            } else {
                //err http 404
            }
        } catch (UfoExceptionPathBad $e) {
            //err http 404
        } catch (UfoExceptionPathUnclosed $e) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->pathRaw . '/');
            } else {
                //err http 404
            }
        } catch (UfoExceptionPathFilenotexists $e) {
            //err http 404
        } catch (UfoExceptionPathComplex $e) {
            //err http 404
        } catch (UfoExceptionPathNotexists $e) {
            //err http 404
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        if ($error) {
            exit();
        }
        $this->path = $this->site->getParsedPath();
    }
    
    /**
     * ������������� ������� �������� �������.
     * @todo errorHamdlerModule
     * @throws Exception
     */
    public function initSection()
    {
        $container =& $this->getContainer();
        $container->setSite($this->site);
        
        $this->loadClass('UfoSection');
        try {
            $this->section = 
                new UfoSection($this->path, 
                               $container);
        } catch (Exception $e) {
            //$this->module =& errorHamdlerModule(404)
            throw new Exception($e->getMessage());
            return;
        }
    }
    
    /**
     * ��������� ����������� ������� �������� 
     * ����������� ������� ������ �������������� ������� ������.
     */
    public function generatePage()
    {
        $this->section->initModule();
        $this->page = $this->section->getPage();
    }
    
    /**
     * �������� ���������� � ����� ������.
     */
    public function finalize()
    {
        if (!is_null($this->db)) {
            $this->db->close();
            unset($this->db);
        }
    }
    
    /**
     * ����������� �������� ����������� ����� ���������� ������ �������.
     */
    public function shutdown()
    {
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
    
    private function &getContainer()
    {
        if (is_null($this->container)) {
            $this->loadClass('UfoContainer');
            $this->container = new UfoContainer();
        }
        return $this->container;
    }
}
