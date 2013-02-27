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
        if (!$core->initDb() && !is_null($core->page)) {
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
     * Конструктор.
     * @param UfoConfig &$config    ссылка на объект конфигурации
     */
    public function __construct(UfoConfig &$config)
    {
        $this->config =& $config;
    }
    
    /**
     * Инициализация среды.
     */
    public function initPhp()
    {
        //включаем вывод ошибок в .htaccess или здесь,
        //если на веб-сервере не установлена поддержка php директив для .htaccess
        ini_set('display_errors', '1');
        ini_set('error_reporting', E_ALL);
        
        //устанавливаем часовой пояс, to avoid warning in date function
        date_default_timezone_set('Europe/Moscow');
        
        //устанавливаем локаль, for strcomp, str_replace etc
        setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
        
        //устанавливаем собственный перехватчик ошибок, для их протоколирования
        //require_once('__errors.php');
        //set_error_handler('err_Php');
        
        $this->loadClass('UfoDebug');
        $this->debug = new UfoDebug($this->config->debugLevel);
        //время выполнения скрипта
        $this->debug->setPageStartTime();
        $this->debug->log('Execution started');
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
        $this->debug->log('Trying use cache');
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings);
        $this->page = $this->loadCache($this->cache);
        $this->debug->log(is_null($this->page) ? 'Cache not used' : 'Using cache');
        return !is_null($this->page);
    }
    
    /**
     * Установка соединения с базой данных.
     * В случае ошибки соединения с базой данных, производится попытка получить данные из кэша.
     * @todo errorHamdlerModule
     * @throws Exception
     * @todo убрать запрос установки кодировки отсюда, сделать его конфигурационной опцией и чатью класса БД.
     */
    public function initDb()
    {
        $this->debug->log('Trying connect to database');
        $this->loadClass('UfoDb');
        try {
            $this->db = new UfoDb($this->config->dbSettings);
            $this->db->query('SET NAMES CP1251');
            $this->debug->log('Connected to database successfully');
            $this->loadClass('UfoDbModel');
            $this->dbModel = new UfoDbModel($this->db);
            $this->debug->log('Data model object created successfully');
            return true;
        } catch (Exception $e) {
            $this->debug->log('Connection to database failed, trying cache');
            //пытаемся получить данные из кэша
            $this->page = $this->loadCache($this->cache);
            if (!is_null($this->page)) {
                $this->debug->log('Using cache');
                return false;
            } else {
                $this->debug->log('Cache not exists, generating error');
                //$this->module =& errorHamdlerModule(500)
                throw new Exception($e->getMessage());
            }
        }
    }
    
    /**
     * Получение данных сайта и обработка пути раздела.
     */
    public function initSite()
    {
        $this->debug->log('Preparing container');
        $container =& $this->getContainer();
        $container->setConfig($this->config);
        $container->setDb($this->db);
        $container->setDbModel($this->dbModel);
        $container->setDebug($this->debug);
        $container->setCore($this);
        
        $this->debug->log('Trying get site object');
        $this->loadClass('UfoSite');
        $error = true;
        try {
            $this->site = 
                new UfoSite($this->pathRaw, 
                            $container);
            $error = false;
            $this->debug->log('Site object created');
        } catch (UfoExceptionPathEmpty $e) {
            $this->debug->log('UfoExceptionPathEmpty');
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/');
            } else {
                //err http 404
            }
        } catch (UfoExceptionPathBad $e) {
            $this->debug->log('UfoExceptionPathBad');
            //err http 404
        } catch (UfoExceptionPathUnclosed $e) {
            $this->debug->log('UfoExceptionPathUnclosed');
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->pathRaw . '/');
            } else {
                //err http 404
            }
        } catch (UfoExceptionPathFilenotexists $e) {
            $this->debug->log('UfoExceptionPathFilenotexists');
            //err http 404
        } catch (UfoExceptionPathComplex $e) {
            $this->debug->log('UfoExceptionPathComplex');
            //err http 404
        } catch (UfoExceptionPathNotexists $e) {
            $this->debug->log('UfoExceptionPathNotexists');
            //err http 404
        } catch (Exception $e) {
            $this->debug->log('Exception: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        if ($error) {
            exit();
        }
        $this->path = $this->site->getParsedPath();
        $this->debug->log('Path: ' . $this->path);
    }
    
    /**
     * Инициализация объекта текущего раздела.
     * @todo errorHamdlerModule
     * @throws Exception
     */
    public function initSection()
    {
        $this->debug->log('Preparing container');
        $container =& $this->getContainer();
        $container->setSite($this->site);
        
        $this->debug->log('Trying get section object');
        $this->loadClass('UfoSection');
        try {
            $this->section = 
                new UfoSection($this->path, 
                               $container);
            $this->debug->log('Section object created');
        } catch (Exception $e) {
            $this->debug->log('Exception: ' . $e->getMessage());
            //$this->module =& errorHamdlerModule(404)
            throw new Exception($e->getMessage());
            return;
        }
    }
    
    /**
     * Генерация содержимого текущей страницы 
     * посредством объекта модуля обслуживающего текущий раздел.
     */
    public function generatePage()
    {
        $this->debug->log('Initialize module');
        try {
            $this->section->initModule();
        } catch (Exception $e) {
            $this->debug->log('Exception: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
        $this->debug->log('Generating page');
        $this->page = $this->section->getPage();
    }
    
    /**
     * Закрытие соединения с базой данных.
     */
    public function finalize()
    {
        $this->debug->log('Finalization');
        if (!is_null($this->db)) {
            $this->db->close();
            unset($this->db);
        }
    }
    
    /**
     * Регистрация процедур выполняемых после завершения работы скрипта.
     */
    public function shutdown()
    {
        $this->debug->log('Shutdown');
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
    
    /*
     * 
     */
    
    /**
     * Вставка информации из разделов.
     * @param array $params = null    параметры вставки, дополнительные данные
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
