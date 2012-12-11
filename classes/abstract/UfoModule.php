<?php
require_once 'UfoModuleInterface.php';
/**
 * Абрстрактный класс модуля, обслуживающего раздел, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoModuleInterface или быть абстрактными.
 * Все классы модулей должны наследовать этот класс.
 */
abstract class UfoModule implements UfoModuleInterface
{
    use UfoTools;
    
    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * Объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
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
     * Ссылка на объект шаблона модуля.
     * @var UfoTemplate
     */
    protected $template = null;

    /**
     * Копия объекта-структуры содержащего данные раздела.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
    /**
     * Конструктор.
     * @param UfoSection   &$section      ссылка на объект текущего раздела
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->section =& $container->getSection();
        $this->db =& $container->getDb();
        $this->debug =& $container->getDebug();
        $this->sectionFields = $this->section->getFields();
        
        $this->container->setModule($this);
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->container);
    }
}
