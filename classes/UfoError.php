<?php
require_once 'UfoTools.php';
/**
 *  ласс генерации страниц ошибок HTTP (404, 500).
 * 
 * @author enikeishik
 *
 */
class UfoError
{
    use UfoTools;

    /**
     * —сылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * —сылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ќбъект дл€ работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * —сылка на объект UfoSite, представл€ющий сайт.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * —сылка на объект UfoSection, представл€ющий текущий раздел.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * —сылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
    }

    /**
     * ѕрисванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
        $this->db =& $this->container->getDb();
        $this->debug =& $this->container->getDebug();
    }
    
    public function getPage()
    {
        ob_start();
        $this->loadTemplate('UfoTemplateError');
        $template = new UfoTemplateError($this->container);
        $this->loadLayout($template, 'error');
        return ob_get_clean();
    }
}
