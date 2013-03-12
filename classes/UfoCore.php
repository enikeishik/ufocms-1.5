<?php
require_once 'UfoTools.php';
/**
 * Основной класс приложения.
 * 
 * Содержит точку входа в виде статического метода UfoCore::main(), 
 * в котором последовательно вызываются основные методы.
 * 
 * @author enikeishik
 *
 */
final class UfoCore
{
    use UfoTools;
    
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    private $config = null;
    
    /**
     * Необработанный путь текущего раздела.
     * @var string
     */
    private $pathRaw = '';
    
    /**
     * Путь текущего раздела.
     * @var string
     */
    private $path = '';
    
    /**
     * Объект для работы с базой данных.
     * @var UfoDb
     */
    private $db = null;
    
    /**
     * Объект для работы моделью данных.
     * @var UfoDbModel
     */
    private $dbModel = null;
    
    /**
     * Объект для работы с кэшем.
     * @var UfoCache
     */
    private $cache = null;
    
    /**
     * Объект-структура для хранения данных сайта.
     * @var UfoSite
     */
    private $site = null;
    
    /**
     * Объект текущего раздела сайта.
     * @var UfoSection
     */
    private $section = null;
    
    /**
     * Содержимое текущей страницы.
     * @var string
     */
    private $page = null;
    
    /**
     * Объект отладки.
     * @var UfoDebug
     */
    private $debug = null;
    
    /**
     * Объект-хранилище ссылок на объекты.
     * @var UfoContainer
     */
    private $container = null;
    
    /**
     * Точка входа приложения.
     * Последовательно выполняет основные методы объекта UfoCore.
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
        //В случае ошибки соединения с базой данных, производится попытка получить данные из кэша.
        if (!$core->initDb()) {
            $this->page = $this->loadCache($this->cache);
            if (!is_null($core->page)) {
                $this->debug->log('Using cache');
                echo $core->page;
            } else {
                $this->debug->log('Cache not exists, generating error');
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
     * Конструктор.
     * @param UfoConfig &$config    ссылка на объект конфигурации
     */
    public function __construct(UfoConfig &$config)
    {
        $this->config =& $config;
        $this->loadClass('UfoDebug');
        $this->debug = new UfoDebug($this->config->debugLevel);
        $container =& $this->getContainer();
        $container->setConfig($this->config);
        $container->setDebug($this->debug);
        $container->setCore($this);
        
        //время выполнения скрипта
        $this->debug->setPageStartTime();
        $this->debug->log('Execution started', __CLASS__, __METHOD__, false);
    }
    
    /**
     * Инициализация среды.
     */
    public function initPhp()
    {
        $this->debug->log('Init PHP', __CLASS__, __METHOD__, false);
        
        //включаем вывод ошибок в .htaccess или здесь,
        //если на веб-сервере не установлена поддержка php директив для .htaccess
        ini_set('display_errors', $this->config->phpDisplayErrors);
        ini_set('error_reporting', $this->config->phpErrorReportingLevel);
        
        //устанавливаем часовой пояс, to avoid warning in date function
        date_default_timezone_set($this->config->phpTimezone);
        
        //устанавливаем локаль, for strcomp, str_replace etc
        setlocale(LC_ALL, $this->config->phpLocales);
        
        //устанавливаем собственный перехватчик ошибок, для их протоколирования
        set_error_handler(array(&$this, 'errorHandler'));
        
        $this->debug->log('Init PHP complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * Инициализация необработанного пути раздела.
     */
    public function setPathRaw()
    {
        //mod_rewrite перекидывает реальный путь в строку запроса
        //иначе посетители попадут на 404 ошибку, так что проверять настройку
        //mod_rewrite в .htaccess в корневой папке сайта
        if (isset($_GET['path']) && '' != $_GET['path']) {
            $this->pathRaw = $_GET['path'];
        //иначе это главная страница
        } else {
            $this->pathRaw = '/';
        }
        $this->debug->log('PathRaw: ' . $this->pathRaw);
    }
    
    /**
     * Попытка использования кэша для текущей страницы.
     * @return boolean
     */
    public function tryCache()
    {
        $this->debug->log('Trying use cache', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings);
        $this->page = $this->loadCache($this->cache);
        $this->debug->log(is_null($this->page) ? 'Cache not used' : 'Using cache', __CLASS__, __METHOD__, true);
        return !is_null($this->page);
    }
    
    /**
     * Установка соединения с базой данных.
     * @return boolean
     */
    public function initDb()
    {
        $this->debug->log('Init database connection', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDb');
        $this->db = new UfoDb($this->config->dbSettings, $this->debug);
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
     * Инициализация объекта модели базы данных.
     */
    public function initDbModel()
    {
        $this->debug->log('Creating database model', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoDbModel');
        $this->dbModel = new UfoDbModel($this->db);
        $this->debug->log('Creating database model complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * Получение данных сайта и обработка пути раздела.
     * @throws Exception
     */
    public function initSite()
    {
        $this->debug->log('Loading container', __CLASS__, __METHOD__, false);
        $container =& $this->getContainer();
        $container->setDb($this->db);
        $container->setDbModel($this->dbModel);
        $this->debug->log('Loading container complete', __CLASS__, __METHOD__, true);
        
        $this->debug->log('Creating site object', __CLASS__, __METHOD__, false);
        $this->loadClass('UfoSite');
        try {
            $this->site = 
                new UfoSite($this->pathRaw, 
                            $container);
            $this->debug->log('Creating site object complete', __CLASS__, __METHOD__, true);
        } catch (UfoExceptionPathEmpty $e) {
            $this->debug->log($e->getMessage());
            //$this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/');
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(301, $e->getMessage(), '/'), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathBad $e) {
            $this->debug->log($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathUnclosed $e) {
            $this->debug->log($e->getMessage());
            //$this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->pathRaw . '/');
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(301, $e->getMessage(), $this->pathRaw . '/'), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathFilenotexists $e) {
            $this->debug->log($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathComplex $e) {
            $this->debug->log($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (UfoExceptionPathNotexists $e) {
            $this->debug->log($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(404, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            $this->debug->log($e->getMessage());
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(500, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        }
        $this->pathRaw = $this->site->getPathRaw();
        $this->path = $this->site->getPathParsed();
        $this->debug->log('Path: ' . $this->path);
    }
    
    /**
     * Инициализация объекта текущего раздела.
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
            $this->loadClass('UfoError');
            $this->loadClass('UfoErrorStruct');
            $ufoErr = new UfoError(new UfoErrorStruct(500, $e->getMessage()), 
                                   $this->getContainer());
            $this->page = $ufoErr->getPage();
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Генерация содержимого текущей страницы 
     * посредством объекта модуля обслуживающего текущий раздел.
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
     * Закрытие соединения с базой данных.
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
     * Регистрация процедур выполняемых после завершения работы скрипта.
     */
    public function shutdown()
    {
        $this->debug->log('Shutdown', __CLASS__, __METHOD__, false);
        if ($this->config->cacheFsSavetime > 0) {
            //восстанавливаем перехватчик ошибок по-умолчанию
            restore_error_handler();
            //отключаем вывод ошибок
            ini_set('display_errors', '0');
            ini_set('error_reporting', 0);
            //регистрируем функцию, вызываемую по завершении выполнения скрипта
            register_shutdown_function('UfoCacheFs::deleteOld', 
                                       $this->config->cacheFsSettings);
        }
        $this->debug->log('Shutdown complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * Загрузка данных текущей страницы из кэша.
     * @param UfoCache $cache               объект работы с кэшем
     * @param boolean  $override = false    принудительно загрузить кэш, даже если он устарел
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
     * Получение ссылки на объект-хранилище ссылок на объекты и его создание при необходимости.
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
     * Установка ссылки на объект-хранилище ссылок на объекты.
     * @param UfoContainer &$container
     */
    public function setContainer(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
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
     * Обработчик ошибок PHP.
     * @param int $errno           уровень ошибки в виде целого числа
     * @param string $errstr       сообщение об ошибке
     * @param string $errfile      имя файла, в котором произошла ошибка
     * @param string $errline      номер строки, в которой произошла ошибка
     * @param array $errcontext    массив всех переменных, существующих в области видимости, где произошла ошибка
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
        echo $data;
        return false; //call default error handler
    }
    
    /*
     * 
     */
    
    /**
     * Вставка информации из разделов.
     * @param array $options = null    параметры вставки, дополнительные данные
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
