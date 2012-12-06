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
     * @param UfoSection &$section         ссылка на объект текущего раздела
     * @param UfoModule  &$module          ссылка на объект модуля текущего раздела
     * @param UfoDebug   &$debug = null    ссылка на объект отладки
     */
    public function __construct(UfoSection &$section, UfoModule &$module, UfoDebug &$debug = null)
    {
        $this->section =& $section;
        $this->module =& $module;
        $this->debug =& $debug;
        $this->fields = $this->section->getFields(); 
    }
}
