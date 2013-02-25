<?php
/**
 * Класс-контейнер, хранит ссылки на объекты.
 * 
 * Используется для передачи подчиненным объектам ссылок 
 * на инициализированные объекты с данными, объекты-структуры, 
 * управляющие и вспомогательные объекты.
 * 
 * Например, объект ядра (UfoCore) создавая объект раздела (UfoSection) 
 * передает ему ссылки на инициализированные в ядре объекты 
 * работы с базой данных, объект конфигурации и др. Объект раздела, 
 * в свою очередь, передает создаваемому им объекту модуля раздела
 * ссылки на эти объекты и ссылку на самого себя, чтобы модуль мог
 * использовать методы и данные раздела в своих целях, либо передать
 * ссылку на объект раздела дальше - объекту шаблона раздела.
 * 
 * @author enikeishik
 *
 */
class UfoContainer
{
    /**
     * Ссылка на объект конфигурации системы.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Ссылка на объект работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;

    /**
     * Ссылка на объект ядра системы.
     * @var UfoCore
     */
    protected $core = null;
    
    /**
     * Ссылка на объект сайта.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * Ссылка на объект раздела.
     * @var UfoSection
     */
    protected $section = null;

    /**
     * Ссылка на объект-структуру хранения данных раздела.
     * @var UfoSectionStruct
     */
    protected $sectionStruct = null;
    
    /**
     * Ссылка на объект модуля.
     * @var UfoModule
     */
    protected $module = null;
    
    /**
     * Конструктор.
     * @param array $vars = null    массив данных раздела
     */
    public function __construct(array $vars = null)
    {
        if (is_null($vars)) {
            return;
        }
        foreach ($vars as $key => $val) {
            if (property_exists(__CLASS__, $key)) {
                if (is_object($val)) {
                    /*
                     NOT $this->$key =& $val;
                     потому что $val - ссылка и при следующей итерации 
                     будет указывать на другое значение, а в месте с этим 
                     и все предыдущие $this->$key (которые будут 
                     при таком присваивании `=&` синонимами $val) 
                     также будут указывать на новое значение $val;
                     */
                    $this->$key =& $vars[$key]; 
                } else {
            		$this->$key = (object) $val;
                }
            }
        }
    }
    
    /**
     * 
     * @param UfoConfig $config
     */
    public function setConfig(UfoConfig &$config) { $this->config =& $config; }
    
    /**
     * 
     * @return UfoConfig
     */
    public function &getConfig() { return $this->config; }
    
    /**
     * 
     * @param UfoDb $db
     */
    public function setDb(UfoDb &$db) { $this->db =& $db; }
    
    /**
     * 
     * @return UfoDb
     */
    public function &getDb() { return $this->db; }
    
    /**
     * 
     * @param UfoDebug $debug
     */
    public function setDebug(UfoDebug &$debug) { $this->debug =& $debug; }
    
    /**
     *
     * @return UfoDebug
     */
    public function &getDebug() { return $this->debug; }

    /**
     *
     * @param UfoCore $core
     */
    public function setCore(UfoCore &$core) { $this->core =& $core; }
    
    /**
     *
     * @return UfoCore
     */
    public function &getCore() { return $this->core; }
    
    /**
     * 
     * @param UfoSite $site
     */
    public function setSite(UfoSite &$site) { $this->site =& $site; }
    
    /**
     * 
     * @return UfoSite
     */
    public function &getSite() { return $this->site; }
    
    /**
     * 
     * @param UfoSection $section
     */
    public function setSection(UfoSection &$section) { $this->section =& $section; }
    
    /**
     * 
     * @return UfoSection
     */
    public function &getSection() { return $this->section; }

    /**
     *
     * @param UfoSectionStruct $sectionStruct
     */
    public function setSectionStruct(UfoSectionStruct &$sectionStruct) { $this->sectionStruct =& $sectionStruct; }
    
    /**
     *
     * @return UfoSectionStruct
     */
    public function &getSectionStruct() { return $this->sectionStruct; }
    
    /**
     * 
     * @param UfoModule $module
     */
    public function setModule(UfoModule &$module) { $this->module =& $module; }
    
    /**
     * 
     * @return UfoModule
     */
    public function &getModule() { return $this->module; }
}
