<?php
require_once 'UfoTools.php';
/**
 * Основной класс приложения.
 * 
 * Содержит точку входа в виде статического метода UfoCore::main(), 
 * который создает объект ядра и вызывает его публичный метод run, 
 * в котором последовательно вызываются основные методы ядра.
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
     * Необработанный путь текущего раздела.
     * @var string
     */
    private $pathRaw = '';
    
    /**
     * Путь служебного раздела.
     * @var string
     */
    private $pathSystem = '';
    
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
     * Объект для работы с моделью данных.
     * @var UfoCoreDbModel
     */
    private $coreDbModel = null;
    
    /**
     * Объект для работы с кэшем.
     * @var UfoCache
     */
    private $cache = null;
    
    /**
     * Объект для работы с зарегистрированными пользователями сайта.
     * @var UfoUsers
     */
    private $users = null;
    
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
     * Объект ошибки.
     * @var UfoError
     */
    private $error = null;
    
    /**
     * Точка входа приложения.
     */
    public static function main()
    {
        $core = new UfoCore(new UfoConfig());
        $core->run();
    }
    
    /**
     * Последовательное выполнение основных методов объекта UfoCore.
     * Выделено в отдельный метод, чтобы иметь доступ к частным членам класса.
     * Все методы кроме этого и конструктора можно объявить частными, публичные они лишь для тестирования.
     */
    public function run()
    {
        $this->initPhp();
        
        $this->setPathRaw();
        
        //если раздел не системный, пробуем использовать кэш
        if (!$this->isSystemPath() && $this->tryCache()) {
            echo $this->page;
            return;
        }
        
        //В случае ошибки соединения с базой данных, производится попытка получить данные из кэша.
        try {
            $this->initDb();
        } catch (Exception $e) {
            $this->debug->trace('Trying use cache', __CLASS__, __METHOD__, false);
            $this->page = $this->loadCache($this->cache);
            if (!is_null($this->page)) {
                $this->debug->trace('Trying use cache complete', __CLASS__, __METHOD__, true);
                echo $this->page;
            } else {
                $this->debug->trace('Trying use cache fail: cache not exists', __CLASS__, __METHOD__, true);
                $this->debug->trace('Generating error', __CLASS__, __METHOD__, false);
                $this->generateError(500, 'Database connection error', __CLASS__, __METHOD__, false);
                $this->debug->trace('Generating error complete', __CLASS__, __METHOD__, false);
                echo $this->page;
            }
            return;
        }
        
        //инициализация класса абстрагирующего данные
        $this->initDbModel();
        
        //инициализация объекта сайта
        try {
            $this->initSite();
        } catch (Exception $e) {
            echo $this->page;
            return;
        }
        
        //инициализация объекта управления зарегистрированными пользователями сайта
        if ($this->config->usersEnabled) {
            $this->loadClass('UfoUsers');
            try {
                $this->users = new UfoUsers($this->getContainer());
            } catch (Exception $e) {
                if ($this->config->usersOverrideError) {
                    $this->config->usersEnabled = false;
                } else {
                    //error 500
                    $this->generateError(500, 'Users initialisation error');
                    echo $this->page;
                    return;
                }
            }
        }
        
        //инициализация объекта текущего раздела сайта
        try {
            $this->initSection();
        } catch (Exception $e) {
            echo $this->page;
            return;
        }
        
        //генерация содержимого страницы
        $this->generatePage();
        if (!is_null($this->error)) {
            $err = $this->error->getError();
            $this->generateError($err->code, $err->text, $err->pathRedirect);
        }
        
        //закрытие соединения с БД, уничтожение объектов
        $this->finalize();
        
        //вывод сгенерированной страницы
        echo $this->page;
        
        //выполнение служебных процедур (очистка кэша и т.п.)
        $this->shutdown();
    }
    
    /**
     * Конструктор.
     * @param UfoConfig &$config    ссылка на объект конфигурации
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
        
        //время выполнения скрипта
        $this->debug->setPageStartTime();
        $this->debug->trace('Execution started', __CLASS__, __METHOD__, false);
    }
    
    /**
     * Инициализация среды.
     */
    public function initPhp()
    {
        $this->debug->trace('Init PHP', __CLASS__, __METHOD__, false);
        
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
        
        $this->debug->trace('Init PHP complete', __CLASS__, __METHOD__, true);
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
        //ErrorDocument в .htaccess
        } else if (isset($_GET['error'])) {
            $this->generateError((int) $_GET['error'], 'External error');
        //иначе это главная страница
        } else {
            $this->pathRaw = '/';
        }
        //также сбрасываем путь служебного раздела
        $this->pathSystem = '';
        $this->debug->trace('PathRaw: ' . $this->pathRaw);
    }
    
    /**
     * Проверка является ли текущий раздел служебным.
     * Данный метод должен срабатывать только один раз, затем надо проверять поле pathSystem.
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
     * Попытка использования кэша для текущей страницы.
     * @return boolean
     */
    public function tryCache()
    {
        $this->debug->trace('Trying use cache', 
                            __CLASS__, __METHOD__, false);
        $this->loadClass('UfoCacheFs');
        $this->cache = new UfoCacheFs($this->pathRaw, 
                                      $this->config->cacheFsSettings);
        $this->page = $this->loadCache($this->cache);
        $this->debug->trace(is_null($this->page) ? 'Cache not used' : 'Using cache', 
                            __CLASS__, __METHOD__, true);
        return !is_null($this->page);
    }
    
    /**
     * Установка соединения с базой данных.
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
     * Инициализация объекта модели базы данных.
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
     * Получение данных сайта и обработка пути раздела.
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
        } catch (UfoExceptionPathEmpty $e) { //не срабатывает, т.к. setPathRaw исключает этот случай
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
     * Инициализация объекта текущего раздела.
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
     * Генерация содержимого текущей страницы 
     * посредством объекта модуля обслуживающего текущий раздел.
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
     * Закрытие соединения с базой данных.
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
     * Регистрация процедур выполняемых после завершения работы скрипта.
     */
    public function shutdown()
    {
        $this->debug->trace('Shutdown', __CLASS__, __METHOD__, false);
        if ($this->config->cacheFsSavetime > 0) {
            //восстанавливаем перехватчик ошибок по-умолчанию
            restore_error_handler();
            //отключаем вывод ошибок
            ini_set('display_errors', '0');
            ini_set('error_reporting', 0);
            //регистрируем функцию, вызываемую по завершении выполнения скрипта
            register_shutdown_function('UfoCacheFs::deleteOld', 
                                       $this->config);
        }
        if ('' != $this->config->logPerformance) {
            $this->writeLog($this->pathRaw . "\t" . $this->debug->getPageExecutionTime(), 
                            $this->config->logPerformance);
        }
        $this->debug->trace('Shutdown complete', __CLASS__, __METHOD__, true);
    }
    
    /**
     * Возвращает содержимое текущей страницы.
     * @return string|null
     */
    public function getPage()
    {
        return $this->page;
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
     * Генерация содержимого страницы вследствие возникшей ошибки.
     * @param int $code                    код ошибки
     * @param string $text                 текст ошибки
     * @param string $pathRedirect = ''    путь переадресации для ошибок 301, 302
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
     * Регистрация ошибки.
     * @param int $code                    код ошибки
     * @param string $text                 текст ошибки
     * @param string $pathRedirect = ''    путь переадресации для ошибок 301, 302
     */
    public function registerError($errno, $errstr, $pathRedirect = '')
    {
        $this->loadClass('UfoError');
        $this->loadClass('UfoErrorStruct');
        $this->error = new UfoError(new UfoErrorStruct($errno, $errstr, $pathRedirect),
                                    $this->getContainer());
    }
    
    /**
     * Обработчик ошибок PHP.
     * @param int $errno           код ошибки
     * @param string $errstr       текст ошибки
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
     * Вставка информации из разделов.
     * @param array $options = null    параметры вставки, дополнительные данные
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
