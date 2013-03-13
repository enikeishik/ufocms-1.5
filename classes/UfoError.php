<?php
require_once 'UfoTools.php';
/**
 * Класс генерации страниц ошибок HTTP (404, 500).
 * 
 * @author enikeishik
 *
 */
class UfoError
{
    use UfoTools;

    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Ссылка на объект ядра системы.
     * @var UfoCore
     */
    protected $core = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Ссылка на объект UfoSite, представляющий сайт.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * Ссылка на объект UfoSection, представляющий текущий раздел.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * Объект-структура с данными ошибки.
     * @var UfoErrorStruct
     */
    protected $errorData = null;
    
    public function __construct(UfoErrorStruct $errorData, UfoContainer &$container)
    {
        $this->errorData = $errorData;
        $this->container =& $container;
        $this->unpackContainer();
        
        $data = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $data = $_SERVER['REMOTE_ADDR'];
        }
        $data .= "\t" . $this->errorData;
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
        if (500 == $this->errorData->code) {
            $this->writeLog($data, $this->config->logError);
        } else {
            $this->writeLog($data, $this->config->logWarnings);
        }
    }

    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->core =& $this->container->getCore();
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
    }
    
    /**
     * Возвращает объект-структуру с данными ошибки.
     * @return UfoErrorStruct
     */
    public function getError()
    {
        return $this->errorData;
    }
    
    public function getPage()
    {
        $this->container->setError($this);
        ob_start();
        $this->loadTemplate('UfoTemplateError');
        $template = new UfoTemplateError($this->container);
        $this->loadLayout($template, 'error');
        return ob_get_clean();
    }
}
