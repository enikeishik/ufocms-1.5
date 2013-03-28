<?php
require_once 'UfoInsertionItemModuleInterface.php';
/**
 * Абрстрактный класс вставки модуля, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoInsertionModuleInterface или быть абстрактными.
 * Все классы вставок модулей должны наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionItemModule implements UfoInsertionItemModuleInterface
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
     * Ссылка на объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Объект для работы моделью данных.
     * @var UfoCoreDbModel
     */
    private $coreDbModel = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Ссылка на объект шаблона модуля.
     * @var UfoInsertionItemTemplate
     */
    protected $template = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-контейнер ссылок на объекты
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName();
    }

    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->coreDbModel =& $this->container->getCoreDbModel();
        $this->debug =& $this->container->getDebug();
    }
}