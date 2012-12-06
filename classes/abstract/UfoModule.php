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
     * @param UfoDb      &$db            ссылка на объект для работы с базой данных
     * @param UfoSection &$section         ссылка на объект текущего раздела
     * @param UfoDebug   &$debug = null    ссылка на объект отладки
     */
    public function __construct(UfoDb &$db, UfoSection &$section, UfoDebug &$debug = null)
    {
        $this->db =& $db;
        $this->section =& $section;
        $this->debug =& $debug;
        $this->sectionFields = $this->section->getFields();
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->section, $this, $this->debug);
    }
}
