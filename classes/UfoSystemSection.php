<?php
require_once 'UfoSection.php';
/**
 * Класс описывающий служебный раздел сайта.
 *
 * @author enikeishik
 * 
 */
class UfoSystemSection extends UfoSection
{
    /**
     * Путь данные служебного раздела сайта.
     * @var string
     */
    protected $pathSystem = '';
    
    /**
     * Конструктор, формирует объект по идентификатору или пути.
     *
     * @param string       $pathSystem    путь данные служебного раздела сайта
     * @param UfoContainer &$container    ссылка на объект-контейнер ссылок на объекты
     *
     * @throws Exception
     */
    public function __construct($pathSystem, UfoContainer &$container)
    {
        $this->pathSystem = $pathSystem;
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->setFields();
    }
    
    /**
     * Получение данных раздела в виде объекта-структуры UfoSectionStruct.
     * @param mixed $section = null    данные раздела сайта
     */
    protected function setFields($section = null)
    {
        $moduleName = $this->config->systemSections[$this->pathSystem];
        $structName = $moduleName . 
                      $this->config->structSuffix;
        $this->loadModuleStruct($moduleName, $structName);
        if (is_null($section)) {
            $this->fields = new $structName();
        } else if (is_array($section)) {
            $this->fields = new $structName($section);
        } else if (is_object($section) && is_a($section, $structName)) {
            $this->fields = $section;
        }
    }
    
    /**
     * Инициализация объекта модуля, обслуживающего раздел.
     * @throws Exception
     */
    public function initModule()
    {
        $this->container->setSection($this);
        //служебные разделы и обслуживающие их модули перечислены в конфигурации
        if (!array_key_exists($this->pathSystem, $this->config->systemSections)) {
            throw new Exception($this->errors->syssectModuleNotDefined);
        }
        $module = $this->config->systemSections[$this->pathSystem];
        $this->loadModule($module);
        $this->module = new $module($this->container);
        if (!is_a($this->module, 'UfoSystemModule')) {
            throw new Exception($this->errors->syssectModuleIncorrect);
        }
    }
}
