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
    protected $fields = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->module =& $container->getModule();
        $this->section =& $container->getSection();
        $this->debug =& $container->getDebug();
        if (!is_null($this->section)) {
            $this->fields = $this->section->getFields();
        }
    }
}
