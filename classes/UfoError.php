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
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
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
    }

    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
        $this->db =& $this->container->getDb();
        $this->debug =& $this->container->getDebug();
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
