<?php
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
        
        if ($this->config->debug) {
            //DEBUG: время выполнения скрипта
            $this->loadClass('UfoDebug');
            $this->debug = new UfoDebug();
            $this->debug->setPageStartTime();
        }
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
    }
    
    /**
     * Попытка использования кэша для текущей страницы.
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
     * Установка соединения с базой данных.
     * В случае ошибки соединения с базой данных, производится попытка получить данные из кэша.
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
            //пытаемся получить данные из кэша
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
     * Получение данных сайта и обработка пути раздела.
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
     * Инициализация объекта текущего раздела.
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
     * Генерация содержимого текущей страницы 
     * посредством объекта модуля обслуживающего текущий раздел.
     */
    public function generatePage()
    {
        $this->section->initModule();
        $this->page = $this->section->getPage();
    }
    
    /**
     * Закрытие соединения с базой данных.
     */
    public function finalize()
    {
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
    
    private function &getContainer()
    {
        if (is_null($this->container)) {
            $this->loadClass('UfoContainer');
            $this->container = new UfoContainer();
        }
        return $this->container;
    }
}
