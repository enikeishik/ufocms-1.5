<?php
require_once 'UfoTemplateInterface.php';
/**
 * Абрстрактный класс шаблона модуля, обслуживающего раздел, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoTemplateInterface или быть абстрактными.
 * Все классы шаблонов модулей должны наследовать этот класс.
 */
abstract class UfoTemplate implements UfoTemplateInterface
{
    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * Ссылка на объект текущего раздела.
     * @var UfoSection
     */
    protected $section = null;

    /**
     * Ссылка на объект модуля текущего раздела.
     * @var UfoModule
     */
    protected $module = null;

    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Копия объекта-структуры содержащего данные раздела.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
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
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->module =& $this->container->getModule();
        $this->section =& $this->container->getSection();
        $this->debug =& $this->container->getDebug();
    }
    
    /*
     * Следующие методы могут быть переопределены в дочерних классах 
     * для реализации специфического вывода.
     */
    
    /**
     * Вывод заголовка, отображаемого в заголовке документа.
     */
    public function drawHeadTitle()
    {
        echo '<title>' . $this->sectionFields->title . '</title>' . "\r\n";
    }
    
    /**
     * Вывод мета тэгов.
     */
    public function drawMetaTags()
    {
        
    }
    
    /**
     * Вывод дополнительного кода (JS, CSS, ...) в заголовке документа.
     */
    public function drawHeadCode()
    {
        
    }
    
    /**
     * Вывод заголовка, отображаемого на странице.
     */
    public function drawBodyTitle()
    {
        return '<h1>' . $this->sectionFields->title . '</h1>' . "\r\n";
    }
    
    /**
     * Вывод информации отладки (в конце страницы, в виде комментария HTML).
     */
    public function drawDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
    }
}
