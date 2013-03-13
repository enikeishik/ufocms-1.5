<?php
require_once 'UfoTemplateInterface.php';
require_once 'classes/UfoToolsExt.php';
/**
 * Абрстрактный класс шаблона модуля, обслуживающего раздел, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoTemplateInterface или быть абстрактными.
 * Все классы шаблонов модулей должны наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoTemplate implements UfoTemplateInterface
{
    use UfoToolsExt;
    
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
     * Объект ошибки.
     * @var UfoError
     */
    protected $error = null;
    
    /**
     * Объект-структура с данными ошибки.
     * @var UfoErrorStruct
     */
    protected $errorData = null;
    
    /**
     * Ссылка на объект UfoSite, представляющий сайт.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * Ссылка на объект текущего раздела.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * Копия объекта-структуры содержащего данные раздела.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
    /**
     * Ссылка на объект модуля текущего раздела.
     * @var UfoModule
     */
    protected $module = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        if (!is_null($this->section)) {
            $this->sectionFields = $this->section->getFields();
        }
        if (!is_null($this->error)) {
            $this->errorData = $this->error->getError();
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
        $this->error =& $this->container->getError();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
        $this->module =& $this->container->getModule();
    }
}
