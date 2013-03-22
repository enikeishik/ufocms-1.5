<?php
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
    }

    /**
     * Инициализация объекта модуля, обслуживающего раздел.
     * @throws Exception
     * @todo использовать константу/переменнут вместо строки в throw
     */
    public function initModule()
    {
        $this->container->setSection($this);
        //служебные разделы и обслуживающие их модули перечислены в конфигурации
        if (!array_key_exists($this->pathSystem, $this->config->systemSections)) {
            throw new Exception('Module class not defined');
        }
        $module = $this->config->systemSections[$this->pathSystem];
        $this->loadModule($module);
        $this->module = new $module($this->container);
        if (!is_a($this->module, 'UfoSystemModule')) {
            throw new Exception('Module class must extends UfoSystemModule abstract class');
        }
    }
}
