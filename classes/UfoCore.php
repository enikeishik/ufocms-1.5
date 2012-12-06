<?php
final class UfoCore
{
    use UfoTools;
    
    private $config = null;
    private $pathRaw = '';
    private $path = '';
    private $db = null;
    private $cache = null;
    private $site = null;
    private $section = null;
    private $params = null;
    private $page = null;
    private $debug = null;
    
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
        if (isset($_GET['path'])) {
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
        //init site params, path, etc.
        $this->path = $this->pathRaw;
    }
    
    /**
     * Инициализация объекта текущего раздела.
     * @todo errorHamdlerModule
     * @throws Exception
     */
    public function initSection()
    {
        $this->loadClass('UfoSection');
        try {
            $this->section = new UfoSection($this->config, 
                                            $this->db, 
                                            $this->path, 
                                            $this->debug);
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
}
